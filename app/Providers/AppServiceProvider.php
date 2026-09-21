<?php

namespace App\Providers;

use App\Services\Content\ContentAggregatorService;
use App\Services\Content\OtakudesuProvider;
use App\Services\Content\OtakudesuScraper;
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
        // OtakudesuProvider butuh OtakudesuScraper (object), bukan string URL.
        // Laravel auto-inject via type-hint constructor, jadi singleton tanpa closure cukup.
        $this->app->singleton(OtakudesuScraper::class);
        $this->app->singleton(OtakudesuProvider::class);

        $this->app->singleton(ContentAggregatorService::class, function ($app) {
            return new ContentAggregatorService(
                $app->make(OtakudesuProvider::class)
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