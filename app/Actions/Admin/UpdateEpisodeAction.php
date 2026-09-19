<?php

namespace App\Actions\Admin;

use App\Models\Episode;
use App\Services\EpisodeService;
use App\Services\FileUploadService;

class UpdateEpisodeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function __invoke(Episode $episode, array $data): Episode
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            $data['thumbnail_path'] = $this->fileUpload->uploadImage($data['thumbnail'], 'thumbnails', $episode->thumbnail_path);
        }
        unset($data['thumbnail']);

        $episode->update($data);

        $this->episodeService->invalidateCache($episode->id, $episode->anime_id);

        return $episode;
    }
}
