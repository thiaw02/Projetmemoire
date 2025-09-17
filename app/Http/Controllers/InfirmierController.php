<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suivi;
use App\Models\Dossier_medicaux;

class InfirmierController extends Controller
{
    public function dashboard()
    {
        // Suivis récents
        $suivisEnCours = Suivi::with('patient')
                              ->orderBy('created_at', 'desc')
                              ->get();

        // Derniers dossiers médicaux
        $dossiers = Dossier_medicaux::with('patient')
                                    ->latest()
                                    ->take(5)
                                    ->get();

        // Dossiers à mettre à jour (exemple : observation vide)
        $dossiersAMettreAJour = $dossiers->filter(function($dossier){
            return empty($dossier->diagnostic); // ou autre condition selon ton besoin
        });

        // ⚡️ Envoi des variables à la vue
        return view('infirmier.dashboard', compact('suivisEnCours', 'dossiers', 'dossiersAMettreAJour'));
    }
}
