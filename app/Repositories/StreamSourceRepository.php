<?php

namespace App\Repositories;

use App\Models\StreamSource;
use App\Repositories\Contracts\StreamSourceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StreamSourceRepository extends BaseRepository implements StreamSourceRepositoryInterface
{
    public function __construct(StreamSource $model) { parent::__construct($model); }

    /** Sources aktif per episode, order priority desc + quality. */
    public function getActiveByEpisode(int $episodeId): Collection
    {
        return $this->model->newQuery()->where('episode_id', $episodeId)->where('is_active', true)
            ->orderByDesc('priority')->orderBy('quality')
            ->get(['id','episode_id','server_name','quality','url','format','priority']);
    }
}

