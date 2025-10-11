<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupPerformanceMonitoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:setup {--force : Forcer l\'écrasement des fichiers existants}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Configurer automatiquement le monitoring des performances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Configuration du monitoring des performances...');
        $this->newLine();

        // 1. Enregistrer le middleware
        $this->registerMiddleware();

        // 2. Ajouter les routes
        $this->addRoutes();

        // 3. Mettre à jour les tâches planifiées
        $this->updateSchedule();

        // 4. Configuration du cache (si nécessaire)
        $this->checkCacheConfiguration();

        $this->newLine();
        $this->info('✅ Configuration terminée !');
        $this->info('📊 Vous pouvez maintenant accéder au monitoring à : /admin/performance');
        $this->newLine();
        
        $this->comment('Prochaines étapes recommandées :');
        $this->line('1. Configurer Redis pour les performances optimales');
        $this->line('2. Activer le middleware sur les routes importantes');
        $this->line('3. Définir des alertes personnalisées');
    }

    private function registerMiddleware()
    {
        $this->info('📝 Enregistrement du middleware...');

        $kernelPath = app_path('Http/Kernel.php');
        
        if (!File::exists($kernelPath)) {
            $this->error('Fichier Kernel.php non trouvé !');
            return;
        }

        $kernelContent = File::get($kernelPath);
        
        // Vérifier si le middleware est déjà enregistré
        if (strpos($kernelContent, 'monitor.performance') !== false) {
            $this->warn('Middleware déjà enregistré dans Kernel.php');
            return;
        }

        // Ajouter le middleware aux middleware de route
        $middlewareToAdd = "        'monitor.performance' => \\App\\Http\\Middleware\\MonitorPerformance::class,";
        
        $pattern = '/(protected \$routeMiddleware = \[.*?)(\s+\];)/s';
        
        if (preg_match($pattern, $kernelContent)) {
            $kernelContent = preg_replace(
                $pattern,
                '$1' . "\n" . $middlewareToAdd . '$2',
                $kernelContent
            );
            
            File::put($kernelPath, $kernelContent);
            $this->info('✅ Middleware ajouté au Kernel.php');
        } else {
            $this->warn('⚠️  Impossible d\'ajouter automatiquement le middleware. Ajoutez manuellement :');
            $this->line($middlewareToAdd);
        }
    }

    private function addRoutes()
    {
        $this->info('🛣️  Ajout des routes de performance...');

        $webRoutesPath = base_path('routes/web.php');
        $routesContent = File::get($webRoutesPath);

        // Vérifier si les routes sont déjà ajoutées
        if (strpos($routesContent, '/admin/performance') !== false) {
            $this->warn('Routes de performance déjà présentes');
            return;
        }

        $routesToAdd = "
// Routes de monitoring des performances (Admin uniquement)
Route::middleware(['auth', 'role:admin'])->prefix('admin/performance')->group(function () {
    Route::get('/', [App\Http\Controllers\PerformanceController::class, 'index'])->name('admin.performance.index');
    Route::get('/stats', [App\Http\Controllers\PerformanceController::class, 'stats'])->name('admin.performance.stats');
    Route::post('/clear-cache', [App\Http\Controllers\PerformanceController::class, 'clearCache'])->name('admin.performance.clear-cache');
});
";

        File::append($webRoutesPath, $routesToAdd);
        $this->info('✅ Routes ajoutées à routes/web.php');
    }

    private function updateSchedule()
    {
        $this->info('⏰ Mise à jour des tâches planifiées...');

        $consolePath = app_path('Console/Kernel.php');
        
        if (!File::exists($consolePath)) {
            $this->error('Fichier Console/Kernel.php non trouvé !');
            return;
        }

        $consoleContent = File::get($consolePath);

        // Vérifier si la tâche est déjà planifiée
        if (strpos($consoleContent, 'performance:cleanup') !== false) {
            $this->warn('Tâche de nettoyage déjà planifiée');
            return;
        }

        // Ajouter la tâche de nettoyage quotidien
        $taskToAdd = "\n        // Nettoyage automatique des données de performance\n        \$schedule->call(function () {\n            \\App\\Services\\PerformanceMonitor::cleanup();\n        })->dailyAt('02:00')->name('performance-cleanup');";

        $pattern = '/(protected function schedule\(Schedule \$schedule\).*?\{)(.*?)(\s+\})/s';
        
        if (preg_match($pattern, $consoleContent)) {
            $consoleContent = preg_replace(
                $pattern,
                '$1$2' . $taskToAdd . '$3',
                $consoleContent
            );
            
            File::put($consolePath, $consoleContent);
            $this->info('✅ Tâche de nettoyage ajoutée au scheduler');
        } else {
            $this->warn('⚠️  Impossible d\'ajouter automatiquement la tâche. Ajoutez manuellement dans Console/Kernel.php :');
            $this->line($taskToAdd);
        }
    }

    private function checkCacheConfiguration()
    {
        $this->info('🗄️  Vérification de la configuration du cache...');

        $cacheDriver = config('cache.default');
        
        if ($cacheDriver === 'file' || $cacheDriver === 'database') {
            $this->warn('⚠️  Driver de cache actuel : ' . $cacheDriver);
            $this->comment('Pour de meilleures performances, considérez Redis ou Memcached');
            
            if ($this->confirm('Voulez-vous configurer Redis maintenant ?', false)) {
                $this->configureRedis();
            }
        } else {
            $this->info('✅ Configuration du cache OK : ' . $cacheDriver);
        }
    }

    private function configureRedis()
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            $this->error('Fichier .env non trouvé !');
            return;
        }

        $envContent = File::get($envPath);

        // Mettre à jour les paramètres de cache pour Redis
        $updates = [
            'CACHE_DRIVER=file' => 'CACHE_DRIVER=redis',
            'CACHE_DRIVER=database' => 'CACHE_DRIVER=redis',
            'SESSION_DRIVER=file' => 'SESSION_DRIVER=redis',
            'SESSION_DRIVER=database' => 'SESSION_DRIVER=redis',
        ];

        foreach ($updates as $old => $new) {
            if (strpos($envContent, $old) !== false) {
                $envContent = str_replace($old, $new, $envContent);
            }
        }

        // Ajouter la configuration Redis si elle n'existe pas
        if (strpos($envContent, 'REDIS_HOST=') === false) {
            $redisConfig = "\n# Redis Configuration\nREDIS_HOST=127.0.0.1\nREDIS_PASSWORD=null\nREDIS_PORT=6379\n";
            $envContent .= $redisConfig;
        }

        File::put($envPath, $envContent);
        $this->info('✅ Configuration Redis ajoutée au .env');
        $this->comment('N\'oubliez pas d\'installer et démarrer Redis sur votre serveur !');
    }
}