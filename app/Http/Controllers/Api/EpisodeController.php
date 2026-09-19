<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EpisodeListResource;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\StreamSourceResource;
use App\Http\Resources\SubtitleResource;
use App\Http\Responses\ApiResponse;
use App\Services\EpisodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EpisodeController extends Controller
{
    public function __construct(
        protected readonly EpisodeService $episodeService,
    ) {}

    /**
     * Get episode detail with relations (sources, subtitles, next, prev).
     */
    public function show(int $id): JsonResponse
    {
        $episode = $this->episodeService->getEpisodeDetail($id);

        if (! $episode) {
            return ApiResponse::error('Episode tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success(EpisodeResource::make($episode), 'Detail episode berhasil dimuat.');
    }

    /**
     * Get stream sources for an episode.
     */
    public function sources(int $id): JsonResponse
    {
        $episode = $this->episodeService->getEpisodeDetail($id);

        if (! $episode) {
            return ApiResponse::error('Episode tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        $sources = $this->episodeService->getSources($id);

        return ApiResponse::success(StreamSourceResource::collection($sources), 'Daftar stream source berhasil dimuat.');
    }

    /**
     * Get subtitles for an episode.
     */
    public function subtitles(int $id): JsonResponse
    {
        $episode = $this->episodeService->getEpisodeDetail($id);

        if (! $episode) {
            return ApiResponse::error('Episode tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        $subtitles = $this->episodeService->getSubtitles($id);

        return ApiResponse::success(SubtitleResource::collection($subtitles), 'Daftar subtitle berhasil dimuat.');
    }

    /**
     * Get latest released episodes.
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(50, $limit));

        $episodes = $this->episodeService->getLatestEpisodes($limit);

        return ApiResponse::success(EpisodeListResource::collection($episodes), 'Episode terbaru berhasil dimuat.');
    }
}
