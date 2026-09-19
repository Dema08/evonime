<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StreamUrlRequest;
use App\Http\Responses\ApiResponse;
use App\Services\StreamTokenService;
use Illuminate\Http\JsonResponse;

class StreamController extends Controller
{
    public function __construct(
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
}
