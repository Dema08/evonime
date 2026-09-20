<?php

namespace App\Providers;

use App\Services\Content\ContentAggregatorService;
use App\Services\Content\ContentProviderInterface;
use App\Services\Content\OtakudesuProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ContentProviderInterface::class . '.otakudesu', function ($app) {
            return new OtakudesuProvider(config('services.otakudesu.url', 'http://localhost:8080'));
        });

        $this->app->singleton(ContentAggregatorService::class, function ($app) {
            return new ContentAggregatorService(
                $app->make(ContentProviderInterface::class . '.otakudesu')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by((string) $request->user()->id)
                : Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip().'|'.$request->input('email')),
                Limit::perMinute(20)->by($request->ip()), // global per IP
            ];
        });

        RateLimiter::for('progress', function (Request $request) {
            return Limit::perMinute(30)->by((string) ($request->user()?->id ?? $request->ip()));
        });
    }
}