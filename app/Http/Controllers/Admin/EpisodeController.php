<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteEpisodeAction;
use App\Actions\Admin\StoreEpisodeAction;
use App\Actions\Admin\UpdateEpisodeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEpisodeRequest;
use App\Http\Requests\Admin\UpdateEpisodeRequest;
use App\Models\Anime;
use App\Models\Episode;
use App\Services\AnimeService;
use App\Services\EpisodeService;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function __construct(
        protected readonly EpisodeService $episodeService,
        protected readonly AnimeService $animeService,
    ) {}

    public function index(Request $request, ?Anime $anime = null)
    {
        $animeId = $anime?->id ?? ($request->integer('anime_id') ?: null);
        $episodes = $this->episodeService->getAdminList(
            $animeId,
            $request->only(['search', 'status']),
            15
        );
        $animes = $this->animeService->getCatalog([], 100);

        return view('admin.episodes.index', compact('episodes', 'animes', 'anime'));
    }

    public function create(Request $request, ?Anime $anime = null)
    {
        $animes = $this->animeService->getCatalog([], 100);
        $selectedAnime = $anime ?? ($request->integer('anime_id') ? Anime::find($request->integer('anime_id')) : null);
        $nextNumber = $selectedAnime ? ($selectedAnime->episodes()->max('episode_number') ?? 0) + 1 : 1;

        return view('admin.episodes.create', compact('animes', 'selectedAnime', 'nextNumber'));
    }

    public function store(StoreEpisodeRequest $request, StoreEpisodeAction $action)
    {
        $episode = $action($request->validated());

        return redirect()->route('admin.animes.episodes.index', $episode->anime_id)->with('success', 'Episode berhasil ditambahkan.');
    }

    public function edit(Anime $anime, Episode $episode)
    {
        $animes = $this->animeService->getCatalog([], 100);

        return view('admin.episodes.edit', compact('anime', 'episode', 'animes'));
    }

    public function update(UpdateEpisodeRequest $request, Anime $anime, Episode $episode, UpdateEpisodeAction $action)
    {
        $action($episode, $request->validated());

        return redirect()->route('admin.animes.episodes.index', $anime->id)->with('success', 'Episode berhasil diperbarui.');
    }

    public function destroy(Anime $anime, Episode $episode, DeleteEpisodeAction $action)
    {
        $action($episode);

        return redirect()->route('admin.animes.episodes.index', $anime->id)->with('success', 'Episode berhasil dihapus.');
    }

    public function updateStatus(Request $request, Episode $episode)
    {
        $request->validate(['status' => 'required|in:draft,processing,ready,failed,hidden']);
        $episode->update(['status' => $request->input('status')]);
        $this->episodeService->invalidateCache($episode->id, $episode->anime_id);

        return back()->with('success', 'Status episode diperbarui.');
    }
}
