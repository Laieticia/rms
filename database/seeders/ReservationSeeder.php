<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Restaurant;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();
        $statuses = ['pending', 'confirmed', 'arrived', 'completed', 'cancelled'];
        
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();
            $restaurant = $restaurants->random();
            $date = now()->addDays(rand(1, 30));
            
            Reservation::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'reservation_number' => 'RES-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'date' => $date,
                'time' => rand(12, 21) . ':00:00',
                'guests_count' => rand(2, 8),
                'special_requests' => rand(0, 1) ? 'Pas d\'oignons svp' : null,
                'status' => $statuses[array_rand($statuses)],
                'customer_name' => $user->name,
                'customer_phone' => '06' . rand(10000000, 99999999),
                'customer_email' => $user->email,
                'created_at' => now(),
            ]);
        }
    }
}