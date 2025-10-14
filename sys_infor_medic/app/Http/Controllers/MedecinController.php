<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultations;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Models\Ordonnances;
use App\Models\Rendez_vous;
use App\Models\Ordonnances as OrdonnanceModel;
use App\Notifications\OrdonnanceCreatedNotification;
use App\Models\AuditLog;

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

        // Infirmiers affectés à ce médecin
        $medecin = \App\Models\User::with(['nurses' => function($q){ $q->select('users.id','users.name','users.pro_phone'); }])->find($medecinId);

        // Dossiers récents consultés par ce médecin (session)
        $sessionKey = 'recent_patients_' . $medecinId;
        $recentIds = session($sessionKey, []);
        $recentPatients = collect();
        if (!empty($recentIds)) {
            $patients = Patient::whereIn('id', $recentIds)->get()->keyBy('id');
            // Conserver l'ordre du plus récent au moins récent
            foreach ($recentIds as $pid) {
                if ($patients->has($pid)) {
                    $recentPatients->push($patients[$pid]);
                }
            }
        }

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

        return view('medecin.dashboard', [
            'upcomingRdv' => $upcomingRdv,
            'recentPatients' => $recentPatients,
            'medecin' => $medecin,
            'stats' => $stats,
        ]);
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

    // Editer consultation
    public function editConsultation(int $id)
    {
        $medId = Auth::id();
        $consult = \App\Models\Consultations::with('patient')->where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        return view('medecin.consultation_edit', compact('consult'));
    }

    public function updateConsultation(Request $request, int $id)
    {
        $medId = Auth::id();
$consult = \App\Models\Consultations::where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        $data = $request->validate([
            'date_consultation' => 'required|date',
            'symptomes' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
        ]);
        $before = array_intersect_key($consult->getOriginal(), $data);
        $consult->update($data);
        $after = array_intersect_key($consult->fresh()->toArray(), $data);
        AuditLog::create([
            'user_id' => $medId,
            'action' => 'consultation_updated',
            'event_type' => 'update',
            'severity' => 'medium',
            'auditable_type' => \App\Models\Consultations::class,
            'auditable_id' => $consult->id,
            'changes' => ['before' => $before, 'after' => $after],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('medecin.consultations')->with('success','Consultation mise à jour');
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

    // Afficher le dossier complet d'un patient (consultations, constantes, actes infirmiers, ordonnances, analyses)
    public function showPatient(int $patientId)
    {
        $medecinId = Auth::id();

        $patient = Patient::with([
            'suivis' => fn ($q) => $q->orderByDesc('created_at'),
            'consultations' => fn ($q) => $q->where('medecin_id', $medecinId)->orderByDesc('date_consultation'),
            'ordonnances' => fn ($q) => $q->orderByDesc('created_at'),
            'analyses' => fn ($q) => $q->orderByDesc('date_analyse'),
        ])->findOrFail($patientId);

        // Mettre à jour la liste des dossiers récents en session
        $sessionKey = 'recent_patients_' . $medecinId;
        $ids = session($sessionKey, []);
        // Enlever si déjà présent
        $ids = array_values(array_filter($ids, fn($id) => (int)$id !== (int)$patientId));
        // Ajouter en tête
        array_unshift($ids, (int)$patientId);
        // Limiter à 5 derniers
        $ids = array_slice($ids, 0, 5);
        session([$sessionKey => $ids]);

        return view('medecin.patient_show', [
            'patient' => $patient,
            'lastSuivi' => $patient->suivis->first(),
        ]);
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
            'dosage' => 'nullable|string|max:1000',
        ]);

        $ord = Ordonnances::create([
            'medecin_id' => Auth::id(),
            'patient_id' => $request->patient_id,
            'medicaments' => $request->medicaments,
            'contenu' => $request->medicaments,
            'dosage' => $request->dosage,
        ]);

        // Audit: création ordonnance
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'ordonnance_created',
            'event_type' => 'create',
            'severity' => 'medium',
            'auditable_type' => Ordonnances::class,
            'auditable_id' => $ord->id,
            'changes' => ['after' => $ord->only(['patient_id','medecin_id','medicaments','dosage'])],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Envoyer le PDF par email au patient
        $ord->load(['patient.user','medecin']);
        if ($ord->patient && $ord->patient->user) {
            $ord->patient->user->notify(new OrdonnanceCreatedNotification($ord));
        }

        return redirect()->back()->with('success', "Ordonnance ajoutée avec succès et envoyée par e-mail au patient.");
    }

    public function editOrdonnance(int $id)
    {
        $medId = Auth::id();
        $ord = OrdonnanceModel::where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        return view('medecin.ordonnance_edit', ['ordonnance' => $ord]);
    }

    public function updateOrdonnance(Request $request, int $id)
    {
        $medId = Auth::id();
        $ord = OrdonnanceModel::where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        $data = $request->validate([
            'medicaments' => 'required|string',
            'dosage' => 'nullable|string|max:1000',
        ]);
        $data['contenu'] = $data['medicaments'];
        $before = array_intersect_key($ord->getOriginal(), $data);
        $ord->update($data);
        $after = array_intersect_key($ord->fresh()->toArray(), $data);
        AuditLog::create([
            'user_id' => $medId,
            'action' => 'ordonnance_updated',
            'event_type' => 'update',
            'severity' => 'medium',
            'auditable_type' => OrdonnanceModel::class,
            'auditable_id' => $ord->id,
            'changes' => ['before' => $before, 'after' => $after],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        return redirect()->route('medecin.ordonnances')->with('success','Ordonnance mise à jour');
    }

    public function downloadOrdonnance(int $id)
    {
        $medId = Auth::id();
        $ord = OrdonnanceModel::with(['patient','medecin'])->where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        $data = [
            'ordonnance' => $ord,
            'patient' => $ord->patient,
            'medecin' => $ord->medecin,
            'generatedAt' => now(),
        ];
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ordonnances.pdf', $data);
            $filename = 'Ordonnance_'.$ord->patient->nom.'_'.$ord->patient->prenom.'_'.$ord->id.'.pdf';
            return $pdf->download($filename);
        }
        $html = view('ordonnances.pdf', $data)->render();
        $filename = 'Ordonnance_'.$ord->patient->nom.'_'.$ord->patient->prenom.'_'.$ord->id.'.html';
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function resendOrdonnance(int $id)
    {
        $medId = Auth::id();
        $ord = OrdonnanceModel::with(['patient.user','medecin'])->where('id',$id)->where('medecin_id',$medId)->firstOrFail();
        if ($ord->patient && $ord->patient->user) {
            $ord->patient->user->notify(new OrdonnanceCreatedNotification($ord));
        }
        return back()->with('success', 'Ordonnance renvoyée au patient par e-mail.');
    }

    public function markRdvConsulted(int $id)
    {
        $rdv = Rendez_vous::where('id', $id)
            ->where('medecin_id', Auth::id())
            ->firstOrFail();
        // La colonne statut est un ENUM ['en_attente','confirmé','annulé','terminé']
        $rdv->update(['statut' => 'terminé']);
        return back()->with('success', 'RDV marqué comme consulté (terminé).');
    }

    // Méthode pour rafraîchir les données patient en AJAX
    public function refreshPatientData(int $patientId)
    {
        $medecinId = Auth::id();

        $patient = Patient::with([
            'suivis' => fn ($q) => $q->orderByDesc('created_at'),
            'consultations' => fn ($q) => $q->where('medecin_id', $medecinId)->orderByDesc('date_consultation'),
            'ordonnances' => fn ($q) => $q->orderByDesc('created_at'),
            'analyses' => fn ($q) => $q->orderByDesc('date_analyse'),
        ])->findOrFail($patientId);

        return response()->json([
            'success' => true,
            'patient' => $patient->only(['id', 'nom', 'prenom', 'email', 'telephone']),
            'lastSuivi' => $patient->suivis->first(),
            'suivis' => $patient->suivis->take(10), // Limiter à 10 derniers suivis
            'consultations' => $patient->consultations->take(10), // Limiter à 10 dernières consultations
            'ordonnances' => $patient->ordonnances->take(10), // Limiter à 10 dernières ordonnances
            'analyses' => $patient->analyses->take(10), // Limiter à 10 dernières analyses
            'updated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
