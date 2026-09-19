<?php

namespace App\Actions\Admin;

use App\Models\Subtitle;
use App\Services\EpisodeService;
use App\Services\FileUploadService;

class StoreSubtitleAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function __invoke(array $data): Subtitle
    {
        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            $data['url'] = $this->fileUpload->uploadSubtitle($data['file'], 'subtitles');
        }
        unset($data['file']);

        $subtitle = Subtitle::create($data);
        $this->episodeService->invalidateCache($subtitle->episode_id);
        return $subtitle;
    }
}
