<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class OtakudesuScraper
{
    private string $b = "https://otakudesu.blog";

    public function search(string $q): array
    {
        $res = Http::timeout(15)->get("{$this->b}/", ["s" => $q, "post_type" => "anime"]);
        if ($res->failed()) return [];
        $c = new Crawler($res->body()); $r = [];
        $c->filter(".chivsrc li")->each(function (Crawler $n) use (&$r) {
            $a = $n->filter("h2 a"); if ($a->count() === 0) return;
            $slug = preg_match("/\\/anime\\/([^\\/]+)/", $a->attr("href"), $m) ? $m[1] : "";
            $poster = $n->filter("img")->count() > 0 ? $n->filter("img")->attr("src") : null;
            $rating = null; $status = null;
            $n->filter(".set")->each(function (Crawler $s) use (&$rating, &$status) {
                $t = trim($s->text());
                if (str_starts_with($t, "Rating :")) $rating = trim(str_replace("Rating :", "", $t));
                elseif (str_starts_with($t, "Status :")) $status = trim(str_replace("Status :", "", $t));
            });
            $r[] = ["title" => trim($a->text()), "slug" => $slug, "poster" => $poster, "rating" => $rating, "status" => $status];
        });
        return $r;
    }

    public function getAnimeInfo(string $slug): array
    {
        $res = Http::timeout(15)->get("{$this->b}/anime/{$slug}/");
        if ($res->failed()) return [];
        $c = new Crawler($res->body());

        $text = fn(string $sel): ?string => $c->filter($sel)->count() > 0
            ? trim($c->filter($sel)->eq(0)->text())
            : null;

        $infoSpanAt = function (int $index) use ($c): ?string {
            $ps = $c->filter(".infozin .infozingle p");
            if ($ps->count() <= $index) return null;
            $p = $ps->eq($index);
            $spans = $p->filter("span");
            if ($spans->count() > 0) return trim($spans->eq(0)->text());
            $t = trim($p->text());
            return $t !== '' ? $t : null;
        };

        $byLabel = [];
        $c->filter(".infozin .infozingle p")->each(function (Crawler $p) use (&$byLabel) {
            $full = trim($p->text());
            if ($full !== '' && str_contains($full, ":")) {
                [$label, $value] = explode(":", $full, 2);
                $byLabel[strtolower(trim($label))] = trim($value);
            }
        });
        $strip = fn(?string $v, string $prefix): ?string => ($v === null || $v === '')
            ? null
            : (str_starts_with($v, $prefix) ? trim(substr($v, strlen($prefix))) : $v);

        $genres = [];
        $c->filter(".info a[href*=\"/genres/\"], .infozin .infozingle p a")->each(function (Crawler $n) use (&$genres) {
            $t = trim($n->text()); if ($t && !in_array($t, $genres)) $genres[] = $t;
        });
        $eps = [];
        $c->filter(".episodelist ul li a")->each(function (Crawler $n) use (&$eps) {
            $href = $n->attr("href");
            if (! $href || ! str_contains($href, "/episode/")) return;
            $slug = preg_match("/\\/episode\\/([^\\/]+)/", $href, $m) ? $m[1] : "";
            if ($slug === "") return;
            $eps[] = ["episode" => trim($n->text()), "slug" => $slug, "otakudesu_url" => $href];
        });

        $rawTitle = $byLabel["judul"] ?? $infoSpanAt(0);
        $title = $strip($rawTitle, "Judul: ") ?? $text(".jdlsub");
        $rawJp = $byLabel["japanese"] ?? $infoSpanAt(1);
        $japanese = $strip($rawJp, "Japanese: ");
        $rawRating = $byLabel["skor"] ?? $infoSpanAt(2);
        $rating = $strip($rawRating, "Skor: ");
        $rawStatus = $byLabel["status"] ?? $infoSpanAt(5);
        $status = $strip($rawStatus, "Status: ");
        $rawRelease = $byLabel["tanggal rilis"] ?? $infoSpanAt(8);
        $releaseDate = $strip($rawRelease, "Tanggal Rilis: ");
        $rawStudio = $byLabel["studio"] ?? $infoSpanAt(9);
        $studio = $strip($rawStudio, "Studio: ");

        return [
            "title" => $title,
            "japanese_title" => $japanese,
            "rating" => $rating,
            "poster" => $c->filter(".fotoanime img")->count() > 0 ? $c->filter(".fotoanime img")->attr("src") : null,
            "synopsis" => $text(".sinopc"), "genres" => $genres,
            "status" => $status,
            "release_date" => $releaseDate,
            "studio" => $studio,
            "episode_lists" => $eps,
        ];
    }

    public function getEpisodeSources(string $episodeSlug): array
    {
        $res = Http::timeout(20)->get("{$this->b}/episode/{$episodeSlug}/");
        if ($res->failed()) return [];
        $body = $res->body();
        $c = new Crawler($body);

        // Iframe player: id="pembed" ada di <div>, bukan <iframe>.
        // Pakai fallback bertingkat + prioritas desustream/dstream, skip iframe iklan.
        $streamUrl = null;
        $selectors = [
            '#pembed iframe',
            '.responsive-embed-stream iframe',
            '.player-embed iframe',
            'iframe[src*="desustream"]',
            'iframe[src*="dstream"]',
        ];

        foreach ($selectors as $sel) {
            $node = $c->filter($sel);
            if ($node->count() > 0) {
                $candidate = $node->eq(0)->attr('src');
                if (!empty($candidate)) { $streamUrl = $candidate; break; }
            }
        }

        // Fallback: cari iframe mana pun yang src-nya desustream/dstream (skip iklan).
        if (empty($streamUrl)) {
            $c->filter('iframe')->each(function (Crawler $iframe) use (&$streamUrl) {
                if (!empty($streamUrl)) return;
                $src = $iframe->attr('src') ?? '';
                if ($src !== '' && (str_contains($src, 'desustream') || str_contains($src, 'dstream'))) {
                    $streamUrl = $src;
                }
            });
        }

        // Fallback terakhir: regex langsung di HTML mentah (paling aman).
        if (empty($streamUrl)) {
            if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $body, $m)) {
                // Jika ada beberapa iframe, prefer yang desustream/dstream.
                preg_match_all('/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $body, $all);
                $found = $all[1] ?? [];
                foreach ($found as $src) {
                    if (str_contains($src, 'desustream') || str_contains($src, 'dstream')) {
                        $streamUrl = $src;
                        break;
                    }
                }
                if (empty($streamUrl) && !empty($m[1])) {
                    $streamUrl = $m[1];
                }
            }
        }
        $dl = ["mp4" => [], "mkv" => []];
        $lists = $c->filter(".download ul");
        if ($lists->count() >= 1) {
            $lists->eq(0)->filter("li")->each(function (Crawler $li) use (&$dl) {
                $resName = $li->filter("strong")->count() > 0 ? trim($li->filter("strong")->eq(0)->text()) : "unknown";
                $resName = trim(preg_replace("/^MP4\\s*/i", "", $resName));
                $urls = []; $li->filter("a")->each(function (Crawler $a) use (&$urls) { $urls[] = ["provider" => trim($a->text()), "url" => $a->attr("href")]; });
                $dl["mp4"][] = ["resolution" => $resName, "urls" => $urls];
            });
        }
        if ($lists->count() >= 2) {
            $lists->eq(1)->filter("li")->each(function (Crawler $li) use (&$dl) {
                $resName = $li->filter("strong")->count() > 0 ? trim($li->filter("strong")->eq(0)->text()) : "unknown";
                $resName = trim(preg_replace("/^MKV\\s*/i", "", $resName));
                $urls = []; $li->filter("a")->each(function (Crawler $a) use (&$urls) { $urls[] = ["provider" => trim($a->text()), "url" => $a->attr("href")]; });
                $dl["mkv"][] = ["resolution" => $resName, "urls" => $urls];
            });
        }
        $prev = null; $next = null; $anime = null; $flir = $c->filter(".flir a"); $cnt = $flir->count();
        if ($cnt > 0) {
            $h0 = $flir->eq(0)->attr("href");
            if ($h0 && str_contains($h0, "/episode/")) { preg_match("/\\/episode\\/([^\\/]+)/", $h0, $m); $prev = ["slug" => $m[1] ?? "", "otakudesu_url" => $h0]; }
            if ($cnt >= 3) {
                $h1 = $flir->eq(1)->attr("href");
                if ($h1 && str_contains($h1, "/anime/")) { preg_match("/\\/anime\\/([^\\/]+)/", $h1, $m); $anime = ["slug" => $m[1] ?? "", "otakudesu_url" => $h1]; }
                $h2 = $flir->eq(2)->attr("href");
                if ($h2 && str_contains($h2, "/episode/")) { preg_match("/\\/episode\\/([^\\/]+)/", $h2, $m); $next = ["slug" => $m[1] ?? "", "otakudesu_url" => $h2]; }
            } elseif ($cnt === 2) {
                $h1 = $flir->eq(1)->attr("href");
                if ($h1 && str_contains($h1, "/episode/")) { preg_match("/\\/episode\\/([^\\/]+)/", $h1, $m); $next = ["slug" => $m[1] ?? "", "otakudesu_url" => $h1]; }
                elseif ($h1 && str_contains($h1, "/anime/")) { preg_match("/\\/anime\\/([^\\/]+)/", $h1, $m); $anime = ["slug" => $m[1] ?? "", "otakudesu_url" => $h1]; }
            }
        }
        return [
            "stream_url" => $streamUrl,
            "download_urls" => $dl,
            "has_next_episode" => $next !== null,
            "next_episode" => $next,
            "has_previous_episode" => $prev !== null,
            "previous_episode" => $prev,
            "anime" => $anime,
        ];
    }
}
