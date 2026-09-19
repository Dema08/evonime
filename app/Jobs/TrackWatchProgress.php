<?php

namespace App\Jobs;

use App\Repositories\Contracts\WatchHistoryRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class TrackWatchProgress implements ShouldQueue
{
    use Queueable;
    public $tries = 3;
    public function __construct(
        public readonly int $userId,
        public readonly int $episodeId,
        public readonly int $progress,
        public readonly int $duration,
    ) {}
    public function handle(WatchHistoryRepositoryInterface $repos): void
    {
        $repos->upsertProgress($this->userId, $this->episodeId, $this->progress, $this->duration);
        Cache::forget("user:{$this->userId}:continue");
    }
}
