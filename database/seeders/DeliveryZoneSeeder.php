<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryZone;
use App\Models\Restaurant;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $zones = ['Centre Ville', 'Nord', 'Sud', 'Est', 'Ouest'];
        
        foreach ($restaurants as $restaurant) {
            foreach ($zones as $index => $zone) {
                DeliveryZone::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $zone,
                    'description' => "Zone de livraison $zone",
                    'coordinates' => json_encode([
                        ['lat' => 48.8566 + ($index * 0.01), 'lng' => 2.3522 + ($index * 0.01)],
                        ['lat' => 48.8566 + ($index * 0.01) + 0.02, 'lng' => 2.3522 + ($index * 0.01)],
                    ]),
                    'delivery_fee' => rand(200, 800) / 100,
                    'min_order_amount' => rand(1000, 3000) / 100,
                    'estimated_time' => rand(20, 60),
                    'is_active' => true,
                ]);
            }
        }
    }
}