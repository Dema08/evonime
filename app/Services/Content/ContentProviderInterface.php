<?php

namespace App\Services\Content;

/**
 * Kontrak integrasi sumber konten eksternal.
 *
 * Integrasi sumber konten LEGAL saja (contoh: API lisensi / feed resmi /
 * library milik sendiri). JANGAN dipakai untuk scraping bajakan.
 *
 * Setiap implementasi WAJIB mengembalikan data sesuai format standar yang
 * didokumentasikan pada masing-masing method di bawah ini, sehingga layer
 * pemanggil (Service/Controller/Job) tidak perlu tahu provider mana yang aktif.
 * Jika provider gagal atau tidak menemukan data, kembalikan array kosong dengan
 * bentuk (shape) yang tetap konsisten — jangan melempar exception ke pemanggil.
 */
interface ContentProviderInterface
{
    /**
     * Cari judul konten berdasarkan kata kunci.
     *
     * Format standar:
     * [
     *     'results' => [
     *         ['id' => 'string', 'title' => 'string', 'image' => 'string|null', 'provider' => 'string'],
     *         ...
     *     ],
     * ]
     *
     * @return array{results: array<int, array{id: string, title: string, image: string|null, provider: string}>}
     */
    public function search(string $query): array;

    /**
     * Ambil detail satu judul beserta daftar episode-nya.
     *
     * Format standar:
     * [
     *     'id' => 'string',
     *     'title' => 'string',
     *     'synopsis' => 'string|null',
     *     'image' => 'string|null',
     *     'genres' => ['Action', 'Fantasy', ...],
     *     'episodes' => [
     *         ['id' => 'string', 'number' => 1, 'title' => 'string|null'],
     *         ...
     *     ],
     * ]
     *
     * @return array{id: string, title: string, synopsis: string|null, image: string|null, genres: array<int, string>, episodes: array<int, array{id: string, number: int, title: string|null}>}
     */
    public function getAnimeInfo(string $providerId): array;

    /**
     * Ambil sumber streaming dan subtitle untuk satu episode.
     *
     * Format standar:
     * [
     *     'sources' => [
     *         ['url' => 'string', 'quality' => 'string', 'is_m3u8' => true],
     *         ...
     *     ],
     *     'subtitles' => [
     *         ['url' => 'string', 'lang' => 'English'],
     *         ...
     *     ],
     * ]
     *
     * @return array{sources: array<int, array{url: string, quality: string, is_m3u8: bool}>, subtitles: array<int, array{url: string, lang: string}>}
     */
    public function getEpisodeSources(string $episodeId): array;

    /**
     * Identifier unik provider (huruf kecil, tanpa spasi).
     */
    public function getProviderName(): string;
}
