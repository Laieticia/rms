<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'restaurant_id' => 1,
                'user_id' => 11,
                'address_id' => 1,
                'order_number' => 'ORD-20240101-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 45.70,
                'tax_amount' => 4.57,
                'delivery_fee' => 3.50,
                'discount_amount' => 0,
                'total' => 53.77,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '123 Rue de la Paix, 75001 Paris',
                'delivery_instructions' => 'Code porte : 1234',
                'created_at' => '2024-01-15 12:30:00',
                'confirmed_at' => '2024-01-15 12:32:00',
                'ready_at' => '2024-01-15 12:55:00',
                'delivered_at' => '2024-01-15 13:25:00',
            ],
            [
                'restaurant_id' => 1,
                'user_id' => 12,
                'address_id' => 3,
                'order_number' => 'ORD-20240102-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 38.80,
                'tax_amount' => 3.88,
                'delivery_fee' => 3.50,
                'discount_amount' => 5.00,
                'total' => 41.18,
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '67 Rue de la Liberté, 69003 Lyon',
                'created_at' => '2024-01-20 19:15:00',
                'confirmed_at' => '2024-01-20 19:18:00',
                'ready_at' => '2024-01-20 19:40:00',
                'delivered_at' => '2024-01-20 20:05:00',
            ],
            [
                'restaurant_id' => 2,
                'user_id' => 13,
                'address_id' => 4,
                'order_number' => 'ORD-20240201-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 28.70,
                'tax_amount' => 2.87,
                'delivery_fee' => 2.50,
                'discount_amount' => 4.31,
                'total' => 29.76,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '89 Boulevard Saint-Michel, 75005 Paris',
                'created_at' => '2024-02-05 20:00:00',
                'confirmed_at' => '2024-02-05 20:02:00',
                'ready_at' => '2024-02-05 20:18:00',
                'delivered_at' => '2024-02-05 20:45:00',
            ],
            [
                'restaurant_id' => 2,
                'user_id' => 14,
                'address_id' => 5,
                'order_number' => 'ORD-20240210-0001',
                'type' => 'takeaway',
                'status' => 'completed',
                'subtotal' => 35.80,
                'tax_amount' => 3.58,
                'delivery_fee' => 0,
                'discount_amount' => 0,
                'total' => 39.38,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'created_at' => '2024-02-10 13:00:00',
                'confirmed_at' => '2024-02-10 13:05:00',
                'ready_at' => '2024-02-10 13:20:00',
            ],
            [
                'restaurant_id' => 3,
                'user_id' => 15,
                'address_id' => 7,
                'order_number' => 'ORD-20240301-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 52.60,
                'tax_amount' => 5.26,
                'delivery_fee' => 4.00,
                'discount_amount' => 10.52,
                'total' => 51.34,
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '56 Rue du Commerce, 69002 Lyon',
                'created_at' => '2024-03-08 19:30:00',
                'confirmed_at' => '2024-03-08 19:35:00',
                'ready_at' => '2024-03-08 19:55:00',
                'delivered_at' => '2024-03-08 20:25:00',
            ],
            [
                'restaurant_id' => 3,
                'user_id' => 11,
                'address_id' => 1,
                'order_number' => 'ORD-20240315-0001',
                'type' => 'delivery',
                'status' => 'cancelled',
                'subtotal' => 45.00,
                'tax_amount' => 4.50,
                'delivery_fee' => 4.00,
                'discount_amount' => 0,
                'total' => 53.50,
                'payment_method' => 'card',
                'payment_status' => 'refunded',
                'payment_gateway' => 'stripe',
                'cancellation_reason' => 'Client a changé d\'avis',
                'delivery_address' => '123 Rue de la Paix, 75001 Paris',
                'created_at' => '2024-03-15 12:00:00',
            ],
            [
                'restaurant_id' => 4,
                'user_id' => 16,
                'address_id' => 8,
                'order_number' => 'ORD-20240401-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 22.70,
                'tax_amount' => 2.27,
                'delivery_fee' => 2.00,
                'discount_amount' => 8.00,
                'total' => 18.97,
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '78 Rue de Rivoli, 75004 Paris',
                'created_at' => '2024-04-03 12:45:00',
                'confirmed_at' => '2024-04-03 12:48:00',
                'ready_at' => '2024-04-03 13:05:00',
                'delivered_at' => '2024-04-03 13:25:00',
            ],
            [
                'restaurant_id' => 4,
                'user_id' => 17,
                'address_id' => 9,
                'order_number' => 'ORD-20240415-0001',
                'type' => 'dine_in',
                'status' => 'completed',
                'table_number' => '12',
                'subtotal' => 31.80,
                'tax_amount' => 3.18,
                'delivery_fee' => 0,
                'discount_amount' => 0,
                'total' => 34.98,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'created_at' => '2024-04-15 13:30:00',
                'confirmed_at' => '2024-04-15 13:30:00',
                'ready_at' => '2024-04-15 13:50:00',
            ],
            [
                'restaurant_id' => 5,
                'user_id' => 18,
                'address_id' => 10,
                'order_number' => 'ORD-20240501-0001',
                'type' => 'delivery',
                'status' => 'completed',
                'subtotal' => 28.80,
                'tax_amount' => 2.88,
                'delivery_fee' => 3.00,
                'discount_amount' => 7.20,
                'total' => 27.48,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '290 Rue Paradis, 13006 Marseille',
                'created_at' => '2024-05-10 12:15:00',
                'confirmed_at' => '2024-05-10 12:18:00',
                'ready_at' => '2024-05-10 12:35:00',
                'delivered_at' => '2024-05-10 13:05:00',
            ],
            [
                'restaurant_id' => 5,
                'user_id' => 14,
                'address_id' => 5,
                'order_number' => 'ORD-20240520-0001',
                'type' => 'delivery',
                'status' => 'in_delivery',
                'subtotal' => 42.90,
                'tax_amount' => 4.29,
                'delivery_fee' => 3.00,
                'discount_amount' => 10.00,
                'total' => 40.19,
                'payment_method' => 'online',
                'payment_status' => 'paid',
                'payment_gateway' => 'stripe',
                'delivery_address' => '234 Avenue du Prado, 13008 Marseille',
                'created_at' => now()->subHours(1),
                'confirmed_at' => now()->subMinutes(55),
                'ready_at' => now()->subMinutes(30),
                'delivery_person_id' => 9,
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);
            
            // Créer des items pour la commande
            $products = \App\Models\Product::where('restaurant_id', $order->restaurant_id)
                ->inRandomOrder()
                ->take(rand(2, 4))
                ->get();
            
            foreach ($products as $product) {
                $quantity = rand(1, 3);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'total_price' => $product->price * $quantity,
                    'product_data' => json_encode([
                        'name' => $product->name,
                        'price' => $product->price,
                        'category' => $product->category->name ?? null,
                    ]),
                ]);
            }
            
            // Créer l'historique des statuts
            $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'in_delivery', 'delivered'];
            foreach ($statuses as $index => $status) {
                if (in_array($order->status, ['completed', 'delivered']) || 
                    ($order->status === 'cancelled' && $status !== 'cancelled') ||
                    array_search($status, $statuses) <= array_search($order->status, $statuses)) {
                    
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'user_id' => rand(1, 10),
                        'status' => $status,
                        'comment' => $status === 'delivered' ? 'Livré avec succès' : null,
                        'created_at' => $order->created_at->addMinutes($index * 5),
                    ]);
                    
                    if ($status === $order->status) break;
                }
            }
        }
    }
}