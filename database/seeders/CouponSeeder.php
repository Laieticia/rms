<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\Product;
use App\Models\Category;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $couponTypes = [
            ['type' => 'percentage', 'value' => 10],
            ['type' => 'percentage', 'value' => 15],
            ['type' => 'percentage', 'value' => 20],
            ['type' => 'fixed_amount', 'value' => 5],
            ['type' => 'fixed_amount', 'value' => 10],
            ['type' => 'free_delivery', 'value' => 0],
        ];
        
        foreach ($restaurants as $restaurant) {
            // Chaque restaurant a 2-3 coupons
            $nbCoupons = rand(2, 3);
            for ($i = 0; $i < $nbCoupons; $i++) {
                $couponData = $couponTypes[array_rand($couponTypes)];
                
                $coupon = Coupon::create([
                    'restaurant_id' => $restaurant->id,
                    'code' => strtoupper(substr($restaurant->name, 0, 3)) . rand(100, 999),
                    'description' => "Offre spéciale sur votre commande",
                    'type' => $couponData['type'],
                    'value' => $couponData['value'],
                    'min_order_amount' => rand(1500, 3000) / 100,
                    'max_discount_amount' => $couponData['type'] === 'percentage' ? rand(1000, 2000) / 100 : null,
                    'max_uses' => rand(50, 200),
                    'max_uses_per_user' => 1,
                    'is_active' => true,
                    'starts_at' => now(),
                    'expires_at' => now()->addMonths(rand(1, 3)),
                    'applies_to_all' => rand(0, 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Si le coupon ne s'applique pas à tout, attacher des produits spécifiques
                if (!$coupon->applies_to_all) {
                    $products = Product::where('restaurant_id', $restaurant->id)
                                       ->inRandomOrder()
                                       ->take(rand(3, 5))
                                       ->get();
                    
                    foreach ($products as $product) {
                        $coupon->products()->attach($product->id);
                    }
                }
            }
        }
    }
}