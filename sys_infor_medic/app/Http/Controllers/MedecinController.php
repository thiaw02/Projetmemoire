<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultations;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Ordonnances;
use App\Models\Rendez_vous;

class MedecinController extends Controller
{
    // Dashboard du médecin
    public function dashboard()
    {
        // Médecin connecté
        $medecin = Auth::user();
        $medecinId = $medecin->id;

        // RDV confirmés à venir pour ce médecin
        $upcomingRdv = Rendez_vous::with(['patient','medecin'])
            ->where('medecin_id', $medecinId)
            ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee'])
            ->whereDate('date','>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('heure')
            ->take(10)
            ->get();

        // 5 derniers patients consultés par ce médecin
        $dossiersRecents = Patient::whereHas('user', function($q) use ($medecinId) {
                                $q->whereHas('consultations', function($q2) use ($medecinId) {
                                    $q2->where('medecin_id', $medecinId);
                                });
                            })
                            ->latest()
                            ->take(5)
                            ->get();

        // Statistiques rapides
        $stats = [
            'aConsulter' => Rendez_vous::where('medecin_id',$medecinId)
                ->whereIn('statut',["confirmé","confirme","confirmée","confirmee"])
                ->whereDate('date','>=', now()->toDateString())
                ->count(),
            'rdvEnAttente' => Rendez_vous::where('medecin_id',$medecinId)
                ->whereIn('statut',["en_attente","en attente","pending"])
                ->count(),
            'consultesCeMois' => Consultations::where('medecin_id',$medecinId)
                ->whereYear('date_consultation', now()->year)
                ->whereMonth('date_consultation', now()->month)
                ->count(),
        ];

        return view('medecin.dashboard', compact('upcomingRdv', 'dossiersRecents', 'medecin','stats'));
    }

    // Page consultations
    public function consultations()
    {
        $medecin = Auth::user();

        // Consultations à venir pour ce médecin
        $consultations = Consultations::where('medecin_id', $medecin->id)
                            ->where('date_consultation', '>=', now())
                            ->orderBy('date_consultation', 'asc')
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
    public function dossierpatient(Request $request)
{
    $medecin_id = Auth::id();

    // Patients ayant au moins une consultation avec ce médecin OU un RDV (assigné à ce médecin)
    $patients = Patient::where(function($query) use ($medecin_id){
        $query->whereHas('consultations', function($q) use ($medecin_id) {
            $q->where('medecin_id', $medecin_id);
        })
        ->orWhereHas('rendez_vous', function($q) use ($medecin_id){
            $q->where('medecin_id', $medecin_id);
        });
    });

    if ($request->filled('patient_id')) {
        $patients->where('id', (int)$request->patient_id);
    }

    $patients = $patients->orderBy('nom')->get();

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
