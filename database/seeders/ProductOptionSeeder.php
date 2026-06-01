<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductOption;
use App\Models\Product;

class ProductOptionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $options = [
            ['name' => 'Suppléments', 'type' => 'multiple', 'is_required' => false],
            ['name' => 'Sauce', 'type' => 'single', 'is_required' => true],
            ['name' => 'Niveau d\'épices', 'type' => 'single', 'is_required' => false],
            ['name' => 'Accompagnement', 'type' => 'single', 'is_required' => true],
        ];
        
        foreach ($products as $product) {
            // Seulement 40% des produits ont des options
            if (rand(1, 100) <= 40) {
                $nbOptions = rand(1, 2);
                for ($i = 0; $i < $nbOptions; $i++) {
                    $option = $options[array_rand($options)];
                    ProductOption::create([
                        'product_id' => $product->id,
                        'name' => $option['name'],
                        'type' => $option['type'],
                        'is_required' => $option['is_required'],
                        'max_choices' => $option['type'] === 'multiple' ? rand(2, 3) : 1,
                        'sort_order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}