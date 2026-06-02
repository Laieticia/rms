<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            MenuSeeder::class,
            CouponSeeder::class,
            AddressSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            LoyaltyRewardSeeder::class,
            DeliveryZoneSeeder::class,
            ReservationSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
