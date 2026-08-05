<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);

    if (!$order) {
        return false;
    }

    return $order->user_id === $user->id
        || $order->delivery_person_id === $user->id
        || $user->restaurants()->where('restaurants.id', $order->restaurant_id)->exists();
});

Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    return $user->restaurants()->where('restaurants.id', $restaurantId)->exists()
        || $user->isAdmin();
});
