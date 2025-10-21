<?php

namespace App\Services;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AuditService
{
    /**
     * Durée de cache pour les statistiques (en minutes)
     */
    private const STATS_CACHE_TTL = 15;

    /**
     * Obtenir les logs d'audit avec filtres optimisés
     */
    public function getAuditLogs(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = AuditLog::query()
            ->withUser()
            ->latest('created_at');

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Appliquer les filtres à la requête
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // Filtre par utilisateur
        if (!empty($filters['user_id'])) {
            $query->byUser((int) $filters['user_id']);
        }

        // Filtre par type d'événement
        if (!empty($filters['event_type'])) {
            $query->eventType($filters['event_type']);
        }

        // Filtre par sévérité
        if (!empty($filters['severity'])) {
            $query->severity($filters['severity']);
        }

        // Filtre par IP
        if (!empty($filters['ip_address'])) {
            $query->fromIp($filters['ip_address']);
        }

        // Filtre par période
        if (!empty($filters['date_from'])) {
            $dateFrom = Carbon::parse($filters['date_from'])->startOfDay();
            $query->where('created_at', '>=', $dateFrom);
        }

        if (!empty($filters['date_to'])) {
            $dateTo = Carbon::parse($filters['date_to'])->endOfDay();
            $query->where('created_at', '<=', $dateTo);
        }

        // Filtre par action/recherche globale
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par entité auditée
        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }
    }

    /**
     * Obtenir les statistiques KPI avec cache
     */
    public function getKpiStats(): array
    {
        return Cache::remember('audit_kpi_stats', self::STATS_CACHE_TTL, function () {
            $now = Carbon::now();
            
            return [
                'total_today' => AuditLog::whereDate('created_at', $now)->count(),
                'total_week' => AuditLog::where('created_at', '>=', $now->copy()->subWeek())->count(),
                'critical_last_24h' => AuditLog::severity('critical')
                    ->where('created_at', '>=', $now->copy()->subDay())
                    ->count(),
                'unique_users_today' => AuditLog::whereDate('created_at', $now)
                    ->distinct('user_id')
                    ->count(),
                'most_active_user' => $this->getMostActiveUser(),
                'distribution_by_severity' => $this->getSeverityDistribution(),
                'top_actions' => $this->getTopActions(),
            ];
        });
    }

    /**
     * Obtenir l'utilisateur le plus actif aujourd'hui
     */
    private function getMostActiveUser(): array
    {
        $result = AuditLog::with('user:id,name')
            ->whereDate('created_at', Carbon::today())
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->first();

        if (!$result || !$result->user) {
            return ['name' => 'Aucun', 'count' => 0];
        }

        return [
            'name' => $result->user->name,
            'count' => $result->count
        ];
    }

    /**
     * Obtenir la distribution par sévérité
     */
    private function getSeverityDistribution(): array
    {
        return AuditLog::selectRaw('severity, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('severity')
            ->orderBy('count', 'desc')
            ->pluck('count', 'severity')
            ->toArray();
    }

    /**
     * Obtenir les actions les plus fréquentes
     */
    private function getTopActions(int $limit = 5): array
    {
        return AuditLog::selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Obtenir les données pour le graphique temporel
     */
    public function getTimelineData(int $days = 7): array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days - 1);

        $data = AuditLog::selectRaw('DATE(created_at) as date, severity, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date', 'severity')
            ->orderBy('date')
            ->get();

        // Préparer les données pour le graphique
        $timeline = [];
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->addDay()
        );

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $timeline[$dateStr] = [
                'date' => $dateStr,
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'critical' => 0,
            ];
        }

        // Remplir avec les données réelles
        foreach ($data as $item) {
            $timeline[$item->date][$item->severity] = $item->count;
        }

        return array_values($timeline);
    }

    /**
     * Obtenir les logs récents d'un utilisateur spécifique
     */
    public function getUserRecentLogs(int $userId, int $limit = 10): array
    {
        return AuditLog::byUser($userId)
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'event_type' => $log->event_type,
                    'severity' => $log->severity,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                    'ip_address' => $log->ip_address,
                ];
            })
            ->toArray();
    }

    /**
     * Obtenir les statistiques d'activité par IP
     */
    public function getIpActivityStats(int $hours = 24): array
    {
        return AuditLog::selectRaw('ip_address, COUNT(*) as count, MAX(created_at) as last_activity')
            ->where('created_at', '>=', Carbon::now()->subHours($hours))
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'ip' => $item->ip_address,
                    'count' => $item->count,
                    'last_activity' => Carbon::parse($item->last_activity)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Détecter les activités suspectes
     */
    public function detectSuspiciousActivity(): array
    {
        $suspicious = [];

        // Activité excessive d'une IP
        $suspiciousIps = AuditLog::selectRaw('ip_address, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->having('count', '>', 100) // Plus de 100 actions en 1 heure
            ->get();

        foreach ($suspiciousIps as $ip) {
            $suspicious[] = [
                'type' => 'high_frequency_ip',
                'description' => "IP {$ip->ip_address} avec {$ip->count} actions en 1 heure",
                'severity' => 'high',
                'details' => ['ip' => $ip->ip_address, 'count' => $ip->count],
            ];
        }

        // Actions critiques multiples
        $criticalActions = AuditLog::severity('critical')
            ->where('created_at', '>=', Carbon::now()->subHours(2))
            ->count();

        if ($criticalActions > 5) {
            $suspicious[] = [
                'type' => 'multiple_critical_actions',
                'description' => "{$criticalActions} actions critiques en 2 heures",
                'severity' => 'critical',
                'details' => ['count' => $criticalActions],
            ];
        }

        return $suspicious;
    }

    /**
     * Exporter les logs d'audit au format CSV
     */
    public function exportToCsv(array $filters = []): string
    {
        $query = AuditLog::query()->withUser()->latest('created_at');
        $this->applyFilters($query, $filters);

        $logs = $query->limit(10000)->get(); // Limiter pour éviter les problèmes de mémoire

        $csvData = [];
        $csvData[] = [
            'ID', 'Date/Heure', 'Utilisateur', 'Action', 'Type', 'Sévérité', 
            'Entité', 'IP', 'Changements'
        ];

        foreach ($logs as $log) {
            $changes = $log->changes ? json_encode($log->changes) : '';
            $csvData[] = [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'Système',
                $log->action,
                $log->event_type,
                $log->severity,
                $log->auditable_type,
                $log->ip_address,
                $changes,
            ];
        }

        $filename = storage_path('app/exports/audit_logs_' . date('Y-m-d_H-i-s') . '.csv');
        
        // Créer le dossier si nécessaire
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        $file = fopen($filename, 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);

        return $filename;
    }

    /**
     * Vider le cache des statistiques
     */
    public function clearStatsCache(): void
    {
        Cache::forget('audit_kpi_stats');
    }

    /**
     * Obtenir un log d'audit avec tous ses détails
     */
    public function getAuditLogDetails(int $logId): ?AuditLog
    {
        return AuditLog::with(['user', 'auditable'])
            ->find($logId);
    }
}