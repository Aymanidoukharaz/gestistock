<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\CookieJar;

class CookieServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('cookie', function ($app) {
            return new CookieJar();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
