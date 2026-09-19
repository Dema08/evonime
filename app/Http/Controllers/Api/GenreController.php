<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeListResource;
use App\Http\Resources\GenreResource;
use App\Http\Responses\ApiResponse;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends Controller
{
    public function __construct(
        protected readonly GenreService $genreService,
    ) {}

    /**
     * Get list of genres with anime count.
     */
    public function index(): JsonResponse
    {
        $genres = $this->genreService->getAllWithCount();

        return ApiResponse::success(GenreResource::collection($genres), 'Daftar genre berhasil dimuat.');
    }

    /**
     * Get genre details and paginated anime under the genre.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $data = $this->genreService->getGenrePage($slug, $perPage);

        if (! $data['genre']) {
            return ApiResponse::error('Genre tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success([
            'genre' => GenreResource::make($data['genre']),
            'animes' => [
                'data' => AnimeListResource::collection($data['animes']->items()),
                'meta' => [
                    'current_page' => $data['animes']->currentPage(),
                    'per_page' => $data['animes']->perPage(),
                    'total' => $data['animes']->total(),
                    'last_page' => $data['animes']->lastPage(),
                    'from' => $data['animes']->firstItem(),
                    'to' => $data['animes']->lastItem(),
                ],
                'links' => [
                    'first' => $data['animes']->url(1),
                    'prev' => $data['animes']->previousPageUrl(),
                    'next' => $data['animes']->nextPageUrl(),
                    'last' => $data['animes']->url($data['animes']->lastPage()),
                ],
            ],
        ], 'Katalog anime per genre berhasil dimuat.');
    }
}
