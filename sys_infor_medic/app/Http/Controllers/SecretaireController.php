<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Admissions;
use App\Models\User;
use Carbon\Carbon;

class SecretaireController extends Controller
{
    public function payments()
    {
        $patients = \App\Models\User::where('role','patient')->orderBy('name')->get(['id','name','email']);
        $orders = \App\Models\Order::with('items','user')->orderByDesc('created_at')->take(20)->get();
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
            'provider' => ['required','in:wave,orangemoney'],
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
        $totalPatients = Patient::count();
        $months = [];
        $rendezvousData = [];
        $admissionsData = [];

        // Statistiques jusqu'à 12 mois (mois courant inclus) pour permettre un découpage dynamique 2/6/12
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');
            $rendezvousData[] = Rendez_vous::whereYear('date', $month->year)
                                           ->whereMonth('date', $month->month)
                                           ->count();
            $admissionsData[] = Admissions::whereYear('date_admission', $month->year)
                                         ->whereMonth('date_admission', $month->month)
                                         ->count();
        }

        // KPIs
        $pendingRdvCount = Rendez_vous::whereIn('statut', ['en_attente', 'en attente', 'pending'])->count();
        $patientsATraiterCount = Admissions::whereNull('date_sortie')->count();

        // Liste des demandes de rendez-vous (en attente)
        $pendingRdvList = Rendez_vous::with(['patient','medecin'])
            ->whereIn('statut', ['en_attente', 'en attente', 'pending'])
            ->orderBy('date')
            ->orderBy('heure')
            ->get();

        // Données de paiement pour l'onglet paiements
        $recentOrders = \App\Models\Order::with('items','user')->orderByDesc('created_at')->take(20)->get();
        $totalPaymentsThisMonth = \App\Models\Order::where('status','paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('total_amount');
        $pendingPayments = \App\Models\Order::where('status','pending')->count();
        
        return view('secretaire.dashboard', compact(
            'totalPatients','months','rendezvousData','admissionsData',
            'pendingRdvCount','patientsATraiterCount','pendingRdvList',
            'recentOrders','totalPaymentsThisMonth','pendingPayments'
        ));
    }

    public function dossiersAdmin()
    {
        $patients = Patient::with('dossier_administratifs')->get();
        $secretaires = User::where('role','secretaire')->orderBy('name')->get();
        return view('secretaire.dossieradmin', compact('patients','secretaires'));
    }

    public function rendezvous()
    {
        $rendezvous = Rendez_vous::with('patient', 'medecin')->get();
        $patients = Patient::all();
        $medecins = User::where('role','medecin')->get();
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

        // Trouver l'utilisateur du patient (user_id) pour respecter la contrainte FK
        $patient = Patient::findOrFail($request->patient_id);
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
        $rdv->statut = 'confirmé';
        $rdv->save();
        // Notify patient by email
        $user = \App\Models\User::find($rdv->user_id);
        if ($user) {
            try { $user->notify(new \App\Notifications\RendezvousStatusChanged($rdv)); } catch (\Throwable $e) { /* noop */ }
        }
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous confirmé.');
    }

    public function cancelRdv($id)
    {
        $rdv = Rendez_vous::findOrFail($id);
        $rdv->statut = 'annulé';
        $rdv->save();
        // Notify patient by email
        $user = \App\Models\User::find($rdv->user_id);
        if ($user) {
            try { $user->notify(new \App\Notifications\RendezvousStatusChanged($rdv)); } catch (\Throwable $e) { /* noop */ }
        }
        return redirect()->route('secretaire.rendezvous')->with('success','Rendez-vous annulé.');
    }

    public function admissions()
    {
        $admissions = Admissions::with('patient')->get();
        $patients = Patient::all(); 
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
    Patient::create($data);

    return redirect()->route('secretaire.dossiersAdmin')->with('success', 'Patient ajouté avec succès.');
}

public function updatePatient(Request $request, $id)
{
    $patient = Patient::findOrFail($id);

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

    $patient->update($request->all());

    return redirect()->route('secretaire.dossiersAdmin')->with('success', 'Patient modifié avec succès.');
}
// Ajouter une nouvelle admission
public function storeAdmission(Request $request)
{
    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date_admission' => 'required|date',
        'motif' => 'required|string|max:255',
    ]);

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

    $admission->update([
        'patient_id' => $request->patient_id,
        'date_admission' => $request->date_admission,
        'motif' => $request->motif,
    ]);

    return redirect()->route('secretaire.admissions')->with('success', 'Admission mise à jour avec succès.');
}

}

