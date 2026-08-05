<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Reservation $reservation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Réservation annulée — {$this->reservation->restaurant->name}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre réservation du {$this->reservation->date->format('d/m/Y')} à {$this->reservation->time->format('H:i')} chez {$this->reservation->restaurant->name} a été annulée.")
            ->line("Motif : {$this->reservation->cancellation_reason}")
            ->line('N\'hésitez pas à réserver un autre créneau.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'title' => 'Réservation annulée',
            'message' => "Votre réservation du {$this->reservation->date->format('d/m/Y')} a été annulée : {$this->reservation->cancellation_reason}",
        ];
    }
}
