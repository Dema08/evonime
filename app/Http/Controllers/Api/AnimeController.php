<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CatalogRequest;
use App\Http\Requests\Api\SearchRequest;
use App\Http\Resources\AnimeListResource;
use App\Http\Resources\AnimeResource;
use App\Http\Resources\EpisodeListResource;
use App\Http\Responses\ApiResponse;
use App\Services\AnimeService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AnimeController extends Controller
{
    public function __construct(
        protected readonly AnimeService $animeService,
    ) {}

    /**
     * Get paginated catalogue of anime with filters.
     */
    public function index(CatalogRequest $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->validated();
        $paginated = $this->animeService->getCatalog($filters, $perPage);

        return ApiResponse::paginated($paginated, AnimeListResource::class, 'Katalog anime berhasil dimuat.');
    }

    /**
     * Get single anime detail with loaded relations.
     */
    public function show(string $slug): JsonResponse
    {
        $anime = $this->animeService->getAnimeDetail($slug);

        if (! $anime) {
            return ApiResponse::error('Anime tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success(AnimeResource::make($anime), 'Detail anime berhasil dimuat.');
    }

    /**
     * Get all episodes for a specific anime.
     */
    public function episodes(string $slug): JsonResponse
    {
        $episodes = $this->animeService->getEpisodesBySlug($slug);

        if ($episodes === null) {
            return ApiResponse::error('Anime tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success(EpisodeListResource::collection($episodes), 'Daftar episode berhasil dimuat.');
    }

    /**
     * Get related anime recommendations for a specific anime.
     */
    public function related(string $slug): JsonResponse
    {
        $related = $this->animeService->getRelatedBySlug($slug, 6);

        if ($related === null) {
            return ApiResponse::error('Anime tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success(AnimeListResource::collection($related), 'Rekomendasi anime terkait berhasil dimuat.');
    }

    /**
     * Search anime by keyword.
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $q = (string) $request->input('q');
        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);

        $results = $this->animeService->searchAnime($q, $perPage, $page);

        return ApiResponse::paginated($results, AnimeListResource::class, 'Hasil pencarian anime berhasil dimuat.');
    }
}
