<?php

namespace App\Jobs;

use App\Repositories\Contracts\AnimeRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementAnimeViews implements ShouldQueue
{
    use Queueable;
    public $tries = 3;
    public function __construct(public readonly int $animeId) {}
    public function handle(AnimeRepositoryInterface $repos): void { $repos->incrementViews($this->animeId); }
}
