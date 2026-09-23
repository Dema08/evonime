<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Drive Video Storage & Streaming Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini mengatur koneksi Evonime ke Google Drive sebagai backend
    | penyimpanan video 1080p. Browser hanya mengakses localhost/stream/...,
    | sedangkan backend Evonime melakukan reverse proxy stream dengan Range Request.
    |
    */

    'enabled' => (bool) env('GOOGLE_DRIVE_ENABLED', true),

    // Mode Autentikasi: 'hybrid', 'oauth', 'service_account', atau 'public'
    'auth_type' => env('GOOGLE_DRIVE_AUTH_TYPE', env('GOOGLE_DRIVE_AUTH_MODE', 'hybrid')),

    // OAuth 2.0 Credentials (untuk akun shenriu44@gmail.com)
    'client_id' => env('GOOGLE_DRIVE_CLIENT_ID', ''),
    'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET', ''),
    'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN', ''),

    // Service Account JSON Path (opsional alternatif OAuth2)
    'service_account_json' => env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON', env('GOOGLE_DRIVE_CREDENTIALS', storage_path('app/google/service-account.json'))),

    // Google API Key (opsional)
    'api_key' => env('GOOGLE_DRIVE_API_KEY', ''),

    // Folder default di Google Drive (opsional)
    'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', ''),

    // Ukuran buffer chunk saat melakukan streaming pipe (default 128KB)
    'chunk_size' => (int) env('GOOGLE_DRIVE_CHUNK_SIZE', 131072),

    // Durasi cache metadata file (Content-Length, mime-type) dalam detik (default 24 jam)
    'metadata_cache_ttl' => (int) env('GOOGLE_DRIVE_METADATA_TTL', 86400),

    // Durasi cache token akses OAuth dalam detik (default 55 menit = 3300 detik)
    'token_cache_ttl' => (int) env('GOOGLE_DRIVE_TOKEN_TTL', 3300),

];
