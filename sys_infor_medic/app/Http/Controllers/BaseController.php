<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    /**
     * Cache TTL par défaut
     */
    protected $defaultCacheTTL = 300; // 5 minutes

    /**
     * Règles de validation communes
     */
    protected $commonValidationRules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
    ];

    /**
     * Réponse JSON standardisée
     */
    protected function jsonResponse($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => $status >= 200 && $status < 300,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], $status);
    }

    /**
     * Réponse d'erreur standardisée
     */
    protected function errorResponse($message = 'Error', $status = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toISOString()
        ], $status);
    }

    /**
     * Validation avec réponse JSON
     */
    protected function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return $this->errorResponse('Données invalides', 422, $validator->errors());
        }
        
        return null;
    }

    /**
     * Cache intelligent avec gestion d'erreur
     */
    protected function cacheRemember($key, $callback, $ttl = null)
    {
        try {
            $ttl = $ttl ?? $this->defaultCacheTTL;
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Erreur cache pour clé {$key}: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Pagination optimisée
     */
    protected function optimizedPaginate($query, $perPage = 15, $cacheKey = null, $cacheTTL = 300)
    {
        if ($cacheKey && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = $query->paginate($perPage);
        
        if ($cacheKey) {
            Cache::put($cacheKey, $result, $cacheTTL);
        }
        
        return $result;
    }

    /**
     * Audit log sécurisé
     */
    protected function logAction($action, $model = null, $details = [])
    {
        if (class_exists('App\Models\AuditLog')) {
            try {
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => $action,
                    'model_type' => $model ? get_class($model) : null,
                    'model_id' => $model ? $model->id : null,
                    'details' => json_encode($details),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $e) {
                Log::error("Erreur audit log: " . $e->getMessage());
            }
        }
    }

    /**
     * Validation des permissions
     */
    protected function checkPermission($permission)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Non authentifié');
        }

        if (!$user->hasRole('admin') && !$user->can($permission)) {
            abort(403, 'Permission insuffisante');
        }

        return true;
    }

    /**
     * Statistiques rapides avec cache
     */
    protected function getQuickStats($models = [])
    {
        return $this->cacheRemember('quick_stats_' . auth()->id(), function() use ($models) {
            $stats = [];
            
            foreach ($models as $alias => $modelClass) {
                try {
                    if (class_exists($modelClass)) {
                        $stats[$alias] = $modelClass::count();
                    }
                } catch (\Exception $e) {
                    $stats[$alias] = 0;
                }
            }
            
            return $stats;
        }, 600); // 10 minutes
    }

    /**
     * Nettoyage sécurisé des entrées
     */
    protected function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gestion d'erreurs avec logging
     */
    protected function handleException(\Exception $e, $context = [])
    {
        Log::error($e->getMessage(), array_merge([
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
            'url' => request()->url(),
        ], $context));

        if (config('app.debug')) {
            throw $e;
        }

        return $this->errorResponse('Une erreur est survenue', 500);
    }
}