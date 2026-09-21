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

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'is_featured' => $anime->is_featured]);
        }

        return back()->with('success', 'Status featured anime diperbarui.');
    }

    public function updateHeroMedia(Request $request, Anime $anime)
    {
        $validated = $request->validate([
            'is_featured' => ['nullable', 'boolean'],
            'banner_path' => ['nullable', 'string', 'max:500'],
            'trailer_url' => ['nullable', 'string', 'max:500'],
            'synopsis'    => ['nullable', 'string'],
        ]);

        $updateData = [];
        if ($request->has('is_featured')) {
            $updateData['is_featured'] = $request->boolean('is_featured');
        }
        if (array_key_exists('banner_path', $validated)) {
            $updateData['banner_path'] = $validated['banner_path'];
        }
        if (array_key_exists('trailer_url', $validated)) {
            $updateData['trailer_url'] = $validated['trailer_url'];
        }
        if (array_key_exists('synopsis', $validated)) {
            $updateData['synopsis'] = $validated['synopsis'];
        }

        $anime->update($updateData);
        $this->animeService->invalidateCache($anime->id);

        return response()->json([
            'success' => true,
            'message' => 'Foto/Video Hero Banner anime berhasil diperbarui!',
            'data'    => $anime,
        ]);
    }
}
