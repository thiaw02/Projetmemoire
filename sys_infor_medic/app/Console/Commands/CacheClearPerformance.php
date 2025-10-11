<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CacheClearPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:cache-clear {--selective : Clear only performance caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear performance caches intelligently';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Nettoyage des caches de performance...');
        
        if ($this->option('selective')) {
            $this->clearSelectiveCaches();
        } else {
            $this->clearAllPerformanceCaches();
        }
        
        $this->info('✅ Caches nettoyés avec succès!');
        $this->info('💡 Conseil: Utilisez --selective pour ne vider que les caches de performance');
    }
    
    private function clearSelectiveCaches()
    {
        $patterns = [
            'admin_dashboard_*',
            'admin_monthly_*',
            'admin_kpis_*',
            'patient_dashboard_*',
            'medecins_list',
            'user_*',
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
            $this->line("🗑️  Nettoyage: {$pattern}");
        }
        
        // Nettoyer les caches de vues
        Artisan::call('view:clear');
        $this->line('🗑️  Caches de vues vidés');
        
        // Optimiser les caches
        Artisan::call('config:cache');
        $this->line('⚡ Configuration mise en cache');
    }
    
    private function clearAllPerformanceCaches()
    {
        // Nettoyer tout le cache
        Cache::flush();
        $this->line('🗑️  Tous les caches vidés');
        
        // Recréer les caches optimisés
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        
        $this->line('⚡ Caches de performance recréés');
    }
}
