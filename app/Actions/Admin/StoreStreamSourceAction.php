<?php

namespace App\Actions\Admin;

use App\Models\StreamSource;
use App\Services\EpisodeService;

class StoreStreamSourceAction
{
    public function __construct(protected readonly EpisodeService $episodeService) {}

    public function __invoke(array $data): StreamSource
    {
        $source = StreamSource::create($data);
        $this->episodeService->invalidateCache($source->episode_id);
        return $source;
    }
}
