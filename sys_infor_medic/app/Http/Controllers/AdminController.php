<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Rendez_vous;
use App\Models\Consultations;
use App\Models\Admissions;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::with(['nurses:id,name','doctors:id,name'])->get();

        // Comptes par rôle
        $rolesCount = [
            'admin' => User::where('role','admin')->count(),
            'medecin' => User::where('role','medecin')->count(),
            'infirmier' => User::where('role','infirmier')->count(),
            'secretaire' => User::where('role','secretaire')->count(),
            'patient' => User::where('role','patient')->count(),
        ];

        // Séries mensuelles (jusqu'à 12 derniers mois: mois courant inclus)
        $months = [];
        $rendezvousCounts = [];
        $admissionsCounts = [];
        $consultationsCounts = [];
        $patientsCounts = [];
        $rdvPendingSeries = [];
        $rdvConfirmedSeries = [];
        $rdvCancelledSeries = [];
        for ($i=11; $i>=0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $months[] = $m->format('M');
            $rendezvousCounts[] = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)->count();
            $admissionsCounts[] = Admissions::whereYear('date_admission', $m->year)->whereMonth('date_admission', $m->month)->count();
            $consultationsCounts[] = Consultations::whereYear('date_consultation', $m->year)->whereMonth('date_consultation', $m->month)->count();
            $patientsCounts[] = Patient::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
            // RDV par statut
            $pending = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['en_attente','en attente','pending'])->count();
            $confirmed = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['confirmé','confirme','confirmee','confirmée'])->count();
            $cancelled = Rendez_vous::whereYear('date', $m->year)->whereMonth('date', $m->month)
                ->whereIn('statut', ['annulé','annule','annulee','annulée','cancelled','canceled'])->count();
            $rdvPendingSeries[] = $pending;
            $rdvConfirmedSeries[] = $confirmed;
            $rdvCancelledSeries[] = $cancelled;
        }

        // KPI
        $kpis = [
            'totalUsers' => User::count(),
            'totalPatients' => $rolesCount['patient'],
            'rdvThisMonth' => Rendez_vous::whereYear('date', now()->year)->whereMonth('date', now()->month)->count(),
            'consultsThisMonth' => Consultations::whereYear('date_consultation', now()->year)->whereMonth('date_consultation', now()->month)->count(),
            // Paiements
            'paymentsPaidThisMonth' => \App\Models\Order::where('status','paid')->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)->sum('total_amount'),
            'paymentsPending' => \App\Models\Order::where('status','pending')->count(),
        ];

        // Répartition des statuts de RDV (tous)
        $rdvStatusCounts = Rendez_vous::select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->pluck('total','statut')
            ->toArray();

        // Permissions par modules (concret hôpital)
        $permissionModules = [
            [
                'icon' => 'bi-person-vcard',
                'title' => 'Patients',
                'permissions' => [
                    ['key' => 'patients.view', 'label' => 'Voir les patients'],
                    ['key' => 'patients.create', 'label' => 'Créer un patient'],
                    ['key' => 'patients.edit', 'label' => 'Modifier un patient'],
                    ['key' => 'patients.delete', 'label' => 'Supprimer un patient'],
                    ['key' => 'patients.documents', 'label' => 'Gérer les documents patients'],
                ],
            ],
            [
                'icon' => 'bi-calendar-week',
                'title' => 'Rendez-vous',
                'permissions' => [
                    ['key' => 'rdv.view', 'label' => 'Voir les rendez-vous'],
                    ['key' => 'rdv.create', 'label' => 'Créer / Planifier'],
                    ['key' => 'rdv.update', 'label' => 'Modifier / Confirmer / Annuler'],
                ],
            ],
            [
                'icon' => 'bi-clipboard2-pulse',
                'title' => 'Consultations',
                'permissions' => [
                    ['key' => 'consultations.view', 'label' => 'Voir les consultations'],
                    ['key' => 'consultations.create', 'label' => 'Créer une consultation'],
                    ['key' => 'consultations.edit', 'label' => 'Modifier diagnostic / traitement'],
                ],
            ],
            [
                'icon' => 'bi-capsule',
                'title' => 'Pharmacie / Ordonnances',
                'permissions' => [
                    ['key' => 'ordonnances.view', 'label' => 'Voir les ordonnances'],
                    ['key' => 'ordonnances.create', 'label' => 'Émettre une ordonnance'],
                    ['key' => 'ordonnances.edit', 'label' => 'Modifier une ordonnance'],
                ],
            ],
            [
                'icon' => 'bi-flask',
                'title' => 'Laboratoire / Analyses',
                'permissions' => [
                    ['key' => 'analyses.view', 'label' => 'Voir les analyses'],
                    ['key' => 'analyses.create', 'label' => 'Demander une analyse'],
                    ['key' => 'analyses.report', 'label' => 'Renseigner un résultat'],
                ],
            ],
            [
                'icon' => 'bi-receipt-cutoff',
                'title' => 'Facturation',
                'permissions' => [
                    ['key' => 'billing.view', 'label' => 'Voir la facturation'],
                    ['key' => 'billing.create', 'label' => 'Émettre une facture / encaissement'],
                    ['key' => 'billing.refund', 'label' => 'Effectuer un remboursement'],
                ],
            ],
            [
                'icon' => 'bi-gear',
                'title' => 'Administration',
                'permissions' => [
                    ['key' => 'users.manage', 'label' => 'Gérer les utilisateurs'],
                    ['key' => 'settings.view', 'label' => 'Voir les paramètres'],
                    ['key' => 'settings.update', 'label' => 'Modifier les paramètres'],
                    ['key' => 'reports.view', 'label' => 'Voir les rapports / statistiques'],
                ],
            ],
        ];

        // Module unique: Indispensables
        $essentialModule = [
            'icon' => 'bi-shield-check',
            'title' => 'Indispensables',
            'permissions' => [
                ['key' => 'patients.view', 'label' => 'Voir patients'],
                ['key' => 'patients.create', 'label' => 'Créer patient'],
                ['key' => 'rdv.view', 'label' => 'Voir rendez-vous'],
                ['key' => 'rdv.create', 'label' => 'Créer rendez-vous'],
                ['key' => 'consultations.view', 'label' => 'Voir consultations'],
                ['key' => 'consultations.create', 'label' => 'Créer consultation'],
                ['key' => 'ordonnances.create', 'label' => 'Émettre ordonnance'],
            ],
        ];

        // État actuel des permissions
        $rolePermissions = [];
        foreach (['admin','secretaire','medecin','infirmier','patient'] as $role) {
            $rolePermissions[$role] = [];
        }
        foreach (RolePermission::all() as $rp) {
            $rolePermissions[$rp->role][$rp->permission] = (bool)$rp->allowed;
        }

        return view('admin.dashboard', [
            'users' => $users,
            'rolesCount' => $rolesCount,
            'months' => $months,
            'rendezvousCounts' => $rendezvousCounts,
            'admissionsCounts' => $admissionsCounts,
            'consultationsCounts' => $consultationsCounts,
            'patientsCounts' => $patientsCounts,
            'kpis' => $kpis,
            'essentialModule' => $essentialModule,
            'rolePermissions' => $rolePermissions,
            'rdvStatusCounts' => $rdvStatusCounts,
            'rdvPendingSeries' => $rdvPendingSeries,
            'rdvConfirmedSeries' => $rdvConfirmedSeries,
            'rdvCancelledSeries' => $rdvCancelledSeries,
        ]);
    }

    public function savePermissions(\Illuminate\Http\Request $request)
    {
        // Niveaux soumis: none | read | full
        $submitted = $request->input('levels', []);
        $roles = ['admin','secretaire','medecin','infirmier','patient'];

        // Si le formulaire provient de la vue simplifiée (Indispensables)
        if (isset($submitted['Indispensables'])) {
            $essentialPerms = [
                'patients.view','patients.create','rdv.view','rdv.create','consultations.view','consultations.create','ordonnances.create'
            ];
            foreach ($roles as $role) {
                $level = $submitted['Indispensables'][$role] ?? 'none';
                foreach ($essentialPerms as $permKey) {
                    $allowed = false;
                    if ($level === 'full') {
                        $allowed = true;
                    } elseif ($level === 'read') {
                        $allowed = str_ends_with($permKey, '.view');
                    }
                    RolePermission::updateOrCreate(
                        ['role' => $role, 'permission' => $permKey],
                        ['allowed' => (bool)$allowed]
                    );
                }
            }
            return redirect()->route('admin.dashboard')->with('success', 'Permissions mises à jour.');
        }

        // Vue avancée désactivée: si non 'Indispensables', ne rien faire
        return redirect()->route('admin.dashboard')->with('success', 'Permissions mises à jour.');
    }
}
