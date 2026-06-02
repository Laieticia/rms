<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            // Zones Restaurant 1 (Paris Centre)
            [
                'restaurant_id' => 1,
                'name' => 'Paris Centre',
                'description' => 'Zone 1-4 arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 48.8660, 'lng' => 2.3200],
                    ['lat' => 48.8660, 'lng' => 2.3700],
                    ['lat' => 48.8500, 'lng' => 2.3700],
                    ['lat' => 48.8500, 'lng' => 2.3200],
                ]),
                'delivery_fee' => 3.50,
                'min_order_amount' => 15.00,
                'estimated_time' => 30,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Paris Élargi',
                'description' => 'Zone 5-10 arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 48.8800, 'lng' => 2.3100],
                    ['lat' => 48.8800, 'lng' => 2.3800],
                    ['lat' => 48.8450, 'lng' => 2.3800],
                    ['lat' => 48.8450, 'lng' => 2.3100],
                ]),
                'delivery_fee' => 5.00,
                'min_order_amount' => 25.00,
                'estimated_time' => 45,
                'is_active' => true,
            ],
            
            // Zones Restaurant 2
            [
                'restaurant_id' => 2,
                'name' => 'Paris Ouest',
                'description' => '8ème, 16ème, 17ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 48.8750, 'lng' => 2.2850],
                    ['lat' => 48.8750, 'lng' => 2.3250],
                    ['lat' => 48.8600, 'lng' => 2.3250],
                    ['lat' => 48.8600, 'lng' => 2.2850],
                ]),
                'delivery_fee' => 2.50,
                'min_order_amount' => 12.00,
                'estimated_time' => 25,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Paris Centre',
                'description' => '1er-7ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 48.8650, 'lng' => 2.3200],
                    ['lat' => 48.8650, 'lng' => 2.3600],
                    ['lat' => 48.8500, 'lng' => 2.3600],
                    ['lat' => 48.8500, 'lng' => 2.3200],
                ]),
                'delivery_fee' => 3.50,
                'min_order_amount' => 15.00,
                'estimated_time' => 35,
                'is_active' => true,
            ],
            
            // Zones Restaurant 3 (Lyon)
            [
                'restaurant_id' => 3,
                'name' => 'Lyon Centre',
                'description' => 'Presqu\'île et centre-ville',
                'coordinates' => json_encode([
                    ['lat' => 45.7700, 'lng' => 4.8250],
                    ['lat' => 45.7700, 'lng' => 4.8450],
                    ['lat' => 45.7550, 'lng' => 4.8450],
                    ['lat' => 45.7550, 'lng' => 4.8250],
                ]),
                'delivery_fee' => 4.00,
                'min_order_amount' => 20.00,
                'estimated_time' => 35,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Lyon Périphérie',
                'description' => 'Villeurbanne, Bron, Caluire',
                'coordinates' => json_encode([
                    ['lat' => 45.7850, 'lng' => 4.8150],
                    ['lat' => 45.7850, 'lng' => 4.8750],
                    ['lat' => 45.7450, 'lng' => 4.8750],
                    ['lat' => 45.7450, 'lng' => 4.8150],
                ]),
                'delivery_fee' => 6.00,
                'min_order_amount' => 30.00,
                'estimated_time' => 50,
                'is_active' => true,
            ],
            
            // Zones Restaurant 4 (Marseille)
            [
                'restaurant_id' => 4,
                'name' => 'Marseille Centre',
                'description' => '1er-7ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 43.3050, 'lng' => 5.3600],
                    ['lat' => 43.3050, 'lng' => 5.3900],
                    ['lat' => 43.2850, 'lng' => 5.3900],
                    ['lat' => 43.2850, 'lng' => 5.3600],
                ]),
                'delivery_fee' => 2.00,
                'min_order_amount' => 10.00,
                'estimated_time' => 20,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Marseille Sud',
                'description' => '8ème-9ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 43.2850, 'lng' => 5.3700],
                    ['lat' => 43.2850, 'lng' => 5.4000],
                    ['lat' => 43.2600, 'lng' => 5.4000],
                    ['lat' => 43.2600, 'lng' => 5.3700],
                ]),
                'delivery_fee' => 4.00,
                'min_order_amount' => 15.00,
                'estimated_time' => 35,
                'is_active' => true,
            ],
            
            // Zones Restaurant 5 (Lyon)
            [
                'restaurant_id' => 5,
                'name' => 'Lyon Est',
                'description' => '3ème, 6ème, 7ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 45.7700, 'lng' => 4.8300],
                    ['lat' => 45.7700, 'lng' => 4.8650],
                    ['lat' => 45.7450, 'lng' => 4.8650],
                    ['lat' => 45.7450, 'lng' => 4.8300],
                ]),
                'delivery_fee' => 3.00,
                'min_order_amount' => 15.00,
                'estimated_time' => 30,
                'is_active' => true,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Lyon Nord',
                'description' => '4ème, 5ème, 9ème arrondissements',
                'coordinates' => json_encode([
                    ['lat' => 45.7850, 'lng' => 4.8100],
                    ['lat' => 45.7850, 'lng' => 4.8350],
                    ['lat' => 45.7600, 'lng' => 4.8350],
                    ['lat' => 45.7600, 'lng' => 4.8100],
                ]),
                'delivery_fee' => 4.00,
                'min_order_amount' => 20.00,
                'estimated_time' => 40,
                'is_active' => true,
            ],
        ];

        foreach ($zones as $zoneData) {
            DeliveryZone::create($zoneData);
        }
    }
}