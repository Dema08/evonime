<?php

namespace App\Repositories;

use App\Models\Genre;
use App\Repositories\Contracts\GenreRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GenreRepository extends BaseRepository implements GenreRepositoryInterface
{
    public function __construct(Genre $model) { parent::__construct($model); }

    /** Semua genre + withCount animes published, cache 24 jam. */
    public function getAllWithCount(): Collection
    {
        return Cache::remember('genre:list', 86400, fn (): Collection => $this->model->newQuery()
            ->withCount(['animes' => fn ($q) => $q->where('is_published', true)])
            ->orderBy('name')->get(['id','name','slug','description']));
    }

    /** Cari genre by slug. */
    public function findBySlug(string $slug): ?Genre
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }

    /** Menu ringan id,name,slug, cache 24 jam. */
    public function getForMenu(): Collection
    {
        return Cache::remember('genre:menu', 86400, fn (): Collection => $this->model->newQuery()
            ->orderBy('name')->get(['id','name','slug']));
    }

    /** List admin paginasi + withCount animes. */
    public function getAdminPaginated(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->newQuery()->withCount('animes')->orderBy('name')->paginate($perPage);
    }
}
