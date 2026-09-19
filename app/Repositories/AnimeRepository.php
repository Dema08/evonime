<?php

namespace App\Repositories;

use App\Models\Anime;
use App\Repositories\Contracts\AnimeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnimeRepository extends BaseRepository implements AnimeRepositoryInterface
{
    public function __construct(Anime $model) { parent::__construct($model); }

    /** Katalog published + filter, eager genres, select minimal. */
    public function getPaginatedPublished(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->published()->with(['genres:id,name,slug'])
            ->select(['id','title','slug','type','status','year','rating','poster_path','views_count','created_at']);
        if (! empty($filters['status'])) $q->where('status', $filters['status']);
        if (! empty($filters['type'])) $q->where('type', $filters['type']);
        if (! empty($filters['year'])) $q->where('year', (int) $filters['year']);
        if (! empty($filters['genre'])) $q->whereHas('genres', fn ($g) => $g->where('genres.slug', $filters['genre']));
        return $q->orderByDesc('created_at')->paginate($perPage);
    }

    /** Featured hero, cache 30 menit. */
    public function getFeatured(int $limit = 5): Collection
    {
        return Cache::remember('homepage:featured', 1800, fn (): Collection => $this->model->newQuery()
            ->published()->where('is_featured', true)->with(['genres:id,name,slug'])
            ->select(['id','title','slug','type','status','year','rating','poster_path','banner_path','views_count'])
            ->orderByDesc('views_count')->limit($limit)->get());
    }

    /** Terbaru, cache 10 menit. */
    public function getLatest(int $limit = 10): Collection
    {
        return Cache::remember('homepage:latest', 600, fn (): Collection => $this->model->newQuery()
            ->published()->with(['genres:id,name,slug'])
            ->select(['id','title','slug','type','status','year','rating','poster_path','views_count','created_at'])
            ->orderByDesc('created_at')->limit($limit)->get());
    }

    /** Terpopuler by views, cache 60 menit. */
    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember('homepage:popular', 3600, fn (): Collection => $this->model->newQuery()
            ->published()->with(['genres:id,name,slug'])
            ->select(['id','title','slug','type','status','year','rating','poster_path','views_count'])
            ->orderByDesc('views_count')->limit($limit)->get());
    }

    /** Detail by slug + genres + episodes, cache 30 menit. */
    public function findBySlug(string $slug): ?Anime
    {
        return Cache::remember('anime:slug:'.$slug, 1800, fn (): ?Anime => $this->model->newQuery()
            ->where('slug', $slug)->with(['genres:id,name,slug',
                'episodes' => fn ($e) => $e->select(['id','anime_id','episode_number','title','thumbnail_path','aired_at','status'])->orderBy('episode_number')])
            ->first(['id','title','title_alternative','slug','synopsis','type','status','rating','total_episodes','duration','studio','season','year','poster_path','banner_path','views_count','created_at']));
    }

    /** Search FULLTEXT>=4char / LIKE prefix<4char, cache 5 menit. */
    public function search(string $keyword, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $keyword = trim($keyword);
        $hash = md5(mb_strtolower($keyword).'|'.$perPage.'|'.$page);
        return Cache::remember('search:'.$hash, 300, function () use ($keyword, $perPage, $page): LengthAwarePaginator {
            $q = $this->model->newQuery()->published()->with(['genres:id,name,slug'])
                ->select(['id', 'title', 'slug', 'type', 'status', 'year', 'rating', 'poster_path', 'views_count']);
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite' && mb_strlen($keyword) >= 4) {
                $q->whereFullText(['title', 'title_alternative', 'synopsis'], $keyword);
            } else {
                $q->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', "%{$keyword}%")
                        ->orWhere('title_alternative', 'like', "%{$keyword}%")
                        ->orWhere('synopsis', 'like', "%{$keyword}%");
                });
            }
            return $q->orderByDesc('views_count')->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /** Anime per genre slug. */
    public function getByGenre(string $genreSlug, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->published()
            ->whereHas('genres', fn ($g) => $g->where('genres.slug', $genreSlug))->with(['genres:id,name,slug'])
            ->select(['id','title','slug','type','status','year','rating','poster_path','views_count','created_at'])
            ->orderByDesc('created_at')->paginate($perPage);
    }

    /** Atomic increment views tanpa load model. */
    public function incrementViews(int $id): int
    {
        return $this->model->newQuery()->whereKey($id)->increment('views_count');
    }

    /** List admin: semua anime (published + draft), filter search/status, select minimal + genres. */
    public function getAdminPaginated(int $perPage = 15, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with(['genres:id,name'])
            ->select(['id','title','slug','type','status','is_published','is_featured','year','rating','views_count','created_at']);
        if (! empty($filters['search'])) $q->where('title', 'like', '%'.$filters['search'].'%');
        if (! empty($filters['status'])) $q->where('status', $filters['status']);
        return $q->orderByDesc('created_at')->paginate($perPage);
    }

    /** Se-genre exclude diri sendiri, cache 60 menit. */
    public function getRelated(int $animeId, int $limit = 6): Collection
    {
        return Cache::remember("anime:related:{$animeId}:{$limit}", 3600, function () use ($animeId, $limit): Collection {
            $ids = DB::table('anime_genre')->where('anime_id', $animeId)->pluck('genre_id')->all();
            if ($ids === []) return new Collection();
            return $this->model->newQuery()->published()->where('id', '!=', $animeId)
                ->whereHas('genres', fn ($g) => $g->whereIn('genres.id', $ids))->with(['genres:id,name,slug'])
                ->select(['id','title','slug','poster_path','rating','views_count'])->withCount('episodes')
                ->orderByDesc('views_count')->limit($limit)->get();
        });
    }
}
