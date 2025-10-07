<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias de middleware personnalisés
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Rappels RDV: demain
        $schedule->call(function() {
            $tomorrow = now()->addDay()->toDateString();
            $rdvs = \App\Models\Rendez_vous::with(['medecin'])
                ->whereDate('date', $tomorrow)
                ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','en_attente','pending'])
                ->get();
            foreach ($rdvs as $rdv) {
                $user = \App\Models\User::find($rdv->user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\RendezVousReminderNotification($rdv, 'tomorrow'));
                }
            }
        })->dailyAt('08:00');

        // Rappels RDV: aujourd'hui
        $schedule->call(function() {
            $today = now()->toDateString();
            $rdvs = \App\Models\Rendez_vous::with(['medecin'])
                ->whereDate('date', $today)
                ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','en_attente','pending'])
                ->get();
            foreach ($rdvs as $rdv) {
                $user = \App\Models\User::find($rdv->user_id);
                if ($user) {
                    $user->notify(new \App\Notifications\RendezVousReminderNotification($rdv, 'today'));
                }
            }
        })->dailyAt('07:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
