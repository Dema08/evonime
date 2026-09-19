<?php

namespace App\Repositories\Contracts;

use App\Models\Subtitle;
use Illuminate\Database\Eloquent\Collection;

interface SubtitleRepositoryInterface extends RepositoryInterface
{
    /** Ambil semua subtitle per episode. */
    public function getByEpisode(int $episodeId): Collection;

    /** Ambil subtitle default untuk episode (is_default ?? bhs ID ?? pertama). */
    public function getDefault(int $episodeId): ?Subtitle;
}
