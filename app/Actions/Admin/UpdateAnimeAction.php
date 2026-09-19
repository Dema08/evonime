<?php

namespace App\Actions\Admin;

use App\Models\Anime;
use App\Services\AnimeService;
use App\Services\FileUploadService;
use Illuminate\Support\Str;

class UpdateAnimeAction
{
    public function __construct(
        protected readonly FileUploadService $fileUpload,
        protected readonly AnimeService $animeService,
    ) {}

    public function __invoke(Anime $anime, array $data): Anime
    {
        if (isset($data['poster']) && $data['poster'] instanceof \Illuminate\Http\UploadedFile) {
            $data['poster_path'] = $this->fileUpload->uploadImage($data['poster'], 'posters', $anime->poster_path);
        }
        if (isset($data['banner']) && $data['banner'] instanceof \Illuminate\Http\UploadedFile) {
            $data['banner_path'] = $this->fileUpload->uploadImage($data['banner'], 'banners', $anime->banner_path);
        }

        $genres = $data['genres'] ?? null;
        unset($data['poster'], $data['banner'], $data['genres']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $anime->update($data);

        if ($genres !== null) {
            $anime->genres()->sync($genres);
        }

        $this->animeService->invalidateCache($anime->id);

        return $anime;
    }
}
