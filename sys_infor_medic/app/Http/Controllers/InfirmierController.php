<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suivi;
use App\Models\Dossier;
use App\Models\Rendez_vous;

class InfirmierController extends Controller
{
    public function dashboard()
    {
        // Charger les suivis récents
        $suivis = Suivi::with('patient')->latest()->take(5)->get();

        // Charger les dossiers en attente
        $dossiers = Dossier::with('patient')
            ->latest()
            ->take(5)
            ->get();

        // Prochains rendez-vous (tous services)
        $upcomingRdv = Rendez_vous::with(['patient.user', 'medecin'])
            ->whereDate('date', '>=', now()->toDateString())
            ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','en_attente','en attente','pending'])
            ->orderBy('date')
            ->orderBy('heure')
            ->take(10)
            ->get();

        // ⚡️ Envoi des variables à la vue
        return view('infirmier.dashboard', compact('suivis', 'dossiers', 'upcomingRdv'));
    }


    public function dossiers()
    {
        return view('infirmier.dossiers');
    }
}
