<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WatchHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $duration = (int) $this->duration_seconds;
        $progress = (int) $this->progress_seconds;
        $progressPercent = $duration > 0 ? (int) min(100, round(($progress / $duration) * 100)) : 0;

        return [
            'id' => $this->id,
            'progress_seconds' => $progress,
            'duration_seconds' => $duration,
            'completed' => (bool) $this->completed,
            'progress_percent' => $progressPercent,
            'last_watched_at' => $this->last_watched_at?->toIso8601String(),
            'episode' => EpisodeResource::make($this->whenLoaded('episode')),
        ];
    }
}
