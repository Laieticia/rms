<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ajoutez cet import

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un des rôles autorisés
        foreach ($roles as $role) {
           if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Si aucun rôle ne correspond
        abort(403, 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
    }
}
