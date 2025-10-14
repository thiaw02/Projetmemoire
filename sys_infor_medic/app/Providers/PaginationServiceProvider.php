<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class PaginationServiceProvider extends ServiceProvider
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
        // Utiliser Bootstrap 5 pour la pagination par défaut
        Paginator::defaultView('pagination.custom');
        Paginator::defaultSimpleView('pagination.simple-custom');
        
        // Forcer l'utilisation de Bootstrap 5
        Paginator::useBootstrap();
    }
}