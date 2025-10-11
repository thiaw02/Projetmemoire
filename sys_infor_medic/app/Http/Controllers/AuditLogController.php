<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditLogController extends Controller
{
    public function __construct(
        private AuditService $auditService
    ) {}

    /**
     * Afficher la liste des logs d'audit avec filtres avancés
     */
    public function index(Request $request)
    {
        // Validation des filtres
        $validated = $request->validate([
            'search' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|integer|exists:users,id',
            'event_type' => 'sometimes|string|in:' . implode(',', array_keys(AuditLog::EVENT_TYPES)),
            'severity' => 'sometimes|string|in:' . implode(',', array_keys(AuditLog::SEVERITIES)),
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'ip_address' => 'sometimes|ip',
            'auditable_type' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:10|max:100',
        ]);

        $perPage = $validated['per_page'] ?? 25;
        
        // Obtenir les logs avec filtres
        $logs = $this->auditService->getAuditLogs($validated, $perPage);
        
        // Obtenir les statistiques KPI
        $kpiStats = $this->auditService->getKpiStats();
        
        // Obtenir les données pour les filtres
        $users = User::orderBy('name')->get(['id', 'name']);
        $eventTypes = AuditLog::EVENT_TYPES;
        $severities = AuditLog::SEVERITIES;
        
        // Obtenir les types d'entités uniques
        $auditableTypes = AuditLog::select('auditable_type')
            ->distinct()
            ->whereNotNull('auditable_type')
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });
        
        // Détecter les activités suspectes
        $suspiciousActivities = $this->auditService->detectSuspiciousActivity();
        
        return view('admin.audit_logs.index', compact(
            'logs',
            'users',
            'eventTypes',
            'severities',
            'auditableTypes',
            'kpiStats',
            'suspiciousActivities',
            'validated'
        ));
    }

    /**
     * Afficher les détails d'un log d'audit
     */
    public function show(int $id)
    {
        $log = $this->auditService->getAuditLogDetails($id);
        
        if (!$log) {
            abort(404, 'Log d\'audit non trouvé');
        }
        
        return view('admin.audit_logs.show', compact('log'));
    }

    /**
     * Exporter les logs d'audit au format CSV
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'search' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|integer|exists:users,id',
            'event_type' => 'sometimes|string|in:' . implode(',', array_keys(AuditLog::EVENT_TYPES)),
            'severity' => 'sometimes|string|in:' . implode(',', array_keys(AuditLog::SEVERITIES)),
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'ip_address' => 'sometimes|ip',
            'auditable_type' => 'sometimes|string',
        ]);

        try {
            $filename = $this->auditService->exportToCsv($validated);
            
            // Créer un log d'audit pour l'export
            AuditLog::createLog(
                'Export des logs d\'audit',
                'export',
                null,
                null,
                'medium',
                ['filters' => $validated],
                90
            );
            
            return Response::download($filename, basename($filename), [
                'Content-Type' => 'text/csv',
            ])->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les données pour le graphique temporel (API)
     */
    public function timelineData(Request $request)
    {
        $days = $request->get('days', 7);
        
        if ($days < 1 || $days > 365) {
            $days = 7;
        }
        
        $data = $this->auditService->getTimelineData($days);
        
        return response()->json($data);
    }

    /**
     * Obtenir les statistiques d'activité par IP (API)
     */
    public function ipActivity(Request $request)
    {
        $hours = $request->get('hours', 24);
        
        if ($hours < 1 || $hours > 168) { // Max 1 semaine
            $hours = 24;
        }
        
        $data = $this->auditService->getIpActivityStats($hours);
        
        return response()->json($data);
    }

    /**
     * Obtenir les logs récents d'un utilisateur (API)
     */
    public function userLogs(int $userId, Request $request)
    {
        $limit = $request->get('limit', 10);
        
        if ($limit < 1 || $limit > 50) {
            $limit = 10;
        }
        
        $logs = $this->auditService->getUserRecentLogs($userId, $limit);
        
        return response()->json($logs);
    }

    /**
     * Vider le cache des statistiques
     */
    public function clearCache()
    {
        $this->auditService->clearStatsCache();
        
        return back()->with('success', 'Cache des statistiques vidé');
    }

    /**
     * Nettoyer manuellement les anciens logs
     */
    public function cleanup(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'severity' => 'sometimes|string|in:' . implode(',', array_keys(AuditLog::SEVERITIES)),
        ]);

        try {
            $deletedCount = 0;
            
            // Supprimer les logs expirés
            $deletedCount += AuditLog::expired()->delete();
            
            // Supprimer les anciens logs selon les paramètres
            $cutoffDate = now()->subDays($validated['days']);
            $query = AuditLog::where('created_at', '<', $cutoffDate);
            
            if (isset($validated['severity'])) {
                $query->where('severity', $validated['severity']);
            }
            
            $deletedCount += $query->delete();
            
            // Créer un log d'audit pour le nettoyage
            AuditLog::createLog(
                'Nettoyage manuel des logs d\'audit',
                'delete',
                null,
                null,
                'high',
                [
                    'deleted_count' => $deletedCount,
                    'parameters' => $validated
                ],
                365
            );
            
            $this->auditService->clearStatsCache();
            
            return back()->with('success', "{$deletedCount} logs supprimés avec succès");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du nettoyage: ' . $e->getMessage());
        }
    }
}
