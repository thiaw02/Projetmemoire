<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Suivis/dossiers retirés du dashboard infirmier
use App\Models\Suivi;
use App\Models\Rendez_vous;
use App\Models\Patient;
use App\Models\Dossier_medicaux;
use Illuminate\Support\Facades\Auth;

class InfirmierController extends Controller
{
    public function dashboard()
    {
        // Infirmier connecté et service
        $infirmier = auth()->user();
        $serviceId = $infirmier->service_id;

        // Suivis et dossiers retirés de la vue: ne pas charger ici

        // Prochains rendez-vous du même service (infirmier et médecins partagent le service)
        $upcomingRdv = Rendez_vous::with(['patient.user', 'medecin'])
            ->whereDate('date', '>=', now()->toDateString())
            ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee'])
            ->whereHas('medecin', function($q) use ($serviceId){
                $q->where('service_id', $serviceId);
            })
            ->orderBy('date')
            ->orderBy('heure')
            ->paginate(10, ['*'], 'infirmier_rdv_page')
            ->withQueryString();

        // Compteurs de statuts RDV (dans le service)
        $baseRdv = Rendez_vous::whereHas('medecin', function($q) use ($serviceId){ $q->where('service_id', $serviceId); });
        $rdvCounts = [
            'attente' => (clone $baseRdv)->whereIn('statut', ['en_attente','en attente','pending'])->count(),
            'cours'   => (clone $baseRdv)->whereIn('statut', ['confirmé','confirme','confirmée','confirmee'])->count(),
            'traites' => (clone $baseRdv)->whereIn('statut', ['terminé','termine','completed'])->count(),
        ];

        // Médecins associés automatiquement: même service
        $infirmier->load(['service']);
        $associatedDoctors = \App\Models\User::where('role','medecin')
            ->where('service_id', $serviceId)
            ->select('id','name','specialite')
            ->get();

        // ⚡️ Envoi des variables à la vue
        return view('infirmier.dashboard', [
            'upcomingRdv' => $upcomingRdv,
            'infirmier' => $infirmier,
            'associatedDoctors' => $associatedDoctors,
            'rdvCounts' => $rdvCounts,
        ]);
    }


    public function dossiers()
    {
        return view('infirmier.dossiers');
    }

    // Page de création d'un suivi (constantes initiales)
    public function createSuivi(\Illuminate\Http\Request $request)
    {
        $serviceId = auth()->user()->service_id;
        $patients = Patient::whereHas('services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })
            ->with('user')
            ->orderBy('nom')
            ->get();
        $preselect = (int)($request->query('patient_id', 0));
        $selectedPatient = $preselect ? Patient::with('user')->find($preselect) : null;
        $rdvId = (int)($request->query('rdv_id', 0));
        return view('infirmier.suivis.create', compact('patients','preselect','selectedPatient','rdvId'));
    }

    // Enregistrement du suivi
    public function storeSuivi(\Illuminate\Http\Request $request)
    {
        $serviceId = auth()->user()->service_id;
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'temperature' => 'nullable|numeric',
            'tension' => 'nullable|string|max:50',
            'date_suivi' => 'nullable|date',
            'rdv_id' => 'nullable|integer',
        ]);
        // Vérifier que le patient est dans le service
        $ok = Patient::where('id',$data['patient_id'])->whereHas('services', function($q) use ($serviceId){ $q->where('services.id',$serviceId); })->exists();
        if (!$ok) { return back()->with('error','Patient hors de votre service.'); }
        if (empty($data['date_suivi'])) { $data['date_suivi'] = now(); }
        $suivi = Suivi::create($data);

        // Créer une entrée dans le dossier médical avec les constantes
        $diagnostic = 'Constantes initiales';
        $details = [];
        if (!empty($data['temperature'])) { $details[] = 'Température: '.$data['temperature'].'°C'; }
        if (!empty($data['tension'])) { $details[] = 'Tension: '.$data['tension']; }
        if ($details) { $diagnostic .= ' — '.implode(', ', $details); }

        Dossier_medicaux::create([
            'patient_id' => $data['patient_id'],
            'diagnostic' => $diagnostic,
            'traitement' => null,
            'date_consultation' => $data['date_suivi'],
        ]);

        // Si un RDV est fourni et cohérent, le passer en "terminé"
        $rdvId = (int)($data['rdv_id'] ?? 0);
        if ($rdvId) {
            $rdv = Rendez_vous::with('medecin')
                ->where('id', $rdvId)
                ->whereHas('medecin', function($q) use ($serviceId){ $q->where('service_id', $serviceId); })
                ->first();
            if ($rdv && optional($rdv->patient)->id == $data['patient_id']) {
                $rdv->statut = 'terminé';
                $rdv->save();
            }
        }

        return redirect()->route('infirmier.rendezvous.index')->with('success','Suivi enregistré et dossier médical mis à jour.');
    }

    // Liste complète des RDV du service, avec filtre de statut
    public function rendezvousIndex(\Illuminate\Http\Request $request)
    {
        $serviceId = auth()->user()->service_id;
        $status = $request->query('status','all');
        $map = [
            'attente' => ['en_attente','en attente','pending'],
            'cours'   => ['confirmé','confirme','confirmée','confirmee'],
            'traites' => ['terminé','termine','completed'],
        ];
        $q = Rendez_vous::with(['patient.user','medecin'])
            ->whereHas('medecin', function($qq) use ($serviceId){ $qq->where('service_id',$serviceId); })
            ->orderBy('date')
            ->orderBy('heure');
        if ($status !== 'all' && isset($map[$status])) {
            $q->whereIn('statut', $map[$status]);
        }
        $rendezvous = $q->paginate(10)->withQueryString();
        return view('infirmier.rendezvous.index', compact('rendezvous','status'));
    }

    /**
     * Valider un RDV (passe en confirmé)
     */
    public function validerRdv(int $id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        $rdv->statut = 'confirmé';
        $rdv->save();

        return back()->with('success', 'Rendez-vous validé.');
    }
}
