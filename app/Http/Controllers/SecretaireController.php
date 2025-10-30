<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InscriptionConfirmee;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Admissions;
use App\Models\User;
use App\Events\RendezVousStatusUpdated;
use Carbon\Carbon;

class SecretaireController extends Controller
{
    public function payments()
    {
        $serviceId = auth()->user()->service_id;
        // Patients du même service uniquement
        $patients = \App\Models\User::where('role','patient')
            ->whereHas('patient.services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })
            ->orderBy('name')
            ->get(['id','name','email']);
        // Commandes liées aux patients du service uniquement
        $orders = \App\Models\Order::with('items','user.patient')
            ->whereHas('user.patient.services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
        $defaults = [
            'consultation' => (int)(\App\Models\Setting::getValue('price.consultation', 5000)),
            'analyse' => (int)(\App\Models\Setting::getValue('price.analyse', 10000)),
            'acte' => (int)(\App\Models\Setting::getValue('price.acte', 7000)),
            'currency' => (string)(\App\Models\Setting::getValue('currency', 'XOF')),
        ];
        return view('secretaire.payments', compact('patients','orders','defaults'));
    }

    public function createPaymentLink(\Illuminate\Http\Request $request, \App\Services\PaymentService $svc)
    {
        $data = $request->validate([
            'patient_user_id' => ['required','integer','exists:users,id'],
            'provider' => ['required','in:paydunya'],
            'kind' => ['required','in:consultation,analyse,acte'],
            'amount' => ['required','integer','min:100'],
            'label' => ['nullable','string','max:255'],
        ]);
        $patientUser = \App\Models\User::findOrFail($data['patient_user_id']);
        $label = $data['label'] ?: ucfirst($data['kind']);
        $order = \App\Models\Order::create([
            'user_id' => $patientUser->id,
            'patient_id' => optional($patientUser->patient)->id,
            'currency' => 'XOF',
            'total_amount' => (int)$data['amount'],
            'status' => 'pending',
            'provider' => $data['provider'],
            'metadata' => ['created_by' => auth()->id()],
        ]);
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'item_type' => $data['kind'],
            'item_id' => null,
            'label' => $label,
            'amount' => (int)$data['amount'],
        ]);
        $order = $svc->createCheckout($order, $data['provider']);

        // notifier par e-mail le patient avec le lien
        try {
            if ($patientUser->email && $order->payment_url) {
                $patientUser->notify(new \App\Notifications\PaymentLinkNotification($order));
            }
        } catch (\Throwable $e) {}

        return redirect()->route('secretaire.payments')->with('success','Lien de paiement généré.')->with('payment_url',$order->payment_url);
    }

