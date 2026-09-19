<?php

namespace App\Repositories;

use App\Enums\SubtitleLanguage;
use App\Models\Subtitle;
use App\Repositories\Contracts\SubtitleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubtitleRepository extends BaseRepository implements SubtitleRepositoryInterface
{
    public function __construct(Subtitle $model) { parent::__construct($model); }

    /** Semua subtitle per episode. */
    public function getByEpisode(int $episodeId): Collection
    {
        return $this->model->newQuery()->where('episode_id', $episodeId)
            ->orderBy('language')->get(['id','episode_id','language','label','format','url','is_default']);
    }

    /** Default subtitle: is_default ?? id ?? pertama. */
    public function getDefault(int $episodeId): ?Subtitle
    {
        return $this->model->newQuery()->where('episode_id', $episodeId)->where('is_default', true)->first()
            ?? $this->model->newQuery()->where('episode_id', $episodeId)->where('language', SubtitleLanguage::Id->value)->first()
            ?? $this->model->newQuery()->where('episode_id', $episodeId)->first();
    }
}

