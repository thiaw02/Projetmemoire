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
        // Cache des statistiques pour 10 minutes
        $cacheKey = 'admin_dashboard_stats_' . now()->format('Y-m-d-H') . '_' . floor(now()->minute / 10);
        
        $stats = \Cache::remember($cacheKey, 600, function() {
            // Récupération optimisée des utilisateurs avec pagination
            $users = User::with(['nurses:id,name','doctors:id,name'])
                ->select('id', 'name', 'role', 'active', 'created_at')
                ->limit(50) // Limiter l'affichage
                ->get();

            // Comptes par rôle en une seule requête
            $rolesCountData = User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray();
                
            $rolesCount = [
                'admin' => $rolesCountData['admin'] ?? 0,
                'medecin' => $rolesCountData['medecin'] ?? 0,
                'infirmier' => $rolesCountData['infirmier'] ?? 0,
                'secretaire' => $rolesCountData['secretaire'] ?? 0,
                'patient' => $rolesCountData['patient'] ?? 0,
            ];
            
            return compact('users', 'rolesCount');
        });
        
        extract($stats);

        // Cache des statistiques mensuelles pour 1 heure
        $monthlyStatsKey = 'admin_monthly_stats_' . now()->format('Y-m-d-H');
        
        $monthlyStats = \Cache::remember($monthlyStatsKey, 3600, function() {
            $months = [];
            $rendezvousCounts = [];
            $admissionsCounts = [];
            $consultationsCounts = [];
            $patientsCounts = [];
            $rdvPendingSeries = [];
            $rdvConfirmedSeries = [];
            $rdvCancelledSeries = [];
            
            // Générer les 12 derniers mois
            $monthsData = [];
            for ($i = 11; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i);
                $months[] = $m->format('M');
                $monthsData[] = [
                    'year' => $m->year,
                    'month' => $m->month,
                    'key' => $m->format('Y-m')
                ];
            }
            
            // Requêtes optimisées avec une seule requête par table
            $rdvStats = Rendez_vous::selectRaw('YEAR(date) as year, MONTH(date) as month, COUNT(*) as total, statut')
                ->where('date', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month', 'statut')
                ->get()
                ->groupBy(function($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });
                
            // Requête alternative plus robuste pour les consultations
            $consultationsData = Consultations::selectRaw('YEAR(date_consultation) as year, MONTH(date_consultation) as month, COUNT(*) as total')
                ->where('date_consultation', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->get();
                
            $consultationsStats = [];
            foreach ($consultationsData as $item) {
                $key = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                $consultationsStats[$key] = $item->total;
            }
                
            // Requête alternative plus robuste pour les patients
            $patientsData = Patient::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->get();
                
            $patientsStats = [];
            foreach ($patientsData as $item) {
                $key = $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                $patientsStats[$key] = $item->total;
            }
                
            // Construire les séries de données
            foreach ($monthsData as $monthData) {
                $key = $monthData['key'];
                
                // RDV totaux
                $monthRdvs = $rdvStats->get($key, collect());
                $rendezvousCounts[] = $monthRdvs->sum('total');
                
                // RDV par statut
                $rdvPendingSeries[] = $monthRdvs->whereIn('statut', ['en_attente','en attente','pending'])->sum('total');
                $rdvConfirmedSeries[] = $monthRdvs->whereIn('statut', ['confirmé','confirme','confirmee','confirmée'])->sum('total');
                $rdvCancelledSeries[] = $monthRdvs->whereIn('statut', ['annulé','annule','annulee','annulée','cancelled','canceled'])->sum('total');
                
                // Autres statistiques
                $consultationsCounts[] = $consultationsStats[$key] ?? 0;
                $patientsCounts[] = $patientsStats[$key] ?? 0;
                $admissionsCounts[] = 0; // À implémenter si nécessaire
            }
            
            return compact('months', 'rendezvousCounts', 'admissionsCounts', 'consultationsCounts', 
                          'patientsCounts', 'rdvPendingSeries', 'rdvConfirmedSeries', 'rdvCancelledSeries');
        });
        
        extract($monthlyStats);

        // KPIs avec cache pour 5 minutes
        $kpisKey = 'admin_kpis_' . now()->format('Y-m-d-H-i');
        $kpis = \Cache::remember($kpisKey, 300, function() use ($rolesCount) {
            $currentMonth = now();
            
            return [
                'totalUsers' => array_sum($rolesCount),
                'totalPatients' => $rolesCount['patient'],
                'rdvThisMonth' => Rendez_vous::whereYear('date', $currentMonth->year)
                    ->whereMonth('date', $currentMonth->month)->count(),
                'consultsThisMonth' => Consultations::whereYear('date_consultation', $currentMonth->year)
                    ->whereMonth('date_consultation', $currentMonth->month)->count(),
                // Paiements (si la table existe)
                'paymentsPaidThisMonth' => class_exists('\App\Models\Order') ? 
                    \App\Models\Order::where('status','paid')
                        ->whereYear('paid_at', $currentMonth->year)
                        ->whereMonth('paid_at', $currentMonth->month)
                        ->sum('total_amount') : 0,
                'paymentsPending' => class_exists('\App\Models\Order') ?
                    \App\Models\Order::where('status','pending')->count() : 0,
            ];
        });

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
