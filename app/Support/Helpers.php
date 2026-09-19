<?php

namespace App\Support;

class Helpers
{
    public static function cdn(string $path): string
    {
        $cdn = rtrim((string) config('stream.cdn_url'), '/');
        if ($cdn !== '') {
            return $cdn.'/'.ltrim($path, '/');
        }

        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }
}