    public function exportPaymentsCsv(\Illuminate\Http\Request $request)
    {
        $rows = \App\Models\Order::with('items','user')->orderByDesc('created_at')->get();
        $filename = 'paiements_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->streamDownload(function() use ($rows){
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8
            fputcsv($out, ['Date','Patient','Libellé','Montant','Prestataire','Statut'], ';');
            foreach ($rows as $o) {
                $label = optional($o->items->first())->label ?: '';
                fputcsv($out, [
                    optional($o->created_at)->format('Y-m-d H:i:s'),
                    optional($o->user)->name,
                    $label,
                    $o->total_amount,
                    strtoupper($o->provider ?? ''),
                    $o->status,
                ], ';');
            }
            fclose($out);
        }, $filename, $headers);
    }

    public function paymentsSettings()
    {
        $defaults = [
            'consultation' => (int)(\App\Models\Setting::getValue('price.consultation', 5000)),
            'analyse' => (int)(\App\Models\Setting::getValue('price.analyse', 10000)),
            'acte' => (int)(\App\Models\Setting::getValue('price.acte', 7000)),
            'currency' => (string)(\App\Models\Setting::getValue('currency', 'XOF')),
        ];
        return view('secretaire.payments_settings', compact('defaults'));
    }

    public function savePaymentsSettings(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'price_consultation' => ['required','integer','min:100'],
            'price_analyse' => ['required','integer','min:100'],
            'price_acte' => ['required','integer','min:100'],
            'currency' => ['required','string','max:8'],
        ]);
        \App\Models\Setting::updateOrCreate(['key'=>'price.consultation'], ['value'=>$data['price_consultation']]);
        \App\Models\Setting::updateOrCreate(['key'=>'price.analyse'], ['value'=>$data['price_analyse']]);
        \App\Models\Setting::updateOrCreate(['key'=>'price.acte'], ['value'=>$data['price_acte']]);
        \App\Models\Setting::updateOrCreate(['key'=>'currency'], ['value'=>$data['currency']]);
        return redirect()->route('secretaire.payments.settings')->with('success','Tarifs mis à jour.');
    }

    public function exportPaymentsPdf()
    {
        $orders = \App\Models\Order::with('items','user')->orderByDesc('created_at')->get();
        $data = ['orders' => $orders, 'generatedAt' => now()];
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('secretaire.payments_pdf', $data);
            return $pdf->download('paiements_'.now()->format('Ymd_His').'.pdf');
        }
        return view('secretaire.payments_pdf', $data);
    }

    public function dashboard()
    {
        // Statistiques des patients
        $totalPatients = Patient::count();
        $newPatientsThisWeek = Patient::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $newPatientsThisMonth = Patient::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        
        // Patients à traiter (admissions actives + nouveaux patients sans rendez-vous)
        $activeAdmissions = Admissions::whereNull('date_sortie')->count();
        $newPatientsWithoutRdv = Patient::whereDoesntHave('rendez_vous')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
        $patientsATraiterCount = $activeAdmissions + $newPatientsWithoutRdv;
        
        // Statistiques des rendez-vous détaillées
        $pendingRdvCount = Rendez_vous::whereIn('statut', ['en_attente', 'en attente', 'pending'])->count();
        $confirmedRdvCount = Rendez_vous::whereIn('statut', ['confirmé', 'confirmed'])->count();
        $completedRdvCount = Rendez_vous::whereIn('statut', ['terminé', 'completed', 'termine'])->count();
        $todayRdvCount = Rendez_vous::whereDate('date', Carbon::today())
            ->whereNotIn('statut', ['annulé', 'canceled'])
            ->count();
            
        // Données pour les graphiques - 12 mois
        $months = [];
        $rendezvousData = [];
        $admissionsData = [];
        $patientsData = [];
        $paymentsData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->translatedFormat('M Y'); // Format plus lisible
            
            // Rendez-vous par mois
            $rendezvousData[] = Rendez_vous::whereYear('date', $month->year)
                                           ->whereMonth('date', $month->month)
                                           ->count();
            
            // Admissions par mois
            $admissionsData[] = Admissions::whereYear('date_admission', $month->year)
                                         ->whereMonth('date_admission', $month->month)
                                         ->count();
                                         
            // Nouveaux patients par mois
            $patientsData[] = Patient::whereYear('created_at', $month->year)
                                    ->whereMonth('created_at', $month->month)
                                    ->count();
                                    
            // Paiements par mois (en milliers XOF)
            $monthlyPayments = \App\Models\Order::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('total_amount');
            $paymentsData[] = round($monthlyPayments / 1000, 1); // En milliers
        }

        // Rendez-vous à venir dans le même service que la secrétaire
        $serviceId = auth()->user()->service_id;
        $upcomingServiceRdv = Rendez_vous::with(['patient.user','medecin'])
            ->whereDate('date','>=', now()->toDateString())
            ->whereIn('statut', ['confirmé','confirme','confirmée','confirmee','en_attente','en attente','pending'])
            ->whereHas('medecin', function($q) use ($serviceId){ $q->where('service_id', $serviceId); })
            ->orderBy('date')
            ->orderBy('heure')
            ->take(25)
            ->get();

        // Liste des demandes de rendez-vous urgentes (en attente) dans le même service
        $pendingRdvList = Rendez_vous::with(['patient.user', 'medecin'])
            ->whereIn('statut', ['en_attente', 'en attente', 'pending'])
            ->whereHas('medecin', function($q) use ($serviceId){ $q->where('service_id', $serviceId); })
            ->orderBy('date')
            ->orderBy('heure') 
            ->take(50)
            ->get();

        // Données de paiement détaillées
        $recentOrders = \App\Models\Order::with(['items', 'user.patient'])
            ->orderByDesc('created_at')
            ->take(20)
            ->get();
            
        $totalPaymentsThisMonth = \App\Models\Order::where('status','paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('total_amount');
            
        $totalPaymentsToday = \App\Models\Order::where('status','paid')
            ->whereDate('paid_at', Carbon::today())
            ->sum('total_amount');
            
        $pendingPayments = \App\Models\Order::where('status','pending')->count();
        $failedPayments = \App\Models\Order::where('status','failed')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        // Statistiques supplémentaires pour les graphiques avancés
        $rdvStatusStats = [
            'en_attente' => $pendingRdvCount,
            'confirme' => $confirmedRdvCount,
            'termine' => $completedRdvCount,
            'aujourd_hui' => $todayRdvCount
        ];
        
        $patientStats = [
            'total' => $totalPatients,
            'nouveaux_semaine' => $newPatientsThisWeek,
            'nouveaux_mois' => $newPatientsThisMonth,
            'a_traiter' => $patientsATraiterCount
        ];
        
        return view('secretaire.dashboard', compact(
            'totalPatients', 'patientStats', 'months', 'rendezvousData', 'admissionsData', 'patientsData', 'paymentsData',
            'pendingRdvCount', 'patientsATraiterCount', 'pendingRdvList', 'rdvStatusStats',
            'recentOrders', 'totalPaymentsThisMonth', 'totalPaymentsToday', 'pendingPayments', 'failedPayments',
            'upcomingServiceRdv'
        ));
    }

    public function dossiersAdmin()
    {
        $serviceId = auth()->user()->service_id;
        $patients = Patient::with('dossier_administratifs')
            ->whereHas('services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })
            ->get();
        $secretaires = User::where('role','secretaire')->where('service_id',$serviceId)->orderBy('name')->get();
        return view('secretaire.dossieradmin', compact('patients','secretaires'));
    }

    public function rendezvous()
    {
        $serviceId = auth()->user()->service_id;
        $rendezvous = Rendez_vous::with('patient', 'medecin')
            ->whereHas('medecin', function($q) use ($serviceId){ $q->where('service_id', $serviceId); })
            ->orderBy('date')
            ->orderBy('heure')
            ->get();
        $patients = Patient::whereHas('services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })->get();
        $medecins = User::where('role','medecin')->where('service_id', $serviceId)->get();
        return view('secretaire.rendezvous', compact('rendezvous','patients','medecins'));
    }

    public function storeRdv(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'heure' => 'required',
            'motif' => 'nullable|string|max:255',
        ]);

        $serviceId = auth()->user()->service_id;
        // Vérifier que le patient appartient au service de la secrétaire
        $patient = Patient::findOrFail($request->patient_id);
        if (!$patient->services()->where('services.id', $serviceId)->exists()) {
            return back()->with('error', 'Patient hors de votre service.');
        }
        // Vérifier que le médecin est dans le même service
        $medecin = User::where('id',$request->medecin_id)->where('role','medecin')->where('service_id',$serviceId)->first();
        if (!$medecin) {
            return back()->with('error', 'Médecin hors de votre service.');
        }

        // Trouver l'utilisateur du patient (user_id) pour respecter la contrainte FK
        Rendez_vous::create([
            'user_id' => $patient->user_id, // user_id (table users)
            'medecin_id' => $request->medecin_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'statut' => 'en_attente',
        ]);

        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous planifié avec succès.');
    }

    public function confirmRdv($id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        // Limiter par service: la secrétaire doit appartenir au même service que le médecin du RDV
        $serviceId = auth()->user()->service_id;
        $rdvServiceId = optional($rdv->medecin)->service_id;
        if ($rdvServiceId && $serviceId && $rdvServiceId !== $serviceId) {
            abort(403);
        }
        $oldStatus = $rdv->statut;
        $rdv->statut = 'confirmé';
        $rdv->save();
        
        // Notify patient by email
        $user = \App\Models\User::find($rdv->user_id);
        if ($user) {
            try { $user->notify(new \App\Notifications\RendezvousStatusChanged($rdv)); } catch (\Throwable $e) { /* noop */ }
        }
        
        // Déclencher l'event pour les notifications temps réel
        try {
            event(new RendezVousStatusUpdated($rdv, $oldStatus));
        } catch (\Throwable $e) {
            \Log::error('Erreur lors de l\'événement de changement de statut: ' . $e->getMessage());
        }
        
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous confirmé.');
    }

    public function cancelRdv($id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        // Limiter par service: la secrétaire doit appartenir au même service que le médecin du RDV
        $serviceId = auth()->user()->service_id;
        $rdvServiceId = optional($rdv->medecin)->service_id;
        if ($rdvServiceId && $serviceId && $rdvServiceId !== $serviceId) {
            abort(403);
        }
        $oldStatus = $rdv->statut;
        $rdv->statut = 'annulé';
        $rdv->save();
        
        // Notify patient by email
        $user = \App\Models\User::find($rdv->user_id);
        if ($user) {
            try { $user->notify(new \App\Notifications\RendezvousStatusChanged($rdv)); } catch (\Throwable $e) { /* noop */ }
        }
        
        // Déclencher l'event pour les notifications temps réel
        try {
            event(new RendezVousStatusUpdated($rdv, $oldStatus));
        } catch (\Throwable $e) {
            \Log::error('Erreur lors de l\'événement de changement de statut: ' . $e->getMessage());
        }
        
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous annulé.');
    }

    public function admissions()
    {
        $serviceId = auth()->user()->service_id;
        $admissions = Admissions::with('patient')
            ->whereHas('patient.services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })
            ->get();
        $patients = Patient::whereHas('services', function($q) use ($serviceId){ $q->where('services.id', $serviceId); })->get(); 
        return view('secretaire.admissions', compact('admissions', 'patients'));
    }
    // Ajouter ces méthodes

public function storePatient(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'nullable|email',
        'telephone' => 'nullable|string|max:20',
        'sexe' => 'nullable|string',
        'date_naissance' => 'nullable|date',
        'adresse' => 'nullable|string',
        'groupe_sanguin' => 'nullable|string',
        'antecedents' => 'nullable|string',
        'secretary_user_id' => 'nullable|exists:users,id',
    ]);

    $data = $request->all();
    if (empty($data['secretary_user_id'])) {
        $data['secretary_user_id'] = auth()->id();
    }
    $serviceId = auth()->user()->service_id;
    $patient = Patient::create($data);
    // Attacher le patient au service de la secrétaire
    if ($serviceId) {
        try { $patient->services()->syncWithoutDetaching([$serviceId]); } catch (\Throwable $e) {}
    }

    // Envoyer infos de compte si email présent (génère un identifiant minimal si besoin)
    try {
        $email = $patient->email ?? null;
        if ($email) {
            $numero = $patient->numero_dossier ?? ('PAT'.now()->format('Ymd').str_pad((string)$patient->id,3,'0',STR_PAD_LEFT));
            $password = 'motdepasse';
            Mail::to($email)->send(new InscriptionConfirmee($numero, $email, $password));
        }
    } catch (\Throwable $e) { /* noop */ }

    return redirect()->route('secretaire.dossiersAdmin')->with('success', 'Patient ajouté avec succès.');
}

