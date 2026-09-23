<?php

namespace App\Services\Content;

use App\Models\Episode;
use Illuminate\Support\Facades\Log;

class ContentAggregatorService
{
    public function __construct(
        private readonly OtakudesuProvider $otakudesu,
        private readonly GoogleDriveStreamService $googleDrive,
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
     * Ambil sumber streaming untuk episode lokal dari database (Google Drive 1080p / lokal) dan Otakudesu.
     *
     * @param  int  $episodeId  ID lokal dari tabel episodes
     * @return array{
     *     sources: array<int, array>,
     *     providers: array{google_drive: array<int, array>, otakudesu: array<int, array>},
     *     subtitles: array,
     *     navigation: array,
     *     download_urls: array,
     *     has_gdrive: bool,
     *     has_otakudesu: bool
     * }
     */
    public function getBestSource(int $episodeId): array
    {
        $episode = Episode::with([
            'anime:id,slug,title',
            'streamSources' => function ($q) {
                $q->where('is_active', true)->orderByDesc('priority');
            },
        ])->find($episodeId);

        if (! $episode) {
            Log::warning('ContentAggregatorService: episode not found', ['episode_id' => $episodeId]);

            return [
                'sources' => [],
                'providers' => ['google_drive' => [], 'otakudesu' => []],
                'subtitles' => [],
                'navigation' => [],
                'download_urls' => [],
                'has_gdrive' => false,
                'has_otakudesu' => false,
            ];
        }

        $gdriveSources = [];
        $localSources = [];
        $animeSlug = $episode->anime?->slug;
        $episodeNum = (int) ($episode->episode_number ?? 1);

        // 1. Ekstrak sumber lokal dari database (Google Drive & HLS/MP4 lokal)
        foreach ($episode->streamSources as $source) {
            $isGDrive = str_contains(strtolower($source->server_name), 'drive')
                || str_contains(strtolower($source->server_name), 'gdrive')
                || str_contains($source->url, 'drive.google.com')
                || $this->googleDrive->extractFileId($source->url) !== null;

            if ($isGDrive) {
                $gdriveSources[] = $this->googleDrive->formatSource(
                    $source->url,
                    $source->server_name ?: 'Google Drive (1080p FHD)',
                    $source->quality?->value ?? '1080p',
                    $animeSlug,
                    $episodeNum
                );
            } else {
                $localSources[] = [
                    'id' => 'local_'.$source->id,
                    'server_name' => $source->server_name,
                    'quality' => $source->quality?->value ?? 'auto',
                    'url' => $source->url,
                    'is_m3u8' => $source->format === 'hls' || str_ends_with($source->url, '.m3u8'),
                    'is_embed' => false,
                    'provider' => 'local',
                    'subtitle_type' => 'softsub',
                    'default_lang' => 'Indonesia',
                    'needs_resolve' => false,
                    'embeddable' => true,
                    'embed_block_reason' => null,
                ];
            }
        }

        // 2. Ambil sumber dinamis dari Otakudesu jika external_id_otakudesu tersedia
        $otakuData = ['sources' => [], 'subtitles' => [], 'navigation' => [], 'download_urls' => []];
        if (! empty($episode->external_id_otakudesu)) {
            $otakuData = $this->otakudesu->getEpisodeSources($episode->external_id_otakudesu);
        }

        $otakuSources = $otakuData['sources'] ?? [];

        // Gabungkan semua sumber: Google Drive (1080p FHD) di urutan pertama, lalu local, lalu Otakudesu
        $mergedSources = array_merge($gdriveSources, $localSources, $otakuSources);

        // 3. Ambil subtitle dari database lokal (jika ada) atau fallback ke provider
        $localSubtitles = $episode->subtitles()->get()->map(fn ($sub) => [
            'id' => $sub->id,
            'language' => is_object($sub->language) ? $sub->language->value : $sub->language,
            'label' => $sub->label ?: 'Indonesia',
            'format' => $sub->format,
            'url' => str_starts_with($sub->url, 'http') ? $sub->url : asset($sub->url),
            'is_default' => (bool) $sub->is_default,
        ])->toArray();

        $subtitles = ! empty($localSubtitles) ? $localSubtitles : ($otakuData['subtitles'] ?? []);

        return [
            'sources' => $mergedSources,
            'providers' => [
                'google_drive' => $gdriveSources,
                'otakudesu' => $otakuSources,
                'local' => $localSources,
            ],
            'subtitles' => $subtitles,
            'navigation' => $otakuData['navigation'] ?? [],
            'download_urls' => $otakuData['download_urls'] ?? [],
            'has_gdrive' => count($gdriveSources) > 0,
            'has_otakudesu' => count($otakuSources) > 0,
        ];
    }
}
