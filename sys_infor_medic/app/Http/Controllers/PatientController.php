<?php

namespace App\Http\Controllers;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\User;
use App\Models\Ordonnances;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard() {
    $user = Auth::user();
    $patient = $user->patient;

    $patientId = $patient?->id;
    $ordonnances = $patient?->ordonnances ?? collect();
    $analyses = $patient?->analyses ?? collect();


    // Historique des consultations avec ordonnances, analyses et médecin
    $consultations = Consultations::with(['ordonnances', 'analyses', 'medecin'])
                                  ->where('patient_id', $patientId)
                                  ->orderBy('date_consultation', 'desc')
                                  ->get();

    // Rendez-vous (liste complète pour l'onglet Mes rendez-vous)
    $rendezVous = $patient ? $patient->rendez_vous()->with('medecin')->orderBy('date','desc')->orderBy('heure','desc')->get() : collect();

    // Prochain rendez-vous (à venir)
    $nextRdv = \App\Models\Rendez_vous::with('medecin')
        ->where('user_id', $user->id)
        ->whereDate('date', '>=', now()->toDateString())
        ->orderBy('date', 'asc')->orderBy('heure','asc')
        ->first();

    // Statistiques simples côté patient
    $stats = [
        'totalConsultations' => Consultations::where('patient_id', $patientId)->count(),
'rdvEnAttente' => \App\Models\Rendez_vous::where('user_id', $user->id)->whereIn('statut',["en_attente","pending"])->count(),
    ];

    // Récupérer la liste des médecins pour le formulaire
    $medecins = User::where('role', 'medecin')->orderBy('name')->get();

    // Passe toutes les variables à la vue
    return view('patient.dashboard', compact('user','patient','consultations','rendezVous','medecins','ordonnances','analyses','nextRdv','stats'));
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
            'statut' => 'en_attente',  // statut par défaut (ENUM)
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Rendez-vous enregistré avec succès ✅');
    }

    public function createRendez()
    {
        // Rediriger vers le dashboard patient (onglet RDV gère déjà la création)
        return redirect()->route('patient.dashboard');
    }

    public function show($id)
{
    $patient = Patient::findOrFail($id);
    return view('patients.show', compact('patient'));
}

    public function downloadOrdonnance(int $id)
    {
        $user = Auth::user();
        $patient = $user->patient;
        abort_unless($patient, 403);

        $ordonnance = Ordonnances::with(['patient','medecin'])->findOrFail($id);
        // Sécurité: le patient ne peut télécharger que ses propres ordonnances
        abort_unless($ordonnance->patient_id === $patient->id, 403);

        // Données pour la vue
        $data = [
            'ordonnance' => $ordonnance,
            'patient' => $patient,
            'medecin' => $ordonnance->medecin,
            'generatedAt' => now(),
        ];

        // Si dompdf est disponible, générer un PDF. Sinon, proposer un HTML téléchargeable.
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ordonnances.pdf', $data);
            $filename = 'Ordonnance_'.$patient->nom.'_'.$patient->prenom.'_'.$ordonnance->id.'.pdf';
            return $pdf->download($filename);
        }

        // Fallback: fichier HTML téléchargeable
        $html = view('ordonnances.pdf', $data)->render();
        $filename = 'Ordonnance_'.$patient->nom.'_'.$patient->prenom.'_'.$ordonnance->id.'.html';
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function resendOrdonnance(int $id)
    {
        $user = Auth::user();
        $patient = $user->patient;
        abort_unless($patient, 403);
        $ord = Ordonnances::with(['patient.user','medecin'])->findOrFail($id);
        abort_unless($ord->patient_id === $patient->id, 403);
        $user->notify(new \App\Notifications\OrdonnanceCreatedNotification($ord));
        return back()->with('success', 'Ordonnance renvoyée sur votre e-mail.');
    }
}
