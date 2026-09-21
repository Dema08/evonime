<?php

namespace App\Services\Content;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtakudesuProvider implements ContentProviderInterface
{
    public function __construct(private readonly OtakudesuScraper $scraper) {}

    public function search(string $query): array
    {
        $cacheKey = "otakudesu.search." . md5($query);
        return Cache::remember($cacheKey, 6 * 3600, function () use ($query) {
            $data = $this->scraper->search($query);
            $results = [];
            foreach ($data ?? [] as $item) {
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
            $data = $this->scraper->getAnimeInfo($providerId);
            if (empty($data) || empty($data['episode_lists'])) {
                Log::warning('OtakudesuProvider info failed', ['providerId' => $providerId]);
                return [
                    'id' => $providerId, 'title' => '', 'synopsis' => null,
                    'image' => null, 'rating' => null, 'genres' => [],
                    'episodes' => [], 'release_year' => null,
                ];
            }
            $episodes = [];
            foreach ($data['episode_lists'] ?? [] as $index => $ep) {
                $episodeNum = $index + 1;
                $slug = $ep['slug'] ?? '';
                if (preg_match('/episode-(\d+)/i', $slug, $matches)) $episodeNum = (int) $matches[1];
                elseif (preg_match('/(\d+)/', $slug, $matches)) $episodeNum = (int) $matches[0];
                $episodes[] = ['id' => $slug, 'number' => $episodeNum, 'title' => $ep['episode'] ?? null];
            }
            $genres = [];
            foreach ($data['genres'] ?? [] as $genre) {
                if (is_string($genre)) $genres[] = $genre;
                elseif (is_array($genre) && isset($genre['name'])) $genres[] = $genre['name'];
            }
            return [
                'id' => $providerId,
                'title' => $data['title'] ?? '',
                'synopsis' => $data['synopsis'] ?? null,
                'image' => $data['poster'] ?? null,
                'rating' => $data['rating'] ?? null,
                'genres' => $genres,
                'episodes' => $episodes,
                'release_year' => $this->extractReleaseYear($data['release_date'] ?? null),
            ];
        });
    }

    public function getEpisodeSources(string $episodeId): array
    {
        $cacheKey = "otakudesu.sources." . md5($episodeId);
        return Cache::remember($cacheKey, 30 * 60, function () use ($episodeId) {
            $data = $this->scraper->getEpisodeSources($episodeId);
            if (empty($data)) {
                Log::warning('OtakudesuProvider sources failed', ['episodeId' => $episodeId]);
                return ['sources' => [], 'subtitles' => [], 'navigation' => [], 'download_urls' => []];
            }
            $sources = [];
            if (!empty($data['stream_url'])) {
                $sources[] = [
                    'url' => $data['stream_url'], 'quality' => 'default',
                    'is_m3u8' => str_contains($data['stream_url'], '.m3u8'),
                    'is_embed' => true, 'provider' => 'otakudesu',
                    'subtitle_type' => 'hardsub', 'default_lang' => 'Indonesia',
                ];
            }
            $downloadUrls = [];
            foreach (['mp4', 'mkv'] as $format) {
                $downloadUrls[$format] = $data['download_urls'][$format] ?? [];
                foreach ($data['download_urls'][$format] ?? [] as $dl) {
                    foreach ($dl['urls'] ?? [] as $urlItem) {
                        $sources[] = [
                            'url' => $urlItem['url'] ?? '', 'quality' => $dl['resolution'] ?? 'default',
                            'is_m3u8' => false, 'is_embed' => false, 'provider' => 'otakudesu',
                            'subtitle_type' => 'hardsub', 'default_lang' => 'Indonesia',
                        ];
                    }
                }
            }
            return [
                'sources' => $sources, 'subtitles' => [],
                'navigation' => [
                    'has_next' => $data['has_next_episode'] ?? false,
                    'next_slug' => $data['next_episode']['slug'] ?? null,
                    'has_previous' => $data['has_previous_episode'] ?? false,
                    'previous_slug' => $data['previous_episode']['slug'] ?? null,
                ],
                'download_urls' => $downloadUrls,
            ];
        });
    }

    public function getProviderName(): string { return 'otakudesu'; }

    private function extractReleaseYear(?string $releaseDate): ?int
    {
        if (empty($releaseDate)) return null;
        if (preg_match('/(\d{4})/', $releaseDate, $matches)) return (int) $matches[1];
        return null;
    }
}
