<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que les rôles existent
        $roles = ['super-admin', 'restaurant-admin', 'cashier', 'client', 'delivery'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        
        // 1. Créer le Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@restaurant.com'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super-admin']);
        $this->command->info('✓ Super Admin créé: admin@restaurant.com / password123');
        
        // 2. Créer des administrateurs de restaurant
        $restoAdmins = [
            ['name' => 'Admin Pizza Express', 'email' => 'admin@pizza.com'],
            ['name' => 'Admin Burger House', 'email' => 'admin@burger.com'],
            ['name' => 'Admin Sushi Master', 'email' => 'admin@sushi.com'],
        ];
        
        foreach ($restoAdmins as $data) {
            $admin = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $admin->syncRoles(['restaurant-admin']);
            $this->command->info("✓ Restaurant Admin créé: {$data['email']} / password123");
        }
        
        // 3. Créer des livreurs
        for ($i = 1; $i <= 3; $i++) {
            $delivery = User::firstOrCreate(
                ['email' => "livreur$i@example.com"],
                [
                    'name' => "Livreur $i",
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $delivery->syncRoles(['delivery']);
            $this->command->info("✓ Livreur créé: livreur$i@example.com / password123");
        }
        
        // 4. Créer des caissiers/vendeurs
        for ($i = 1; $i <= 3; $i++) {
            $cashier = User::firstOrCreate(
                ['email' => "caissier$i@example.com"],
                [
                    'name' => "Caissier $i",
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $cashier->syncRoles(['cashier']);
            $this->command->info("✓ Caissier créé: caissier$i@example.com / password123");
        }
        
        // 5. Créer des clients normaux
        for ($i = 1; $i <= 10; $i++) {
            $client = User::firstOrCreate(
                ['email' => "client$i@example.com"],
                [
                    'name' => "Client $i",
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $client->syncRoles(['client']);
            $this->command->info("✓ Client créé: client$i@example.com / password123");
        }
        
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('📋 TOUS LES COMPTES DE TEST');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('🔴 SUPER ADMIN:     admin@restaurant.com     → password123');
        $this->command->info('🟠 RESTAURANT:      admin@pizza.com          → password123');
        $this->command->info('🟠 RESTAURANT:      admin@burger.com         → password123');
        $this->command->info('🟠 RESTAURANT:      admin@sushi.com          → password123');
        $this->command->info('🟡 CAISSIER:        caissier1@example.com    → password123');
        $this->command->info('🟡 CAISSIER:        caissier2@example.com    → password123');
        $this->command->info('🟡 CAISSIER:        caissier3@example.com    → password123');
        $this->command->info('🟢 LIVREUR:         livreur1@example.com     → password123');
        $this->command->info('🟢 LIVREUR:         livreur2@example.com     → password123');
        $this->command->info('🟢 LIVREUR:         livreur3@example.com     → password123');
        $this->command->info('🔵 CLIENT:          client1@example.com      → password123');
        $this->command->info('🔵 CLIENT:          client2@example.com      → password123');
        $this->command->info('🔵 CLIENT:          ... jusqu\'à client10     → password123');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}