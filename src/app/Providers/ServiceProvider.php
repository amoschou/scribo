<?php

namespace AMoschou\Scribo\App\Providers;

use Illuminate\Contracts\Foundation\Application;
// use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            $this->path('config/public.php') => config_path('scribo.php'),
        ], 'scribo-config');

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom($this->path('routes/web.php'));
        });

        $this->loadViewsFrom($this->path('resources/views'), 'scribo');

        $this->publishes([
            $this->path('resources/views') => resource_path('views/vendor/amoschou/scribo'),
        ], 'scribo-views');

    }

    private function path($path): string
    {
        return __DIR__.'/../../' . $path;
    }
}
