<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suivi;
use App\Models\Dossier;

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


        // ⚡️ Envoi des variables à la vue
        return view('infirmier.dashboard', compact('suivis', 'dossiers'));
    }


    public function dossiers()
    {
        return view('infirmier.dossiers');
    }
}
