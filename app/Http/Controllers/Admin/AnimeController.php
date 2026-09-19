<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteAnimeAction;
use App\Actions\Admin\StoreAnimeAction;
use App\Actions\Admin\UpdateAnimeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnimeRequest;
use App\Http\Requests\Admin\UpdateAnimeRequest;
use App\Models\Anime;
use App\Services\AnimeService;
use App\Services\GenreService;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function __construct(
        protected readonly AnimeService $animeService,
        protected readonly GenreService $genreService,
    ) {}

    public function index(Request $request)
    {
        $animes = $this->animeService->getAdminList(
            $request->only(['search', 'status']),
            15
        );

        return view('admin.animes.index', compact('animes'));
    }

    public function create()
    {
        $genres = $this->genreService->getAllWithCount();

        return view('admin.animes.create', compact('genres'));
    }

    public function store(StoreAnimeRequest $request, StoreAnimeAction $action)
    {
        $action($request->validated());

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil ditambahkan.');
    }

    public function show(Anime $anime)
    {
        $anime->load(['genres', 'episodes']);

        return view('admin.animes.show', compact('anime'));
    }

    public function edit(Anime $anime)
    {
        $genres = $this->genreService->getAllWithCount();
        $anime->load('genres:id');
        $selectedGenres = $anime->genres->pluck('id')->all();

        return view('admin.animes.edit', compact('anime', 'genres', 'selectedGenres'));
    }

    public function update(UpdateAnimeRequest $request, Anime $anime, UpdateAnimeAction $action)
    {
        $action($anime, $request->validated());

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil diperbarui.');
    }

    public function destroy(Anime $anime, DeleteAnimeAction $action)
    {
        $action($anime);

        return redirect()->route('admin.animes.index')->with('success', 'Anime berhasil dihapus.');
    }

    public function toggleFeatured(Anime $anime)
    {
        $anime->update(['is_featured' => ! $anime->is_featured]);
        $this->animeService->invalidateCache($anime->id);

        return back()->with('success', 'Status featured anime diperbarui.');
    }
}
