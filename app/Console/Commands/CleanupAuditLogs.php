<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:cleanup 
                           {--days= : Nombre de jours à conserver (par défaut: 90)}
                           {--severity= : Nettoyer seulement une sévérité spécifique}
                           {--dry-run : Afficher ce qui sera supprimé sans exécuter}
                           {--force : Forcer la suppression sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les anciens logs d\'audit selon les règles de rétention';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📋 Démarrage du nettoyage des logs d\'audit...');
        
        $defaultDays = $this->option('days') ?? 90;
        $severity = $this->option('severity');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Statistiques avant nettoyage
        $totalLogs = AuditLog::count();
        $this->info("📊 Total des logs actuels: {$totalLogs}");

        // Nettoyage basé sur la date d'expiration
        $expiredCount = $this->cleanupExpiredLogs($dryRun, $force);

        // Nettoyage basé sur l'âge (avec option --days)
        $oldCount = $this->cleanupOldLogs($defaultDays, $severity, $dryRun, $force);

        // Nettoyage des logs orphelins
        $orphanCount = $this->cleanupOrphanLogs($dryRun, $force);

        // Optimisation de la table
        if (!$dryRun && ($expiredCount > 0 || $oldCount > 0 || $orphanCount > 0)) {
            $this->optimizeTable();
        }

        // Résumé
        $totalCleaned = $expiredCount + $oldCount + $orphanCount;
        
        if ($dryRun) {
            $this->warn("🔍 Mode simulation: {$totalCleaned} logs seraient supprimés");
        } else {
            $this->info("✅ Nettoyage terminé: {$totalCleaned} logs supprimés");
        }

        $remainingLogs = AuditLog::count();
        $this->info("📊 Logs restants: {$remainingLogs}");
        
        return Command::SUCCESS;
    }

    /**
     * Nettoyer les logs expirés
     */
    protected function cleanupExpiredLogs(bool $dryRun, bool $force): int
    {
        $query = AuditLog::whereNotNull('expires_at')
                         ->where('expires_at', '<=', Carbon::now());
        
        $count = $query->count();
        
        if ($count === 0) {
            $this->line('ℹ️ Aucun log expiré trouvé');
            return 0;
        }

        $this->line("🗑️ Logs expirés à supprimer: {$count}");
        
        if ($dryRun) {
            return $count;
        }

        if (!$force && !$this->confirm("Supprimer {$count} logs expirés?")) {
            return 0;
        }

        return $query->delete();
    }

    /**
     * Nettoyer les anciens logs
     */
    protected function cleanupOldLogs(int $days, ?string $severity, bool $dryRun, bool $force): int
    {
        $cutoffDate = Carbon::now()->subDays($days);
        
        $query = AuditLog::where('created_at', '<', $cutoffDate);
        
        if ($severity) {
            $query->where('severity', $severity);
        }
        
        $count = $query->count();
        
        if ($count === 0) {
            $severityText = $severity ? " (sévérité: {$severity})" : '';
            $this->line("ℹ️ Aucun log ancien trouvé (> {$days} jours){$severityText}");
            return 0;
        }

        $severityText = $severity ? " de sévérité {$severity}" : '';
        $this->line("🗑️ Anciens logs{$severityText} à supprimer (> {$days} jours): {$count}");
        
        if ($dryRun) {
            return $count;
        }

        if (!$force && !$this->confirm("Supprimer {$count} anciens logs{$severityText}?")) {
            return 0;
        }

        return $query->delete();
    }

    /**
     * Nettoyer les logs orphelins (référençant des entités supprimées)
     */
    protected function cleanupOrphanLogs(bool $dryRun, bool $force): int
    {
        $orphanCount = 0;
        
        // Vérifier les logs liés aux utilisateurs supprimés
        $orphanUserLogs = AuditLog::whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereRaw('users.id = audit_logs.user_id');
            })->count();

        if ($orphanUserLogs > 0) {
            $this->line("🔗 Logs orphelins (utilisateurs): {$orphanUserLogs}");
            $orphanCount += $orphanUserLogs;
            
            if (!$dryRun) {
                if ($force || $this->confirm("Supprimer {$orphanUserLogs} logs d'utilisateurs supprimés?")) {
                    AuditLog::whereNotNull('user_id')
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                                  ->from('users')
                                  ->whereRaw('users.id = audit_logs.user_id');
                        })->delete();
                }
            }
        }

        return $orphanCount;
    }

    /**
     * Optimiser la table après suppression
     */
    protected function optimizeTable(): void
    {
        $this->info('🚀 Optimisation de la table audit_logs...');
        
        try {
            DB::statement('OPTIMIZE TABLE audit_logs');
            $this->info('✅ Table optimisée');
        } catch (\Exception $e) {
            $this->warn('⚠️ Impossible d\'optimiser la table: ' . $e->getMessage());
        }
    }

    /**
     * Afficher les statistiques détaillées
     */
    protected function showDetailedStats(): void
    {
        $this->info('📊 Statistiques détaillées:');
        
        // Par sévérité
        $severityStats = AuditLog::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->orderBy('count', 'desc')
            ->get();
            
        $this->table(
            ['Sévérité', 'Nombre'],
            $severityStats->map(fn($stat) => [$stat->severity, $stat->count])
        );
        
        // Par type d'événement
        $eventStats = AuditLog::selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
            
        $this->table(
            ['Type d\'événement', 'Nombre'],
            $eventStats->map(fn($stat) => [$stat->event_type, $stat->count])
        );
    }
}
