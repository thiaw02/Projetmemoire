<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suivi;

class SuiviController extends Controller
{
    // Affiche le formulaire pour créer un suivi
    public function create()
    {
        return view('suivi.create');
    }

    // Enregistre un suivi dans la base
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer',
            'temperature' => 'required|numeric',
            'tension' => 'required|string|max:10',
        ]);

        Suivi::create([
            'patient_id' => $request->patient_id,
            'temperature' => $request->temperature,
            'tension' => $request->tension,
        ]);

        return redirect()->route('suivi.index')->with('success', 'Suivi enregistré avec succès ✅');
    }

    // Affiche tous les suivis
    public function index()
    {
        $suivis = Suivi::with('patient')->orderBy('created_at', 'desc')->get();

        return view('suivi.index', compact('suivis'));
    }
}
