<?php

namespace App\Providers;

use App\Models\Restaurant;
use App\Observers\RestaurantObserver;
use App\Models\Review;
use App\Observers\ReviewObserver;
use App\Models\Favorite;
use App\Observers\FavoriteObserver;
use App\Models\PickupOrder;
use App\Observers\PickupOrderObserver;
use App\Models\Reservation;
use App\Observers\ReservationObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Restaurant Observer for automatic cache invalidation
        Restaurant::observe(RestaurantObserver::class);
        
        // Register Review Observer for notifications
        Review::observe(ReviewObserver::class);
        
        // Log all sent emails
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Mail\Events\MessageSent::class,
            \App\Listeners\LogSentEmail::class
        );
        
        // Register Favorite Observer for notifications
        Favorite::observe(FavoriteObserver::class);

        // WhatsApp notifications to owners on new orders/reservations
        PickupOrder::observe(PickupOrderObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
