<?php

namespace App\Observers;

use App\Models\Favorite;
use App\Models\OwnerNotification;

class FavoriteObserver
{
    public function created(Favorite $favorite): void
    {
        // Only notify for claimed restaurants
        if ($favorite->restaurant->is_claimed && $favorite->restaurant->owner_id && $favorite->user_id) {
            OwnerNotification::notifyNewFavorite($favorite->restaurant, $favorite->user);
        }
    }
}
