<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PerformanceMonitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin'); // Seuls les admins peuvent voir les performances
    }
    
    /**
     * Dashboard des performances
     */
    public function index()
    {
        $stats1h = PerformanceMonitor::getStats(1);
        $stats24h = PerformanceMonitor::getStats(24);
        
        // Métriques système
        $systemMetrics = $this->getSystemMetrics();
        
        // Statuts cache et base de données
        $cacheStatus = $this->getCacheStatus();
        $dbStatus = $this->getDatabaseStatus();
        
        return view('admin.performance.index', compact(
            'stats1h', 
            'stats24h', 
            'systemMetrics', 
            'cacheStatus', 
            'dbStatus'
        ));
    }
    
    /**
     * API pour obtenir les statistiques en temps réel
     */
    public function stats(Request $request)
    {
        $hours = $request->get('hours', 1);
        $stats = PerformanceMonitor::getStats($hours);
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'system' => $this->getSystemMetrics()
        ]);
    }
    
    /**
     * Nettoyer le cache de performance
     */
    public function clearCache()
    {
        try {
            // Nettoyer les données de performance
            PerformanceMonitor::cleanup();
            
            // Nettoyer le cache général
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache de performance nettoyé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage : ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtenir les métriques système
     */
    private function getSystemMetrics()
    {
        return [
            'memory_usage' => [
                'current' => $this->formatBytes(memory_get_usage(true)),
                'peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'limit' => ini_get('memory_limit')
            ],
            'opcache' => function_exists('opcache_get_status') ? opcache_get_status() : null,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->format('Y-m-d H:i:s'),
            'uptime' => $this->getServerUptime()
        ];
    }
    
    /**
     * Obtenir le statut du cache
     */
    private function getCacheStatus()
    {
        try {
            $driver = config('cache.default');
            $testKey = 'performance_test_' . time();
            
            $start = microtime(true);
            Cache::put($testKey, 'test', 1);
            $writeTime = (microtime(true) - $start) * 1000;
            
            $start = microtime(true);
            $value = Cache::get($testKey);
            $readTime = (microtime(true) - $start) * 1000;
            
            Cache::forget($testKey);
            
            return [
                'driver' => $driver,
                'status' => $value === 'test' ? 'OK' : 'ERROR',
                'write_time' => round($writeTime, 2) . 'ms',
                'read_time' => round($readTime, 2) . 'ms'
            ];
            
        } catch (\Exception $e) {
            return [
                'driver' => config('cache.default'),
                'status' => 'ERROR',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtenir le statut de la base de données
     */
    private function getDatabaseStatus()
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $start) * 1000;
            
            // Obtenir des stats sur les connexions
            $connections = DB::select("SHOW STATUS LIKE 'Threads_connected'");
            $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");
            
            return [
                'status' => 'OK',
                'response_time' => round($responseTime, 2) . 'ms',
                'driver' => config('database.default'),
                'connections' => $connections[0]->Value ?? 'N/A',
                'max_connections' => $maxConnections[0]->Value ?? 'N/A'
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'ERROR',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getServerUptime()
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = file_get_contents('/proc/uptime');
            if ($uptime) {
                $seconds = (int) explode(' ', $uptime)[0];
                return gmdate('H:i:s', $seconds);
            }
        }
        return 'N/A';
    }
}