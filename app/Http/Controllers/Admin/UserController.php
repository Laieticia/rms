<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            match($request->status) {
                'active' => $query->active(),
                'blocked' => $query->where('is_blocked', true),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        // $user->load(['roles', 'orders' => function($q) {
        //     $q->latest()->take(10)->with('restaurant');
        // }, 'addresses', 'reviews']);

        // return view('admin.users.show', compact('user'));
        
        $user->load([
            'roles', 
            'orders' => fn($q) => $q->latest()->take(10)->with('restaurant'),
            'addresses', 
            'reviews' => fn($q) => $q->latest()->take(5),
            'restaurants',
        ]);

        $loyaltyPoints = $user->getLoyaltyBalance();

        return view('admin.users.show', compact('user', 'loyaltyPoints'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'blocked_reason' => 'nullable|string',
            'roles' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_blocked'] = $request->boolean('is_blocked');

        $user->update($validated);

        // Mettre à jour les rôles
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function block(Request $request, User $user)
    {
        // $request->validate(['reason' => 'required|string|max:500']);
        $request->validate([
            'reason' => 'required|string|max:500',
            'days' => 'nullable|integer|min:1',
        ]);

        $user->update([
            'is_blocked' => true,
            'blocked_reason' => $request->reason,
            'blocked_until' => $request->filled('until') ? now()->addDays($request->until) : null,
        ]);

        return back()->with('success', 'Utilisateur bloqué.');
    }

    public function unblock(User $user)
    {
        $user->update([
            'is_blocked' => false,
            'blocked_reason' => null,
            'blocked_until' => null,
        ]);

        return back()->with('success', 'Utilisateur débloqué.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé.');
    }
}