<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteStreamSourceAction;
use App\Actions\Admin\StoreStreamSourceAction;
use App\Actions\Admin\UpdateStreamSourceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStreamSourceRequest;
use App\Http\Requests\Admin\UpdateStreamSourceRequest;
use App\Models\Episode;
use App\Models\StreamSource;
use App\Repositories\Contracts\StreamSourceRepositoryInterface;

class StreamSourceController extends Controller
{
    public function __construct(protected readonly StreamSourceRepositoryInterface $sources) {}

    public function index(Episode $episode)
    {
        $sources = $episode->streamSources()->orderByDesc('priority')->paginate(15);
        $episode->load('anime:id,title');

        return view('admin.sources.index', compact('episode', 'sources'));
    }

    public function create(Episode $episode)
    {
        return view('admin.sources.create', compact('episode'));
    }

    public function store(StoreStreamSourceRequest $request, Episode $episode, StoreStreamSourceAction $action)
    {
        $data = array_merge($request->validated(), ['episode_id' => $episode->id]);
        $action($data);

        return redirect()->route('admin.episodes.sources.index', $episode->id)->with('success', 'Stream source berhasil ditambahkan.');
    }

    public function edit(Episode $episode, StreamSource $source)
    {
        return view('admin.sources.edit', compact('episode', 'source'));
    }

    public function update(UpdateStreamSourceRequest $request, Episode $episode, StreamSource $source, UpdateStreamSourceAction $action)
    {
        $data = array_merge($request->validated(), ['episode_id' => $episode->id]);
        $action($source, $data);

        return redirect()->route('admin.episodes.sources.index', $episode->id)->with('success', 'Stream source berhasil diperbarui.');
    }

    public function destroy(Episode $episode, StreamSource $source, DeleteStreamSourceAction $action)
    {
        $action($source);

        return redirect()->route('admin.episodes.sources.index', $episode->id)->with('success', 'Stream source berhasil dihapus.');
    }
}
