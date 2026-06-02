<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Super Admin
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'admin@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33612345678',
                'email_verified_at' => now(),
                'is_active' => true,
                'preferences' => json_encode([
                    'language' => 'fr',
                    'notifications' => ['email' => true, 'push' => true, 'sms' => false],
                    'theme' => 'light',
                ]),
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Laurent',
                'email' => 'marie@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33623456789',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            // Managers
            [
                'first_name' => 'Pierre',
                'last_name' => 'Martin',
                'email' => 'pierre@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33634567890',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Bernard',
                'email' => 'sophie@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33645678901',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            // Chefs
            [
                'first_name' => 'Antoine',
                'last_name' => 'Petit',
                'email' => 'antoine@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33656789012',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Julie',
                'last_name' => 'Roux',
                'email' => 'julie@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33667890123',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            // Serveurs
            [
                'first_name' => 'Thomas',
                'last_name' => 'Moreau',
                'email' => 'thomas@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33678901234',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Laura',
                'last_name' => 'Simon',
                'email' => 'laura@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33689012345',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            // Livreurs
            [
                'first_name' => 'Nicolas',
                'last_name' => 'Michel',
                'email' => 'nicolas@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33690123456',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Leroy',
                'email' => 'emma@restaurant.com',
                'password' => Hash::make('password123'),
                'phone' => '+33601234567',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            // Clients
            [
                'first_name' => 'Lucas',
                'last_name' => 'Garcia',
                'email' => 'lucas@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33611111111',
                'email_verified_at' => now(),
                'is_active' => true,
                'preferences' => json_encode([
                    'language' => 'fr',
                    'dietary_preferences' => ['vegetarian'],
                    'allergies' => ['gluten'],
                ]),
            ],
            [
                'first_name' => 'Léa',
                'last_name' => 'Martinez',
                'email' => 'lea@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33622222222',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Hugo',
                'last_name' => 'Robin',
                'email' => 'hugo@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33633333333',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Chloé',
                'last_name' => 'Durand',
                'email' => 'chloe@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33644444444',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Gabriel',
                'last_name' => 'Lefebvre',
                'email' => 'gabriel@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33655555555',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Morel',
                'email' => 'alice@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33666666666',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Raphaël',
                'last_name' => 'Fournier',
                'email' => 'raphael@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33677777777',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'first_name' => 'Camille',
                'last_name' => 'Girard',
                'email' => 'camille@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33688888888',
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        $roles = [
            0 => 'super_admin',
            1 => 'admin',
            2 => 'manager',
            3 => 'manager',
            4 => 'chef',
            5 => 'chef',
            6 => 'waiter',
            7 => 'waiter',
            8 => 'delivery_person',
            9 => 'delivery_person',
        ];

        foreach ($users as $index => $userData) {
            $user = User::create($userData);
            
            if (isset($roles[$index])) {
                $user->assignRole($roles[$index]);
            } else {
                $user->assignRole('customer');
            }
        }
    }
}