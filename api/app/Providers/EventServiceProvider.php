<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    
    protected $listen = [
        // Ajoute ici tes événements personnalisés si besoin
    ];

    
    public function boot(): void
    {
        //
    }
}
