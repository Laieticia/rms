<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager', 'chef', 'waiter']);
    }

    public function view(User $user, Menu $menu): bool
    {
        if ($user->isAdmin()) return true;
        return $user->restaurants()->where('restaurant_id', $menu->restaurant_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function update(User $user, Menu $menu): bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) return true;
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('restaurant_id', $menu->restaurant_id)->exists();
        }
        return false;
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $this->update($user, $menu);
    }
}