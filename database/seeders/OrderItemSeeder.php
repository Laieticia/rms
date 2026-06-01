<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        
        foreach ($orders as $order) {
            // Chaque commande a 1-5 articles
            $nbItems = rand(1, 5);
            $products = Product::where('restaurant_id', $order->restaurant_id)
                               ->inRandomOrder()
                               ->take($nbItems)
                               ->get();
            
            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $quantity;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'special_instructions' => rand(0, 1) ? "Sans oignons svp" : null,
                    'product_data' => json_encode([
                        'name' => $product->name,
                        'price' => $unitPrice,
                        'category' => $product->category->name ?? null,
                    ]),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->created_at,
                ]);
            }
        }
    }
}