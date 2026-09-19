<?php

namespace App\Services;

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

    public function invalidateCache(int $userId): void
    {
        Cache::forget("user:{$userId}:continue");
        for ($p = 1; $p <= 5; $p++) Cache::forget("user:{$userId}:history:page:{$p}");
    }
}
