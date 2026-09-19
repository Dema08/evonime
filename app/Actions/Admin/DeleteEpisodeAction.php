<?php

namespace App\Actions\Admin;

use App\Models\Episode;
use App\Services\EpisodeService;
use App\Services\FileUploadService;

class DeleteEpisodeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function __invoke(Episode $episode): bool
    {
        $this->fileUpload->deleteFile($episode->thumbnail_path);

        $episodeId = $episode->id;
        $animeId = $episode->anime_id;
        $deleted = $episode->delete();

        $this->episodeService->invalidateCache($episodeId, $animeId);

        return $deleted;
    }
}
