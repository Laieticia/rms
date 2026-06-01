<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $categories = ['Entrées', 'Plats Principaux', 'Desserts', 'Boissons', 'Salades', 'Pizzas', 'Burgers', 'Sushis'];

        foreach ($restaurants as $restaurant) {
            foreach ($categories as $index => $catName) {
                Category::create([
                    'restaurant_id' => $restaurant->id,
                    'parent_id' => null,
                    'name' => $catName,
                    'slug' => strtolower(str_replace(' ', '-', $catName)),
                    'description' => "Description de la catégorie $catName",
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }
        }
    }
}