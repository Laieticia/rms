<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
             RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            RestaurantSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            ProductVariantSeeder::class,
            ProductOptionSeeder::class,
            ProductOptionItemSeeder::class,
            MenuSeeder::class,
            MenuItemSeeder::class,
            CouponSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            ReviewSeeder::class,
            AddressSeeder::class,
            ReservationSeeder::class,
            FavoriteSeeder::class,
            DeliveryZoneSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
