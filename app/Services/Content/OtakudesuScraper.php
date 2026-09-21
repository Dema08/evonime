<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Cookie\CookieJar;
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
        // --- Extract SEMUA mirror (kualitas x server) dari .mirrorstream ---
        // Struktur: <div class="mirrorstream"><ul class="m360p"><li><a data-content="base64">otakuplay</a></li>...
        // data-content = base64 JSON: {"id":92625,"i":0,"q":"360p"}
        $mirrors = [];
        $c->filter('.mirrorstream ul')->each(function (Crawler $ul) use (&$mirrors) {
            $class = $ul->attr('class') ?? '';
            if (!preg_match('/m(\d+p)/i', $class, $m)) return;
            $quality = strtolower($m[1]); // '360p', '480p', '720p'
            $ul->filter('li a')->each(function (Crawler $a) use (&$mirrors, $quality) {
                $dataContent = $a->attr('data-content') ?? '';
                $serverName = trim($a->text());
                if ($dataContent !== '' && $serverName !== '') {
                    $mirrors[] = [
                        'quality' => $quality,
                        'server' => $serverName,
                        'data_content' => $dataContent,
                    ];
                }
            });
        });
        // Regex fallback kalau DomCrawler gagal (mis. markup tidak standar)
        if (empty($mirrors) && preg_match_all('/data-content=["\']([^"\']+)["\'][^>]*>([^<]+)</i', $body, $all, PREG_SET_ORDER)) {
            foreach ($all as $row) {
                $decoded = json_decode(base64_decode($row[1]), true);
                if (is_array($decoded) && isset($decoded['q'])) {
                    $mirrors[] = [
                        'quality' => strtolower(trim((string) $decoded['q'])),
                        'server' => trim($row[2]),
                        'data_content' => $row[1],
                    ];
                }
            }
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
            "mirrors" => $mirrors,
            "download_urls" => $dl,
            "has_next_episode" => $next !== null,
            "next_episode" => $next,
            "has_previous_episode" => $prev !== null,
            "previous_episode" => $prev,
            "anime" => $anime,
        ];
    }

    /**
     * Resolve mirror server Otakudesu (data-content base64 JSON {id,i,q})
     * menjadi URL iframe embed via admin-ajax.php (2 langkah: nonce + resolve).
     * Nonce direspons sebagai JSON {"data":"..."} dan kedua request harus
     * berbagi cookie session yang sama (seperti browser).
     */
    public function resolveMirror(string $dataContent): ?string
    {
        $decoded = json_decode(base64_decode($dataContent), true);
        if (!is_array($decoded) || !isset($decoded['id'])) return null;

        try {
            $jar = new CookieJar();
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Referer' => 'https://otakudesu.blog/',
                'X-Requested-With' => 'XMLHttpRequest',
            ];

            // Step 1: Get nonce (respons JSON {"data":"..."})
            $nonceRes = Http::timeout(15)
                ->withOptions(['cookies' => $jar])
                ->withHeaders($headers)
                ->asForm()
                ->post('https://otakudesu.blog/wp-admin/admin-ajax.php', [
                    'action' => 'aa1208d27f29ca340c92c66d1926f13f',
                ]);
            if ($nonceRes->failed()) return null;
            $nonceJson = json_decode($nonceRes->body(), true);
            $nonce = is_array($nonceJson) && !empty($nonceJson['data'])
                ? trim((string) $nonceJson['data'])
                : trim($nonceRes->body(), " \t\n\r\0\x0B\"'");
            if ($nonce === '') return null;

            // Step 2: Resolve mirror (respons JSON {"data":"<base64 html>"})
            $res = Http::timeout(20)
                ->withOptions(['cookies' => $jar])
                ->withHeaders($headers)
                ->asForm()
                ->post('https://otakudesu.blog/wp-admin/admin-ajax.php', [
                    'id' => $decoded['id'],
                    'i' => $decoded['i'] ?? 0,
                    'q' => $decoded['q'] ?? '480p',
                    'nonce' => $nonce,
                    'action' => '2a3505c93b0035d3f455df82bf976b84',
                ]);

            if ($res->failed()) return null;
            $resJson = json_decode($res->body(), true);
            $html = !empty($resJson['data']) ? base64_decode((string) $resJson['data']) : base64_decode($res->body());
            if (empty($html)) return null;

            $crawler = new Crawler($html);
            if ($crawler->filter('iframe')->count() > 0) {
                return $crawler->filter('iframe')->eq(0)->attr('src');
            }
            return null;
        } catch (\Throwable $e) {
            Log::warning("resolveMirror failed: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Cek apakah $url boleh di-load di dalam <iframe> dari origin aplikasi.
     * Membaca header X-Frame-Options dan Content-Security-Policy frame-ancestors.
     *
     * Contoh nyata: desustream.me mengirim
     *   Content-Security-Policy: frame-ancestors 'self' https://otakudesu.blog ... http://localhost:* http://127.0.0.1:*
     * sehingga embed hanya tampil kalau Evonime diakses via localhost/127.0.0.1.
     *
     * @return array{embeddable: bool, reason: ?string}
     */
    public function isEmbeddable(string $url, ?string $appOrigin = null): array
    {
        if (trim($url) === '') {
            return ['embeddable' => false, 'reason' => 'URL embed kosong.'];
        }

        $target = parse_url($url) ?: [];
        $targetScheme = strtolower((string) ($target['scheme'] ?? 'https'));
        $targetHost = strtolower((string) ($target['host'] ?? ''));
        $targetPort = isset($target['port']) ? (int) $target['port'] : ($targetScheme === 'https' ? 443 : 80);

        $app = parse_url($appOrigin ?: (string) config('app.url', 'http://localhost:8000')) ?: [];
        $appScheme = strtolower((string) ($app['scheme'] ?? 'http'));
        $appHost = strtolower((string) ($app['host'] ?? 'localhost'));
        $appPort = isset($app['port']) ? (int) $app['port'] : ($appScheme === 'https' ? 443 : 80);

        $sameOriginAsTarget = ($appScheme === $targetScheme && $appHost === $targetHost && $appPort === $targetPort);

        try {
            $headers = [
                'Referer' => "{$this->b}/",
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ];
            $res = Http::timeout(8)->withHeaders($headers)->head($url);
            if ($res->status() >= 400) {
                // Sebagian server menolak HEAD — ulangi pakai GET.
                $res = Http::timeout(10)->withHeaders($headers)->get($url);
            }
            $xfo = strtoupper(trim((string) $res->header('X-Frame-Options')));
            $csp = trim((string) $res->header('Content-Security-Policy'));
        } catch (\Throwable $e) {
            // Gagal cek (timeout/network) → fail-open, biarkan browser yang mendeteksi.
            return ['embeddable' => true, 'reason' => null];
        }

        if ($xfo !== '') {
            if (str_contains($xfo, 'DENY')) {
                return [
                    'embeddable' => false,
                    'reason' => 'Server mengirim X-Frame-Options: DENY — embed dilarang di semua situs lain.',
                ];
            }
            if (str_contains($xfo, 'SAMEORIGIN') && ! $sameOriginAsTarget) {
                return [
                    'embeddable' => false,
                    'reason' => 'Server mengirim X-Frame-Options: SAMEORIGIN — hanya boleh di-embed dari domain server itu sendiri.',
                ];
            }
        }

        $frameAncestors = $this->parseFrameAncestors($csp);
        if ($frameAncestors !== null) {
            $allowed = false;
            foreach ($frameAncestors as $source) {
                if ($source === "'none'") {
                    $allowed = false;
                    break;
                }
                if ($source === '*') {
                    $allowed = true;
                    break;
                }
                if ($source === "'self'") {
                    if ($sameOriginAsTarget) {
                        $allowed = true;
                        break;
                    }
                    continue;
                }
                if ($this->hostSourceMatches($source, $appScheme, $appHost, $appPort)) {
                    $allowed = true;
                    break;
                }
            }

            if (! $allowed) {
                return [
                    'embeddable' => false,
                    'reason' => 'Server hanya mengizinkan embed dari: '
                        . implode(' ', $frameAncestors)
                        . '. Buka Evonime via origin yang diizinkan (mis. http://localhost:8000 / http://127.0.0.1:8000) '
                        . 'atau gunakan tombol "Buka di Tab Baru".',
                ];
            }
        }

        return ['embeddable' => true, 'reason' => null];
    }

    /**
     * Ambil daftar source expression dari direktif frame-ancestors.
     * Null = direktif tidak ada (tidak ada batasan).
     *
     * @return array<int, string>|null
     */
    private function parseFrameAncestors(string $csp): ?array
    {
        if ($csp === '' || ! preg_match('/frame-ancestors([^;]*)/i', $csp, $m)) {
            return null;
        }

        $sources = preg_split('/\s+/', trim($m[1])) ?: [];

        return array_values(array_filter(
            array_map('trim', $sources),
            fn (string $s): bool => $s !== ''
        ));
    }

    /**
     * Cocokkan satu CSP host-source (mis. "http://localhost:*", "*.otakudesu.blog")
     * dengan origin aplikasi.
     */
    private function hostSourceMatches(string $source, string $scheme, string $host, int $port): bool
    {
        $source = strtolower(trim($source));
        if ($source === '') {
            return false;
        }

        $sourceScheme = null;
        if (str_contains($source, '://')) {
            [$sourceScheme, $source] = explode('://', $source, 2);
        }

        $sourcePort = null;
        if (preg_match('/^(.*):(\*|\d+)$/', $source, $pm)) {
            $source = $pm[1];
            $sourcePort = $pm[2] === '*' ? '*' : (int) $pm[2];
        }

        if ($sourceScheme !== null && $sourceScheme !== '*' && $sourceScheme !== $scheme) {
            return false;
        }
        if ($sourcePort !== null && $sourcePort !== '*' && (int) $sourcePort !== $port) {
            return false;
        }
        if ($source === '*') {
            return true;
        }
        if (str_starts_with($source, '*.')) {
            return str_ends_with($host, substr($source, 1)); // ".otakudesu.blog"
        }

        return $source === $host;
    }
}
