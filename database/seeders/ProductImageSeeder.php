<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Ajouter 2-3 images par produit
            for ($i = 1; $i <= rand(2, 3); $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => "products/product_{$product->id}_image_{$i}.jpg",
                    'alt_text' => "Image du produit {$product->name}",
                    'sort_order' => $i,
                    'is_primary' => $i === 1, // La première image est l'image principale
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}