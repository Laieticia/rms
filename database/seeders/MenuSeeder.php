<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            // Menus Restaurant 1
            [
                'restaurant_id' => 1,
                'name' => 'Menu Déjeuner',
                'type' => 'lunch',
                'description' => 'Entrée + Plat + Dessert - Disponible du lundi au vendredi de 12h à 14h',
                'available_from' => '12:00:00',
                'available_until' => '14:00:00',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Menu Dîner',
                'type' => 'dinner',
                'description' => 'Entrée + Plat + Dessert - Menu gastronomique du soir',
                'available_from' => '19:00:00',
                'available_until' => '22:00:00',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Menu Weekend',
                'type' => 'weekend',
                'description' => 'Brunch gourmand - Samedi et Dimanche',
                'available_from' => '10:00:00',
                'available_until' => '15:00:00',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Menu Saint-Valentin',
                'type' => 'special',
                'description' => 'Menu spécial Saint-Valentin avec champagne',
                'start_date' => '2024-02-14 00:00:00',
                'end_date' => '2024-02-14 23:59:59',
                'is_active' => true,
                'sort_order' => 4,
            ],
            
            // Menus Restaurant 2
            [
                'restaurant_id' => 2,
                'name' => 'Menu Midi',
                'type' => 'lunch',
                'description' => 'Pizza ou Pâtes + Boisson + Dessert',
                'available_from' => '12:00:00',
                'available_until' => '14:30:00',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Menu Famille',
                'type' => 'special',
                'description' => '2 Pizzas + 2 Boissons + 2 Desserts',
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // Menus Restaurant 3
            [
                'restaurant_id' => 3,
                'name' => 'Menu Découverte',
                'type' => 'special',
                'description' => 'Plateau découverte : 12 sushis variés + soupe miso',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Menu Bento',
                'type' => 'lunch',
                'description' => 'Bento complet : riz, poisson, légumes, pickles',
                'available_from' => '12:00:00',
                'available_until' => '14:30:00',
                'is_active' => true,
                'sort_order' => 2,
            ],
            
            // Menus Restaurant 4
            [
                'restaurant_id' => 4,
                'name' => 'Menu Étudiant',
                'type' => 'special',
                'description' => 'Burger Classic + Frites + Boisson 33cl',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Menu Maxi',
                'type' => 'special',
                'description' => 'Double Cheese + Frites + Onion Rings + Boisson 50cl + Dessert',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Menu Duo',
                'type' => 'special',
                'description' => '2 Burgers au choix + 2 Accompagnements + 2 Boissons',
                'is_active' => true,
                'sort_order' => 3,
            ],
            
            // Menus Restaurant 5
            [
                'restaurant_id' => 5,
                'name' => 'Menu Vitalité',
                'type' => 'special',
                'description' => 'Buddha Bowl + Jus Detox + Dessert Vegan',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Menu Brunch Healthy',
                'type' => 'weekend',
                'description' => 'Smoothie Bowl + Toast Avocat + Boisson Chaude',
                'available_from' => '10:00:00',
                'available_until' => '15:00:00',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::create($menuData);
            
            // Ajouter des items aléatoires aux menus
            $products = \App\Models\Product::where('restaurant_id', $menu->restaurant_id)
                ->inRandomOrder()
                ->take(rand(3, 6))
                ->get();
            
            foreach ($products as $index => $product) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'product_id' => $product->id,
                    'special_price' => $product->price * 0.85, // 15% de réduction
                    'sort_order' => $index,
                ]);
            }
        }
    }
}