<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function index()
    {
        $checks = [
            'database' => false,
            'storage' => is_writable(storage_path()),
            'bootstrap_cache' => is_writable(base_path('bootstrap/cache')),
            'redis' => false,
            'app_key' => !empty(config('app.key')),
        ];

        try {
            DB::connection()->getPdo();
            $checks['database'] = true;
        } catch (\Throwable $e) {
            $checks['database'] = false;
        }

        if (config('cache.default') === 'redis') {
            try {
                $redis = new \Redis();
                $redis->connect(
                    config('database.redis.default.host', '127.0.0.1'),
                    config('database.redis.default.port', 6379),
                    1
                );
                $redis->ping();
                $checks['redis'] = true;
            } catch (\Throwable $e) {
                $checks['redis'] = false;
            }
        } else {
            $checks['redis'] = 'fallback';
        }

        return view('admin.health', compact('checks'));
    }

    public function clearCache()
    {
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');

        return back()->with('success', 'Semua cache berhasil dibersihkan.');
    }

    public function migrate()
    {
        Artisan::call('migrate', ['--force' => true]);

        return back()->with('success', 'Database migration berhasil dijalankan.');
    }
}
