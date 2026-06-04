<?php

namespace Database\Seeders;

use App\Models\OperatingHour;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class OperatingHourSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();
        
        foreach ($restaurants as $restaurant) {
            $defaultHours = OperatingHour::getDefaultHours();
            
            foreach ($defaultHours as $day => $hours) {
                OperatingHour::create([
                    'restaurant_id' => $restaurant->id,
                    'day' => $day,
                    'open_time' => $hours['open_time'],
                    'close_time' => $hours['close_time'],
                    'is_closed' => false,
                ]);
            }
        }
    }
}