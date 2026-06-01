<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Address;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'in_delivery', 'delivered', 'completed'];
        
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();
            $restaurant = $restaurants->random();
            $address = Address::where('user_id', $user->id)->first();
            
            $subtotal = rand(2000, 8000) / 100;
            $deliveryFee = rand(200, 500) / 100;
            $total = $subtotal + $deliveryFee;
            
            Order::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'coupon_id' => null,
                'delivery_person_id' => null,
                'order_number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'type' => 'delivery',
                'status' => $statuses[array_rand($statuses)],
                'delivery_address' => $address?->street_address,
                'delivery_city' => $address?->city,
                'delivery_postal_code' => $address?->postal_code,
                'subtotal' => $subtotal,
                'tax_amount' => $subtotal * 0.20,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => 0,
                'tip_amount' => rand(0, 500) / 100,
                'total' => $total,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'source' => 'web',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}