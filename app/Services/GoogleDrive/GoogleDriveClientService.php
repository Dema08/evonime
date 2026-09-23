<?php

namespace App\Services\GoogleDrive;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class GoogleDriveClientService
{
    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 30.0,
            'connect_timeout' => 10.0,
        ]);
    }

    /**
     * Dapatkan OAuth2 Access Token (dengan auto-refresh dan caching).
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'gdrive_access_token';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $clientId = config('gdrive.client_id');
        $clientSecret = config('gdrive.client_secret');
        $refreshToken = config('gdrive.refresh_token');

        // 1. Coba OAuth 2.0 Refresh Token
        if (! empty($clientId) && ! empty($clientSecret) && ! empty($refreshToken)) {
            try {
                $response = $this->httpClient->post('https://oauth2.googleapis.com/token', [
                    'form_params' => [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                        'refresh_token' => $refreshToken,
                        'grant_type' => 'refresh_token',
                    ],
                ]);

                $data = json_decode((string) $response->getBody(), true);
                if (! empty($data['access_token'])) {
                    $ttl = (int) config('gdrive.token_cache_ttl', 3300);
                    Cache::put($cacheKey, $data['access_token'], $ttl);

                    return $data['access_token'];
                }
            } catch (\Throwable $e) {
                Log::warning('GoogleDriveClientService: OAuth refresh token failed', ['error' => $e->getMessage()]);
            }
        }

        // 2. Coba Service Account JSON jika file ada
        $serviceAccountPath = config('gdrive.service_account_json');
        if (! empty($serviceAccountPath) && file_exists($serviceAccountPath)) {
            $token = $this->getAccessTokenFromServiceAccount($serviceAccountPath);
            if ($token) {
                Cache::put($cacheKey, $token, (int) config('gdrive.token_cache_ttl', 3300));

                return $token;
            }
        }

        return null;
    }

    /**
     * Ambil metadata file dari Google Drive (ukuran dalam bytes, MIME type, dan nama file).
     * Hasil di-cache untuk mencegah over-query ke Google Drive API saat Range Requests.
     *
     * @return array{id: string, name: string, size: int, mimeType: string}|null
     */
    public function getFileMetadata(string $fileId): ?array
    {
        $cacheKey = "gdrive_meta_{$fileId}";

        return Cache::remember($cacheKey, (int) config('gdrive.metadata_cache_ttl', 86400), function () use ($fileId) {
            $token = $this->getAccessToken();

            if ($token) {
                try {
                    $response = $this->httpClient->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                        'headers' => [
                            'Authorization' => "Bearer {$token}",
                            'Accept' => 'application/json',
                        ],
                        'query' => [
                            'fields' => 'id,name,size,mimeType',
                        ],
                    ]);

                    $data = json_decode((string) $response->getBody(), true);
                    if ($data && isset($data['id'])) {
                        return [
                            'id' => $data['id'],
                            'name' => $data['name'] ?? "video_{$fileId}.mp4",
                            'size' => isset($data['size']) ? (int) $data['size'] : 0,
                            'mimeType' => $data['mimeType'] ?? 'video/mp4',
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning('GoogleDriveClientService: Failed to get metadata via API', ['error' => $e->getMessage()]);
                }
            }

            // Fallback: Query HEAD atau Range 0-0 ke Google Drive Direct Stream
            return $this->getMetadataViaDirectStream($fileId);
        });
    }

    /**
     * Cari file video di Google Drive berdasarkan nama atau di dalam folder tertentu.
     *
     * @return array<int, array{id: string, name: string, size: int, mimeType: string}>
     */
    public function searchVideoFiles(string $searchTerm = '', ?string $folderId = null): array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            if (! empty($folderId)) {
                return $this->scrapePublicFolderFiles($folderId, $searchTerm);
            }

            return [];
        }

        $queryParts = ['trashed = false'];

        if (! empty($folderId)) {
            $queryParts[] = "'{$folderId}' in parents";
        }

        if (! empty($searchTerm)) {
            $cleanTerm = str_replace(["'", '"', '\\'], '', $searchTerm);
            $queryParts[] = "name contains '{$cleanTerm}'";
        }

        $queryParts[] = "(mimeType contains 'video/' or name contains '.mp4' or name contains '.mkv')";

        $q = implode(' and ', $queryParts);

        try {
            $response = $this->httpClient->get('https://www.googleapis.com/drive/v3/files', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'q' => $q,
                    'fields' => 'files(id, name, size, mimeType)',
                    'pageSize' => 100,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['files'] ?? [];
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Failed to search files in Drive', [
                'error' => $e->getMessage(),
                'query' => $q,
            ]);

            return [];
        }
    }

    /**
     * Cari subfolder anime di dalam parent folder (misal: mencari subfolder "Sousou no Frieren" di dalam folder "1080p").
     *
     * @return array{id: string, name: string}|null
     */
    public function findAnimeFolder(string $parentFolderId, string $animeTitle): ?array
    {
        $token = $this->getAccessToken();
        if (! $token) {
            return $this->scrapePublicSubfolders($parentFolderId, $animeTitle);
        }

        $cleanTitle = str_replace(["'", '"', '\\'], '', $animeTitle);
        $escapedTitle = addslashes($cleanTitle);

        // 1. Coba cari subfolder yang mengandung judul lengkap
        $q = "'{$parentFolderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false and name contains '{$escapedTitle}'";

        try {
            $response = $this->httpClient->get('https://www.googleapis.com/drive/v3/files', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'q' => $q,
                    'fields' => 'files(id, name)',
                    'pageSize' => 5,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            $folders = $data['files'] ?? [];

            if (! empty($folders)) {
                return $folders[0];
            }

            // 2. Jika tidak cocok, coba kata kunci utama judul (misal "Frieren" dari "Sousou no Frieren")
            $words = array_values(array_filter(explode(' ', $cleanTitle), fn ($w) => strlen($w) >= 4));
            foreach ($words as $word) {
                $wordEscaped = addslashes($word);
                $qWord = "'{$parentFolderId}' in parents and mimeType = 'application/vnd.google-apps.folder' and trashed = false and name contains '{$wordEscaped}'";
                $res = $this->httpClient->get('https://www.googleapis.com/drive/v3/files', [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json',
                    ],
                    'query' => [
                        'q' => $qWord,
                        'fields' => 'files(id, name)',
                        'pageSize' => 5,
                    ],
                ]);
                $d = json_decode((string) $res->getBody(), true);
                if (! empty($d['files'])) {
                    return $d['files'][0];
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Failed to find anime folder in Drive', [
                'error' => $e->getMessage(),
                'parent' => $parentFolderId,
                'title' => $animeTitle,
            ]);

            return null;
        }
    }

    /**
     * Ambil semua file video yang berada di dalam folder tertentu.
     *
     * @return array<int, array{id: string, name: string, size: int, mimeType: string}>
     */
    public function getVideosInFolder(string $folderId): array
    {
        return $this->searchVideoFiles('', $folderId);
    }

    /**
     * Ambil streaming response dari Google Drive dengan meneruskan header Range.
     */
    public function getStream(string $fileId, ?string $range = null): ResponseInterface
    {
        $token = $this->getAccessToken();
        $headers = [];

        if (! empty($range)) {
            $headers['Range'] = $range;
        }

        // Mode 1: Google Drive API v3 (Jika token tersedia)
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";

            return $this->httpClient->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media", [
                'headers' => $headers,
                'stream' => true,
                'http_errors' => false,
            ]);
        }

        // Mode 2: Direct Web Stream Fallback (Jika file shared "Anyone with link")
        $url = "https://drive.usercontent.google.com/download?id={$fileId}&export=download&confirm=t";
        $headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';

        return $this->httpClient->get($url, [
            'headers' => $headers,
            'stream' => true,
            'http_errors' => false,
            'allow_redirects' => [
                'max' => 5,
                'strict' => true,
                'referer' => true,
                'track_redirects' => true,
            ],
        ]);
    }

    /**
     * Fallback untuk mendeteksi ukuran file melalui Direct Stream.
     */
    protected function getMetadataViaDirectStream(string $fileId): ?array
    {
        try {
            $url = "https://drive.usercontent.google.com/download?id={$fileId}&export=download&confirm=t";
            $response = $this->httpClient->get($url, [
                'headers' => [
                    'Range' => 'bytes=0-0',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ],
                'allow_redirects' => true,
                'http_errors' => false,
            ]);

            $contentRange = $response->getHeaderLine('Content-Range');
            $size = 0;
            if (preg_match('/\/(\d+)$/', $contentRange, $matches)) {
                $size = (int) $matches[1];
            } elseif ($response->hasHeader('Content-Length')) {
                $size = (int) $response->getHeaderLine('Content-Length');
            }

            return [
                'id' => $fileId,
                'name' => "video_{$fileId}.mp4",
                'size' => $size,
                'mimeType' => 'video/mp4',
            ];
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Direct metadata fallback failed', ['error' => $e->getMessage()]);

            return [
                'id' => $fileId,
                'name' => "video_{$fileId}.mp4",
                'size' => 0,
                'mimeType' => 'video/mp4',
            ];
        }
    }

    /**
     * Menghasilkan Access Token dari file Service Account JSON (JWT Assertion).
     */
    protected function getAccessTokenFromServiceAccount(string $jsonPath): ?string
    {
        try {
            $json = json_decode(file_contents($jsonPath), true);
            if (! isset($json['private_key'], $json['client_email'])) {
                return null;
            }

            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claim = base64_encode(json_encode([
                'iss' => $json['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive.readonly',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signatureInput = "{$header}.{$claim}";
            openssl_sign($signatureInput, $signature, $json['private_key'], OPENSSL_ALGO_SHA256);
            $jwt = $signatureInput.'.'.base64_encode($signature);

            $response = $this->httpClient->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Service account token error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Fallback pencarian subfolder anime di dalam public Google Drive folder (tanpa OAuth).
     *
     * @return array{id: string, name: string}|null
     */
    public function scrapePublicSubfolders(string $parentFolderId, string $animeTitle): ?array
    {
        try {
            $url = "https://drive.google.com/drive/folders/{$parentFolderId}";
            $response = $this->httpClient->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ],
                'timeout' => 15,
            ]);
            $html = (string) $response->getBody();

            preg_match_all('/data-id="([a-zA-Z0-9_-]{25,45})"[^>]*>.*?<strong[^>]*>([^<]+)<\/strong>/s', $html, $matches);

            $folders = [];
            for ($i = 0; $i < count($matches[0]); $i++) {
                $folders[] = [
                    'id' => $matches[1][$i],
                    'name' => trim(html_entity_decode($matches[2][$i])),
                ];
            }

            // 1. Direct or contains match
            $cleanTitle = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $animeTitle)));
            foreach ($folders as $folder) {
                $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $folder['name'])));
                if (str_contains($cleanName, $cleanTitle) || str_contains($cleanTitle, $cleanName)) {
                    return $folder;
                }
            }

            // 2. Keyword match (kata >= 4 karakter, misal "Frieren")
            $words = array_values(array_filter(explode(' ', $cleanTitle), fn ($w) => strlen($w) >= 4));
            foreach ($words as $word) {
                foreach ($folders as $folder) {
                    $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $folder['name'])));
                    if (str_contains($cleanName, $word)) {
                        return $folder;
                    }
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Failed to scrape public subfolders', [
                'error' => $e->getMessage(),
                'parent' => $parentFolderId,
            ]);

            return null;
        }
    }

    /**
     * Fallback scanning file video di dalam public Google Drive folder (tanpa OAuth).
     *
     * @return array<int, array{id: string, name: string, size: int, mimeType: string}>
     */
    public function scrapePublicFolderFiles(string $folderId, string $searchTerm = ''): array
    {
        try {
            $url = "https://drive.google.com/drive/folders/{$folderId}";
            $response = $this->httpClient->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ],
                'timeout' => 15,
            ]);
            $html = (string) $response->getBody();

            $files = [];

            // Pattern 1: data-id with <strong>filename</strong>
            if (preg_match_all('/data-id="([a-zA-Z0-9_-]{25,45})"[^>]*>.*?<strong[^>]*>([^<]+\.(?:mkv|mp4|webm))<\/strong>/si', $html, $m)) {
                for ($i = 0; $i < count($m[0]); $i++) {
                    $files[] = [
                        'id' => $m[1][$i],
                        'name' => trim(html_entity_decode($m[2][$i])),
                        'size' => 0,
                        'mimeType' => str_ends_with(strtolower($m[2][$i]), '.mkv') ? 'video/x-matroska' : 'video/mp4',
                    ];
                }
            }

            // Pattern 2: Serialized JS array in HTML
            if (empty($files) && preg_match_all('/"([a-zA-Z0-9_-]{28,45})",\["[^"]*"\],"([^"]+\.(?:mkv|mp4|webm))"/i', $html, $m2)) {
                for ($i = 0; $i < count($m2[0]); $i++) {
                    $files[] = [
                        'id' => $m2[1][$i],
                        'name' => trim($m2[2][$i]),
                        'size' => 0,
                        'mimeType' => str_ends_with(strtolower($m2[2][$i]), '.mkv') ? 'video/x-matroska' : 'video/mp4',
                    ];
                }
            }

            if (! empty($searchTerm)) {
                $term = strtolower($searchTerm);
                $files = array_values(array_filter($files, fn ($f) => str_contains(strtolower($f['name']), $term)));
            }

            return $files;
        } catch (\Throwable $e) {
            Log::warning('GoogleDriveClientService: Failed to scrape public folder files', [
                'error' => $e->getMessage(),
                'folder' => $folderId,
            ]);

            return [];
        }
    }
}
