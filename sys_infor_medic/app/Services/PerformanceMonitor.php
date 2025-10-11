<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    private static $startTime;
    private static $queries = [];
    
    public static function start()
    {
        self::$startTime = microtime(true);
        
        // Activer le logging des requêtes
        DB::enableQueryLog();
    }
    
    public static function end($route = null)
    {
        $executionTime = microtime(true) - self::$startTime;
        $queries = DB::getQueryLog();
        $memoryUsage = memory_get_peak_usage(true);
        
        $performanceData = [
            'route' => $route ?: request()->route()?->getName(),
            'execution_time' => round($executionTime * 1000, 2), // en ms
            'memory_usage' => self::formatBytes($memoryUsage),
            'queries_count' => count($queries),
            'slow_queries' => self::getSlowQueries($queries),
            'timestamp' => now()->toISOString(),
        ];
        
        // Logger les performances lentes
        if ($executionTime > 2.0) { // > 2 secondes
            Log::warning('Performance lente détectée', $performanceData);
        }
        
        // Sauvegarder en cache pour le monitoring
        self::savePerformanceData($performanceData);
        
        return $performanceData;
    }
    
    private static function getSlowQueries($queries)
    {
        return array_filter($queries, function($query) {
            return $query['time'] > 100; // > 100ms
        });
    }
    
    private static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private static function savePerformanceData($data)
    {
        $key = 'performance_' . now()->format('Y-m-d-H');
        
        $existing = Cache::get($key, []);
        $existing[] = $data;
        
        // Garder seulement les 100 dernières mesures par heure
        if (count($existing) > 100) {
            $existing = array_slice($existing, -100);
        }
        
        Cache::put($key, $existing, 3600); // 1 heure
    }
    
    /**
     * Obtenir les statistiques de performance récentes
     */
    public static function getStats($hours = 1)
    {
        $stats = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $key = 'performance_' . now()->subHours($i)->format('Y-m-d-H');
            $hourData = Cache::get($key, []);
            $stats = array_merge($stats, $hourData);
        }
        
        if (empty($stats)) {
            return [
                'avg_execution_time' => 0,
                'total_requests' => 0,
                'slow_requests' => 0,
                'avg_queries' => 0,
                'top_slow_routes' => []
            ];
        }
        
        $totalRequests = count($stats);
        $slowRequests = count(array_filter($stats, fn($s) => $s['execution_time'] > 2000));
        
        $avgExecution = array_sum(array_column($stats, 'execution_time')) / $totalRequests;
        $avgQueries = array_sum(array_column($stats, 'queries_count')) / $totalRequests;
        
        // Top des routes lentes
        $routeStats = [];
        foreach ($stats as $stat) {
            $route = $stat['route'] ?: 'unknown';
            if (!isset($routeStats[$route])) {
                $routeStats[$route] = [];
            }
            $routeStats[$route][] = $stat['execution_time'];
        }
        
        $topSlowRoutes = [];
        foreach ($routeStats as $route => $times) {
            $topSlowRoutes[$route] = array_sum($times) / count($times);
        }
        
        arsort($topSlowRoutes);
        $topSlowRoutes = array_slice($topSlowRoutes, 0, 5, true);
        
        return [
            'avg_execution_time' => round($avgExecution, 2),
            'total_requests' => $totalRequests,
            'slow_requests' => $slowRequests,
            'slow_requests_percentage' => round(($slowRequests / $totalRequests) * 100, 1),
            'avg_queries' => round($avgQueries, 1),
            'top_slow_routes' => $topSlowRoutes
        ];
    }
    
    /**
     * Nettoyer les anciennes données de performance
     */
    public static function cleanup()
    {
        $keys = [];
        
        // Nettoyer les données de plus de 24h
        for ($i = 24; $i <= 72; $i++) {
            $key = 'performance_' . now()->subHours($i)->format('Y-m-d-H');
            Cache::forget($key);
        }
        
        Log::info('Nettoyage des données de performance terminé');
    }
}