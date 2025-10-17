<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Accès interdit - vous devez être connecté');
        }
        
        $userRole = Auth::user()->role;
        
        // Si aucun rôle spécifié, on laisse passer
        if (empty($roles)) {
            return $next($request);
        }
        
        // Vérifier si l'utilisateur a l'un des rôles autorisés
        if (!in_array($userRole, $roles)) {
            abort(403, 'Accès interdit - rôle insuffisant');
        }

        return $next($request);
    }
}

