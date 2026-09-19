<?php

namespace App\Http\Resources;

use App\Services\StreamTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StreamSourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->user()?->id ?? 0;
        // Signed URL generation moved to /stream/url endpoint to reduce payload size.
        // $streamService = app(StreamTokenService::class);
        // $signedUrl = $streamService->signedUrl($this->episode_id, $userId);

        return [
            'id' => $this->id,
            'server_name' => $this->server_name,
            'quality' => $this->quality instanceof \BackedEnum ? $this->quality->value : $this->quality,
            'format' => $this->format,
            'priority' => $this->priority,
            // 'stream_url' => $signedUrl, // removed, use /stream/url endpoint
        ];
    }
}
