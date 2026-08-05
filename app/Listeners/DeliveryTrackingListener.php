<?php

namespace App\Listeners;

use App\Events\DeliveryLocationUpdated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeliveryTrackingListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Le broadcast Reverb (canal privé orders.{id}) met déjà à jour la carte en
     * temps réel côté client. Ce listener ne gère que les notifications
     * ponctuelles qui ne doivent pas dépendre du fait que le client ait
     * l'app ouverte (ex: arrivée du livreur).
     */
    public function handleLocationUpdate(DeliveryLocationUpdated $event): void
    {
        $tracking = $event->tracking;

        if ($tracking->status === 'arrived') {
            $order = $tracking->order;

            if ($order && $order->user) {
                \App\Models\Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'delivery',
                    'title' => 'Votre livreur est arrivé',
                    'message' => "Votre livreur est arrivé pour la commande #{$order->order_number}.",
                    'data' => json_encode(['order_id' => $order->id]),
                ]);

                if ($order->user->phone) {
                    $this->notificationService->sendSMS(
                        $order->user->phone,
                        "Votre livreur est arrivé pour la commande #{$order->order_number}."
                    );
                }
            }
        }
    }
}
