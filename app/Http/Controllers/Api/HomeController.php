<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimeListResource;
use App\Http\Resources\GenreResource;
use App\Http\Responses\ApiResponse;
use App\Services\AnimeService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __construct(
        protected readonly AnimeService $animeService,
    ) {}

    /**
     * Get home page data (Featured, Latest, Popular, Genres).
     */
    public function index(): JsonResponse
    {
        $data = $this->animeService->getHomePageData();

        return ApiResponse::success([
            'featured' => AnimeListResource::collection($data['featured']),
            'latest' => AnimeListResource::collection($data['latest']),
            'popular' => AnimeListResource::collection($data['popular']),
            'genres' => GenreResource::collection($data['genres']),
        ], 'Beranda berhasil dimuat.');
    }
}
