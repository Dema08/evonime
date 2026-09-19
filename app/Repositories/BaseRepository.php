<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Kenapa Eloquent (bukan query builder / DB::raw):
 * scope model (published, search, ...), casts enum (VideoStatus/Quality/Language),
 * accessor (poster_url, ...), dan event tetap jalan. Query builder mentah
 * mengembalikan stdClass dan memutus semua itu.
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(protected readonly Model $model) {}

    /** Ambil semua baris model. */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    /** Cari by PK via Eloquent. */
    public function find(int|string $id, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->find($id, $columns);
    }

    /** Cari by PK atau throw ModelNotFoundException. */
    public function findOrFail(int|string $id, array $columns = ['*']): Model
    {
        return $this->model->newQuery()->findOrFail($id, $columns);
    }

    /** Cari baris pertama by kolom arbitrary. */
    public function findBy(string $column, mixed $value, array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->where($column, $value)->first($columns);
    }

    /** Insert via Eloquent create. */
    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    /** Update by PK tanpa load model dulu. */
    public function update(int|string $id, array $data): bool
    {
        return $this->model->newQuery()->whereKey($id)->update($data) > 0;
    }

    /** Hapus by PK. */
    public function delete(int|string $id): bool
    {
        return $this->model->newQuery()->whereKey($id)->delete() > 0;
    }

    /** Paginasi LengthAware standar. */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage, $columns);
    }

    /** Cek eksistensi PK via EXISTS query. */
    public function exists(int|string $id): bool
    {
        return $this->model->newQuery()->whereKey($id)->exists();
    }

    /** COUNT(*) tanpa load baris. */
    public function count(): int
    {
        return (int) $this->model->newQuery()->count();
    }
}

