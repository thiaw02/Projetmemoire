<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{User, Patient, Rendez_vous, Consultations};

class DataService
{
    /**
     * Obtenir les statistiques du dashboard admin optimisées
     */
    public static function getAdminDashboardStats()
    {
        return Cache::remember('admin_dashboard_complete', 600, function() {
            // Stats utilisateurs
            $userStats = User::selectRaw('role, COUNT(*) as count, SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active_count')
                ->groupBy('role')
                ->get()
                ->mapWithKeys(function($item) {
                    return [
                        $item->role => [
                            'total' => $item->count,
                            'active' => $item->active_count
                        ]
                    ];
                })->toArray();

            // Stats mensuelles (6 derniers mois)
            $monthlyData = self::getMonthlyStats(6);
            
            // KPIs rapides
            $currentMonth = now();
            $kpis = [
                'total_users' => User::count(),
                'total_patients' => Patient::count(),
                'rdv_this_month' => Rendez_vous::whereYear('date', $currentMonth->year)
                    ->whereMonth('date', $currentMonth->month)->count(),
                'consults_this_month' => Consultations::whereYear('date_consultation', $currentMonth->year)
                    ->whereMonth('date_consultation', $currentMonth->month)->count(),
            ];

            return [
                'user_stats' => $userStats,
                'monthly_data' => $monthlyData,
                'kpis' => $kpis
            ];
        });
    }

    /**
     * Obtenir les statistiques mensuelles optimisées
     */
    private static function getMonthlyStats($months = 12)
    {
        $startDate = Carbon::now()->subMonths($months);
        
        // Générer la structure des mois
        $monthsData = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthsData->push([
                'label' => $date->format('M Y'),
                'key' => $date->format('Y-m'),
                'year' => $date->year,
                'month' => $date->month
            ]);
        }

        // Requêtes optimisées
        $rdvData = Rendez_vous::selectRaw('YEAR(date) as year, MONTH(date) as month, statut, COUNT(*) as count')
            ->where('date', '>=', $startDate)
            ->groupBy('year', 'month', 'statut')
            ->get()
            ->groupBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $consultData = Consultations::selectRaw('YEAR(date_consultation) as year, MONTH(date_consultation) as month, COUNT(*) as count')
            ->where('date_consultation', '>=', $startDate)
            ->groupBy('year', 'month')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT) => $item->count];
            });

        $patientData = Patient::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('year', 'month')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT) => $item->count];
            });

        // Construire les séries
        $series = [
            'rdv_total' => [],
            'rdv_confirmed' => [],
            'rdv_pending' => [],
            'rdv_cancelled' => [],
            'consultations' => [],
            'patients' => [],
            'months' => []
        ];

        foreach ($monthsData as $month) {
            $key = $month['key'];
            $series['months'][] = $month['label'];
            
            // RDV par statut
            $monthRdvs = $rdvData->get($key, collect());
            $series['rdv_total'][] = $monthRdvs->sum('count');
            $series['rdv_confirmed'][] = $monthRdvs->whereIn('statut', ['confirmé', 'confirmée', 'confirmed'])->sum('count');
            $series['rdv_pending'][] = $monthRdvs->whereIn('statut', ['en_attente', 'pending'])->sum('count');
            $series['rdv_cancelled'][] = $monthRdvs->whereIn('statut', ['annulé', 'cancelled'])->sum('count');
            
            // Autres données
            $series['consultations'][] = $consultData->get($key, 0);
            $series['patients'][] = $patientData->get($key, 0);
        }

        return $series;
    }

    /**
     * Obtenir les statistiques patient optimisées
     */
    public static function getPatientDashboardStats($patientId)
    {
        return Cache::remember("patient_dashboard_{$patientId}", 300, function() use ($patientId) {
            $patient = Patient::with(['user:id,name,email'])->find($patientId);
            if (!$patient) return null;

            return [
                'recent_consultations' => $patient->consultations()
                    ->with(['medecin:id,name'])
                    ->latest('date_consultation')
                    ->limit(5)
                    ->get(),
                
                'upcoming_appointments' => $patient->rendezvous()
                    ->with(['medecin:id,name'])
                    ->where('date', '>=', now())
                    ->orderBy('date')
                    ->limit(3)
                    ->get(),
                
                'recent_prescriptions' => $patient->ordonnances()
                    ->latest()
                    ->limit(3)
                    ->get(),
                
                'stats' => [
                    'total_consultations' => $patient->consultations()->count(),
                    'upcoming_rdv' => $patient->rendezvous()
                        ->where('date', '>=', now())
                        ->where('statut', '!=', 'annulé')
                        ->count(),
                    'total_prescriptions' => $patient->ordonnances()->count(),
                ]
            ];
        });
    }

    /**
     * Nettoyer les caches anciens
     */
    public static function cleanOldCache()
    {
        $keys = [
            'admin_dashboard_complete',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Nettoyer les caches de patients (pattern)
        for ($i = 1; $i <= 1000; $i++) {
            Cache::forget("patient_dashboard_{$i}");
        }
    }

    /**
     * Obtenir les données de performance
     */
    public static function getPerformanceData()
    {
        return Cache::remember('performance_overview', 180, function() {
            try {
                return [
                    'database' => [
                        'status' => DB::connection()->getPdo() ? 'OK' : 'ERROR',
                        'tables_count' => DB::select("SHOW TABLES") ? count(DB::select("SHOW TABLES")) : 0
                    ],
                    'cache' => [
                        'driver' => config('cache.default'),
                        'status' => Cache::get('test_key') !== null || Cache::put('test_key', 'test', 1) ? 'OK' : 'ERROR'
                    ],
                    'memory' => [
                        'current' => memory_get_usage(true),
                        'peak' => memory_get_peak_usage(true),
                        'limit' => ini_get('memory_limit')
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'database' => ['status' => 'ERROR', 'error' => $e->getMessage()],
                    'cache' => ['status' => 'ERROR'],
                    'memory' => ['current' => memory_get_usage(true), 'peak' => memory_get_peak_usage(true)]
                ];
            }
        });
    }
}