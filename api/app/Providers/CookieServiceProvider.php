<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\CookieJar;

class CookieServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->singleton('cookie', function ($app) {
            return new CookieJar();
        });
    }

    
    public function boot(): void
    {
        //
    }
}
