<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifie si l'utilisateur est connecté
        if (!$user) {
            return redirect()->route('login');
        }

        // Vérifie si le compte est actif
        if (!$user->is_active || $user->is_blocked) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte est désactivé ou bloqué.',
            ]);
        }

        // Vérifie les rôles autorisés pour l'admin
        $allowedRoles = ['super_admin', 'admin', 'manager', 'chef', 'waiter'];
        
        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}