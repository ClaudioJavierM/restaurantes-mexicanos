<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;

class MenuItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, MenuItem $menuItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $restaurantId = $menuItem->category?->restaurant_id;
            return $restaurantId && $user->restaurants()->where('id', $restaurantId)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $restaurantId = $menuItem->category?->restaurant_id;
            return $restaurantId && $user->restaurants()->where('id', $restaurantId)->exists();
        }

        return false;
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $restaurantId = $menuItem->category?->restaurant_id;
            return $restaurantId && $user->restaurants()->where('id', $restaurantId)->exists();
        }

        return false;
    }

    public function restore(User $user, MenuItem $menuItem): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, MenuItem $menuItem): bool
    {
        return $user->isAdmin();
    }
}
