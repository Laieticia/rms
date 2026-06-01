<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $titles = [
            'Bienvenue sur notre plateforme!',
            'Votre commande est confirmée',
            'Promotion exclusive -20%',
            'Nouveau restaurant disponible',
            'Votre commande est en livraison',
        ];
        
        $messages = [
            'Merci de vous être inscrit.',
            'Votre commande a été confirmée avec succès.',
            'Profitez de notre offre spéciale aujourd\'hui.',
            'Découvrez nos nouveaux partenaires.',
            'Votre livreur est en route.',
        ];
        
        foreach ($users as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $index = array_rand($titles);
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'system',
                    'title' => $titles[$index],
                    'message' => $messages[$index],
                    'is_read' => rand(0, 1),
                    'channel' => 'database',
                    'created_at' => now()->subDays(rand(0, 10)),
                ]);
            }
        }
    }
}