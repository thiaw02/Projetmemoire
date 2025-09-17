<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultations;

class HistoriqueController extends Controller
{
    public function index()
    {
        // Récupère toutes les consultations avec patient et médecin
        $consultations = Consultations::with(['patient', 'medecin'])
                                      ->orderBy('date_consultation', 'desc')
                                      ->get();

        return view('historique.index', compact('consultations'));
    }
}
