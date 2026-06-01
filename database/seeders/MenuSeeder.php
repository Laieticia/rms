<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Restaurant;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $menuTypes = [
            ['name' => 'Menu du Jour', 'type' => 'regular'],
            ['name' => 'Menu Déjeuner', 'type' => 'lunch'],
            ['name' => 'Menu Dîner', 'type' => 'dinner'],
            ['name' => 'Menu Week-end', 'type' => 'weekend'],
            ['name' => 'Menu Spécial', 'type' => 'special'],
        ];
        
        foreach ($restaurants as $restaurant) {
            // Chaque restaurant a 2-3 menus
            $nbMenus = rand(2, 3);
            for ($i = 0; $i < $nbMenus; $i++) {
                $menuType = $menuTypes[$i % count($menuTypes)];
                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $menuType['name'] . ' - ' . $restaurant->name,
                    'slug' => strtolower(str_replace(' ', '-', $menuType['name'])) . '-' . $restaurant->id,
                    'description' => "Découvrez notre {$menuType['name']} composé de plats frais et savoureux.",
                    'type' => $menuType['type'],
                    'start_date' => now()->startOfMonth(),
                    'end_date' => now()->endOfMonth(),
                    'available_from' => '11:00:00',
                    'available_until' => '22:00:00',
                    'is_active' => true,
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}