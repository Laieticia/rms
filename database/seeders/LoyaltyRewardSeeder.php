<?php

namespace Database\Seeders;

use App\Models\LoyaltyReward;
use Illuminate\Database\Seeder;

class LoyaltyRewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'restaurant_id' => 1,
                'name' => 'Café offert',
                'description' => 'Un café ou thé de votre choix offert',
                'points_cost' => 100,
                'reward_type' => 'free_product',
                'free_product_id' => null,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Dessert gratuit',
                'description' => 'Un dessert au choix de la carte',
                'points_cost' => 250,
                'reward_type' => 'free_product',
                'free_product_id' => null,
                'is_active' => true,
                'stock' => 50,
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Réduction 5€',
                'description' => '5€ de réduction sur votre prochaine commande',
                'points_cost' => 300,
                'reward_type' => 'discount',
                'discount_value' => 5.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Pizza Margherita offerte',
                'description' => 'Une pizza Margherita gratuite',
                'points_cost' => 200,
                'reward_type' => 'free_product',
                'free_product_id' => 6,
                'is_active' => true,
                'stock' => 100,
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Livraison gratuite',
                'description' => 'Frais de livraison offerts sur votre prochaine commande',
                'points_cost' => 150,
                'reward_type' => 'free_delivery',
                'is_active' => true,
                'stock' => null,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'California offert',
                'description' => 'Un California Saumon Avocat gratuit',
                'points_cost' => 250,
                'reward_type' => 'free_product',
                'free_product_id' => 14,
                'is_active' => true,
                'stock' => 30,
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Réduction 10%',
                'description' => '10% de réduction sur l\'ensemble de la carte',
                'points_cost' => 400,
                'reward_type' => 'discount',
                'discount_value' => 10.00,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Burger Classic offert',
                'description' => 'Un Classic Burger gratuit',
                'points_cost' => 200,
                'reward_type' => 'free_product',
                'free_product_id' => 20,
                'is_active' => true,
                'stock' => 75,
            ],
            [
                'restaurant_id' => 4,
                'name' => 'Frites gratuites',
                'description' => 'Une portion de frites maison offerte',
                'points_cost' => 100,
                'reward_type' => 'free_product',
                'free_product_id' => 24,
                'is_active' => true,
                'stock' => null,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Smoothie offert',
                'description' => 'Un smoothie bowl au choix',
                'points_cost' => 250,
                'reward_type' => 'free_product',
                'free_product_id' => 30,
                'is_active' => true,
                'stock' => 40,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Menu Vitalité à -50%',
                'description' => '50% de réduction sur le Menu Vitalité',
                'points_cost' => 500,
                'reward_type' => 'discount',
                'discount_value' => 50.00,
                'is_active' => true,
                'stock' => 20,
            ],
            [
                'restaurant_id' => 5,
                'name' => 'Jus détox gratuit',
                'description' => 'Un Green Detox offert',
                'points_cost' => 150,
                'reward_type' => 'free_product',
                'free_product_id' => 29,
                'is_active' => true,
                'stock' => null,
            ],
        ];

        foreach ($rewards as $rewardData) {
            LoyaltyReward::create($rewardData);
        }
    }
}