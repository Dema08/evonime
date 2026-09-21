<?php

namespace App\Services;

use App\Models\WatchHistory;
use App\Repositories\Contracts\WatchHistoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class WatchHistoryService
{
    public function __construct(protected readonly WatchHistoryRepositoryInterface $histories) {}

    public function continueWatching(int $userId, int $limit = 10) { return $this->histories->getContinueWatching($userId, $limit); }

    public function history(int $userId, int $perPage = 20) { return $this->histories->getHistory($userId, $perPage); }

    public function progressPercent(int $userId, int $episodeId): int
    {
        $h = $this->histories->findProgress($userId, $episodeId);
        if (! $h || ! $h->duration_seconds) return 0;
        return (int) min(100, round($h->progress_seconds / $h->duration_seconds * 100));
    }

    public function deleteHistory(int $userId, int $episodeId): bool
    {
        return $this->histories->deleteHistory($userId, $episodeId);
    }

    /**
     * Catat tontonan level-EPISODE (bukan timestamp).
     *
     * Video diputar via iframe pihak ketiga (desustream.me) yang cross-origin,
     * sehingga currentTime tidak bisa dibaca. Jadi "lanjut tonton" ditandai
     * cukup dari episode terakhir yang dibuka per anime.
     */
    public function record(int $userId, int $episodeId): WatchHistory
    {
        $history = $this->histories->recordEpisode($userId, $episodeId);
        $this->invalidateCache($userId);

        return $history;
    }

    /** Tandai 1 riwayat selesai. $historyId = primary key tabel watch_histories. */
    public function markCompleted(int $userId, int $historyId): bool
    {
        $row = $this->histories->findForUser($userId, $historyId);
        if (! $row) {
            return false;
        }

        $updated = $this->histories->markCompleted($userId, (int) $row->episode_id);
        if ($updated) {
            $this->invalidateCache($userId);
        }

        return $updated;
    }

    /** Hapus 1 riwayat. $historyId = primary key tabel watch_histories. */
    public function remove(int $userId, int $historyId): bool
    {
        $row = $this->histories->findForUser($userId, $historyId);
        if (! $row) {
            return false;
        }

        $deleted = $this->histories->deleteHistory($userId, (int) $row->episode_id);
        if ($deleted) {
            $this->invalidateCache($userId);
        }

        return $deleted;
    }

    /** Hapus seluruh riwayat tontonan user. Mengembalikan jumlah baris terhapus. */
    public function clearAll(int $userId): int
    {
        $deleted = $this->histories->clearHistory($userId);
        if ($deleted > 0) {
            $this->invalidateCache($userId);
        }

        return $deleted;
    }

    public function invalidateCache(int $userId): void
    {
        Cache::forget("user:{$userId}:continue");
        for ($p = 1; $p <= 5; $p++) Cache::forget("user:{$userId}:history:page:{$p}");
    }
}
