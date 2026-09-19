<?php

namespace App\Actions\Admin;

use App\Models\Genre;
use Illuminate\Support\Facades\Cache;

class DeleteGenreAction
{
    public function __invoke(Genre $genre): bool
    {
        $deleted = $genre->delete();
        Cache::forget('genre:menu');
        Cache::forget('genre:list');
        return $deleted;
    }
}
