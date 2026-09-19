<?php

namespace App\Repositories;

use App\Models\WatchHistory;
use App\Repositories\Contracts\WatchHistoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class WatchHistoryRepository extends BaseRepository implements WatchHistoryRepositoryInterface
{
    public function __construct(WatchHistory $model) { parent::__construct($model); }

    /** Upsert progres; auto completed jika >=95%. */
    public function upsertProgress(int $userId, int $episodeId, int $progress, int $duration): WatchHistory
    {
        $completed = $duration > 0 && ($progress / $duration) >= 0.95;
        return $this->model->newQuery()->updateOrCreate(
            ['user_id' => $userId, 'episode_id' => $episodeId],
            ['progress_seconds' => $progress, 'duration_seconds' => $duration,
                'completed' => $completed, 'last_watched_at' => now()]
        );
    }

    /** Continue watching user, cache 5 menit per user. */
    public function getContinueWatching(int $userId, int $limit = 10): Collection
    {
        return Cache::remember("user:{$userId}:continue", 300, fn (): Collection => $this->model->newQuery()
            ->where('user_id', $userId)->where('completed', false)
            ->with(['episode:id,anime_id,episode_number,title,thumbnail_path,duration',
                'episode.anime:id,title,slug,poster_path'])
            ->orderByDesc('last_watched_at')->limit($limit)->get());
    }

    /** Riwayat paginasi user. */
    public function getHistory(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        $page = (int) request()->query('page', 1);
        return Cache::remember("user:{$userId}:history:page:{$page}", 300, fn (): LengthAwarePaginator => $this->model->newQuery()
            ->where('user_id', $userId)
            ->with(['episode:id,anime_id,episode_number,title,thumbnail_path', 'episode.anime:id,title,slug,poster_path'])
            ->orderByDesc('last_watched_at')->paginate($perPage));
    }

    /** Tandai selesai. */
    public function markCompleted(int $userId, int $episodeId): bool
    {
        return $this->model->newQuery()->where('user_id', $userId)->where('episode_id', $episodeId)
            ->update(['completed' => true, 'last_watched_at' => now()]) > 0;
    }

    /** 1 baris progres user-episode. */
    public function findProgress(int $userId, int $episodeId): ?WatchHistory
    {
        return $this->model->newQuery()->where('user_id', $userId)->where('episode_id', $episodeId)->first();
    }
}
