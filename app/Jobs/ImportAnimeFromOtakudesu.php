<?php

namespace App\Jobs;

use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Services\Content\OtakudesuScraper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportAnimeFromOtakudesu implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public function __construct(public readonly string $slug) {}

    public function handle(OtakudesuScraper $scraper): void
    {
        $slug = $this->slug;
        try {
            Cache::put("import_progress_{$slug}", ['current' => 0, 'total' => 0, 'status' => 'fetching_info', 'title' => $slug], 3600);

            $info = $scraper->getAnimeInfo($slug);
            if (empty($info) || empty($info['title'])) {
                Cache::put("import_progress_{$slug}", ['current' => 0, 'total' => 0, 'status' => 'failed', 'title' => $slug], 3600);
                Log::error("ImportAnimeFromOtakudesu: Failed to fetch info for slug {$slug}");

                return;
            }

            $title = $info['title'];
            $episodeLists = $info['episode_lists'] ?? [];
            $totalEpisodes = count($episodeLists);

            Cache::put("import_progress_{$slug}", ['current' => 0, 'total' => $totalEpisodes, 'status' => 'processing', 'title' => $title], 3600);

            $releaseYear = null;
            if (! empty($info['release_date']) && preg_match('/(\d{4})/', $info['release_date'], $m)) {
                $releaseYear = (int) $m[1];
            }

            $anime = Anime::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'title_alternative' => $info['japanese_title'] ?? null,
                    'synopsis' => $info['synopsis'] ?? null,
                    'status' => VideoStatus::mapAnimeStatus($info['status'] ?? null),
                    'rating' => is_numeric($info['rating']) ? (float) $info['rating'] : 0.0,
                    'total_episodes' => $totalEpisodes > 0 ? $totalEpisodes : null,
                    'studio' => $info['studio'] ?? null,
                    'year' => $releaseYear,
                    'poster_path' => $info['poster'] ?? null,
                    'is_published' => true,
                ]
            );

            if (! empty($info['genres'])) {
                $genreIds = [];
                foreach ($info['genres'] as $genreName) {
                    $genre = Genre::firstOrCreate(['slug' => Str::slug($genreName)], ['name' => $genreName]);
                    $genreIds[] = $genre->id;
                }
                $anime->genres()->sync($genreIds);
            }

            foreach ($episodeLists as $index => $epData) {
                $current = $index + 1;
                Cache::put("import_progress_{$slug}", [
                    'current' => $current,
                    'total' => $totalEpisodes,
                    'status' => 'processing',
                    'title' => $title,
                    'current_episode' => $epData['episode'] ?? "Episode {$current}",
                ], 3600);

                $epSlug = $epData['slug'] ?? '';
                if (empty($epSlug)) {
                    continue;
                }

                $episodeNumber = $current;
                if (preg_match('/episode-(\d+)/i', $epSlug, $m)) {
                    $episodeNumber = (int) $m[1];
                } elseif (preg_match('/(\d+)/', $epSlug, $m)) {
                    $episodeNumber = (int) $m[0];
                }

                Episode::updateOrCreate(
                    ['anime_id' => $anime->id, 'external_id_otakudesu' => $epSlug],
                    [
                        'episode_number' => $episodeNumber,
                        'title' => $epData['episode'] ?? "Episode {$episodeNumber}",
                        'status' => 'ready',
                    ]
                );

                // TIDAK ADA fetch sources di sini!
                // Stream sources di-fetch on-demand oleh StreamController saat user buka watch page
            }

            // Otomatis sinkronkan file 1080p dari Google Drive jika diaktifkan di .env
            if (config('gdrive.enabled', false)) {
                try {
                    Artisan::call('anime:sync-gdrive', ['anime' => $anime->slug]);
                } catch (\Throwable $e) {
                    Log::info("Auto-sync Google Drive for {$slug} skipped/non-fatal: ".$e->getMessage());
                }
            }

            Cache::put("import_progress_{$slug}", [
                'current' => $totalEpisodes,
                'total' => $totalEpisodes,
                'status' => 'completed',
                'title' => $title,
            ], 3600);
        } catch (\Throwable $e) {
            Log::error("Import failed for {$slug}: ".$e->getMessage(), [
                'exception' => $e,
            ]);
            Cache::put("import_progress_{$slug}", [
                'current' => 0,
                'total' => 0,
                'status' => 'failed',
                'title' => $slug,
                'error' => $e->getMessage(),
            ], 3600);
            throw $e;
        }
    }

    private function mapStatus(?string $otakudesuStatus): string
    {
        return VideoStatus::mapAnimeStatus($otakudesuStatus);
    }
}
