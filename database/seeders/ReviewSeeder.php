<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Order;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();
        $orders = Order::all();
        
        $comments = [
            'Très bon restaurant, je recommande!',
            'Plats délicieux, service rapide.',
            'Un peu cher mais qualité au rendez-vous.',
            'Livraison rapide, commande conforme.',
            'Bonne expérience, je reviendrai.',
            'Rapport qualité prix excellent.',
            'Service client réactif.',
        ];

        foreach ($orders as $order) {
            if (rand(0, 1)) {
                Review::create([
                    'user_id' => $order->user_id,
                    'restaurant_id' => $order->restaurant_id,
                    'order_id' => $order->id,
                    'product_id' => null,
                    'rating' => rand(3, 5),
                    'food_rating' => rand(3, 5),
                    'delivery_rating' => rand(3, 5),
                    'service_rating' => rand(3, 5),
                    'comment' => $comments[array_rand($comments)],
                    'is_approved' => true,
                    'helpful_count' => rand(0, 20),
                    'created_at' => $order->created_at,
                ]);
            }
        }
    }
}