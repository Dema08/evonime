<?php

namespace App\Console\Commands;

use App\Enums\VideoQuality;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\StreamSource;
use App\Services\GoogleDrive\GoogleDriveClientService;
use Illuminate\Console\Command;

class SyncGoogleDriveCommand extends Command
{
    protected $signature = 'anime:sync-gdrive
                            {anime : Slug atau ID anime di database}
                            {--folder= : ID folder Google Drive spesifik}
                            {--query= : Keyword pencarian di Google Drive (default: judul anime)}';

    protected $description = 'Scan dan tautkan otomatis (auto-match) semua episode 1080p anime dari Google Drive ke database';

    public function handle(GoogleDriveClientService $gdriveClient): int
    {
        $animeInput = $this->argument('anime');
        $folderId = $this->option('folder') ?: config('gdrive.folder_id');
        $customQuery = $this->option('query');

        // 1. Cari Anime
        $anime = is_numeric($animeInput)
            ? Anime::find((int) $animeInput)
            : Anime::where('slug', $animeInput)->orWhere('slug', 'like', "%{$animeInput}%")->first();

        if (! $anime) {
            $this->error("Anime '{$animeInput}' tidak ditemukan di database!");

            return Command::FAILURE;
        }

        $this->info("🔍 Memulai sinkronisasi Google Drive untuk: {$anime->title} ({$anime->slug})");

        // 2. Tentukan kata kunci pencarian file di Google Drive
        $searchTerm = $customQuery;
        if (empty($searchTerm)) {
            // Ambil kata pertama atau nama utama (misal: "Sousou no Frieren" -> "Frieren")
            $titleWords = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $anime->title));
            $searchTerm = end($titleWords);
            if (strlen($searchTerm) < 4 && count($titleWords) > 1) {
                $searchTerm = $titleWords[0];
            }
        }

        $this->line("   Mencari file video di Google Drive dengan kata kunci: <comment>'{$searchTerm}'</comment>");
        if ($folderId) {
            $this->line("   Di dalam Root Folder: <comment>{$folderId}</comment>");
        }

        // 3. Cek apakah ada subfolder khusus untuk anime ini (misal: "1080p/Sousou no Frieren")
        $targetFolderId = $folderId;
        $foundSubfolder = null;

        if ($targetFolderId) {
            $subfolder = $gdriveClient->findAnimeFolder($targetFolderId, $anime->title);
            if ($subfolder) {
                $targetFolderId = $subfolder['id'];
                $foundSubfolder = $subfolder['name'];
                $this->info("📂 Terdeteksi subfolder anime: <comment>[{$foundSubfolder}]</comment> (ID: {$targetFolderId})");
            }
        }

        // 4. Cari file di Google Drive (di dalam subfolder anime atau global)
        $files = $foundSubfolder
            ? $gdriveClient->getVideosInFolder($targetFolderId)
            : $gdriveClient->searchVideoFiles($searchTerm, $targetFolderId);

        if (empty($files)) {
            $this->warn("⚠️  Tidak ada file video yang cocok ditemukan di Google Drive dengan kata kunci '{$searchTerm}'.");
            $this->line('   Tips: Anda bisa mencoba custom query:');
            $this->line("   <info>php artisan anime:sync-gdrive {$anime->slug} --query=\"NamaFileDiDrive\"</info>");

            return Command::SUCCESS;
        }

        $this->info('📁 Ditemukan '.count($files).' file video di Google Drive. Memulai proses pencocokan (auto-match)...');

        $matchedCount = 0;
        $tableRows = [];

        // Ambil semua episode anime dari database
        $episodes = $anime->episodes()->get()->keyBy('episode_number');

        foreach ($files as $file) {
            $fileName = $file['name'];
            $fileId = $file['id'];

            // Ekstrak nomor episode dari nama file
            $detectedEpisode = $this->extractEpisodeNumber($fileName);

            if ($detectedEpisode !== null && isset($episodes[$detectedEpisode])) {
                $episode = $episodes[$detectedEpisode];

                // Simpan atau update ke tabel stream_sources
                StreamSource::updateOrCreate(
                    [
                        'episode_id' => $episode->id,
                        'quality' => VideoQuality::P1080,
                        'server_name' => 'Google Drive (1080p FHD)',
                    ],
                    [
                        'url' => $fileId,
                        'format' => 'mp4',
                        'is_active' => true,
                        'priority' => 10,
                    ]
                );

                $matchedCount++;
                $tableRows[] = [
                    "Ep. {$detectedEpisode}",
                    $episode->title,
                    $fileName,
                    $fileId,
                    '✅ Terhubung',
                ];
            } else {
                $tableRows[] = [
                    $detectedEpisode ? "Ep. {$detectedEpisode} (?)" : '-',
                    'Belum ada di DB',
                    $fileName,
                    $fileId,
                    '⚠️ Dilewati',
                ];
            }
        }

        $this->table(
            ['Episode', 'Judul Episode di DB', 'Nama File Google Drive', 'Google Drive File ID', 'Status'],
            $tableRows
        );

        $this->info("🎉 Selesai! Sebanyak {$matchedCount} episode berhasil ditautkan secara otomatis ke 1080p Local Proxy.");
        $this->line('   User kini dapat langsung memilih resolusi [1080p FHD] saat menonton anime ini.');

        return Command::SUCCESS;
    }

    /**
     * Algoritma cerdas deteksi nomor episode dari nama file video.
     * Mendukung format seperti:
     * - "Sousou no Frieren - 01 [1080p].mkv"
     * - "Frieren Episode 12.mp4"
     * - "[SubsPlease] Frieren - 28 (1080p).mkv"
     */
    protected function extractEpisodeNumber(string $filename): ?int
    {
        // 1. Pola eksplisit: "Episode 01", "Ep. 05", "Ep 12"
        if (preg_match('/(?:episode|ep)[\s._-]*0*(\d{1,3})/i', $filename, $m)) {
            return (int) $m[1];
        }

        // 2. Pola standar fansub: " - 01 ", " - 01 [", " - 01("
        if (preg_match('/[\s_-]+0*(\d{1,3})(?:[\s._\-\[\(]|$)/', $filename, $m)) {
            // Abaikan jika angka adalah 720, 1080, 2023, 2024 (resolusi/tahun)
            $num = (int) $m[1];
            if ($num !== 720 && $num !== 1080 && $num < 1000) {
                return $num;
            }
        }

        // 3. Pola kurung: "[01]" atau "(01)"
        if (preg_match('/[\[\(]0*(\d{1,3})[\]\)]/', $filename, $m)) {
            $num = (int) $m[1];
            if ($num !== 720 && $num !== 1080 && $num < 1000) {
                return $num;
            }
        }

        // 4. Pola angka murni di awal nama: "01.mp4", "1.mp4", "01 - Judul.mkv"
        if (preg_match('/^0*(\d{1,3})(?:[\s._\-\[\(]|$)/', $filename, $m)) {
            $num = (int) $m[1];
            if ($num !== 720 && $num !== 1080 && $num < 1000) {
                return $num;
            }
        }

        return null;
    }
}
