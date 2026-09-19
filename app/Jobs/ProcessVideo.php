<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessVideo implements ShouldQueue
{
    use Queueable;

    public $tries = 3;      // --tries=3
    public $timeout = 1200; // transcode bisa lama
    public $backoff = [60, 300];

    public function __construct(public int $episodeId) {}

    public function handle(): void
    {
        // TODO: picks file dari disk "streams", transcode HLS, update status via Enum VideoStatus.
    }
}
