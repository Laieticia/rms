<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Le Bistro Parisien',
                'description' => 'Restaurant gastronomique français au cœur de Paris. Cuisine traditionnelle revisitée avec des produits frais et de saison.',
                'address' => '15 Rue de Rivoli',
                'city' => 'Paris',
                'postal_code' => '75004',
                'country' => 'FR',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'phone' => '01 45 67 89 10',
                'email' => 'contact@bistroparisien.fr',
                'website' => 'https://www.bistroparisien.fr',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '10:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '10:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '10:00', 'close' => '22:00'],
                    'thursday' => ['open' => '10:00', 'close' => '23:00'],
                    'friday' => ['open' => '10:00', 'close' => '23:00'],
                    'saturday' => ['open' => '09:00', 'close' => '23:00'],
                    'sunday' => ['open' => '09:00', 'close' => '15:00'],
                ]),
                'minimum_order' => 15.00,
                'delivery_fee' => 3.50,
                'tax_rate' => 10.00,
                'currency' => 'EUR',
                'estimated_delivery_time' => 30,
                'is_active' => true,
                'accepts_delivery' => true,
                'accepts_takeaway' => true,
                'accepts_dine_in' => true,
            ],
            [
                'name' => 'Pizza Roma',
                'description' => 'Authentique pizzeria italienne avec four à bois. Pâtes fraîches faites maison et pizzas traditionnelles.',
                'address' => '28 Avenue des Champs-Élysées',
                'city' => 'Paris',
                'postal_code' => '75008',
                'country' => 'FR',
                'latitude' => 48.8698,
                'longitude' => 2.3075,
                'phone' => '01 56 78 90 11',
                'email' => 'info@pizzaroma.fr',
                'website' => 'https://www.pizzaroma.fr',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '11:00', 'close' => '23:00'],
                    'tuesday' => ['open' => '11:00', 'close' => '23:00'],
                    'wednesday' => ['open' => '11:00', 'close' => '23:00'],
                    'thursday' => ['open' => '11:00', 'close' => '00:00'],
                    'friday' => ['open' => '11:00', 'close' => '00:00'],
                    'saturday' => ['open' => '11:00', 'close' => '00:00'],
                    'sunday' => ['open' => '11:00', 'close' => '22:00'],
                ]),
                'minimum_order' => 12.00,
                'delivery_fee' => 2.50,
                'tax_rate' => 10.00,
                'currency' => 'EUR',
                'estimated_delivery_time' => 25,
                'is_active' => true,
                'accepts_delivery' => true,
                'accepts_takeaway' => true,
                'accepts_dine_in' => true,
            ],
            [
                'name' => 'Sushi Master',
                'description' => 'Restaurant japonais proposant sushis, makis et autres spécialités nippones préparés par des chefs experts.',
                'address' => '5 Rue du Commerce',
                'city' => 'Lyon',
                'postal_code' => '69002',
                'country' => 'FR',
                'latitude' => 45.7640,
                'longitude' => 4.8357,
                'phone' => '04 78 90 12 34',
                'email' => 'contact@sushimaster.fr',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '12:00', 'close' => '14:30', 'open2' => '19:00', 'close2' => '22:30'],
                    'tuesday' => ['open' => '12:00', 'close' => '14:30', 'open2' => '19:00', 'close2' => '22:30'],
                    'wednesday' => ['open' => '12:00', 'close' => '14:30', 'open2' => '19:00', 'close2' => '22:30'],
                    'thursday' => ['open' => '12:00', 'close' => '14:30', 'open2' => '19:00', 'close2' => '23:00'],
                    'friday' => ['open' => '12:00', 'close' => '14:30', 'open2' => '19:00', 'close2' => '23:00'],
                    'saturday' => ['open' => '12:00', 'close' => '23:00'],
                    'sunday' => ['closed' => true],
                ]),
                'minimum_order' => 20.00,
                'delivery_fee' => 4.00,
                'tax_rate' => 10.00,
                'currency' => 'EUR',
                'estimated_delivery_time' => 35,
                'is_active' => true,
                'accepts_delivery' => true,
                'accepts_takeaway' => true,
                'accepts_dine_in' => true,
            ],
            [
                'name' => 'Burger House',
                'description' => 'Burgers gourmets préparés avec des ingrédients frais et locaux. Viande française, pains artisanaux et sauces maison.',
                'address' => '42 Rue de la République',
                'city' => 'Marseille',
                'postal_code' => '13001',
                'country' => 'FR',
                'latitude' => 43.2965,
                'longitude' => 5.3698,
                'phone' => '04 91 23 45 67',
                'email' => 'hello@burgerhouse.fr',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '11:30', 'close' => '22:00'],
                    'tuesday' => ['open' => '11:30', 'close' => '22:00'],
                    'wednesday' => ['open' => '11:30', 'close' => '22:00'],
                    'thursday' => ['open' => '11:30', 'close' => '23:00'],
                    'friday' => ['open' => '11:30', 'close' => '23:00'],
                    'saturday' => ['open' => '11:30', 'close' => '23:00'],
                    'sunday' => ['open' => '12:00', 'close' => '21:00'],
                ]),
                'minimum_order' => 10.00,
                'delivery_fee' => 2.00,
                'tax_rate' => 10.00,
                'currency' => 'EUR',
                'estimated_delivery_time' => 20,
                'is_active' => true,
                'accepts_delivery' => true,
                'accepts_takeaway' => true,
                'accepts_dine_in' => true,
            ],
            [
                'name' => 'Le Jardin Vert',
                'description' => 'Restaurant végétarien et vegan proposant une cuisine créative et savoureuse à base de produits biologiques.',
                'address' => '8 Place Bellecour',
                'city' => 'Lyon',
                'postal_code' => '69002',
                'country' => 'FR',
                'latitude' => 45.7578,
                'longitude' => 4.8320,
                'phone' => '04 78 56 78 90',
                'email' => 'info@jardinvert.fr',
                'opening_hours' => json_encode([
                    'monday' => ['open' => '09:00', 'close' => '20:00'],
                    'tuesday' => ['open' => '09:00', 'close' => '20:00'],
                    'wednesday' => ['open' => '09:00', 'close' => '20:00'],
                    'thursday' => ['open' => '09:00', 'close' => '21:00'],
                    'friday' => ['open' => '09:00', 'close' => '21:00'],
                    'saturday' => ['open' => '10:00', 'close' => '21:00'],
                    'sunday' => ['open' => '10:00', 'close' => '18:00'],
                ]),
                'minimum_order' => 15.00,
                'delivery_fee' => 3.00,
                'tax_rate' => 10.00,
                'currency' => 'EUR',
                'estimated_delivery_time' => 30,
                'is_active' => true,
                'accepts_delivery' => true,
                'accepts_takeaway' => true,
                'accepts_dine_in' => true,
            ],
        ];

        foreach ($restaurants as $index => $restaurantData) {
            $restaurant = Restaurant::create($restaurantData);

            // Assigner les managers et staff
            if ($index === 0) {
                $restaurant->users()->attach(2, ['role' => 'manager']);
                $restaurant->users()->attach(4, ['role' => 'chef']);
                $restaurant->users()->attach(6, ['role' => 'waiter']);
            } elseif ($index === 1) {
                $restaurant->users()->attach(3, ['role' => 'manager']);
                $restaurant->users()->attach(5, ['role' => 'chef']);
                $restaurant->users()->attach(7, ['role' => 'waiter']);
            }
        }
    }
}
