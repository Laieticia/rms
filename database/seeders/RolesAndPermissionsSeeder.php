<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Réinitialiser le cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions par module
        $permissions = [
            // Restaurants
            'view restaurants', 'create restaurants', 'edit restaurants', 'delete restaurants',
            
            // Produits
            'view products', 'create products', 'edit products', 'delete products',
            
            // Catégories
            'view categories', 'create categories', 'edit categories', 'delete categories',
            
            // Commandes
            'view orders', 'create orders', 'edit orders', 'delete orders', 'update order status',
            
            // Utilisateurs
            'view users', 'create users', 'edit users', 'delete users',
            
            // Livraison
            'view deliveries', 'update delivery status',
            
            // Avis
            'view reviews', 'create reviews', 'delete reviews', 'moderate reviews',
            
            // Paiements
            'view payments', 'process payments',
            
            // Rapports
            'view reports',
            
            // Paramètres
            'manage settings',
        ];

        // Créer les permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Créer les rôles
        // 1. Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin Restaurant
        $restoAdmin = Role::firstOrCreate(['name' => 'restaurant-admin', 'guard_name' => 'web']);
        $restoAdmin->syncPermissions([
            'view restaurants', 'edit restaurants',
            'view products', 'create products', 'edit products', 'delete products',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view orders', 'update order status',
            'view reviews', 'moderate reviews',
            'view reports',
        ]);

        // 3. Manager
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'view products', 'create products', 'edit products',
            'view categories', 'create categories', 'edit categories',
            'view orders', 'update order status',
            'view reviews',
        ]);

        // 4. Client
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $client->syncPermissions([
            'view products',
            'create orders', 'view orders',
            'create reviews',
        ]);

        // 5. Livreur
        $delivery = Role::firstOrCreate(['name' => 'delivery', 'guard_name' => 'web']);
        $delivery->syncPermissions([
            'view deliveries',
            'update delivery status',
        ]);

        // 6. Staff
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'view products',
            'view orders',
        ]);

        // Assigner super-admin au premier utilisateur
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super-admin');
        }
    }
}