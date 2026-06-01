<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Menu;
use App\Models\Product;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $menus = Menu::all();
        
        foreach ($menus as $menu) {
            // Récupérer les produits du restaurant
            $products = Product::where('restaurant_id', $menu->restaurant_id)
                               ->inRandomOrder()
                               ->take(rand(5, 10))
                               ->get();
            
            foreach ($products as $index => $product) {
                // Prix spécial parfois moins cher que le prix normal
                $specialPrice = rand(0, 1) ? $product->price * (rand(70, 95) / 100) : null;
                
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'product_id' => $product->id,
                    'special_price' => $specialPrice ? round($specialPrice, 2) : null,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}