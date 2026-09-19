<?php

namespace App\Actions\Admin;

use App\Models\Subtitle;
use App\Services\EpisodeService;
use App\Services\FileUploadService;

class DeleteSubtitleAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function __invoke(Subtitle $subtitle): bool
    {
        // Jika url relatif di storage public, hapus file.
        if ($subtitle->url && ! str_starts_with($subtitle->url, 'http')) {
            $this->fileUpload->deleteFile($subtitle->url);
        }

        $episodeId = $subtitle->episode_id;
        $deleted = $subtitle->delete();
        $this->episodeService->invalidateCache($episodeId);
        return $deleted;
    }
}
