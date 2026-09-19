<?php

namespace App\Repositories\Contracts;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

interface GenreRepositoryInterface extends RepositoryInterface
{
    /** Semua genre + withCount animes published, order name, cache 24 jam. */
    public function getAllWithCount(): Collection;

    /** Cari genre by slug. */
    public function findBySlug(string $slug): ?Genre;

    /** Menu ringan: id,name,slug saja, cache 24 jam. */
    public function getForMenu(): Collection;

    /** List admin paginasi + withCount animes. */
    public function getAdminPaginated(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
