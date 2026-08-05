<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderInDelivery extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Commande en livraison #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre commande #{$this->order->order_number} est en cours de livraison.")
            ->action('Suivre ma commande', route('orders.track', $this->order));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Commande en livraison',
            'message' => "Votre commande #{$this->order->order_number} est en cours de livraison.",
        ];
    }
}
