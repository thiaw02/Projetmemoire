<?php

namespace App\Http\Controllers;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard() {
    $user = Auth::user();
    $patient = $user->patient;

    $patientId = $patient->id;
    $ordonnances = $patient->ordonnances ?? collect();
    $analyses = $patient->analyses ?? collect();


    // Historique des consultations avec ordonnances, analyses et médecin
    $consultations = Consultations::with(['ordonnances', 'analyses', 'medecin'])
                                  ->where('patient_id', $patientId)
                                  ->orderBy('date_consultation', 'desc')
                                  ->get();

    // Rendez-vous à venir
    $rendezVous = $patient->rendez_vous()->with('medecin')->get();

    // Récupérer la liste des médecins pour le formulaire
    $medecins = User::where('role', 'medecin')->get();

    // Passe toutes les variables à la vue
    return view('patient.dashboard', compact('user', 'patient', 'consultations', 'rendezVous','medecins','ordonnances',
        'analyses'));
}


    public function rendezvous()
    {
       // return view('patient.rendezvous');
         // Récupérer les rendez-vous du patient connecté
        $rendezVous = Rendez_vous::where('user_id', Auth::id())->get();

        // Envoyer à la vue
       return view('patient.rendezvous', compact('rendezVous'));

    }

    public function dossier()
    {
        return view('patient.dossiermedical');
    }
    public function storeRendez(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'heure' => 'required',
            'motif' => 'required|string|max:255',
            'medecin_id' => 'required|integer', // ⚡ Choix du médecin
        ]);

        Rendez_vous::create([
            'user_id' => Auth::id(),   // Patient connecté
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => 'en attente',  // statut par défaut
        ]);

        return redirect()->route('rendez.create')->with('success', 'Rendez-vous enregistré avec succès ✅');
    }
    public function show($id)
{
    $patient = Patient::findOrFail($id);
    return view('patients.show', compact('patient'));
}

}
