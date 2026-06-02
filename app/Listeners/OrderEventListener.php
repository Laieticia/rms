<?php

namespace App\Listeners;

use App\Events\NewOrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\PaymentCompleted;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderEventListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handleNewOrder(NewOrderPlaced $event): void
    {
        $order = $event->order;

        // Notifier le restaurant
        $this->notificationService->notifyRestaurantStaff(
            $order->restaurant,
            'Nouvelle commande #' . $order->order_number,
            "Une nouvelle commande de {$order->user->full_name} a été reçue",
            [
                'order_id' => $order->id,
                'type' => 'new_order',
                'priority' => 'high',
            ]
        );

        // Envoyer un email de confirmation au client
        $order->user->notify(new \App\Notifications\OrderConfirmation($order));

        // Envoyer un SMS si configuré
        if ($order->user->phone) {
            $this->notificationService->sendSMS(
                $order->user->phone,
                "Votre commande #{$order->order_number} a été confirmée. Montant: {$order->formatted_total}"
            );
        }

        // Logger l'activité
        activity()
            ->performedOn($order)
            ->causedBy($order->user)
            ->log('Nouvelle commande créée');
    }

    public function handleStatusChange(OrderStatusChanged $event): void
    {
        $order = $event->order;

        // Mettre à jour les timestamps
        switch ($event->newStatus) {
            case 'confirmed':
                $order->update(['confirmed_at' => now()]);
                break;
            case 'preparing':
                $order->update(['prepared_at' => now()]);
                break;
            case 'ready':
                $order->update(['ready_at' => now()]);
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                break;
        }

        // Notifier le client selon le statut
        $this->notifyCustomerAboutStatus($order, $event->newStatus);

        // Si la commande est livrée, attribuer des points de fidélité
        if (in_array($event->newStatus, ['delivered', 'completed'])) {
            $this->awardLoyaltyPoints($order);
        }
    }

    public function handlePaymentCompleted(PaymentCompleted $event): void
    {
        $order = $event->order;

        // Mettre à jour le statut de la commande
        if ($order->status === 'pending') {
            $order->updateStatus('confirmed', 'Paiement reçu');
        }

        // Envoyer la facture
        $this->notificationService->sendInvoice($order);

        // Notification de paiement
        $order->user->notify(new \App\Notifications\PaymentReceived($order));
    }

    protected function notifyCustomerAboutStatus($order, string $status): void
    {
        $notificationData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $status,
        ];

        $notifications = [
            'confirmed' => \App\Notifications\OrderConfirmed::class,
            'preparing' => \App\Notifications\OrderPreparing::class,
            'ready' => \App\Notifications\OrderReady::class,
            'in_delivery' => \App\Notifications\OrderInDelivery::class,
            'delivered' => \App\Notifications\OrderDelivered::class,
            'cancelled' => \App\Notifications\OrderCancelled::class,
        ];

        if (isset($notifications[$status])) {
            $order->user->notify(new $notifications[$status]($order));
        }
    }

    protected function awardLoyaltyPoints($order): void
    {
        $points = floor($order->total); // 1 point par euro dépensé
        
        // Bonus points selon le type de commande
        if ($order->type === 'delivery') {
            $points += 5; // Bonus pour livraison
        }

        $order->user->addLoyaltyPoints(
            $points,
            "Commande #{$order->order_number}",
            $order
        );

        // Vérifier si l'utilisateur atteint un nouveau niveau
        $this->checkLoyaltyLevel($order->user);
    }

    protected function checkLoyaltyLevel($user): void
    {
        $totalPoints = $user->getLoyaltyBalance();
        $newLevel = null;

        if ($totalPoints >= 1000) {
            $newLevel = 'gold';
        } elseif ($totalPoints >= 500) {
            $newLevel = 'silver';
        } elseif ($totalPoints >= 100) {
            $newLevel = 'bronze';
        }

        if ($newLevel && $user->loyalty_level !== $newLevel) {
            $user->update(['loyalty_level' => $newLevel]);
            $user->notify(new \App\Notifications\LoyaltyLevelUp($newLevel));
        }
    }
}