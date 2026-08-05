<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\HasRestaurant;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    use HasRestaurant;

    /**
     * Afficher la liste du personnel
     */
    public function index(Request $request)
    {
        $restaurantId = $this->getRestaurantId();

        $query = User::role(['admin', 'manager', 'chef', 'waiter', 'delivery_person'])
            ->whereHas('restaurants', function($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId);
            })
            ->with('roles');

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('is_blocked', false);
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staff = $query->latest()->paginate(20);
        $roles = Role::whereIn('name', ['manager', 'chef', 'waiter', 'delivery_person'])->get();
        $restaurant = Restaurant::find($restaurantId);

        // Statistiques
        $stats = [
            'total' => User::role(['admin', 'manager', 'chef', 'waiter', 'delivery_person'])
                ->whereHas('restaurants', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->count(),
            'managers' => User::role('manager')
                ->whereHas('restaurants', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->count(),
            'chefs' => User::role('chef')
                ->whereHas('restaurants', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->count(),
            'waiters' => User::role('waiter')
                ->whereHas('restaurants', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->count(),
            'delivery_persons' => User::role('delivery_person')
                ->whereHas('restaurants', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->count(),
        ];

        return view('admin.staff.index', compact('staff', 'roles', 'restaurant', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['manager', 'chef', 'waiter', 'delivery_person'])->get();
        $restaurant = Restaurant::find($this->getRestaurantId());

        return view('admin.staff.create', compact('roles', 'restaurant'));
    }

    /**
     * Enregistrer un nouveau membre du personnel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:manager,chef,waiter,delivery_person',
            'is_active' => 'boolean',
        ]);

        $staff = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(), // Auto-vérifier l'email pour le personnel
        ]);

        // Assigner le rôle
        $staff->assignRole($validated['role']);

        // Associer au restaurant
        $restaurantId = $this->getRestaurantId();
        $staff->restaurants()->attach($restaurantId, [
            'role' => $validated['role'],
            'permissions' => json_encode($this->getDefaultPermissions($validated['role'])),
        ]);

        // Créer les paramètres de notification
        $staff->notificationSettings()->create();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Membre du personnel ajouté avec succès. Il peut maintenant se connecter.');
    }

    /**
     * Afficher les détails d'un membre
     */
    public function show(User $staff)
    {
        $staff->load(['roles', 'restaurants', 'orders' => function($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(User $staff)
    {
        $roles = Role::whereIn('name', ['manager', 'chef', 'waiter', 'delivery_person'])->get();
        $restaurant = Restaurant::find($this->getRestaurantId());

        $staff = $staff;

        return view('admin.staff.edit', compact('staff', 'roles', 'restaurant'));
    }

    /**
     * Mettre à jour un membre
     */
    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:manager,chef,waiter,delivery_person',
            'is_active' => 'boolean',
        ]);

        $data = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'is_active' => $request->boolean('is_active'),
        ];

        // Mettre à jour le mot de passe seulement si fourni
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $staff->update($data);

        // Mettre à jour le rôle
        $staff->syncRoles([$validated['role']]);

        // Mettre à jour l'association au restaurant
        $restaurantId = $this->getRestaurantId();
        if (!$staff->restaurants()->where('restaurant_id', $restaurantId)->exists()) {
            $staff->restaurants()->attach($restaurantId, [
                'role' => $validated['role'],
                'permissions' => json_encode($this->getDefaultPermissions($validated['role'])),
            ]);
        } else {
            $staff->restaurants()->updateExistingPivot($restaurantId, [
                'role' => $validated['role'],
            ]);
        }

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Membre du personnel mis à jour.');
    }

    /**
     * Bloquer un membre
     */
    public function block(Request $request, User $staff)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $staff->update([
            'is_blocked' => true,
            'blocked_reason' => $request->reason,
        ]);

        // Révoquer les tokens
        if (method_exists($staff, 'tokens')) {
            $staff->tokens()->delete();
        }

        return back()->with('success', 'Membre bloqué.');
    }

    /**
     * Débloquer un membre
     */
    public function unblock(User $staff)
    {
        $staff->update([
            'is_blocked' => false,
            'blocked_reason' => null,
        ]);

        return back()->with('success', 'Membre débloqué.');
    }

    /**
     * Supprimer un membre
     */
    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Détacher du restaurant
        $staff->restaurants()->detach();
        
        // Supprimer l'utilisateur
        $staff->delete();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Membre supprimé.');
    }

    /**
     * Permissions par défaut selon le rôle
     */
    private function getDefaultPermissions(string $role): array
    {
        return match($role) {
            'manager' => ['manage_orders', 'manage_products', 'manage_staff', 'view_reports'],
            'chef' => ['view_orders', 'manage_orders_status'],
            'waiter' => ['view_orders', 'create_orders'],
            'delivery_person' => ['view_deliveries', 'update_delivery_status'],
            default => [],
        };
    }

    /**
     * Récupérer l'ID du restaurant
     */
    // getRestaurantId() est fourni par le trait HasRestaurant
}
