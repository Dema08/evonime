<?php

namespace App\Services\Content;

use App\Models\Episode;
use Illuminate\Support\Facades\Log;

class ContentAggregatorService
{
    public function __construct(
        private readonly OtakudesuProvider $otakudesu,
    ) {}

    /**
     * Cari anime dari Otakudesu.
     *
     * @return array{results: array<int, array{id: string, title: string, image: string|null, provider: string, rating: string|null, status: string|null}>}
     */
    public function searchAll(string $query): array
    {
        return $this->otakudesu->search($query);
    }

    /**
     * Ambil sumber streaming untuk episode lokal dari Otakudesu.
     *
     * @param int $episodeId ID lokal dari tabel episodes
     * @return array{sources: array<int, array{url: string, quality: string, is_m3u8: bool, is_embed: bool, provider: string, subtitle_type: string, default_lang: string}>, subtitles: array<int, array{url: string|null, lang: string}>, navigation: array{has_next: bool, next_slug: string|null, has_previous: bool, previous_slug: string|null}, download_urls: array}
     */
    public function getBestSource(int $episodeId): array
    {
        $episode = Episode::find($episodeId);

        if (! $episode) {
            Log::warning('ContentAggregatorService: episode not found', ['episode_id' => $episodeId]);
            return ['sources' => [], 'subtitles' => [], 'navigation' => [], 'download_urls' => []];
        }

        if (empty($episode->external_id_otakudesu)) {
            Log::warning('ContentAggregatorService: episode has no external_id_otakudesu', [
                'episode_id' => $episodeId,
            ]);
            return ['sources' => [], 'subtitles' => [], 'navigation' => [], 'download_urls' => []];
        }

        return $this->otakudesu->getEpisodeSources($episode->external_id_otakudesu);
    }
}