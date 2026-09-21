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

    /**
     * Catat tontonan level-episode (tanpa progres).
     * Baris lama tidak direset progress/completed-nya, hanya last_watched_at
     * yang diperbarui agar fitur Continue Watching tetap akurat.
     */
    public function recordEpisode(int $userId, int $episodeId): WatchHistory
    {
        $history = $this->model->newQuery()->firstOrNew([
            'user_id' => $userId,
            'episode_id' => $episodeId,
        ]);

        if (! $history->exists) {
            $history->progress_seconds = 0;
            $history->completed = false;
        }

        $history->last_watched_at = now();
        $history->save();

        $this->flushUserCache($userId);

        return $history;
    }

    /** 1 baris riwayat milik user (scope user) berdasarkan PK tabel. */
    public function findForUser(int $userId, int $historyId): ?WatchHistory
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->whereKey($historyId)
            ->first();
    }

    /** Hapus seluruh riwayat tontonan user. */
    public function clearHistory(int $userId): int
    {
        $deleted = (int) $this->model->newQuery()->where('user_id', $userId)->delete();

        if ($deleted > 0) {
            $this->flushUserCache($userId);
        }

        return $deleted;
    }

    /** Continue watching user, cache 5 menit per user. */
    public function getContinueWatching(int $userId, int $limit = 10): Collection
    {
        return Cache::remember("user:{$userId}:continue", 300, fn (): Collection => $this->model->newQuery()
            ->where('user_id', $userId)->where('completed', false)
            ->with(['episode:id,anime_id,episode_number,title,thumbnail_path,duration',
                'episode.anime:id,title,slug,poster_path,banner_path'])
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

    /** Tandai selesai (parameter kedua = episode_id). */
    public function markCompleted(int $userId, int $episodeId): bool
    {
        $updated = $this->model->newQuery()->where('user_id', $userId)->where('episode_id', $episodeId)
            ->update(['completed' => true, 'last_watched_at' => now()]) > 0;

        if ($updated) {
            $this->flushUserCache($userId);
        }

        return $updated;
    }

    /** 1 baris progres user-episode. */
    public function findProgress(int $userId, int $episodeId): ?WatchHistory
    {
        return $this->model->newQuery()->where('user_id', $userId)->where('episode_id', $episodeId)->first();
    }

    /** Hapus 1 riwayat tontonan user (parameter kedua = episode_id). */
    public function deleteHistory(int $userId, int $episodeId): bool
    {
        $deleted = $this->model->newQuery()->where('user_id', $userId)->where('episode_id', $episodeId)->delete() > 0;

        if ($deleted) {
            $this->flushUserCache($userId);
        }

        return $deleted;
    }

    /** Bersihkan cache Continue Watching + riwayat halaman 1..5 milik 1 user. */
    protected function flushUserCache(int $userId): void
    {
        Cache::forget("user:{$userId}:continue");
        for ($p = 1; $p <= 5; $p++) {
            Cache::forget("user:{$userId}:history:page:{$p}");
        }
    }
}
