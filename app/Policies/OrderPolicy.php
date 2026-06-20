<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager', 'chef', 'waiter']);
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->id === $order->user_id) return true;
        return $user->restaurants()->where('restaurant_id', $order->restaurant_id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) return true;
        return $user->restaurants()->where('restaurant_id', $order->restaurant_id)->exists();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}