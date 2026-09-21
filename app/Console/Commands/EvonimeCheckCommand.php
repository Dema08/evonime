<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EvonimeCheckCommand extends Command
{
    protected $signature = 'evonime:check';
    protected $description = 'Check application environment, database, storage, cache, and configuration.';

    public function handle(): int
    {
        $allOk = true;

        // 1. Database connection
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: OK');
        } catch (\Throwable $e) {
            $this->error('❌ Database connection: FAILED (' . $e->getMessage() . ')');
            $allOk = false;
        }

        // 2. Storage writable
        $storagePath = storage_path();
        if (is_writable($storagePath)) {
            $this->info('✅ Storage writable: OK');
        } else {
            $this->error('❌ Storage writable: FAILED (' . $storagePath . ' is not writable)');
            $allOk = false;
        }

        // 3. Bootstrap cache writable
        $bootstrapCachePath = base_path('bootstrap/cache');
        if (is_writable($bootstrapCachePath)) {
            $this->info('✅ Bootstrap cache writable: OK');
        } else {
            $this->error('❌ Bootstrap cache writable: FAILED (' . $bootstrapCachePath . ' is not writable)');
            $allOk = false;
        }

        // 4. Redis or fallback
        $cacheDefault = config('cache.default');
        if ($cacheDefault === 'redis') {
            try {
                $redis = new \Redis();
                $redis->connect(
                    config('database.redis.default.host', '127.0.0.1'),
                    config('database.redis.default.port', 6379),
                    1
                );
                $redis->ping();
                $this->info('✅ Redis: aktif & terhubung');
            } catch (\Throwable $e) {
                $this->warn('⚠️  Redis: tidak aktif, fallback ke file');
            }
        } else {
            $this->warn('⚠️  Redis: tidak aktif, fallback ke file');
        }

        // 5. APP_KEY
        $appKey = config('app.key');
        if (!empty($appKey)) {
            $this->info('✅ APP_KEY: set');
        } else {
            $this->error('❌ APP_KEY: belum diset (jalankan php artisan key:generate)');
            $allOk = false;
        }

        if ($allOk) {
            $this->info('✅ Semua OK. Project siap dijalankan.');
            return self::SUCCESS;
        } else {
            $this->error('❌ Ada beberapa pemeriksaan yang gagal. Silakan periksa error di atas.');
            return self::FAILURE;
        }
    }
}
