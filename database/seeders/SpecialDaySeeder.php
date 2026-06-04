<?php

namespace Database\Seeders;

use App\Models\SpecialDay;
use Illuminate\Database\Seeder;

class SpecialDaySeeder extends Seeder
{
    public function run(): void
    {
        $specialDays = [
            [
                'restaurant_id' => 1,
                'name' => 'Noël',
                'date' => '2024-12-25',
                'is_closed' => true,
                'description' => 'Fermé pour le jour de Noël',
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Jour de l\'An',
                'date' => '2025-01-01',
                'is_closed' => true,
                'description' => 'Fermé pour le jour de l\'An',
            ],
            [
                'restaurant_id' => 1,
                'name' => 'Saint-Valentin',
                'date' => '2025-02-14',
                'open_time' => '18:00',
                'close_time' => '00:00',
                'is_closed' => false,
                'description' => 'Soirée spéciale Saint-Valentin - Menu spécial',
            ],
            [
                'restaurant_id' => 2,
                'name' => 'Fête de la Musique',
                'date' => '2025-06-21',
                'open_time' => '11:00',
                'close_time' => '02:00',
                'is_closed' => false,
                'description' => 'Ouverture exceptionnelle pour la Fête de la Musique',
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Fermeture annuelle',
                'date' => '2025-08-01',
                'is_closed' => true,
                'description' => 'Début des congés d\'été',
            ],
            [
                'restaurant_id' => 3,
                'name' => 'Fermeture annuelle',
                'date' => '2025-08-15',
                'is_closed' => true,
                'description' => 'Congés d\'été',
            ],
        ];

        foreach ($specialDays as $day) {
            SpecialDay::create($day);
        }
    }
}