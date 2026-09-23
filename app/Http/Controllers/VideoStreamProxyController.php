<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Episode;
use App\Services\Content\GoogleDriveStreamService;
use App\Services\GoogleDrive\GoogleDriveClientService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamProxyController extends Controller
{
    public function __construct(
        protected GoogleDriveClientService $gdriveClient,
        protected GoogleDriveStreamService $gdriveHelper,
    ) {}

    /**
     * Endpoint Streaming Reverse Proxy: GET /stream/{anime}/{episode}/{quality?}
     *
     * Menghubungkan HTML5 Video Player browser ke Google Drive API secara aman
     * dengan dukungan penuh HTTP Range Requests (206 Partial Content).
     */
    public function stream(Request $request, string $anime, string $episode, string $quality = '1080p'): Response
    {
        // 1. Cari Anime
        $animeModel = is_numeric($anime)
            ? Anime::find((int) $anime)
            : Anime::where('slug', $anime)->orWhere('slug', 'like', "%{$anime}%")->first();

        if (! $animeModel) {
            return response()->json(['message' => 'Anime tidak ditemukan.'], 404);
        }

        // 2. Cari Episode
        $epNum = (int) $episode;
        $episodeModel = $animeModel->episodes()->where('episode_number', $epNum)->first();

        if (! $episodeModel) {
            return response()->json(['message' => 'Episode tidak ditemukan.'], 404);
        }

        // 3. Cari Sumber Streaming (Prioritaskan kualitas yang diminta, mis. 1080p)
        $source = $episodeModel->streamSources()
            ->where('is_active', true)
            ->where(function ($q) use ($quality) {
                $q->where('quality', $quality)
                    ->orWhere('server_name', 'like', '%drive%');
            })
            ->orderByDesc('priority')
            ->first();

        if (! $source) {
            return response()->json(['message' => "Sumber streaming resolusi {$quality} belum tersedia untuk episode ini."], 404);
        }

        // 4. Ekstrak Google Drive File ID
        $fileId = $this->gdriveHelper->extractFileId($source->url) ?? $source->url;

        if (empty($fileId)) {
            return response()->json(['message' => 'File ID Google Drive tidak valid.'], 422);
        }

        // 5. Tangkap header Range dari browser (cth: "bytes=0-1048576" atau "bytes=5242880-")
        $rangeHeader = $request->header('Range');

        // 6. Ambil stream dari Google Drive API
        try {
            $gdriveResponse = $this->gdriveClient->getStream($fileId, $rangeHeader);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menghubungi storage Google Drive.',
                'error' => $e->getMessage(),
            ], 502);
        }

        $statusCode = $gdriveResponse->getStatusCode();

        $contentType = $gdriveResponse->getHeaderLine('Content-Type');
        if (empty($contentType) || $contentType === 'application/octet-stream') {
            $isMkv = ($source->format === 'mkv')
                || str_contains(strtolower($source->server_name), 'mkv')
                || str_contains(strtolower($source->url), '.mkv');

            $contentType = $isMkv ? 'video/x-matroska' : 'video/mp4';
        }

        // 7. Siapkan headers respons streaming untuk browser
        $responseHeaders = [
            'Content-Type' => $contentType,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
        ];

        if ($gdriveResponse->hasHeader('Content-Range')) {
            $responseHeaders['Content-Range'] = $gdriveResponse->getHeaderLine('Content-Range');
        }

        if ($gdriveResponse->hasHeader('Content-Length')) {
            $responseHeaders['Content-Length'] = $gdriveResponse->getHeaderLine('Content-Length');
        }

        if ($gdriveResponse->hasHeader('ETag')) {
            $responseHeaders['ETag'] = $gdriveResponse->getHeaderLine('ETag');
        }

        // 8. Stream chunk langsung dari socket Guzzle ke output buffer browser
        $bodyStream = $gdriveResponse->getBody();
        $chunkSize = (int) config('gdrive.chunk_size', 131072);

        return new StreamedResponse(function () use ($bodyStream, $chunkSize) {
            while (! $bodyStream->eof() && ! connection_aborted()) {
                echo $bodyStream->read($chunkSize);

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }

            $bodyStream->close();
        }, $statusCode, $responseHeaders);
    }
}
