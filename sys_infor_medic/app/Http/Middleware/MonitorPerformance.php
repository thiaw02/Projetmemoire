<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PerformanceMonitor;
use Illuminate\Support\Facades\Log;

class MonitorPerformance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Démarrer le monitoring
        PerformanceMonitor::start();
        
        $response = $next($request);
        
        // Terminer le monitoring et enregistrer les métriques
        try {
            $performanceData = PerformanceMonitor::end($request->route()?->getName());
            
            // Ajouter les headers de performance pour le debug
            if (config('app.debug')) {
                $response->headers->set('X-Execution-Time', $performanceData['execution_time'] . 'ms');
                $response->headers->set('X-Memory-Usage', $performanceData['memory_usage']);
                $response->headers->set('X-Query-Count', $performanceData['queries_count']);
            }
            
            // Logger les alertes de performance
            if ($performanceData['execution_time'] > 3000) { // > 3 secondes
                Log::alert('Performance critique détectée', [
                    'route' => $performanceData['route'],
                    'execution_time' => $performanceData['execution_time'],
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'user_id' => auth()->id(),
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur dans le monitoring de performance', [
                'error' => $e->getMessage(),
                'route' => $request->route()?->getName(),
            ]);
        }
        
        return $response;
    }
}