<?php

namespace App\Services;

use App\Jobs\IncrementAnimeViews;
use App\Models\Anime;
use App\Repositories\Contracts\AnimeRepositoryInterface;
use App\Repositories\Contracts\GenreRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class AnimeService
{
    public function __construct(
        protected readonly AnimeRepositoryInterface $animes,
        protected readonly GenreRepositoryInterface $genres,
    ) {}

    public function getHomePageData(): array
    {
        return [
            'featured' => $this->animes->getFeatured(5),
            'latest' => $this->animes->getLatest(10),
            'popular' => $this->animes->getPopular(10),
            'genres' => $this->genres->getForMenu(),
        ];
    }

    public function getAnimeDetail(string $slug): ?Anime
    {
        $anime = $this->animes->findBySlug($slug);
        if ($anime) {
            IncrementAnimeViews::dispatch($anime->id)->afterResponse();
            $anime->setRelation('related', $this->animes->getRelated($anime->id, 6));
        }
        return $anime;
    }

    public function getEpisodesBySlug(string $slug)
    {
        $anime = $this->animes->findBySlug($slug);
        if (! $anime) {
            return null;
        }

        return $anime->episodes;
    }

    public function getRelatedBySlug(string $slug, int $limit = 6)
    {
        $anime = $this->animes->findBySlug($slug);
        if (! $anime) {
            return null;
        }

        return $this->animes->getRelated($anime->id, $limit);
    }

    public function getCatalog(array $filters, int $perPage = 15)
    {
        return $this->animes->getPaginatedPublished($perPage, $filters);
    }

    public function searchAnime(string $q, int $perPage = 15, int $page = 1)
    {
        return $this->animes->search($q, $perPage, $page);
    }

    /** List admin: semua anime (published + draft) + filter search/status, paginate. */
    public function getAdminList(array $filters = [], int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->animes->getAdminPaginated($perPage, $filters);
    }

    public function findForEdit(int $id): ?Anime
    {
        return $this->animes->findOrFail($id);
    }

    public function invalidateCache(int $animeId): void
    {
        $anime = $this->animes->find($animeId, ['id', 'slug']);
        Cache::forget('homepage:featured');
        Cache::forget('homepage:latest');
        Cache::forget('homepage:popular');
        Cache::forget('genre:menu');
        Cache::forget('genre:list');

        // Hapus semua variasi cache episodes untuk anime ini (karena ada filter $filters di key md5).
        $store = Cache::getStore();
        if (method_exists($store, 'connection')) {
            $prefix = config('cache.prefix');
            foreach ($store->connection()->keys($prefix."anime:episodes:{$animeId}*") as $key) {
                Cache::forget(str_replace($prefix, '', $key));
            }
        }

        if ($anime) {
            Cache::forget('anime:slug:'.$anime->slug);
            foreach ([6, 10] as $limit) Cache::forget("anime:related:{$animeId}:{$limit}");
        }
    }
}
