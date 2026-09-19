<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteSubtitleAction;
use App\Actions\Admin\StoreSubtitleAction;
use App\Actions\Admin\UpdateSubtitleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubtitleRequest;
use App\Http\Requests\Admin\UpdateSubtitleRequest;
use App\Models\Episode;
use App\Models\Subtitle;

class SubtitleController extends Controller
{
    public function index(Episode $episode)
    {
        $subtitles = $episode->subtitles()->orderBy('language')->paginate(15);
        $episode->load('anime:id,title');

        return view('admin.subtitles.index', compact('episode', 'subtitles'));
    }

    public function create(Episode $episode)
    {
        return view('admin.subtitles.create', compact('episode'));
    }

    public function store(StoreSubtitleRequest $request, Episode $episode, StoreSubtitleAction $action)
    {
        $data = array_merge($request->validated(), ['episode_id' => $episode->id]);
        $action($data);

        return redirect()->route('admin.episodes.subtitles.index', $episode->id)->with('success', 'Subtitle berhasil ditambahkan.');
    }

    public function edit(Episode $episode, Subtitle $subtitle)
    {
        return view('admin.subtitles.edit', compact('episode', 'subtitle'));
    }

    public function update(UpdateSubtitleRequest $request, Episode $episode, Subtitle $subtitle, UpdateSubtitleAction $action)
    {
        $data = array_merge($request->validated(), ['episode_id' => $episode->id]);
        $action($subtitle, $data);

        return redirect()->route('admin.episodes.subtitles.index', $episode->id)->with('success', 'Subtitle berhasil diperbarui.');
    }

    public function destroy(Episode $episode, Subtitle $subtitle, DeleteSubtitleAction $action)
    {
        $action($subtitle);

        return redirect()->route('admin.episodes.subtitles.index', $episode->id)->with('success', 'Subtitle berhasil dihapus.');
    }
}
