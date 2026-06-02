<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'user_id' => 11,
                'type' => 'order_status',
                'title' => 'Commande confirmée',
                'message' => 'Votre commande ORD-20240101-0001 a été confirmée et est en préparation.',
                'data' => json_encode(['order_id' => 1, 'order_number' => 'ORD-20240101-0001']),
                'is_read' => true,
                'read_at' => now()->subDays(10),
                'channel' => 'database',
            ],
            [
                'user_id' => 11,
                'type' => 'order_status',
                'title' => 'Commande livrée',
                'message' => 'Votre commande a été livrée avec succès. Bon appétit !',
                'data' => json_encode(['order_id' => 1]),
                'is_read' => true,
                'read_at' => now()->subDays(9),
                'channel' => 'database',
            ],
            [
                'user_id' => 11,
                'type' => 'promotion',
                'title' => 'Offre spéciale !',
                'message' => 'Profitez de -20% sur votre prochaine commande avec le code BISTRO20',
                'data' => json_encode(['coupon_code' => 'BISTRO20']),
                'is_read' => false,
                'channel' => 'database',
            ],
            [
                'user_id' => 11,
                'type' => 'loyalty',
                'title' => 'Points de fidélité',
                'message' => 'Félicitations ! Vous avez gagné 54 points de fidélité.',
                'data' => json_encode(['points' => 54]),
                'is_read' => true,
                'read_at' => now()->subDays(8),
                'channel' => 'database',
            ],
            [
                'user_id' => 12,
                'type' => 'order_status',
                'title' => 'Commande en préparation',
                'message' => 'Votre commande ORD-20240102-0001 est en cours de préparation.',
                'data' => json_encode(['order_id' => 2]),
                'is_read' => true,
                'read_at' => now()->subDays(5),
                'channel' => 'database',
            ],
            [
                'user_id' => 12,
                'type' => 'promotion',
                'title' => 'Nouveau menu !',
                'message' => 'Découvrez notre nouveau menu d\'été avec des produits frais de saison.',
                'data' => json_encode(['restaurant_id' => 1]),
                'is_read' => false,
                'channel' => 'database',
            ],
            [
                'user_id' => 13,
                'type' => 'order_status',
                'title' => 'Commande livrée',
                'message' => 'Votre pizza est arrivée ! Bon appétit.',
                'data' => json_encode(['order_id' => 3]),
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'channel' => 'database',
            ],
            [
                'user_id' => 13,
                'type' => 'review',
                'title' => 'Avis publié',
                'message' => 'Votre avis sur Pizza Roma a été publié. Merci pour votre contribution !',
                'data' => json_encode(['review_id' => 3]),
                'is_read' => true,
                'read_at' => now()->subDays(2),
                'channel' => 'database',
            ],
            [
                'user_id' => 14,
                'type' => 'reservation',
                'title' => 'Réservation confirmée',
                'message' => 'Votre réservation du 18/06 à 19h00 pour 8 personnes est confirmée.',
                'data' => json_encode(['reservation_id' => 4]),
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'channel' => 'database',
            ],
            [
                'user_id' => 15,
                'type' => 'promotion',
                'title' => 'Offre de parrainage',
                'message' => 'Parrainez un ami et gagnez 200 points de fidélité !',
                'data' => json_encode(['referral_code' => 'SUSHI15REF']),
                'is_read' => false,
                'channel' => 'database',
            ],
            [
                'user_id' => 16,
                'type' => 'system',
                'title' => 'Bienvenue !',
                'message' => 'Bienvenue sur notre plateforme ! Découvrez nos restaurants et commandez en quelques clics.',
                'is_read' => true,
                'read_at' => now()->subMonths(2),
                'channel' => 'database',
            ],
            [
                'user_id' => 17,
                'type' => 'order_status',
                'title' => 'Commande prête',
                'message' => 'Votre commande est prête ! Présentez-vous au comptoir.',
                'data' => json_encode(['order_id' => 8]),
                'is_read' => true,
                'read_at' => now()->subDays(4),
                'channel' => 'database',
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}