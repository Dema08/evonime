<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TrackProgressRequest;
use App\Http\Resources\AnimeListResource;
use App\Http\Resources\EpisodeResource;
use App\Http\Resources\WatchHistoryResource;
use App\Http\Responses\ApiResponse;
use App\Services\EpisodeService;
use App\Services\WatchHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WatchController extends Controller
{
    public function __construct(
        protected readonly EpisodeService $episodeService,
        protected readonly WatchHistoryService $historyService,
    ) {}

    /**
     * Get watch page payload for an episode.
     */
    public function show(Request $request, int $episodeId): JsonResponse
    {
        $userId = $request->user()?->id;
        $data = $this->episodeService->getWatchPageData($episodeId, $userId);

        if (! $data) {
            return ApiResponse::error('Episode tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success([
            'episode' => EpisodeResource::make($data['episode']),
            'history' => $data['history'] ? WatchHistoryResource::make($data['history']) : null,
            'continue' => WatchHistoryResource::collection($data['continue']),
            'recommended' => AnimeListResource::collection($data['recommended']),
        ], 'Halaman nonton berhasil dimuat.');
    }

    /**
     * Track user watch progress (dispatched via queue job).
     */
    public function track(TrackProgressRequest $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $episodeId = (int) $request->input('episode_id');
        $progress = (int) $request->input('progress_seconds');
        $duration = (int) $request->input('duration_seconds');

        $this->episodeService->trackProgress($userId, $episodeId, $progress, $duration);

        return ApiResponse::success([
            'episode_id' => $episodeId,
            'progress_seconds' => $progress,
            'duration_seconds' => $duration,
        ], 'Progres menonton berhasil dicatat.');
    }

    /**
     * Get continue watching list for the authenticated user.
     */
    public function continueWatching(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $limit = (int) $request->input('limit', 10);
        $limit = max(1, min(30, $limit));

        $continueList = $this->historyService->continueWatching($userId, $limit);

        return ApiResponse::success(WatchHistoryResource::collection($continueList), 'Daftar lanjut nonton berhasil dimuat.');
    }

    /**
     * Get paginated watch history for the authenticated user.
     */
    public function history(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $perPage = (int) $request->input('per_page', 20);
        $perPage = max(1, min(50, $perPage));

        $historyPaginated = $this->historyService->history($userId, $perPage);

        return ApiResponse::paginated($historyPaginated, WatchHistoryResource::class, 'Riwayat menonton berhasil dimuat.');
    }

    /**
     * Delete an episode from user's watch history.
     */
    public function destroyHistory(Request $request, int $episodeId): JsonResponse
    {
        $userId = (int) $request->user()->id;
        $deleted = $this->historyService->deleteHistory($userId, $episodeId);

        if (! $deleted) {
            return ApiResponse::error('Riwayat menonton tidak ditemukan.', Response::HTTP_NOT_FOUND);
        }

        return ApiResponse::success(null, 'Riwayat menonton berhasil dihapus.');
    }
}
