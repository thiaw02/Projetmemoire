<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Dossier_medicaux;
use App\Models\User;
use App\Models\Ordonnances;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // Dashboard du patient
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $patient = $user->patient;
        if (!$patient) {
            return redirect()->route('home')->with('error', 'Aucun dossier patient trouvé pour cet utilisateur.');
        }

        $patientId = $patient->id;
        $ordonnances = $patient->ordonnances ?? collect();
        $analyses = $patient->analyses ?? collect();

        $consultations = Consultations::with(['ordonnances', 'analyses', 'medecin'])
                                      ->where('patient_id', $patientId)
                                      ->orderBy('date_consultation', 'desc')
                                      ->get();

        $rendezVous = $patient->rendez_vous()->with('medecin')->get();
        $medecins = User::where('role', 'medecin')->get();

        return view('patient.dashboard', compact(
            'user', 'patient', 'consultations', 'rendezVous', 'medecins', 'ordonnances', 'analyses'
        ));
    }

    // Liste des rendez-vous du patient
    public function rendezvous()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $patient = $user->patient;
        if (!$patient) {
            return redirect()->route('home')->with('error', 'Aucun dossier patient trouvé pour cet utilisateur.');
        }

        $rendezVous = $patient->rendez_vous()->with('medecin')->get();

        return view('patient.rendezvous', compact('rendezVous'));
    }

    // Affichage des dossiers médicaux
    public function dossierMedical()
    {
        $user = Auth::user();
        $patient = $user ? $user->patient : null;

        // Récupère les dossiers médicaux associés au patient
        $dossiers = $patient ? $patient->dossiers : collect();

        return view('patient.dossiermedical', compact('patient', 'dossiers'));
    }

    // Création d'un rendez-vous
    public function storeRendez(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'heure' => 'required',
            'motif' => 'required|string|max:255',
            'medecin_id' => 'required|integer',
        ]);

        Rendez_vous::create([
            'user_id' => Auth::id(),
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => 'en attente',
        ]);

        return redirect()->route('rendez.create')->with('success', 'Rendez-vous enregistré avec succès ✅');
    }

    // Affichage des détails d'un patient
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.show', compact('patient'));
    }
}
