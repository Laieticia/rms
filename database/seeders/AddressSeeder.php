<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $addresses = [
            ['label' => 'Domicile', 'street' => '12 Rue de Paris', 'city' => 'Paris', 'postal' => '75001'],
            ['label' => 'Travail', 'street' => '45 Avenue des Champs', 'city' => 'Paris', 'postal' => '75008'],
            ['label' => 'Famille', 'street' => '78 Boulevard Saint-Michel', 'city' => 'Paris', 'postal' => '75005'],
        ];

        foreach ($users as $user) {
            foreach ($addresses as $index => $addr) {
                Address::create([
                    'user_id' => $user->id,
                    'label' => $addr['label'],
                    'street_address' => $addr['street'],
                    'city' => $addr['city'],
                    'postal_code' => $addr['postal'],
                    'country' => 'FR',
                    'latitude' => 48.8566 + ($index * 0.005),
                    'longitude' => 2.3522 + ($index * 0.005),
                    'is_default' => $index === 0,
                ]);
            }
        }
    }
}