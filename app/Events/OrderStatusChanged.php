<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('orders.' . $this->order->id),
            new PrivateChannel('restaurant.' . $this->order->restaurant_id),
        ];

        // Si un livreur est assigné, lui envoyer aussi
        if ($this->order->delivery_person_id) {
            $channels[] = new PrivateChannel('delivery.' . $this->order->delivery_person_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_label' => $this->order->status_label,
            'status_color' => $this->order->status_color,
            'total' => $this->order->formatted_total,
            'timestamp' => now()->toDateTimeString(),
            'message' => "Commande {$this->order->order_number} : " . $this->order->status_label,
        ];
    }
}
