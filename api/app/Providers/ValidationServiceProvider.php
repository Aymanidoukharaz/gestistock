<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Factory;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('validator', function ($app) {
            return $app->make(Factory::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ceci est juste pour s'assurer que la façade Validator est disponible
        Validator::extend('test', function () {
            return true;
        });
    }
}
