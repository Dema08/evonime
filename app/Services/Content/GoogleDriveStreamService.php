<?php

namespace App\Services\Content;

class GoogleDriveStreamService
{
    /**
     * Ekstrak File ID dari berbagai format URL Google Drive atau raw ID.
     */
    public function extractFileId(string $input): ?string
    {
        $input = trim($input);

        // Jika input sudah berupa file ID (alphanumeric, dash, underscore, panjang ~25-50 karakter)
        if (preg_match('/^[a-zA-Z0-9_-]{25,50}$/', $input)) {
            return $input;
        }

        // Format: https://drive.google.com/file/d/{FILE_ID}/...
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        // Format: https://drive.google.com/open?id={FILE_ID} atau uc?id={FILE_ID}
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        // Format: https://drive.google.com/uc?export=download&id={FILE_ID}
        if (preg_match('/export=download.*?id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Dapatkan URL embed preview Google Drive untuk iframe.
     */
    public function getEmbedUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    /**
     * Dapatkan URL direct download/stream Google Drive (cocok untuk VLC dan direct download).
     */
    public function getDirectStreamUrl(string $fileId): string
    {
        return "https://drive.google.com/uc?export=download&id={$fileId}";
    }

    /**
     * Dapatkan link protokol VLC desktop.
     */
    public function getVlcProtocolUrl(string $fileId): string
    {
        $directUrl = $this->getDirectStreamUrl($fileId);

        return "vlc://{$directUrl}";
    }

    /**
     * Bangun struktur data sumber stream Google Drive yang siap dikirim ke frontend player.
     *
     * @return array{
     *     id: string,
     *     server_name: string,
     *     quality: string,
     *     provider: string,
     *     is_embed: bool,
     *     is_m3u8: bool,
     *     file_id: string,
     *     embed_url: string,
     *     direct_url: string,
     *     vlc_url: string,
     *     url: string,
     *     embeddable: bool,
     *     subtitle_type: string,
     *     default_lang: string
     * }
     */
    public function formatSource(
        string $fileIdOrUrl,
        string $serverName = 'Google Drive (1080p FHD)',
        string $quality = '1080p',
        ?string $animeSlug = null,
        ?int $episodeNum = null
    ): array {
        $fileId = $this->extractFileId($fileIdOrUrl) ?? $fileIdOrUrl;
        $embedUrl = $this->getEmbedUrl($fileId);
        $directUrl = $this->getDirectStreamUrl($fileId);
        $vlcUrl = $this->getVlcProtocolUrl($fileId);
        $proxyUrl = ($animeSlug && $episodeNum)
            ? "/stream/{$animeSlug}/{$episodeNum}/{$quality}"
            : null;

        return [
            'id' => 'gdrive_'.$fileId,
            'server_name' => $serverName,
            'quality' => $quality,
            'provider' => 'google_drive',
            'is_embed' => false,
            'is_proxy' => true,
            'is_m3u8' => false,
            'file_id' => $fileId,
            'proxy_url' => $proxyUrl,
            'embed_url' => $embedUrl,
            'direct_url' => $directUrl,
            'vlc_url' => $vlcUrl,
            'url' => $proxyUrl ?: $embedUrl,
            'embeddable' => true,
            'subtitle_type' => 'softsub/hardsub',
            'default_lang' => 'Indonesia',
            'needs_resolve' => false,
            'data_content' => null,
            'embed_block_reason' => null,
        ];
    }
}
