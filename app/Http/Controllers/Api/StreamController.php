<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StreamUrlRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Episode;
use App\Services\Content\ContentAggregatorService;
use App\Services\Content\OtakudesuScraper;
use App\Services\StreamTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /**
     * Resolve mirror server Otakudesu menjadi URL iframe embed.
     * POST /api/v1/stream/resolve-mirror  Body: { data_content: "base64..." }
     */
    public function resolveMirror(Request $request): JsonResponse
    {
        $request->validate(['data_content' => 'required|string']);

        $scraper = app(OtakudesuScraper::class);
        $url = $scraper->resolveMirror($request->input('data_content'));

        if (empty($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Server tidak tersedia. Coba server atau kualitas lain.',
            ], 503);
        }

        // Beri tahu frontend apakah server ini mengizinkan embed dari origin aplikasi
        // (banyak mirror memakai X-Frame-Options / CSP frame-ancestors).
        $embedCheck = $scraper->isEmbeddable($url, $request->getSchemeAndHttpHost());

        return response()->json([
            'success' => true,
            'url' => $url,
            'embeddable' => $embedCheck['embeddable'],
            'reason' => $embedCheck['reason'],
        ]);
    }
}