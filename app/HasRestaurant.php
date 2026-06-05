<?php

namespace App;

use App\Models\Restaurant;

trait HasRestaurant
{
    protected function getRestaurantId(): ?int
    {
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }

        // Si un restaurant_id est passé en paramètre (pour les admins)
        if (request()->filled('restaurant_id')) {
            $restaurantId = request()->get('restaurant_id');
            
            // Vérifier que l'utilisateur a accès à ce restaurant
            if ($user->isAdmin() || $user->restaurants()->where('restaurant_id', $restaurantId)->exists()) {
                return $restaurantId;
            }
        }

        // Pour les admins sans restaurant spécifié, retourner le premier restaurant
        if ($user->isAdmin()) {
            return Restaurant::first()?->id;
        }

        // Pour les autres utilisateurs, retourner leur restaurant assigné
        $restaurant = $user->restaurants()->first();
        
        return $restaurant ? $restaurant->id : Restaurant::first()?->id;
    }

    protected function getUserRestaurants()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return Restaurant::all();
        }

        return $user->restaurants;
    }
}
