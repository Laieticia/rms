<?php

namespace App\Notifications;

use App\Helpers\CameroonHelper;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
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
            ->subject("Confirmation de votre commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre commande #{$this->order->order_number} chez {$this->order->restaurant->name} a bien été reçue.")
            ->line('Montant total : ' . CameroonHelper::formatCurrency($this->order->total))
            ->line('Nous vous tiendrons informé(e) de son avancement.')
            ->action('Suivre ma commande', route('orders.track', $this->order))
            ->line('Merci de votre confiance !');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'title' => 'Commande reçue',
            'message' => "Votre commande #{$this->order->order_number} a été reçue.",
        ];
    }
}
