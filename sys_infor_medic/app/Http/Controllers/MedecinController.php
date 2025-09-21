<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultations;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Ordonnances;

class MedecinController extends Controller
{
    // Dashboard du médecin
    public function dashboard()
    {
        // Médecin connecté
        $medecin = Auth::user();

        // 5 prochaines consultations
        $consultations = Consultations::where('medecin_id', $medecin->id)
                            ->where('date_consultation', '>=', now())
                            ->orderBy('date_consultation', 'asc')
                            ->take(5)
                            ->get();

        // 5 derniers patients consultés par ce médecin
        $dossiersRecents = Patient::whereHas('user', function($q) use ($medecin) {
                                $q->whereHas('consultations', function($q2) use ($medecin) {
                                    $q2->where('medecin_id', $medecin->id);
                                });
                            })
                            ->latest()
                            ->take(5)
                            ->get();

        return view('medecin.dashboard', compact('consultations', 'dossiersRecents', 'medecin'));
    }

    // Page consultations
    public function consultations()
    {
        $medecin = Auth::user();

        // Toutes les consultations du médecin
        $consultations = Consultations::where('medecin_id', $medecin->id)
                            ->orderBy('date_consultation', 'desc')
                            ->get();

        // Tous les patients pour le formulaire
        $patients = Patient::all();

        return view('medecin.consultations', compact('consultations', 'patients'));
    }

    // Ajouter une nouvelle consultation
    public function storeConsultation(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
        ]);

        Consultations::create([
            'medecin_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'date_consultation' => $request->date_consultation,
            'symptomes' => $request->symptomes,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'statut' => 'En attente',
        ]);

        return redirect()->back()->with('success', 'Consultation ajoutée avec succès.');
    }

    // Page dossiers patients
    public function dossierpatient()
{
    $medecin_id = Auth::id();

    // Récupérer les patients ayant au moins une consultation avec ce médecin
    $patients = Patient::whereHas('consultations', function($q) use ($medecin_id) {
        $q->where('medecin_id', $medecin_id);
    })->get();

    return view('medecin.dossierpatient', compact('patients'));
}


    // Page ordonnances
    public function ordonnances()
{
    $medecin_id = Auth::id();

    // Patients pour le formulaire
    $patients = Patient::whereHas('consultations', function($q) use ($medecin_id) {
        $q->where('medecin_id', $medecin_id);
    })->get();

    // Ordonnances du médecin
    $ordonnances = Ordonnances::where('medecin_id', $medecin_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

    return view('medecin.ordonnances', compact('patients', 'ordonnances'));
}

public function storeOrdonnance(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'medicaments' => 'required|string',
    ]);

    Ordonnances::create([
        'medecin_id' => Auth::id(),
        'patient_id' => $request->patient_id,
        'medicaments' => $request->medicaments,
    ]);

    return redirect()->back()->with('success', "Ordonnance ajoutée avec succès.");
}

}
