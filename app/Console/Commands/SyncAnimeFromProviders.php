<?php

namespace App\Console\Commands;

use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\Genre;
use App\Services\Content\OtakudesuProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncAnimeFromProviders extends Command
{
    protected $signature = 'anime:sync-multi {query : Judul anime yang ingin di-sync}';

    protected $description = 'Sinkronkan anime dari Otakudesu ke database Evonime';

    public function handle(
        OtakudesuProvider $otaku,
    ): int {
        $query = $this->argument('query');

        if (empty(trim($query))) {
            $this->error('Query tidak boleh kosong.');
            return self::FAILURE;
        }

        $this->info("🔍 Mencari '{$query}' di Otakudesu...");

        // --- 1. Search di Otakudesu ---
        $results = $otaku->search($query);
        $count = count($results['results'] ?? []);

        $this->line("📥 Otakudesu: {$count} hasil ditemukan");

        if ($count === 0) {
            $this->error('Anime tidak ditemukan di Otakudesu.');
            return self::FAILURE;
        }

        // --- 2. Ambil detail anime (hasil pertama) ---
        $primaryData = $results['results'][0];
        $primaryInfo = $otaku->getAnimeInfo($primaryData['id']);

        if (empty($primaryInfo['title'])) {
            $this->error('Gagal mengambil detail anime dari Otakudesu.');
            return self::FAILURE;
        }

        // --- 3. Simpan/update anime ---
        $slug = Str::slug($primaryInfo['title']);
        $anime = Anime::updateOrCreate(
            ['slug' => $slug],
            [
                'title'           => $primaryInfo['title'],
                'status'          => VideoStatus::mapAnimeStatus($primaryInfo['status'] ?? null),
                'synopsis'        => $primaryInfo['synopsis'] ?? '',
                'poster_path'     => $primaryInfo['image'] ?? '',
                'banner_path'     => $primaryInfo['image'] ?? '',
                'is_published'    => true,
                'rating'          => $primaryInfo['rating'] ?? 0,
                'year'            => $primaryInfo['release_year'] ?? null,
                'total_episodes'  => count($primaryInfo['episodes'] ?? []),
            ]
        );

        $this->info("✅ Anime: {$anime->title}");

        // --- 4. Sync genres ---
        $genreIds = [];
        foreach ($primaryInfo['genres'] ?? [] as $genreName) {
            $genre = Genre::firstOrCreate(
                ['slug' => Str::slug($genreName)],
                ['name' => $genreName]
            );
            $genreIds[] = $genre->id;
        }
        if (!empty($genreIds)) {
            $anime->genres()->sync($genreIds);
        }

        // --- 5. Sync episodes ---
        // Hapus semua episode lama untuk anime ini (hindari duplikat)
        Episode::where('anime_id', $anime->id)->delete();
        $countMapped = 0;

        foreach ($primaryInfo['episodes'] ?? [] as $ep) {
            $episodeNumber = (int) ($ep['number'] ?? 0);

            // Fallback: ekstrak nomor dari slug jika number == 0
            if ($episodeNumber === 0 && !empty($ep['id'])) {
                if (preg_match('/(\d+)/', $ep['id'], $matches)) {
                    $episodeNumber = (int) $matches[0];
                }
            }

            Episode::create([
                'anime_id'             => $anime->id,
                'episode_number'       => $episodeNumber,
                'title'                => $ep['title'] ?? "Episode {$episodeNumber}",
                'status'               => VideoStatus::Ready->value,
                'external_id_otakudesu' => $ep['id'] ?? null,
                'external_id_consumet'  => null,
            ]);

            $countMapped++;
        }

        $totalEpisodes = count($primaryInfo['episodes'] ?? []);
        $this->line("📺 Total episode: {$totalEpisodes}");
        $this->line("🇮🇩 Otakudesu episode mapped: {$countMapped}");
        $this->info('✅ Selesai!');

        return self::SUCCESS;
    }
}