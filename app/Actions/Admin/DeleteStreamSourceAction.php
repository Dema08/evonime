<?php

namespace App\Actions\Admin;

use App\Models\StreamSource;
use App\Services\EpisodeService;

class DeleteStreamSourceAction
{
    public function __construct(protected readonly EpisodeService $episodeService) {}

    public function __invoke(StreamSource $source): bool
    {
        $episodeId = $source->episode_id;
        $deleted = $source->delete();
        $this->episodeService->invalidateCache($episodeId);
        return $deleted;
    }
}
