<?php

namespace App\Events;

use App\Models\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->reservation->restaurant_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reservation.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'customer_name' => $this->reservation->customer_name,
            'date' => $this->reservation->date->format('d/m/Y'),
            'time' => $this->reservation->time->format('H:i'),
            'guests_count' => $this->reservation->guests_count,
            'special_requests' => $this->reservation->special_requests,
        ];
    }
}