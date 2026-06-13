<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;

class CouponPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function view(User $user, Coupon $coupon): bool
    {
        if ($user->isAdmin()) return true;
        return $user->restaurants()->where('restaurant_id', $coupon->restaurant_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function update(User $user, Coupon $coupon): bool
    {
        if ($user->hasRole(['super_admin', 'admin'])) return true;
        if ($user->hasRole('manager')) {
            return $user->restaurants()->where('restaurant_id', $coupon->restaurant_id)->exists();
        }
        return false;
    }

    public function delete(User $user, Coupon $coupon): bool
    {
        return $this->update($user, $coupon);
    }
}