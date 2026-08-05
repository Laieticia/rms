<?php

namespace App\Notifications;

use App\Helpers\CameroonHelper;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderInvoice extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Facture — Commande #{$this->order->order_number}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Voici le récapitulatif de votre commande #{$this->order->order_number} chez {$this->order->restaurant->name}.");

        foreach ($this->order->items as $item) {
            $mail->line("{$item->quantity} x {$item->product_name} — " . CameroonHelper::formatCurrency($item->total_price));
        }

        $mail->line('---')
            ->line('Sous-total : ' . CameroonHelper::formatCurrency($this->order->subtotal));

        if ($this->order->discount_amount > 0) {
            $mail->line('Réduction : -' . CameroonHelper::formatCurrency($this->order->discount_amount));
        }

        if ($this->order->delivery_fee > 0) {
            $mail->line('Frais de livraison : ' . CameroonHelper::formatCurrency($this->order->delivery_fee));
        }

        $mail->line('Total : ' . CameroonHelper::formatCurrency($this->order->total))
            ->action('Voir ma commande', route('orders.track', $this->order))
            ->line('Merci de votre confiance !');

        return $mail;
    }
}
