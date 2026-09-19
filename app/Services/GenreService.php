<?php

namespace App\Services;

use App\Repositories\Contracts\AnimeRepositoryInterface;
use App\Repositories\Contracts\GenreRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GenreService
{
    public function __construct(
        protected readonly GenreRepositoryInterface $genres,
        protected readonly AnimeRepositoryInterface $animes,
    ) {}

    public function getMenu() { return $this->genres->getForMenu(); }

    public function getGenrePage(string $slug, int $perPage = 15): array
    {
        return ['genre' => $this->genres->findBySlug($slug), 'animes' => $this->animes->getByGenre($slug, $perPage)];
    }

    /** List admin: semua genre + withCount animes, paginate. */
    public function getAdminList(int $perPage = 15): LengthAwarePaginator
    {
        return $this->genres->getAdminPaginated($perPage);
    }

    public function getAllWithCount() { return $this->genres->getAllWithCount(); }
}
