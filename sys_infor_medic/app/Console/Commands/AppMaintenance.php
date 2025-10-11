<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Cache, DB, Log, File};
use Carbon\Carbon;

class AppMaintenance extends Command
{
    protected $signature = 'app:maintenance 
                          {--clean-cache : Nettoyer le cache}
                          {--clean-logs : Nettoyer les anciens logs}
                          {--optimize : Optimiser l\'application}
                          {--all : Effectuer toutes les maintenances}';

    protected $description = 'Maintenance unifiée de l\'application (cache, logs, optimisations)';

    public function handle()
    {
        $this->info('🔧 Début de la maintenance de l\'application...');
        $this->newLine();

        $cleanCache = $this->option('clean-cache') || $this->option('all');
        $cleanLogs = $this->option('clean-logs') || $this->option('all');
        $optimize = $this->option('optimize') || $this->option('all');

        if ($cleanCache) {
            $this->cleanCache();
        }

        if ($cleanLogs) {
            $this->cleanLogs();
        }

        if ($optimize) {
            $this->optimizeApp();
        }

        if (!$cleanCache && !$cleanLogs && !$optimize) {
            $this->warn('Aucune option spécifiée. Utilisez --help pour voir les options disponibles.');
            return;
        }

        $this->newLine();
        $this->info('✅ Maintenance terminée !');
    }

    private function cleanCache()
    {
        $this->info('🗑️  Nettoyage du cache...');

        try {
            // Nettoyer le cache Laravel
            Cache::flush();
            $this->line('  ✓ Cache application vidé');

            // Nettoyer les caches spécifiques
            $keys = [
                'admin_dashboard_complete',
                'performance_overview',
                'quick_stats_*',
                'patient_dashboard_*',
                'rdv_status_counts',
            ];

            foreach ($keys as $pattern) {
                if (str_contains($pattern, '*')) {
                    // Pattern matching - simplifié pour les patterns courants
                    $baseKey = str_replace('*', '', $pattern);
                    for ($i = 1; $i <= 1000; $i++) {
                        Cache::forget($baseKey . $i);
                    }
                } else {
                    Cache::forget($pattern);
                }
            }
            $this->line('  ✓ Caches spécifiques nettoyés');

            // Nettoyer les caches de configuration
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->line('  ✓ Caches Laravel système nettoyés');

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur lors du nettoyage du cache: ' . $e->getMessage());
            Log::error('Cache cleanup error: ' . $e->getMessage());
        }
    }

    private function cleanLogs()
    {
        $this->info('📋 Nettoyage des logs...');

        try {
            $logsPath = storage_path('logs');
            $oldLogsDays = config('app_optimized.cleanup.old_logs_days', 30);
            $cutoffDate = Carbon::now()->subDays($oldLogsDays);

            $files = File::files($logsPath);
            $deletedCount = 0;

            foreach ($files as $file) {
                if ($file->getMTime() < $cutoffDate->timestamp) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }

            $this->line("  ✓ {$deletedCount} fichiers de log anciens supprimés");

            // Nettoyer les logs d'audit anciens
            if (class_exists('App\Models\AuditLog')) {
                $auditDays = config('app_optimized.cleanup.old_audit_days', 90);
                $deleted = \App\Models\AuditLog::where('created_at', '<', Carbon::now()->subDays($auditDays))->delete();
                $this->line("  ✓ {$deleted} logs d'audit anciens supprimés");
            }

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur lors du nettoyage des logs: ' . $e->getMessage());
            Log::error('Logs cleanup error: ' . $e->getMessage());
        }
    }

    private function optimizeApp()
    {
        $this->info('⚡ Optimisation de l\'application...');

        try {
            // Optimiser l'autoloader
            $this->call('optimize');
            $this->line('  ✓ Autoloader optimisé');

            // Mettre en cache les configurations
            $this->call('config:cache');
            $this->line('  ✓ Configuration mise en cache');

            // Mettre en cache les routes
            $this->call('route:cache');
            $this->line('  ✓ Routes mises en cache');

            // Mettre en cache les vues
            $this->call('view:cache');
            $this->line('  ✓ Vues mises en cache');

            // Optimiser les assets si la commande existe
            if ($this->hasCommand('assets:optimize')) {
                $this->call('assets:optimize');
                $this->line('  ✓ Assets optimisés');
            }

            // Vérifier l'état de la base de données
            $this->checkDatabase();

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur lors de l\'optimisation: ' . $e->getMessage());
            Log::error('App optimization error: ' . $e->getMessage());
        }
    }

    private function checkDatabase()
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $start) * 1000;
            
            if ($responseTime < 10) {
                $this->line('  ✓ Base de données OK (' . round($responseTime, 2) . 'ms)');
            } else {
                $this->warn('  ⚠️  Base de données lente (' . round($responseTime, 2) . 'ms)');
            }

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur base de données: ' . $e->getMessage());
        }
    }

    private function hasCommand($command)
    {
        try {
            $this->call($command, [], $this->getOutput());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}