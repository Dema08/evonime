<?php

namespace App\Actions\Admin;

use App\Models\Episode;
use App\Services\EpisodeService;
use App\Services\FileUploadService;

class StoreEpisodeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly EpisodeService $episodeService,
    ) {}

    public function __invoke(array $data): Episode
    {
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            $data['thumbnail_path'] = $this->fileUpload->uploadImage($data['thumbnail'], 'thumbnails');
        }
        unset($data['thumbnail']);

        $episode = Episode::create($data);

        $this->episodeService->invalidateCache($episode->id, $episode->anime_id);

        return $episode;
    }
}
