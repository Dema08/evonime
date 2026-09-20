<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtakudesuProvider implements ContentProviderInterface
{
    public function __construct(
        private readonly string $baseUrl = 'http://localhost:8080',
    ) {}

    public function search(string $query): array
    {
        $cacheKey = "otakudesu.search." . md5($query);

        return Cache::remember($cacheKey, 6 * 3600, function () use ($query) {
            $response = Http::timeout(15)
                ->get("{$this->baseUrl}/v1/search/" . urlencode($query));

            if ($response->failed()) {
                Log::warning('OtakudesuProvider search failed', [
                    'query' => $query,
                    'status' => $response->status(),
                ]);
                return ['results' => []];
            }

            $data = $response->json();
            $results = [];
            foreach ($data['data'] ?? [] as $item) {
                $results[] = [
                    'id'       => $item['slug'] ?? '',
                    'title'    => $item['title'] ?? '',
                    'image'    => $item['poster'] ?? null,
                    'provider' => $this->getProviderName(),
                    'rating'   => $item['rating'] ?? null,
                    'status'   => $item['status'] ?? null,
                ];
            }

            return ['results' => $results];
        });
    }

    public function getAnimeInfo(string $providerId): array
    {
        $cacheKey = "otakudesu.info." . md5($providerId);

        return Cache::remember($cacheKey, 12 * 3600, function () use ($providerId) {
            $response = Http::timeout(15)
                ->get("{$this->baseUrl}/v1/anime/{$providerId}");

            if ($response->failed()) {
                Log::warning('OtakudesuProvider info failed', [
                    'providerId' => $providerId,
                    'status'     => $response->status(),
                ]);
                return [
                    'id'           => $providerId,
                    'title'        => '',
                    'synopsis'     => null,
                    'image'        => null,
                    'rating'       => null,
                    'genres'       => [],
                    'episodes'     => [],
                    'release_year' => null,
                ];
            }

            $body = $response->json();
            $data = $body['data'] ?? [];

            $episodes = [];
            foreach ($data['episode_lists'] ?? [] as $index => $ep) {
                $episodeNum = $index + 1;
                $slug = $ep['slug'] ?? '';

                // Coba extract nomor episode dari slug dengan beberapa pola
                if (preg_match('/episode-(\d+)/i', $slug, $matches)) {
                    $episodeNum = (int) $matches[1];
                } elseif (preg_match('/(\d+)/', $slug, $matches)) {
                    $episodeNum = (int) $matches[0];
                }

                $episodes[] = [
                    'id'     => $slug,
                    'number' => $episodeNum,
                    'title'  => $ep['episode'] ?? null,
                ];
            }

            $genres = [];
            foreach ($data['genres'] ?? [] as $genre) {
                if (is_string($genre)) {
                    $genres[] = $genre;
                } elseif (is_array($genre) && isset($genre['name'])) {
                    $genres[] = $genre['name'];
                }
            }

            return [
                'id'           => $data['slug'] ?? $providerId,
                'title'        => $data['title'] ?? '',
                'synopsis'     => $data['synopsis'] ?? null,
                'image'        => $data['poster'] ?? null,
                'rating'       => $data['rating'] ?? null,
                'genres'       => $genres,
                'episodes'     => $episodes,
                'release_year' => $this->extractReleaseYear($data['release_date'] ?? null),
            ];
        });
    }

    public function getEpisodeSources(string $episodeId): array
    {
        $cacheKey = "otakudesu.sources." . md5($episodeId);

        return Cache::remember($cacheKey, 30 * 60, function () use ($episodeId) {
            $response = Http::timeout(20)
                ->get("{$this->baseUrl}/v1/episode/{$episodeId}");

            if ($response->failed()) {
                Log::warning('OtakudesuProvider sources failed', [
                    'episodeId' => $episodeId,
                    'status'    => $response->status(),
                ]);
                return ['sources' => [], 'subtitles' => [], 'navigation' => [], 'download_urls' => []];
            }

            $body = $response->json();
            $data = $body['data'] ?? [];

            $sources = [];
            if (!empty($data['stream_url'])) {
                $sources[] = [
                    'url'          => $data['stream_url'],
                    'quality'      => 'default',
                    'is_m3u8'      => str_contains($data['stream_url'], '.m3u8'),
                    'is_embed'     => true,
                    'provider'     => 'otakudesu',
                    'subtitle_type' => 'hardsub',
                    'default_lang' => 'Indonesia',
                ];
            }

            // download_urls: mp4 + mkv
            $downloadUrls = [];
            foreach (['mp4', 'mkv'] as $format) {
                $downloadUrls[$format] = $data['download_urls'][$format] ?? [];
                foreach ($data['download_urls'][$format] ?? [] as $dl) {
                    $sources[] = [
                        'url'          => $dl['urls'][0]['url'] ?? '',
                        'quality'      => $dl['resolution'] ?? 'default',
                        'is_m3u8'      => false,
                        'is_embed'     => false,
                        'provider'     => 'otakudesu',
                        'subtitle_type' => 'hardsub',
                        'default_lang' => 'Indonesia',
                    ];
                }
            }

            return [
                'sources'      => $sources,
                'subtitles'    => [],
                'navigation'   => [
                    'has_next'        => $data['has_next_episode'] ?? false,
                    'next_slug'       => $data['next_episode']['slug'] ?? null,
                    'has_previous'    => $data['has_previous_episode'] ?? false,
                    'previous_slug'   => $data['previous_episode']['slug'] ?? null,
                ],
                'download_urls' => $downloadUrls,
            ];
        });
    }

    public function getProviderName(): string
    {
        return 'otakudesu';
    }

    /**
     * Ekstrak tahun dari field release_date (contoh "Apr 5, 2017" → 2017).
     */
    private function extractReleaseYear(?string $releaseDate): ?int
    {
        if (empty($releaseDate)) {
            return null;
        }
        if (preg_match('/(\d{4})/', $releaseDate, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}