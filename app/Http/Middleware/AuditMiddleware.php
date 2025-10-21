<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Actions sensibles qui doivent être auditées
     */
    protected array $sensitiveRoutes = [
        'admin.*',
        'audit.*',
        'users.destroy',
        'users.store',
        'users.update',
        'ordonnances.*',
        'consultations.*',
        'payments.*',
        'backup.*',
        'export.*',
    ];

    /**
     * Méthodes HTTP qui doivent être auditées
     */
    protected array $auditableMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Auditer uniquement si nécessaire
        if ($this->shouldAudit($request, $response)) {
            $this->createAuditLog($request, $response);
        }

        return $response;
    }

    /**
     * Déterminer si la requête doit être auditée
     */
    protected function shouldAudit(Request $request, Response $response): bool
    {
        // Ne pas auditer si pas authentifié
        if (!Auth::check()) {
            return false;
        }

        // Ne pas auditer les requêtes de lecture simples
        if (!in_array($request->method(), $this->auditableMethods)) {
            return false;
        }

        // Ne pas auditer les erreurs 4xx/5xx
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        // Vérifier si la route est sensible
        $routeName = $request->route() ? $request->route()->getName() : null;
        if (!$routeName) {
            return false;
        }

        foreach ($this->sensitiveRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Créer un log d'audit
     */
    protected function createAuditLog(Request $request, Response $response): void
    {
        try {
            $routeName = $request->route()->getName();
            $method = $request->method();
            
            // Déterminer le type d'événement et la sévérité
            [$eventType, $severity] = $this->determineEventTypeAndSeverity($routeName, $method);
            
            // Collecter les métadonnées
            $metadata = [
                'route_name' => $routeName,
                'status_code' => $response->getStatusCode(),
                'response_size' => strlen($response->getContent()),
                'execution_time' => $this->getExecutionTime(),
            ];

            // Ajouter les paramètres sensibles (sans mots de passe)
            $params = $request->except(['password', 'password_confirmation', '_token']);
            if (!empty($params)) {
                $metadata['parameters'] = $params;
            }

            // Créer le log
            AuditLog::createLog(
                $this->generateActionName($routeName, $method),
                $eventType,
                null, // Pas d'entité spécifique pour les actions générales
                null, // Pas de changements spécifiques
                $severity,
                $metadata,
                $this->getExpirationDays($severity)
            );
        } catch (\Exception $e) {
            // Log silencieux en cas d'erreur pour ne pas casser l'application
            \Log::error('Erreur lors de la création du log d\'audit: ' . $e->getMessage());
        }
    }

    /**
     * Déterminer le type d'événement et la sévérité
     */
    protected function determineEventTypeAndSeverity(string $routeName, string $method): array
    {
        // Règles spécifiques par route
        $rules = [
            'admin.*' => ['update', 'high'],
            'users.destroy' => ['delete', 'critical'],
            'users.store' => ['create', 'medium'],
            'users.update' => ['update', 'medium'],
            'backup.*' => ['backup', 'critical'],
            'export.*' => ['export', 'medium'],
            'payments.*' => ['payment', 'high'],
        ];

        foreach ($rules as $pattern => $config) {
            if (fnmatch($pattern, $routeName)) {
                return $config;
            }
        }

        // Règles par méthode HTTP par défaut
        return match ($method) {
            'POST' => ['create', 'low'],
            'PUT', 'PATCH' => ['update', 'low'],
            'DELETE' => ['delete', 'medium'],
            default => ['view', 'low'],
        };
    }

    /**
     * Générer un nom d'action lisible
     */
    protected function generateActionName(string $routeName, string $method): string
    {
        $parts = explode('.', $routeName);
        $resource = $parts[0] ?? 'resource';
        $action = $parts[1] ?? 'action';

        $actionLabels = [
            'index' => 'consultation',
            'show' => 'affichage',
            'store' => 'création',
            'update' => 'modification',
            'destroy' => 'suppression',
        ];

        $actionName = $actionLabels[$action] ?? $action;
        return ucfirst($actionName) . ' ' . str_replace('_', ' ', $resource);
    }

    /**
     * Obtenir le temps d'exécution approximatif
     */
    protected function getExecutionTime(): float
    {
        return defined('LARAVEL_START') ? round((microtime(true) - LARAVEL_START) * 1000, 2) : 0;
    }

    /**
     * Déterminer la durée de conservation selon la sévérité
     */
    protected function getExpirationDays(string $severity): int
    {
        return match ($severity) {
            'critical' => 365, // 1 an
            'high' => 180,     // 6 mois
            'medium' => 90,    // 3 mois
            'low' => 30,       // 1 mois
            default => 30,
        };
    }
}
