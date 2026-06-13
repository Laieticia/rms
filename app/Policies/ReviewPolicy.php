<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function view(User $user, Review $review): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->id === $review->user_id) return true;
        return $user->restaurants()->where('restaurant_id', $review->restaurant_id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        if ($user->isAdmin()) return true;
        return $user->restaurants()->where('restaurant_id', $review->restaurant_id)->exists();
    }

    public function delete(User $user, Review $review): bool
    {
        if ($user->isAdmin()) return true;
        return $user->restaurants()->where('restaurant_id', $review->restaurant_id)->exists();
    }
}