<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    private static bool $redisWarningLogged = false;

    public function register(): void
    {
        if (config('cache.default') === 'redis') {
            try {
                $redis = new \Redis();
                $redis->connect(
                    config('database.redis.default.host', '127.0.0.1'),
                    config('database.redis.default.port', 6379),
                    1 // timeout 1 detik
                );
                $redis->ping();
            } catch (\Throwable $e) {
                // Redis tidak bisa connect → fallback ke file
                config(['cache.default' => 'file']);
                config(['session.driver' => 'file']);
                config(['queue.default' => 'sync']);

                if (!self::$redisWarningLogged) {
                    \Log::warning('Redis tidak tersedia, fallback ke file/database.');
                    self::$redisWarningLogged = true;
                }
            }
        }
    }
}
