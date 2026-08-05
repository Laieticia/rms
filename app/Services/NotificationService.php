<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer une notification de statut de commande
     */
    public function sendOrderStatusUpdate(Order $order, string $status): void
    {
        $user = $order->user;
        
        if (!$user) return;

        $messages = [
            'confirmed' => 'Votre commande #' . $order->order_number . ' a été confirmée et sera bientôt préparée.',
            'preparing' => 'Votre commande #' . $order->order_number . ' est en cours de préparation.',
            'ready' => 'Votre commande #' . $order->order_number . ' est prête !',
            'in_delivery' => 'Votre commande #' . $order->order_number . ' est en cours de livraison.',
            'delivered' => 'Votre commande #' . $order->order_number . ' a été livrée. Bon appétit ! 🎉',
            'completed' => 'Votre commande #' . $order->order_number . ' est terminée. Merci !',
            'cancelled' => 'Votre commande #' . $order->order_number . ' a été annulée.',
        ];

        // Créer une notification en base de données
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'order_status',
            'title' => 'Mise à jour de commande',
            'message' => $messages[$status] ?? 'Statut de commande mis à jour.',
            'data' => json_encode([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $status,
            ]),
        ]);
    }

    /**
     * Notifier le staff du restaurant
     */
    public function notifyRestaurantStaff(Restaurant $restaurant, string $title, string $message, array $data = []): void
    {
        $staff = $restaurant->users()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'manager', 'chef']))
            ->get();

        foreach ($staff as $user) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => $data['type'] ?? 'info',
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
            ]);
        }
    }

    /**
     * Envoyer une notification au livreur
     */
    public function notifyDeliveryPerson(User $deliveryPerson, Order $order): void
    {
        \App\Models\Notification::create([
            'user_id' => $deliveryPerson->id,
            'type' => 'delivery',
            'title' => 'Nouvelle livraison assignée',
            'message' => "Vous avez été assigné à la commande #{$order->order_number}",
            'data' => json_encode([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'address' => $order->delivery_address,
            ]),
        ]);
    }

    /**
     * Envoyer un SMS via la passerelle Nexah.
     * En local (SMS_DRIVER=log dans .env), le SMS est simplement journalisé
     * au lieu d'être réellement envoyé — pratique pour développer sans compte Nexah.
     */
    public function sendSMS(string $phone, string $message): bool
    {
        $validation = PhoneValidationService::validate($phone);

        if (!$validation['valid']) {
            Log::warning("SMS non envoyé : numéro invalide ({$phone})");
            return false;
        }

        $driver = config('services.nexah.driver', 'log');

        if ($driver === 'log') {
            Log::info("[SMS SIMULÉ] À {$validation['formatted']} : {$message}");
            return true;
        }

        try {
            $response = Http::asForm()->post(config('services.nexah.url'), [
                'user' => config('services.nexah.user'),
                'password' => config('services.nexah.password'),
                'senderid' => config('services.nexah.sender_id'),
                'sms' => $message,
                // Nexah attend le numéro au format local sans le +237
                'mobiles' => PhoneValidationService::cleanPhone($phone),
            ]);

            if (!$response->successful()) {
                Log::error("Échec envoi SMS Nexah pour {$phone} : " . $response->body());
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Erreur envoi SMS Nexah pour {$phone} : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer la facture/récapitulatif d'une commande au client par email.
     */
    public function sendInvoice(Order $order): void
    {
        if (!$order->user || !$order->user->email) {
            return;
        }

        $order->user->notify(new \App\Notifications\OrderInvoice($order));
    }
}