<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface StreamSourceRepositoryInterface extends RepositoryInterface
{
    /** Ambil semua stream source aktif per episode, urut priority desc lalu quality. */
    public function getActiveByEpisode(int $episodeId): Collection;
}
