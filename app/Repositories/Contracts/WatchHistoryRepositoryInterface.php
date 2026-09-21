<?php

namespace App\Repositories\Contracts;

use App\Models\WatchHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WatchHistoryRepositoryInterface extends RepositoryInterface
{
    /** Upsert progres; auto completed jika progress/duration >= 0.95. */
    public function upsertProgress(int $userId, int $episodeId, int $progress, int $duration): WatchHistory;

    /**
     * Catat tontonan level-episode (tanpa progres).
     * Dipakai watch page: player iframe cross-origin tidak bisa dibaca currentTime-nya,
     * jadi progres/completed lama tidak pernah direset — hanya last_watched_at.
     */
    public function recordEpisode(int $userId, int $episodeId): WatchHistory;

    /** Ambil 1 baris riwayat milik user (scope user) berdasarkan primary key tabel. */
    public function findForUser(int $userId, int $historyId): ?WatchHistory;

    /** Hapus seluruh riwayat tontonan user. Mengembalikan jumlah baris terhapus. */
    public function clearHistory(int $userId): int;

    /** Continue watching: belum selesai, order last_watched_at desc, cache 5 mnt/user. */
    public function getContinueWatching(int $userId, int $limit = 10): Collection;

    /** Riwayat paginasi + episode.anime minimal. */
    public function getHistory(int $userId, int $perPage = 20): LengthAwarePaginator;

    /** Tandai 1 episode selesai (completed=true). Parameter kedua = episode_id. */
    public function markCompleted(int $userId, int $episodeId): bool;

    /** Ambil 1 baris progres user-episode. */
    public function findProgress(int $userId, int $episodeId): ?WatchHistory;

    /** Hapus 1 riwayat tontonan user. */
    public function deleteHistory(int $userId, int $episodeId): bool;
}
