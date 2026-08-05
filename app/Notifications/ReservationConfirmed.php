<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification implements ShouldQueue
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
        $mail = (new MailMessage)
            ->subject("Réservation confirmée — {$this->reservation->restaurant->name}")
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Votre réservation du {$this->reservation->date->format('d/m/Y')} à {$this->reservation->time->format('H:i')} pour {$this->reservation->guests_count} personne(s) a été confirmée par {$this->reservation->restaurant->name}.");

        if ($this->reservation->table_number) {
            $mail->line("Table attribuée : {$this->reservation->table_number}");
        }

        return $mail->line('Nous avons hâte de vous accueillir !');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'title' => 'Réservation confirmée',
            'message' => "Votre réservation du {$this->reservation->date->format('d/m/Y')} à {$this->reservation->time->format('H:i')} est confirmée.",
        ];
    }
}
