<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteGenreAction;
use App\Actions\Admin\StoreGenreAction;
use App\Actions\Admin\UpdateGenreAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGenreRequest;
use App\Http\Requests\Admin\UpdateGenreRequest;
use App\Models\Genre;
use App\Services\GenreService;

class GenreController extends Controller
{
    public function __construct(protected readonly GenreService $genreService) {}

    public function index()
    {
        $genres = $this->genreService->getAdminList(15);

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(StoreGenreRequest $request, StoreGenreAction $action)
    {
        $action($request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil ditambahkan.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre, UpdateGenreAction $action)
    {
        $action($genre, $request->validated());

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre, DeleteGenreAction $action)
    {
        $action($genre);

        return redirect()->route('admin.genres.index')->with('success', 'Genre berhasil dihapus.');
    }
}
