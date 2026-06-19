<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->order->restaurant_id),
            new PrivateChannel('admin.notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.order';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->user->full_name,
            'total' => number_format($this->order->total, 2) . ' €',
            'items_count' => $this->order->items->sum('quantity'),
            'type' => $this->order->type,
            'type_label' => $this->order->type == 'delivery' ? '🛵 Livraison' : ($this->order->type == 'takeaway' ? '🥡 À emporter' : '🏠 Sur place'),
            'created_at' => $this->order->created_at->diffForHumans(),
            'status' => $this->order->status,
        ];
    }
}