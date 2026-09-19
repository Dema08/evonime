<?php

namespace App\Repositories\Contracts;

use App\Models\Anime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AnimeRepositoryInterface extends RepositoryInterface
{
    /** Katalog published + filter opsional + eager genres, select minimal. */
    public function getPaginatedPublished(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /** Featured hero: is_featured + published, cache 30 menit. */
    public function getFeatured(int $limit = 5): Collection;

    /** Terbaru: published order created_at desc, cache 10 menit. */
    public function getLatest(int $limit = 10): Collection;

    /** Terpopuler: published order views_count desc, cache 60 menit. */
    public function getPopular(int $limit = 10): Collection;

    /** Detail by slug + genres + episodes minimal, cache 30 menit. */
    public function findBySlug(string $slug): ?Anime;

    /** Search FULLTEXT (>=4 char) / LIKE prefix (<4), published only, cache 5 menit. */
    public function search(string $keyword, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /** Daftar anime per genre slug + eager genres. */
    public function getByGenre(string $genreSlug, int $perPage = 15): LengthAwarePaginator;

    /** List admin: semua anime (published + draft), filter search/status, paginate. */
    public function getAdminPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /** Atomic increment views_count tanpa load model. */
    public function incrementViews(int $id): int;

    /** Anime se-genre, exclude diri sendiri, cache 60 menit. */
    public function getRelated(int $animeId, int $limit = 6): Collection;
}
