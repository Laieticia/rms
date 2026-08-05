<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoyaltyLevelUp extends Notification implements ShouldQueue
{
    use Queueable;

    protected const LABELS = [
        'bronze' => 'Bronze',
        'silver' => 'Argent',
        'gold' => 'Or',
    ];

    public function __construct(protected string $newLevel)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $label = self::LABELS[$this->newLevel] ?? ucfirst($this->newLevel);

        return (new MailMessage)
            ->subject('Félicitations, vous passez au niveau ' . $label . ' !')
            ->greeting("Bonjour {$notifiable->first_name},")
            ->line("Grâce à votre fidélité, vous venez d'atteindre le niveau {$label} !")
            ->line('Profitez des avantages associés à votre nouveau statut sur vos prochaines commandes.')
            ->action('Voir mes avantages', route('profile.index'));
    }

    public function toDatabase($notifiable): array
    {
        $label = self::LABELS[$this->newLevel] ?? ucfirst($this->newLevel);

        return [
            'title' => 'Nouveau niveau de fidélité',
            'message' => "Vous avez atteint le niveau {$label} !",
            'level' => $this->newLevel,
        ];
    }
}
