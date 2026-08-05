<?php

namespace App\Notifications;

use App\Helpers\CameroonHelper;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
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
            ->subject("Paiement reçu pour la commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line('Nous avons bien reçu votre paiement de ' . CameroonHelper::formatCurrency($this->order->total) . '.')
            ->line("Votre commande #{$this->order->order_number} est en cours de traitement.")
            ->action('Voir ma commande', route('orders.track', $this->order));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Paiement reçu',
            'message' => 'Votre paiement de ' . CameroonHelper::formatCurrency($this->order->total) . ' a été confirmé.',
        ];
    }
}
