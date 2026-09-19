<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Streaming
    |--------------------------------------------------------------------------
    | STREAM_TOKEN_SECRET: kunci HMAC untuk signed URL /stream/{episode}?token=
    | STREAM_TOKEN_TTL: umur token dalam detik (default 7200 = 2 jam).
    | CDN_URL: opsional, prefix CDN di depan signed URL (kosong = pakai APP_URL).
    | STREAM_DISK: "local" atau "s3", menunjuk ke disk "streams" di filesystems.php.
    |
    */

    'token_secret' => env('STREAM_TOKEN_SECRET', env('APP_KEY')),
    'token_ttl' => (int) env('STREAM_TOKEN_TTL', 7200),
    'disk' => env('STREAM_DISK', 'local'),
    'cdn_url' => rtrim((string) env('CDN_URL', ''), '/'),

];
