<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Restaurant;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen on the channel.
|
*/

// Private channel for restaurant orders
// Only authenticated users who own the restaurant can listen
Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    // Check if user owns this restaurant or is a team member
    $restaurant = Restaurant::find($restaurantId);
    
    if (!$restaurant) {
        return false;
    }
    
    // Check if user is the owner
    if ($restaurant->user_id === $user->id) {
        return true;
    }
    
    // Check if user is a team member (if you have team functionality)
    // return $restaurant->teamMembers()->where('user_id', $user->id)->exists();
    
    return false;
});

// Admin channel for all orders (optional - for admin dashboard)
Broadcast::channel('admin.orders', function ($user) {
    return $user->role === 'admin';
});