public function updatePatient(Request $request, $id)
{
    $patient = Patient::findOrFail($id);
    // Empêcher la modification si le patient n'est pas dans le service de la secrétaire
    $serviceId = auth()->user()->service_id;
    if (!$patient->services()->where('services.id',$serviceId)->exists()) {
        abort(403);
    }

    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'nullable|email',
        'telephone' => 'nullable|string|max:20',
        'sexe' => 'nullable|string',
        'date_naissance' => 'nullable|date',
        'adresse' => 'nullable|string',
        'groupe_sanguin' => 'nullable|string',
        'antecedents' => 'nullable|string',
        'secretary_user_id' => 'nullable|exists:users,id',
    ]);

    return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous planifié avec succès.');
}

// Ajouter une nouvelle admission
public function storeAdmission(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date_admission' => 'required|date',
        'motif' => 'required|string|max:255',
    ]);

    $serviceId = auth()->user()->service_id;
    // Patient doit être du service
    $patient = Patient::findOrFail($request->patient_id);
    if (!$patient->services()->where('services.id', $serviceId)->exists()) {
        return back()->with('error', 'Patient hors de votre service.');
    }

    Admissions::create([
        'patient_id' => $request->patient_id,
        'date_admission' => $request->date_admission,
        'motif' => $request->motif,
    ]);

    return redirect()->route('secretaire.admissions')->with('success', 'Admission ajoutée avec succès.');
}

// Mettre à jour une admission existante
public function updateAdmission(Request $request, $id)
{
    $admission = Admissions::findOrFail($id);

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date_admission' => 'required|date',
        'motif' => 'required|string|max:255',
    ]);

    $serviceId = auth()->user()->service_id;
    $patient = Patient::findOrFail($request->patient_id);
    if (!$patient->services()->where('services.id', $serviceId)->exists()) {
        return back()->with('error', 'Patient hors de votre service.');
    }

    $admission->update([
        'patient_id' => $request->patient_id,
        'date_admission' => $request->date_admission,
        'motif' => $request->motif,
    ]);

    return redirect()->route('secretaire.admissions')->with('success', 'Admission mise à jour avec succès.');
}
}
// ...