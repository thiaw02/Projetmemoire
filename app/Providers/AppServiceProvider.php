<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
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
        // Configuration de la pagination personnalisée
        Paginator::defaultView('layouts.partials.pagination');
        Paginator::defaultSimpleView('layouts.partials.pagination');

        // Observer pour l'affectation automatique infirmier ↔ médecin par service
        User::observe(UserObserver::class);
    }
}
