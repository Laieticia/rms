<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $variants = [
            ['name' => 'Petite', 'price_adjustment' => -2.00],
            ['name' => 'Moyenne', 'price_adjustment' => 0],
            ['name' => 'Grande', 'price_adjustment' => 3.00],
            ['name' => 'Extra Large', 'price_adjustment' => 5.00],
        ];
        
        foreach ($products as $product) {
            // Seulement 30% des produits ont des variantes
            if (rand(1, 100) <= 30) {
                $nbVariants = rand(2, 3);
                for ($i = 0; $i < $nbVariants; $i++) {
                    $variant = $variants[$i];
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variant['name'],
                        'sku' => $product->sku . '-' . strtoupper(substr($variant['name'], 0, 1)),
                        'price_adjustment' => $variant['price_adjustment'],
                        'stock_quantity' => rand(20, 100),
                        'is_available' => true,
                        'sort_order' => $i,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}