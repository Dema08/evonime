<?php

namespace App\Actions\Admin;

use App\Models\StreamSource;
use App\Services\EpisodeService;

class UpdateStreamSourceAction
{
    public function __construct(protected readonly EpisodeService $episodeService) {}

    public function __invoke(StreamSource $source, array $data): StreamSource
    {
        $source->update($data);
        $this->episodeService->invalidateCache($source->episode_id);
        return $source;
    }
}
