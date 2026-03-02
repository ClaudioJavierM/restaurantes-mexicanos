<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can see all restaurants
        if ($user->isAdmin()) {
            return true;
        }

        // Owners can see their restaurants
        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Restaurant $restaurant): bool
    {
        // Admins can see all restaurants
        if ($user->isAdmin()) {
            return true;
        }

        // Owners can only see their own restaurants
        if ($user->isOwner()) {
            return $restaurant->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create restaurants from the panel
        // Owners get their restaurants through the claim process
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
        // Admins can update any restaurant
        if ($user->isAdmin()) {
            return true;
        }

        // Owners can only update their own restaurants
        if ($user->isOwner()) {
            return $restaurant->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Restaurant $restaurant): bool
    {
        // Only admins can delete restaurants
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Restaurant $restaurant): bool
    {
        // Only admins can restore restaurants
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Restaurant $restaurant): bool
    {
        // Only admins can permanently delete restaurants
        return $user->isAdmin();
    }
}
