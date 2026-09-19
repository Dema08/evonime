<?php

namespace App\Repositories\Contracts;

use App\Models\WatchHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WatchHistoryRepositoryInterface extends RepositoryInterface
{
    /** Upsert progres; auto completed jika progress/duration >= 0.95. */
    public function upsertProgress(int $userId, int $episodeId, int $progress, int $duration): WatchHistory;

    /** Continue watching: belum selesai, order last_watched_at desc, cache 5 mnt/user. */
    public function getContinueWatching(int $userId, int $limit = 10): Collection;

    /** Riwayat paginasi + episode.anime minimal. */
    public function getHistory(int $userId, int $perPage = 20): LengthAwarePaginator;

    /** Tandai 1 episode selesai (completed=true). */
    public function markCompleted(int $userId, int $episodeId): bool;

    /** Ambil 1 baris progres user-episode. */
    public function findProgress(int $userId, int $episodeId): ?WatchHistory;

    /** Hapus 1 riwayat tontonan user. */
    public function deleteHistory(int $userId, int $episodeId): bool;
}
