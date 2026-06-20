<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs (clients uniquement)
     */
    public function index(Request $request)
    {
        // Ne montrer que les clients (pas le personnel admin)
        $query = User::role('customer')->with('roles');

        // Filtre par statut
        if ($request->filled('status')) {
            match($request->status) {
                'active' => $query->where('is_active', true)->where('is_blocked', false),
                'blocked' => $query->where('is_blocked', true),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        
        // Statistiques rapides
        $stats = [
            'total' => User::role('customer')->count(),
            'active' => User::role('customer')->where('is_active', true)->where('is_blocked', false)->count(),
            'blocked' => User::role('customer')->where('is_blocked', true)->count(),
            'new_today' => User::role('customer')->whereDate('created_at', today())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $user->load([
            'roles',
            'orders' => fn($q) => $q->latest()->take(10)->with('restaurant'),
            'addresses',
            'reviews' => fn($q) => $q->latest()->take(5)->with('restaurant'),
            'favorites' => fn($q) => $q->latest()->take(5)->with('product'),
            'loyaltyPoints' => fn($q) => $q->latest()->take(20),
        ]);

        $loyaltyBalance = $user->getLoyaltyBalance();
        
        // Statistiques de commande
        $orderStats = $user->getOrderStats();

        return view('admin.users.show', compact('user', 'loyaltyBalance', 'orderStats'));
    }
     /**
     * Formulaire d'édition
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'blocked_reason' => 'nullable|string|max:500',
            'roles' => 'nullable|array',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => $request->boolean('is_active'),
            'is_blocked' => $request->boolean('is_blocked'),
            'blocked_reason' => $request->input('blocked_reason'),
        ]);

        // Mettre à jour les rôles si fournis
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Bloquer un utilisateur
     */
    public function block(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $user->update([
            'is_blocked' => true,
            'blocked_reason' => $request->reason,
            'blocked_until' => $request->filled('days') ? now()->addDays($request->days) : null,
        ]);

        // Révoquer les tokens Sanctum si existants
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return back()->with('success', 'Utilisateur bloqué avec succès.');
    }

    /**
     * Débloquer un utilisateur
     */
    public function unblock(User $user)
    {
        $user->update([
            'is_blocked' => false,
            'blocked_reason' => null,
            'blocked_until' => null,
        ]);

        return back()->with('success', 'Utilisateur débloqué avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Supprimer les relations
        $user->addresses()->delete();
        $user->favorites()->delete();
        $user->loyaltyPoints()->delete();
        $user->notificationSettings()->delete();
        
        // Détacher les rôles
        $user->syncRoles([]);
        
        // Supprimer l'utilisateur
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}