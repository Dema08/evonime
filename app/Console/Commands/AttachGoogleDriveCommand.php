<?php

namespace App\Console\Commands;

use App\Enums\VideoQuality;
use App\Enums\VideoStatus;
use App\Models\Anime;
use App\Models\Episode;
use App\Models\StreamSource;
use App\Services\Content\GoogleDriveStreamService;
use Illuminate\Console\Command;

class AttachGoogleDriveCommand extends Command
{
    protected $signature = 'anime:attach-gdrive 
        {anime : Slug atau ID anime} 
        {episode : Nomor episode (angka)} 
        {url_or_id : URL Google Drive atau File ID} 
        {--server=Google Drive (1080p FHD) : Nama server tampilan} 
        {--quality=1080p : Kualitas video (360p, 480p, 720p, 1080p)} 
        {--priority=10 : Prioritas server (angka lebih tinggi lebih utama)}';

    protected $description = 'Pasang / tautkan link Google Drive 1080p ke episode anime';

    public function handle(GoogleDriveStreamService $gdriveService): int
    {
        $animeInput = $this->argument('anime');
        $episodeNumber = (int) $this->argument('episode');
        $rawUrlOrId = (string) $this->argument('url_or_id');
        $serverName = (string) $this->option('server');
        $qualityInput = (string) $this->option('quality');
        $priority = (int) $this->option('priority');

        // 1. Cari Anime
        $anime = is_numeric($animeInput)
            ? Anime::find((int) $animeInput)
            : Anime::where('slug', $animeInput)->first();

        if (! $anime) {
            $this->error("❌ Anime '{$animeInput}' tidak ditemukan.");

            return self::FAILURE;
        }

        // 2. Ekstrak File ID
        $fileId = $gdriveService->extractFileId($rawUrlOrId);
        if (! $fileId) {
            $this->error("❌ Format Google Drive ID / URL tidak valid: '{$rawUrlOrId}'.");

            return self::FAILURE;
        }

        // 3. Cari / Buat Episode jika belum ada
        $episode = Episode::firstOrCreate(
            [
                'anime_id' => $anime->id,
                'episode_number' => $episodeNumber,
            ],
            [
                'title' => "Episode {$episodeNumber}",
                'status' => VideoStatus::Ready->value,
                'views_count' => 0,
            ]
        );

        // 4. Validasi VideoQuality Enum
        $qualityEnum = VideoQuality::tryFrom($qualityInput) ?? VideoQuality::FullHD;

        // 5. Simpan / Update StreamSource
        $source = StreamSource::updateOrCreate(
            [
                'episode_id' => $episode->id,
                'server_name' => $serverName,
                'quality' => $qualityEnum->value,
            ],
            [
                'url' => $fileId,
                'format' => 'mp4',
                'is_active' => true,
                'priority' => $priority,
            ]
        );

        $embedUrl = $gdriveService->getEmbedUrl($fileId);
        $directUrl = $gdriveService->getDirectStreamUrl($fileId);

        $this->info('✅ Berhasil menautkan Google Drive ke episode!');
        $this->table(
            ['Parameter', 'Nilai'],
            [
                ['Anime', "{$anime->title} ({$anime->slug})"],
                ['Episode', "Episode {$episode->episode_number}"],
                ['Server Name', $source->server_name],
                ['Kualitas', $source->quality->value],
                ['Google Drive File ID', $fileId],
                ['Embed URL', $embedUrl],
                ['Direct Stream / VLC', $directUrl],
                ['Priority', (string) $source->priority],
            ]
        );

        return self::SUCCESS;
    }
}
