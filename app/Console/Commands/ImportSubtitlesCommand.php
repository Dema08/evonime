<?php

namespace App\Console\Commands;

use App\Enums\SubtitleLanguage;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\Subtitle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportSubtitlesCommand extends Command
{
    protected $signature = 'anime:import-subs
                            {anime : Slug atau ID anime}
                            {folder? : Path folder tempat file .ass atau .vtt berada (opsional)}';

    protected $description = 'Konversi dan impor file subtitle .ass / .vtt untuk web player Evonime';

    public function handle(): int
    {
        $animeInput = $this->argument('anime');
        $rawFolder = $this->argument('folder');
        $folderPath = $rawFolder ? rtrim(trim($rawFolder, "\"'"), '\\/') : null;

        // 1. Cari Anime di Database
        $anime = is_numeric($animeInput)
            ? Anime::find((int) $animeInput)
            : Anime::where('slug', $animeInput)->orWhere('slug', 'like', "%{$animeInput}%")->first();

        if (! $anime) {
            $this->error("Anime '{$animeInput}' tidak ditemukan di database!");

            return Command::FAILURE;
        }

        if (empty($folderPath)) {
            $folderPath = public_path("subtitles/{$anime->slug}");
        }

        if (! File::isDirectory($folderPath)) {
            $this->error("Folder '{$folderPath}' tidak ditemukan atau tidak dapat diakses!");

            return Command::FAILURE;
        }

        $this->info("🔍 Membaca file subtitle dari: {$folderPath}");
        $this->info("   Target Anime: {$anime->title} ({$anime->slug})");

        // 2. Cek apakah ada file .ass atau .vtt di folder
        $files = File::files($folderPath);
        $assFiles = array_filter($files, fn ($f) => strtolower($f->getExtension()) === 'ass');
        $vttFiles = array_filter($files, fn ($f) => strtolower($f->getExtension()) === 'vtt');

        if (empty($assFiles) && empty($vttFiles)) {
            $this->warn('⚠️  Tidak ada file .ass atau .vtt ditemukan di folder tersebut.');

            return Command::FAILURE;
        }

        // Buat direktori tujuan di public/subtitles/{anime_slug} jika belum ada
        $destDir = public_path("subtitles/{$anime->slug}");
        if (! File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $episodes = $anime->episodes()->get()->keyBy('episode_number');
        $successCount = 0;
        $tableRows = [];

        // Mode A: Registrasi langsung file .vtt yang sudah ada di public/subtitles
        if (empty($assFiles) && ! empty($vttFiles)) {
            $this->info('📁 Terdeteksi '.count($vttFiles).' file WebVTT (.vtt). Memulai registrasi ke database...');

            foreach ($vttFiles as $file) {
                $fileName = $file->getFilename();
                $detectedEpisode = $this->extractEpisodeNumber($fileName);

                if ($detectedEpisode === null || ! isset($episodes[$detectedEpisode])) {
                    $tableRows[] = [
                        $detectedEpisode ? "Ep. {$detectedEpisode} (?)" : '-',
                        'Belum ada di DB',
                        $fileName,
                        round($file->getSize() / 1024, 1).' KB',
                        '⚠️ Dilewati',
                    ];
                    continue;
                }

                $episode = $episodes[$detectedEpisode];
                $relativeUrl = "subtitles/{$anime->slug}/{$fileName}";

                Subtitle::updateOrCreate(
                    [
                        'episode_id' => $episode->id,
                        'language' => SubtitleLanguage::Id,
                    ],
                    [
                        'label' => 'Indonesia',
                        'format' => 'vtt',
                        'url' => $relativeUrl,
                        'is_default' => true,
                    ]
                );

                $successCount++;
                $tableRows[] = [
                    "Ep. {$detectedEpisode}",
                    $episode->title,
                    $fileName,
                    round($file->getSize() / 1024, 1).' KB',
                    '✅ Terdaftar di DB',
                ];
            }

            $this->table(
                ['Episode', 'Judul Episode', 'File VTT', 'Ukuran', 'Status'],
                $tableRows
            );

            $this->info("🎉 Selesai! Sebanyak {$successCount} episode berhasil didaftarkan subtitlenya ke database.");
            return Command::SUCCESS;
        }

        $this->info('📁 Ditemukan '.count($assFiles).' file subtitle .ass. Memulai konversi ke WebVTT...');

        $episodes = $anime->episodes()->get()->keyBy('episode_number');
        $successCount = 0;
        $tableRows = [];

        foreach ($assFiles as $file) {
            $fileName = $file->getFilename();
            $detectedEpisode = $this->extractEpisodeNumber($fileName);

            if ($detectedEpisode === null || ! isset($episodes[$detectedEpisode])) {
                $tableRows[] = [
                    $detectedEpisode ? "Ep. {$detectedEpisode} (?)" : '-',
                    'Belum ada di DB',
                    $fileName,
                    '-',
                    '⚠️ Dilewati',
                ];

                continue;
            }

            $episode = $episodes[$detectedEpisode];

            // Baca konten .ass dan konversi ke .vtt
            $assContent = File::get($file->getRealPath());
            $vttContent = $this->convertAssToVtt($assContent);

            $vttFileName = "episode-{$detectedEpisode}.vtt";
            $vttPath = "{$destDir}/{$vttFileName}";
            File::put($vttPath, $vttContent);

            $relativeUrl = "subtitles/{$anime->slug}/{$vttFileName}";

            // Simpan ke database
            Subtitle::updateOrCreate(
                [
                    'episode_id' => $episode->id,
                    'language' => SubtitleLanguage::Id,
                ],
                [
                    'label' => 'Indonesia',
                    'format' => 'vtt',
                    'url' => $relativeUrl,
                    'is_default' => true,
                ]
            );

            $successCount++;
            $tableRows[] = [
                "Ep. {$detectedEpisode}",
                $episode->title,
                $fileName,
                round(strlen($vttContent) / 1024, 1).' KB',
                '✅ Berhasil Diimpor',
            ];
        }

        $this->table(
            ['Episode', 'Judul Episode', 'File Sumber ASS', 'Ukuran VTT', 'Status'],
            $tableRows
        );

        $this->info("🎉 Selesai! Sebanyak {$successCount} episode berhasil dipasangkan subtitle WebVTT Bahasa Indonesia.");
        $this->line('   Subtitle akan langsung tampil di player web Evonime.');

        return Command::SUCCESS;
    }

    /**
     * Konversi ASS ke WebVTT yang bersih (menyaring karaoke frame spam).
     */
    protected function convertAssToVtt(string $assContent): string
    {
        $lines = explode("\n", $assContent);
        $vtt = ["WEBVTT\n"];
        $inEvents = false;
        $formatCols = [];
        $seen = [];

        $formatTime = function (string $t): string {
            $p = explode(':', $t);
            if (count($p) < 3) {
                return '00:00:00.000';
            }
            $h = sprintf('%02d', (int) $p[0]);
            $m = sprintf('%02d', (int) $p[1]);
            $secParts = explode('.', $p[2]);
            $s = sprintf('%02d', (int) $secParts[0]);
            $ms = str_pad(substr($secParts[1] ?? '0', 0, 3), 3, '0');

            return "{$h}:{$m}:{$s}.{$ms}";
        };

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '[Events]') {
                $inEvents = true;

                continue;
            }
            if (! $inEvents) {
                continue;
            }

            if (str_starts_with($line, 'Format:')) {
                $formatCols = array_map('trim', explode(',', substr($line, 7)));

                continue;
            }

            if (str_starts_with($line, 'Dialogue:')) {
                if (empty($formatCols)) {
                    $formatCols = ['Layer', 'Start', 'End', 'Style', 'Name', 'MarginL', 'MarginR', 'MarginV', 'Effect', 'Text'];
                }
                $parts = explode(',', substr($line, 9), count($formatCols));
                if (count($parts) < count($formatCols)) {
                    continue;
                }

                $data = array_combine($formatCols, $parts);
                $style = trim($data['Style']);
                $start = trim($data['Start']);
                $end = trim($data['End']);
                $text = trim($data['Text']);

                // Abaikan frame animation spam (opening-romaji, TS1..6, dsb)
                if (preg_match('/^(?:opening-romaji|opening-indo|Lagu Penutup - Romaji|Lagu Penutup - Kanji|.*TS\d+)$/i', $style)) {
                    continue;
                }

                // Abaikan vector drawings
                if (str_contains($text, '{\p1}') || str_contains($text, '{\p2}') || str_contains($text, '{\p4}')) {
                    continue;
                }

                // Bersihkan tag override ASS: {\...}
                $cleanText = preg_replace('/\{[^}]+\}/', '', $text);
                $cleanText = str_replace(['\N', '\n', '\h'], ["\n", "\n", ' '], $cleanText);
                $cleanText = trim($cleanText);

                if (empty($cleanText)) {
                    continue;
                }

                // Cegah duplikasi teks di timestamp yang sama
                $hash = "{$start}_{$end}_{$cleanText}";
                if (isset($seen[$hash])) {
                    continue;
                }
                $seen[$hash] = true;

                $vtt[] = $formatTime($start).' --> '.$formatTime($end);
                $vtt[] = $cleanText."\n";
            }
        }

        return implode("\n", $vtt);
    }

    /**
     * Ekstrak nomor episode dari nama file.
     */
    protected function extractEpisodeNumber(string $filename): ?int
    {
        if (preg_match('/(?:episode|ep)[\s._-]*0*(\d{1,3})/i', $filename, $m)) {
            return (int) $m[1];
        }

        if (preg_match('/[\s_-]+0*(\d{1,3})(?:[\s._\-\[\(]|$)/', $filename, $m)) {
            $num = (int) $m[1];
            if ($num !== 720 && $num !== 1080 && $num < 1000) {
                return $num;
            }
        }

        if (preg_match('/^0*(\d{1,3})(?:[\s._\-\[\(]|$)/', $filename, $m)) {
            $num = (int) $m[1];
            if ($num !== 720 && $num !== 1080 && $num < 1000) {
                return $num;
            }
        }

        return null;
    }
}
