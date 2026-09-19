<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /** Ambil semua baris (hindari untuk tabel besar, pakai paginate/cursor). */
    public function all(array $columns = ['*']): Collection;

    /** Cari by PK, return null jika tidak ada. */
    public function find(int|string $id, array $columns = ['*']): ?Model;

    /** Cari by PK, throw ModelNotFoundException jika tidak ada. */
    public function findOrFail(int|string $id, array $columns = ['*']): Model;

    /** Cari baris pertama by kolom arbitrary. */
    public function findBy(string $column, mixed $value, array $columns = ['*']): ?Model;

    /** Insert satu baris via Eloquent (event + mutator tetap jalan). */
    public function create(array $data): Model;

    /** Update satu baris by PK, return bool. */
    public function update(int|string $id, array $data): bool;

    /** Hapus satu baris by PK, return bool. */
    public function delete(int|string $id): bool;

    /** Paginasi standar (pakai simplePaginate di method khusus jika tak butuh total). */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /** Cek eksistensi PK tanpa load model. */
    public function exists(int|string $id): bool;

    /** Hitung total baris via COUNT(*) query. */
    public function count(): int;
}
