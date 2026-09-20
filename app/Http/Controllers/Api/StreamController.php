<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StreamUrlRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Episode;
use App\Services\Content\ContentAggregatorService;
use App\Services\StreamTokenService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StreamController extends Controller
{
    public function __construct(
        protected readonly ContentAggregatorService $aggregator,
        protected readonly StreamTokenService $tokenService,
    ) {}

    /**
     * Generate temporary signed streaming URL.
     */
    public function url(StreamUrlRequest $request): JsonResponse
    {
        $episodeId = (int) $request->input('episode_id');
        $userId = $request->user()?->id ?? 0;
        $quality = $request->input('quality');

        $issued = $this->tokenService->issue($episodeId, $userId);
        $signedUrl = $this->tokenService->signedUrl($episodeId, $userId);

        if ($quality) {
            $signedUrl .= "&q={$quality}";
        }

        return ApiResponse::success([
            'episode_id' => $episodeId,
            'quality' => $quality,
            'stream_url' => $signedUrl,
            'token' => $issued['token'],
            'expires_at' => $issued['expires'],
            'ttl_seconds' => (int) config('stream.token_ttl', 7200),
        ], 'URL stream berhasil digenerate.');
    }

    /**
     * Get streaming sources for an episode from Otakudesu.
     */
    public function sources(int $episodeId): JsonResponse
    {
        $episode = Episode::find($episodeId);

        if (! $episode) {
            return ApiResponse::error(
                'Episode tidak ditemukan.',
                Response::HTTP_NOT_FOUND
            );
        }

        $data = $this->aggregator->getBestSource($episodeId);

        return ApiResponse::success([
            'episode' => [
                'id'                   => $episode->id,
                'episode_number'       => $episode->episode_number,
                'title'                => $episode->title,
                'external_id_otakudesu' => $episode->external_id_otakudesu,
            ],
            'sources'       => $data['sources'],
            'subtitles'     => $data['subtitles'],
            'navigation'    => $data['navigation'],
            'download_urls' => $data['download_urls'],
        ], 'Sumber streaming Otakudesu.');
    }
}