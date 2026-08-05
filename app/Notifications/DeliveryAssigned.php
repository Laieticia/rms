<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouvelle livraison — Commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Vous avez été assigné(e) à la livraison de la commande #{$this->order->order_number}.")
            ->line("Adresse : {$this->order->delivery_address}, {$this->order->delivery_city}")
            ->action('Voir la commande', route('delivery.dashboard'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Nouvelle livraison assignée',
            'message' => "Livraison de la commande #{$this->order->order_number} à {$this->order->delivery_city}.",
        ];
    }
}
