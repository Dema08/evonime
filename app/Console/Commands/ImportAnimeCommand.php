<?php

namespace App\Console\Commands;

use App\Jobs\ImportAnimeFromOtakudesu;
use App\Models\Anime;
use App\Services\Content\OtakudesuScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ImportAnimeCommand extends Command
{
    protected $signature = 'anime:import
                            {query : Slug anime (misal: sousou-frieren-sub-indo) atau judul pencarian}
                            {--sync-gdrive : Otomatis sinkronkan dengan Google Drive setelah import (default: true)}';

    protected $description = 'Import anime lengkap dari Otakudesu beserta episodenya dan otomatis link ke Google Drive';

    public function handle(OtakudesuScraper $scraper): int
    {
        $query = $this->argument('query');

        $this->info("🔍 Mencari anime di Otakudesu untuk: '{$query}'...");

        // 1. Cek apakah input sudah berupa slug langsung
        $slug = $query;
        $info = $scraper->getAnimeInfo($slug);

        // Jika bukan slug valid, lakukan pencarian terlebih dahulu
        if (empty($info) || empty($info['title'])) {
            $searchResults = $scraper->search($query);

            if (empty($searchResults)) {
                $this->error("❌ Anime dengan kata kunci '{$query}' tidak ditemukan di Otakudesu.");

                return Command::FAILURE;
            }

            // Tampilkan hasil pencarian jika lebih dari 1
            if (count($searchResults) === 1) {
                $selected = $searchResults[0];
                $slug = $selected['slug'];
                $this->line("   Ditemukan: <comment>{$selected['title']}</comment> (Slug: {$slug})");
            } else {
                $choices = [];
                foreach ($searchResults as $idx => $r) {
                    $choices[$r['slug']] = "{$r['title']} [{$r['status']}] (slug: {$r['slug']})";
                }
                $slug = $this->choice('Pilih anime yang ingin di-import:', array_keys($choices), 0);
            }

            $info = $scraper->getAnimeInfo($slug);
        }

        if (empty($info) || empty($info['title'])) {
            $this->error("❌ Gagal mengambil detail info anime dari Otakudesu untuk slug: '{$slug}'.");

            return Command::FAILURE;
        }

        $title = $info['title'];
        $episodes = $info['episode_lists'] ?? [];
        $totalEps = count($episodes);

        $this->info("📥 Mengimpor: <comment>{$title}</comment>");
        $this->line("   Total episode ditemukan di Otakudesu: <comment>{$totalEps} episode</comment>");

        // 2. Jalankan Job Import secara sinkron
        $this->output->progressStart($totalEps);

        try {
            app(ImportAnimeFromOtakudesu::class, ['slug' => $slug])->handle($scraper);
        } catch (\Throwable $e) {
            $this->output->progressFinish();
            $this->error('❌ Gagal mengimpor anime: '.$e->getMessage());

            return Command::FAILURE;
        }

        $this->output->progressFinish();
        $this->info("✅ Berhasil mengimpor anime dan {$totalEps} episode ke database!");

        // 3. Tampilkan detail anime yang tersimpan
        $animeModel = Anime::where('slug', $slug)->first();
        if ($animeModel) {
            $this->table(
                ['Parameter', 'Detail'],
                [
                    ['ID Anime', $animeModel->id],
                    ['Judul', $animeModel->title],
                    ['Slug', $animeModel->slug],
                    ['Total Episode', $animeModel->episodes()->count()],
                    ['Status', $animeModel->status?->value ?? 'ready'],
                    ['URL Watch', url("/watch/{$animeModel->slug}/1")],
                ]
            );
        }

        // 4. Otomatis sinkronkan dengan Google Drive jika diaktifkan
        if ($this->option('sync-gdrive') !== false && config('gdrive.enabled', false)) {
            $this->newLine();
            $this->info('⚡ Menjalankan auto-sync Google Drive untuk 1080p...');
            Artisan::call('anime:sync-gdrive', ['anime' => $slug], $this->output);
        }

        $this->info('🎉 Selesai! Anda dapat membuka halaman nonton di browser:');
        $this->line('👉 <info>'.url("/watch/{$slug}/1").'</info>');

        return Command::SUCCESS;
    }
}
