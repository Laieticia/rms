<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que les rôles existent
        $roles = ['super-admin', 'restaurant-admin', 'cashier', 'client', 'delivery'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        
        // Assigner les rôles aux utilisateurs
        // Super Admin
        $admin = User::where('email', 'admin@restaurant.com')->first();
        if ($admin) {
            $admin->syncRoles(['super-admin']);
            $this->command->info('Super Admin rôle assigné');
        }
        
        // Restaurant Admins
        $restoAdmins = User::whereIn('email', [
            'admin@pizza.com', 
            'admin@burger.com', 
            'admin@sushi.com'
        ])->get();
        
        foreach ($restoAdmins as $user) {
            $user->syncRoles(['restaurant-admin']);
            $this->command->info("Restaurant Admin rôle assigné à {$user->email}");
        }
        
        // Cashiers (caissiers)
        $cashiers = User::where('email', 'like', 'caissier%@example.com')->get();
        foreach ($cashiers as $user) {
            $user->syncRoles(['cashier']);
            $this->command->info("Cashier rôle assigné à {$user->email}");
        }
        
        // Delivery (livreurs)
        $deliveries = User::where('email', 'like', 'livreur%@example.com')->get();
        foreach ($deliveries as $user) {
            $user->syncRoles(['delivery']);
            $this->command->info("Delivery rôle assigné à {$user->email}");
        }
        
        // Clients (tous les autres utilisateurs)
        $clients = User::whereDoesntHave('roles')->get();
        foreach ($clients as $user) {
            $user->syncRoles(['client']);
            $this->command->info("Client rôle assigné à {$user->email}");
        }
        
        $this->command->info('Tous les rôles ont été assignés avec succès !');
    }
}