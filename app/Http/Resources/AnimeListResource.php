<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimeListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_alternative' => $this->title_alternative,
            'slug' => $this->slug,
            'type' => $this->type,
            'status' => $this->status,
            'release_date' => $this->release_date?->format('Y-m-d'),
            'rating' => $this->rating !== null ? (float) $this->rating : null,
            'total_episodes' => $this->total_episodes,
            'duration' => $this->duration,
            'studio' => $this->studio,
            'season' => $this->season,
            'year' => $this->year,
            'poster_url' => $this->poster_url,
            'banner_url' => $this->banner_url,
            'views_count' => $this->views_count,
            'is_featured' => (bool) $this->is_featured,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
