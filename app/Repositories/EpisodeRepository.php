<?php

namespace App\Repositories;

use App\Enums\VideoStatus;
use App\Models\Episode;
use App\Repositories\Contracts\EpisodeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class EpisodeRepository extends BaseRepository implements EpisodeRepositoryInterface
{
    public function __construct(Episode $model) { parent::__construct($model); }

    /** List episode per anime + sources aktif & subtitles, cache 1 jam. */
    public function getByAnime(int $animeId, array $filters = []): Collection
    {
        $key = "anime:episodes:{$animeId}:".md5(json_encode($filters));
        return Cache::remember($key, 3600, function () use ($animeId, $filters): Collection {
            $q = $this->model->newQuery()->where('anime_id', $animeId)
                ->with(['streamSources' => fn ($s) => $s->where('is_active', true)->orderByDesc('priority'),
                    'subtitles' => fn ($s) => $s->orderBy('language')])
                ->select(['id','anime_id','episode_number','title','synopsis','duration','thumbnail_path','aired_at','status','views_count']);
            if (! empty($filters['status'])) $q->where('status', $filters['status'] instanceof VideoStatus ? $filters['status']->value : $filters['status']);
            return $q->orderBy('episode_number')->get();
        });
    }

    /** Detail + anime + sources + subtitles. */
    public function findWithRelations(int $id): ?Episode
    {
        return Cache::remember("episode:detail:{$id}", 3600, fn (): ?Episode => $this->model->newQuery()
            ->with(['anime:id,title,slug,poster_path', 'streamSources' => fn ($s) => $s->where('is_active', true)->orderByDesc('priority'), 'subtitles'])
            ->find($id));
    }

    /** Episode ready terbaru + anime minimal. */
    public function getLatestReady(int $limit = 10): Collection
    {
        return $this->model->newQuery()->where('status', VideoStatus::Ready->value)
            ->with(['anime:id,title,slug,poster_path'])
            ->select(['id','anime_id','episode_number','title','thumbnail_path','aired_at'])
            ->orderByDesc('aired_at')->limit($limit)->get();
    }

    /** Episode berikutnya by number. */
    public function getNextEpisode(int $animeId, int $currentEpisodeNumber): ?Episode
    {
        return $this->model->newQuery()->where('anime_id', $animeId)
            ->where('episode_number', '>', $currentEpisodeNumber)
            ->orderBy('episode_number')->first(['id','anime_id','episode_number','title']);
    }

    /** Episode sebelumnya by number. */
    public function getPrevEpisode(int $animeId, int $currentEpisodeNumber): ?Episode
    {
        return $this->model->newQuery()->where('anime_id', $animeId)
            ->where('episode_number', '<', $currentEpisodeNumber)
            ->orderByDesc('episode_number')->first(['id','anime_id','episode_number','title']);
    }

    /** Atomic increment views. */
    public function incrementViews(int $id): int
    {
        return $this->model->newQuery()->whereKey($id)->increment('views_count');
    }

    /** Update status massal 1 query, return jumlah baris. */
    public function bulkUpdateStatus(array $ids, VideoStatus $status): int
    {
        return $this->model->newQuery()->whereIn('id', $ids)->update(['status' => $status->value]);
    }

    /** List admin paginasi: filter anime + search + status, eager anime minimal. */
    public function getAdminPaginated(?int $animeId = null, array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['anime:id,title,slug'])
            ->select(['id','anime_id','episode_number','title','status','aired_at','views_count','created_at']);
        if ($animeId) $q->where('anime_id', $animeId);
        if (! empty($filters['search'])) $q->where('title', 'like', '%'.$filters['search'].'%');
        if (! empty($filters['status'])) $q->where('status', $filters['status'] instanceof VideoStatus ? $filters['status']->value : $filters['status']);
        return $q->orderBy('episode_number')->paginate($perPage);
    }
}
