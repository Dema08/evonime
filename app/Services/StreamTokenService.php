<?php

namespace App\Services;

class StreamTokenService
{
    /**
     * Buat token HMAC untuk episode. Format: expires.signature (sha256 HMAC).
     */
    public function issue(int $episodeId, int $userId = 0): array
    {
        $expires = time() + config('stream.token_ttl');
        $payload = "{$episodeId}.{$userId}.{$expires}";
        $sig = hash_hmac('sha256', $payload, (string) config('stream.token_secret'));

        return ['expires' => $expires, 'token' => "{$expires}.{$sig}", 'payload' => $payload];
    }

    public function validate(int $episodeId, int $userId, string $token): bool
    {
        if (! str_contains($token, '.')) {
            return false;
        }
        [$expires, $sig] = explode('.', $token, 2);
        if ((int) $expires < time()) {
            return false;
        }
        $expected = hash_hmac('sha256', "{$episodeId}.{$userId}.{$expires}", (string) config('stream.token_secret'));

        return hash_equals($expected, $sig);
    }

    public function signedUrl(int $episodeId, int $userId = 0): string
    {
        $issued = $this->issue($episodeId, $userId);
        $base = config('stream.cdn_url') ?: rtrim((string) config('app.url'), '/');

        return "{$base}/stream/{$episodeId}?uid={$userId}&expires={$issued['expires']}&token={$issued['token']}";
    }
}
