<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReservationListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation;

        $this->notificationService->notifyRestaurantStaff(
            $reservation->restaurant,
            'Nouvelle réservation',
            "{$reservation->customer_name} — {$reservation->guests_count} personne(s) le {$reservation->date->format('d/m/Y')} à {$reservation->time->format('H:i')}",
            [
                'type' => 'reservation',
                'reservation_id' => $reservation->id,
            ]
        );
    }
}
