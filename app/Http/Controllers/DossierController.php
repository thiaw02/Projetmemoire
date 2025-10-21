<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class DossierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dossiers = Dossier::with('patient')->orderByDesc('updated_at')->paginate(15);
        return view('dossier.index', compact('dossiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::orderBy('nom')->get();
        return view('dossier.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'statut' => 'required|string|max:50',
            'observation' => 'required|string|max:1000',
        ]);

        Dossier::create([
            'patient_id' => $request->patient_id,
            'statut' => $request->statut,
            'observation' => $request->observation,
        ]);

        return redirect()->route('dossier.index')
            ->with('success', 'Dossier créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dossier = Dossier::with('patient')->findOrFail($id);
        return view('dossier.show', compact('dossier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dossier = Dossier::findOrFail($id);
        $patients = Patient::orderBy('nom')->get();
        return view('dossier.edit', compact('dossier', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dossier = Dossier::findOrFail($id);
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'statut' => 'required|string|max:50',
            'observation' => 'required|string|max:1000',
        ]);

        $dossier->update([
            'patient_id' => $request->patient_id,
            'statut' => $request->statut,
            'observation' => $request->observation,
        ]);

        return redirect()->route('dossier.index')
            ->with('success', 'Dossier mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dossier = Dossier::findOrFail($id);
        $dossier->delete();

        return redirect()->route('dossier.index')
            ->with('success', 'Dossier supprimé avec succès.');
    }
}
