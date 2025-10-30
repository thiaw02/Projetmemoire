<?php

namespace App\Http\Controllers;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\User;
use App\Models\Ordonnances;
use App\Models\Service;
use App\Notifications\NewRendezvousRequest;
use App\Events\RendezVousCreated;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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

        // Prochain rendez-vous (propre au patient)
        $nextRdv = \App\Models\Rendez_vous::with('medecin:id,name')
            ->where('user_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('heure', 'asc')
            ->first();

        // RDV à venir dans les services du patient (vision service)
        $serviceIds = $patient->services()->pluck('services.id');
        $serviceUpcomingRdv = collect();
        if ($serviceIds && $serviceIds->count()) {
            $serviceUpcomingRdv = \App\Models\Rendez_vous::with(['medecin:id,name,service_id','patient'])
                ->whereDate('date','>=', now()->toDateString())
                ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','en_attente','en attente','pending'])
                ->whereHas('medecin', function($q) use ($serviceIds){ $q->whereIn('service_id', $serviceIds); })
                ->orderBy('date')
                ->orderBy('heure')
                ->limit(20)
                ->get();
        }

        // Statistiques en une requête
        $stats = [
            'totalConsultations' => $consultations->count(),
            'rdvEnAttente' => $rendezVous->whereIn('statut', ["en_attente", "pending"])->count(),
        ];
        
        return compact('ordonnances', 'analyses', 'consultations', 'rendezVous', 'nextRdv', 'serviceUpcomingRdv', 'stats');
    });
    
    extract($data);

    // Restreindre les médecins proposés aux services du patient
    $serviceIds = $patient->services()->pluck('services.id');
    $medecins = User::where('role', 'medecin')
        ->when($serviceIds && $serviceIds->count(), function($q) use ($serviceIds){
            $q->whereIn('service_id', $serviceIds);
        })
        ->select('id','name')
        ->orderBy('name')
        ->get();

    // Charger les préférences utilisateur
    $preferences = $this->loadUserPreferences();
    
    // Passe toutes les variables à la vue
    return view('patient.dashboard', compact('user','patient','consultations','rendezVous','medecins','ordonnances','analyses','nextRdv','serviceUpcomingRdv','stats','preferences'));
}


    public function rendezvous()
    {
        // Récupérer les rendez-vous du patient connecté (pagination)
        $rendezVous = Rendez_vous::where('user_id', Auth::id())
            ->orderByDesc('date')
            ->orderByDesc('heure')
            ->paginate(10, ['*'], 'patient_rdv_page')
            ->withQueryString();

        // Offrir tous les services actifs pour permettre une demande dans un autre service
        $user = Auth::user();
        $patient = $user?->patient;
        $services = Service::where('active', true)->orderBy('name')->get(['id','name']);

        return view('patient.rendezvous', compact('rendezVous','services'));
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

        $rendezVous = Rendez_vous::create([
            'user_id' => Auth::id(),   // Patient connecté
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => 'en_attente',  // statut par défaut (ENUM)
        ]);

        // Notifier les secrétaires du même service que le médecin par email
        try {
            $medecinServiceId = optional($rendezVous->medecin)->service_id;
            $secretaires = User::where('role', 'secretaire')
                ->where('active', true)
                ->when($medecinServiceId, function($q) use ($medecinServiceId){ $q->where('service_id', $medecinServiceId); })
                ->get();
            Notification::send($secretaires, new NewRendezvousRequest($rendezVous));
        } catch (\Throwable $e) {
            \Log::error('Erreur lors de l\'envoi des notifications secrétaires: ' . $e->getMessage());
        }

        // Déclencher l'event pour les notifications temps réel
        try {
            event(new RendezVousCreated($rendezVous));
        } catch (\Throwable $e) {
            \Log::error('Erreur lors de l\'événement temps réel: ' . $e->getMessage());
        }

        return redirect()->route('patient.dashboard')->with('success', 'Rendez-vous enregistré avec succès ✅ Les secrétaires ont été notifiées.');
    }

    public function createRendez()
    {
        // Rediriger vers le dashboard patient (onglet RDV gère déjà la création)
        return redirect()->route('patient.dashboard');
    }

    /**
     * Retourne les médecins d'un service en JSON (pour chargement dynamique côté patient)
     */
    public function getMedecinsByService(int $serviceId)
    {
        $medecins = User::where('role', 'medecin')
            ->where('service_id', $serviceId)
            ->where(function($q){ $q->whereNull('active')->orWhere('active', true); })
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json([
            'success' => true,
            'data' => $medecins,
        ]);
    }

    public function show($id)
{
    $patient = Patient::findOrFail($id);
    return view('patients.show', compact('patient'));
}

    public function cancelRendezVous($id)
    {
        $user = Auth::user();
        $rdv = Rendez_vous::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        
        // Vérifier que le rendez-vous peut être annulé
        if (!in_array(strtolower($rdv->statut), ['en_attente', 'en attente', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce rendez-vous ne peut pas être annulé.'
            ], 400);
        }
        
        $rdv->statut = 'annulé';
        $rdv->save();
        
        // Notifier les secrétaires du même service de l'annulation
        try {
            $medecinServiceId = optional($rdv->medecin)->service_id;
            $secretaires = User::where('role', 'secretaire')
                ->where('active', true)
                ->when($medecinServiceId, function($q) use ($medecinServiceId){ $q->where('service_id', $medecinServiceId); })
                ->get();
            foreach ($secretaires as $secretaire) {
                $secretaire->notify(new \App\Notifications\RendezvousStatusChanged($rdv));
            }
        } catch (\Throwable $e) {
            \Log::error('Erreur notification annulation RDV: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous annulé avec succès.'
        ]);
    }
    
    public function getRendezVousDetails($id)
    {
        $user = Auth::user();
        $rdv = Rendez_vous::with(['medecin', 'patient'])
                          ->where('user_id', $user->id)
                          ->where('id', $id)
                          ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rdv->id,
                'date' => \Carbon\Carbon::parse($rdv->date)->format('d/m/Y'),
                'heure' => $rdv->heure,
                'motif' => $rdv->motif,
                'statut' => $rdv->statut,
                'medecin' => $rdv->medecin->name ?? 'Non assigné',
                'created_at' => $rdv->created_at->format('d/m/Y à H:i')
            ]
        ]);
    }
    
    public function editRendezVous($id)
    {
        $user = Auth::user();
        $rdv = Rendez_vous::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        
        // Vérifier que le rendez-vous peut être modifié
        if (!in_array(strtolower($rdv->statut), ['en_attente', 'en attente', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce rendez-vous ne peut pas être modifié.'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rdv->id,
                'date' => $rdv->date,
                'heure' => $rdv->heure,
                'motif' => $rdv->motif,
                'medecin_id' => $rdv->medecin_id
            ]
        ]);
    }
    
    public function updateRendezVous(Request $request, $id)
    {
        $user = Auth::user();
        $rdv = Rendez_vous::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        
        // Vérifier que le rendez-vous peut être modifié
        if (!in_array(strtolower($rdv->statut), ['en_attente', 'en attente', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Ce rendez-vous ne peut pas être modifié.'
            ], 400);
        }
        
        $request->validate([
            'date' => 'required|date|after:today',
            'heure' => 'required',
            'motif' => 'required|string|max:255',
            'medecin_id' => 'required|exists:users,id'
        ]);
        
        $rdv->date = $request->date;
        $rdv->heure = $request->heure;
        $rdv->motif = $request->motif;
        $rdv->medecin_id = $request->medecin_id;
        $rdv->save();
        
        // Notifier les secrétaires du même service de la modification
        try {
            $medecinServiceId = optional($rdv->medecin)->service_id;
            $secretaires = User::where('role', 'secretaire')
                ->where('active', true)
                ->when($medecinServiceId, function($q) use ($medecinServiceId){ $q->where('service_id', $medecinServiceId); })
                ->get();
            foreach ($secretaires as $secretaire) {
                $secretaire->notify(new \App\Notifications\RendezvousStatusChanged($rdv));
            }
        } catch (\Throwable $e) {
            \Log::error('Erreur notification modification RDV: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous modifié avec succès.',
            'data' => [
                'date' => \Carbon\Carbon::parse($rdv->date)->format('d/m/Y'),
                'heure' => $rdv->heure,
                'motif' => $rdv->motif,
                'medecin' => $rdv->medecin->name ?? 'Non assigné'
            ]
        ]);
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
