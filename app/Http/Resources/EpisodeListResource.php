<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'anime_id' => $this->anime_id,
            'episode_number' => $this->episode_number,
            'title' => $this->title,
            'duration' => $this->duration,
            'thumbnail_url' => $this->thumbnail_url,
            'aired_at' => $this->aired_at?->toIso8601String(),
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'views_count' => $this->views_count,
        ];
    }
}
