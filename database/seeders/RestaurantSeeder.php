<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            ['name' => 'Chez Mario', 'slug' => 'chez-mario', 'city' => 'Paris', 'phone' => '0123456789', 'email' => 'mario@restaurant.com'],
            ['name' => 'Sushi Master', 'slug' => 'sushi-master', 'city' => 'Lyon', 'phone' => '0123456790', 'email' => 'sushi@restaurant.com'],
            ['name' => 'Burger House', 'slug' => 'burger-house', 'city' => 'Marseille', 'phone' => '0123456791', 'email' => 'burger@restaurant.com'],
            ['name' => 'Pizza Express', 'slug' => 'pizza-express', 'city' => 'Bordeaux', 'phone' => '0123456792', 'email' => 'pizza@restaurant.com'],
            ['name' => 'Le Gourmet', 'slug' => 'le-gourmet', 'city' => 'Nice', 'phone' => '0123456793', 'email' => 'gourmet@restaurant.com'],
            ['name' => 'Wok Royal', 'slug' => 'wok-royal', 'city' => 'Toulouse', 'phone' => '0123456794', 'email' => 'wok@restaurant.com'],
            ['name' => 'Taco Loco', 'slug' => 'taco-loco', 'city' => 'Lille', 'phone' => '0123456795', 'email' => 'taco@restaurant.com'],
            ['name' => 'Thai Spice', 'slug' => 'thai-spice', 'city' => 'Strasbourg', 'phone' => '0123456796', 'email' => 'thai@restaurant.com'],
            ['name' => 'Le Bistro', 'slug' => 'le-bistro', 'city' => 'Nantes', 'phone' => '0123456797', 'email' => 'bistro@restaurant.com'],
            ['name' => 'Indian Palace', 'slug' => 'indian-palace', 'city' => 'Montpellier', 'phone' => '0123456798', 'email' => 'indian@restaurant.com'],
        ];

        foreach ($restaurants as $key => $data) {
            Restaurant::create(array_merge($data, [
                'description' => 'Description du restaurant ' . $data['name'],
                'address' => $data['city'] . ' Centre',
                'postal_code' => '7500' . ($key + 1),
                'country' => 'FR',
                'latitude' => 48.8566 + ($key * 0.01),
                'longitude' => 2.3522 + ($key * 0.01),
                'website' => 'https://' . $data['slug'] . '.com',
                'minimum_order' => rand(1000, 5000) / 100,
                'delivery_fee' => rand(200, 500) / 100,
                'is_active' => true,
            ]));
        }
    }
}