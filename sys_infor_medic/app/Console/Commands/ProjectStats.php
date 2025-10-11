<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\{File, DB};
use App\Models\{User, Patient, Rendez_vous, Consultations};

class ProjectStats extends Command
{
    protected $signature = 'project:stats';
    protected $description = 'Afficher les statistiques complètes du projet';

    public function handle()
    {
        $this->info('📊 Statistiques du projet SMART-HEALTH');
        $this->line('==========================================');
        $this->newLine();

        $this->showFileStats();
        $this->newLine();
        $this->showDatabaseStats();
        $this->newLine();
        $this->showPerformanceStats();
    }

    private function showFileStats()
    {
        $this->info('📁 Structure du projet :');

        $stats = [
            'Controllers' => $this->countFiles('app/Http/Controllers', '*.php'),
            'Models' => $this->countFiles('app/Models', '*.php'),
            'Services' => $this->countFiles('app/Services', '*.php'),
            'Middlewares' => $this->countFiles('app/Http/Middleware', '*.php'),
            'Commands' => $this->countFiles('app/Console/Commands', '*.php'),
            'Migrations' => $this->countFiles('database/migrations', '*.php'),
            'Views' => $this->countFiles('resources/views', '*.blade.php'),
            'Components' => $this->countFiles('resources/views/components', '*.blade.php'),
        ];

        foreach ($stats as $type => $count) {
            $this->line("  {$type}: {$count} fichiers");
        }

        // Taille totale
        $totalSize = $this->getDirectorySize(base_path());
        $this->line("  Taille totale: " . $this->formatSize($totalSize));
    }

    private function showDatabaseStats()
    {
        $this->info('🗄️ Base de données :');

        try {
            $stats = [
                'Utilisateurs' => User::count(),
                'Patients' => Patient::count(),
                'Rendez-vous' => Rendez_vous::count(),
                'Consultations' => Consultations::count(),
            ];

            foreach ($stats as $table => $count) {
                $this->line("  {$table}: {$count}");
            }

            // Tables système
            $tables = DB::select('SHOW TABLES');
            $this->line("  Total tables: " . count($tables));

        } catch (\Exception $e) {
            $this->error('  Erreur connexion base de données: ' . $e->getMessage());
        }
    }

    private function showPerformanceStats()
    {
        $this->info('⚡ Performance :');

        // Vérifier l'état du cache
        $cacheDriver = config('cache.default');
        $this->line("  Driver cache: {$cacheDriver}");

        // Vérifier la configuration
        $appEnv = config('app.env');
        $appDebug = config('app.debug') ? 'Activé' : 'Désactivé';
        
        $this->line("  Environnement: {$appEnv}");
        $this->line("  Mode debug: {$appDebug}");

        // Mémoire PHP
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = $this->formatSize(memory_get_usage(true));
        
        $this->line("  Limite mémoire: {$memoryLimit}");
        $this->line("  Mémoire utilisée: {$memoryUsage}");

        // Vérifier OpCache
        $opcacheEnabled = function_exists('opcache_get_status') && opcache_get_status() ? 'Activé' : 'Désactivé';
        $this->line("  OpCache: {$opcacheEnabled}");
    }

    private function countFiles($directory, $pattern = '*.php')
    {
        $path = base_path($directory);
        if (!File::exists($path)) {
            return 0;
        }

        return count(File::glob("{$path}/{$pattern}"));
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        
        if (!File::exists($directory)) {
            return $size;
        }

        try {
            foreach (File::allFiles($directory) as $file) {
                try {
                    $size += $file->getSize();
                } catch (\Exception $e) {
                    // Ignorer les fichiers inaccessibles (sessions, cache temporaires)
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Si l'accès au répertoire échoue
            return 0;
        }

        return $size;
    }

    private function formatSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}