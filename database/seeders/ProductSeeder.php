<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Salade César', 'price' => 12.99],
            ['name' => 'Pizza Margherita', 'price' => 14.99],
            ['name' => 'Burger Classic', 'price' => 11.99],
            ['name' => 'Sushi Mix', 'price' => 18.99],
            ['name' => 'Pâtes Carbonara', 'price' => 13.99],
            ['name' => 'Tiramisu', 'price' => 6.99],
            ['name' => 'Coca Cola', 'price' => 2.99],
            ['name' => 'Frites Maison', 'price' => 4.99],
            ['name' => 'Steak Frites', 'price' => 16.99],
            ['name' => 'Moules Frites', 'price' => 15.99],
        ];

        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {
            $categories = Category::where('restaurant_id', $restaurant->id)->get();
            
            for ($i = 1; $i <= 10; $i++) {
                $product = $products[($i - 1) % count($products)];
                $category = $categories->random();
                
                Product::create([
                    'restaurant_id' => $restaurant->id,
                    'category_id' => $category->id,
                    'name' => $product['name'] . " " . $i,
                    'slug' => strtolower(str_replace(' ', '-', $product['name'])) . "-$i",
                    'description' => "Délicieux " . $product['name'] . " préparé avec soin",
                    'price' => $product['price'],
                    'preparation_time' => rand(10, 30),
                    'is_available' => true,
                    'is_vegetarian' => rand(0, 1),
                    'is_vegan' => rand(0, 1),
                    'rating_avg' => rand(35, 50) / 10,
                    'rating_count' => rand(10, 100),
                ]);
            }
        }
    }
}