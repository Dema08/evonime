<?php

namespace App\Services;

use App\Jobs\TrackWatchProgress;
use App\Models\Episode;
use App\Repositories\Contracts\AnimeRepositoryInterface;
use App\Repositories\Contracts\EpisodeRepositoryInterface;
use App\Repositories\Contracts\WatchHistoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class EpisodeService
{
    public function __construct(
        protected readonly EpisodeRepositoryInterface $episodes,
        protected readonly AnimeRepositoryInterface $animes,
        protected readonly WatchHistoryRepositoryInterface $histories,
    ) {}

    public function getEpisodeDetail(int $id): ?Episode
    {
        $ep = $this->episodes->findWithRelations($id);
        if ($ep) {
            $ep->setRelation('next', $this->episodes->getNextEpisode($ep->anime_id, $ep->episode_number));
            $ep->setRelation('prev', $this->episodes->getPrevEpisode($ep->anime_id, $ep->episode_number));
        }
        return $ep;
    }

    public function getWatchPageData(int $episodeId, ?int $userId = null): ?array
    {
        $ep = $this->getEpisodeDetail($episodeId);
        if (! $ep) return null;
        return [
            'episode' => $ep,
            'history' => $userId ? $this->histories->findProgress($userId, $episodeId) : null,
            'continue' => $userId ? $this->histories->getContinueWatching($userId, 6) : [],
            'recommended' => $this->animes->getRelated($ep->anime_id, 6),
        ];
    }

    public function trackProgress(int $userId, int $episodeId, int $progress, int $duration): void
    {
        TrackWatchProgress::dispatch($userId, $episodeId, $progress, $duration)->afterResponse();
    }

    /** List admin: episode per anime (atau semua jika $animeId null) + filter. */
    public function getAdminList(?int $animeId = null, array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->episodes->getAdminPaginated($animeId, $filters, $perPage);
    }

    public function invalidateCache(int $episodeId, ?int $animeId = null): void
    {
        Cache::forget("episode:detail:{$episodeId}");
        if ($animeId !== null || ($ep = $this->episodes->find($episodeId, ['id', 'anime_id'])) !== null) {
            $aid = $animeId ?? $ep?->anime_id;
            if ($aid) {
                // Hapus semua variasi key anime:episodes:{aid}:{md5(filters)}.
                $store = Cache::getStore();
                if (method_exists($store, 'connection')) {
                    $prefix = config('cache.prefix');
                    foreach ($store->connection()->keys($prefix."anime:episodes:{$aid}*") as $key) {
                        Cache::forget(str_replace($prefix, '', $key));
                    }
                }
            }
        }
    }
}
