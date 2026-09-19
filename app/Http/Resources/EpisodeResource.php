<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'anime_id' => $this->anime_id,
            'episode_number' => $this->episode_number,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'duration' => $this->duration,
            'thumbnail_url' => $this->thumbnail_url,
            'aired_at' => $this->aired_at?->toIso8601String(),
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'views_count' => $this->views_count,
            'anime' => AnimeListResource::make($this->whenLoaded('anime')),
            'stream_sources' => StreamSourceResource::collection($this->whenLoaded('streamSources')),
            'subtitles' => SubtitleResource::collection($this->whenLoaded('subtitles')),
            'next' => EpisodeListResource::make($this->whenLoaded('next')),
            'prev' => EpisodeListResource::make($this->whenLoaded('prev')),
        ];
    }
}
