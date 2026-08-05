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

        // super_admin et admin : accès complet, aucune restriction supplémentaire
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';

        // La gestion globale des clients (pas liée à un restaurant précis) reste
        // réservée aux administrateurs, même pour un manager.
        if (str_starts_with($routeName, 'admin.users.') || $routeName === 'admin.users') {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        // Le personnel de cuisine et de salle n'a accès qu'aux sections
        // pertinentes à son rôle : tableau de bord, commandes, et pour les
        // serveurs, les réservations de table.
        if ($user->hasAnyRole(['chef', 'waiter'])) {
            $allowedPrefixes = ['admin.dashboard', 'admin.orders.'];

            if ($user->hasRole('waiter')) {
                $allowedPrefixes[] = 'admin.reservations.';
            }

            $isAllowed = collect($allowedPrefixes)->contains(
                fn ($prefix) => $routeName === rtrim($prefix, '.') || str_starts_with($routeName, $prefix)
            );

            if (!$isAllowed) {
                abort(403, 'Cette section n\'est pas accessible à votre rôle.');
            }
        }

        return $next($request);
    }
}