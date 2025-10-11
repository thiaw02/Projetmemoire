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

    if (!$patient) {
        abort(404, 'Profil patient non trouvé');
    }

    $patientId = $patient->id;
    
    // Cache des données patient pour 5 minutes
    $cacheKey = 'patient_dashboard_' . $patientId . '_' . now()->format('Y-m-d-H-i');
    
    $data = \Cache::remember($cacheKey, 300, function() use ($patient, $patientId, $user) {
        // Requêtes optimisées avec limites
        $ordonnances = $patient->ordonnances()
            ->with('medecin:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $analyses = $patient->analyses()
            ->orderBy('date_analyse', 'desc')
            ->limit(10)
            ->get();

        // Consultations avec relations optimisées
        $consultations = Consultations::with([
                'ordonnances' => fn($q) => $q->limit(5),
                'analyses' => fn($q) => $q->limit(5),
                'medecin:id,name'
            ])
            ->where('patient_id', $patientId)
            ->orderBy('date_consultation', 'desc')
            ->limit(15)
            ->get();

        // Rendez-vous avec limitation
        $rendezVous = $patient->rendez_vous()
            ->with('medecin:id,name')
            ->orderBy('date', 'desc')
            ->orderBy('heure', 'desc')
            ->limit(20)
            ->get();

        // Prochain rendez-vous
        $nextRdv = \App\Models\Rendez_vous::with('medecin:id,name')
            ->where('user_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('heure', 'asc')
            ->first();

        // Statistiques en une requête
        $stats = [
            'totalConsultations' => $consultations->count(),
            'rdvEnAttente' => $rendezVous->whereIn('statut', ["en_attente", "pending"])->count(),
        ];
        
        return compact('ordonnances', 'analyses', 'consultations', 'rendezVous', 'nextRdv', 'stats');
    });
    
    extract($data);

    // Médecins avec cache séparé (changé rarement)
    $medecins = \Cache::remember('medecins_list', 3600, function() {
        return User::where('role', 'medecin')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    });

    // Charger les préférences utilisateur
    $preferences = $this->loadUserPreferences();
    
    // Passe toutes les variables à la vue
    return view('patient.dashboard', compact('user','patient','consultations','rendezVous','medecins','ordonnances','analyses','nextRdv','stats','preferences'));
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
    
    /**
     * Charger les préférences utilisateur
     */
    private function loadUserPreferences()
    {
        $user = Auth::user();
        $preferencesPath = "preferences/patient_{$user->id}.json";
        
        if (\Storage::exists($preferencesPath)) {
            $preferences = json_decode(\Storage::get($preferencesPath), true);
            return array_merge($this->getDefaultPreferences(), $preferences);
        }
        
        return $this->getDefaultPreferences();
    }
    
    /**
     * Obtenir les préférences par défaut
     */
    private function getDefaultPreferences()
    {
        return [
            'theme_color' => 'blue',
            'card_style' => 'modern',
            'animation_speed' => 'normal',
            'compact_mode' => false,
            'dark_mode' => false,
            'show_health_score' => true,
            'show_statistics' => true,
            'default_tab' => 'rdv'
        ];
    }
}
