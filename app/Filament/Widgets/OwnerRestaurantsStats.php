<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use App\Models\Coupon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OwnerRestaurantsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // If user is admin, show all stats
        if ($user && $user->isAdmin()) {
            $totalRestaurants = Restaurant::count();
            $approvedRestaurants = Restaurant::where("status", "approved")->count();
            
            return [
                Stat::make("Total Restaurantes", number_format($totalRestaurants))
                    ->description("En todo el sistema")
                    ->descriptionIcon("heroicon-m-building-storefront")
                    ->color("success"),
                Stat::make("Restaurantes Activos", number_format($approvedRestaurants))
                    ->description("Restaurantes activos")
                    ->descriptionIcon("heroicon-m-check-circle")
                    ->color("success"),
                Stat::make("Total Cupones", Coupon::count())
                    ->description("Cupones en el sistema")
                    ->descriptionIcon("heroicon-m-ticket")
                    ->color("warning"),
                Stat::make("Cupones Activos", Coupon::where("is_active", true)->count())
                    ->description("Cupones activos")
                    ->descriptionIcon("heroicon-m-check-badge")
                    ->color("info"),
            ];
        }

        // If user is owner, show only their stats
        if ($user && $user->isOwner()) {
            $myRestaurants = Restaurant::where("user_id", $user->id);
            $restaurantIds = $myRestaurants->pluck("id");

            $totalReviews = \DB::table("reviews")
                ->whereIn("restaurant_id", $restaurantIds)
                ->where("status", "approved")
                ->count();

            $totalCoupons = Coupon::whereIn("restaurant_id", $restaurantIds)->count();
            $activeCoupons = Coupon::whereIn("restaurant_id", $restaurantIds)
                ->where("is_active", true)
                ->count();

            return [
                Stat::make("Mis Restaurantes", $myRestaurants->count())
                    ->description("Restaurantes registrados")
                    ->descriptionIcon("heroicon-m-building-storefront")
                    ->color("success")
                    ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
                Stat::make("Rating Promedio", number_format($myRestaurants->avg("average_rating") ?? 0, 1))
                    ->description("De todos mis restaurantes")
                    ->descriptionIcon("heroicon-m-star")
                    ->color("warning"),
                Stat::make("Total Reviews", $totalReviews)
                    ->description("Reviews aprobados")
                    ->descriptionIcon("heroicon-m-chat-bubble-left-right")
                    ->color("info"),
                Stat::make("Mis Cupones", $activeCoupons . " / " . $totalCoupons)
                    ->description("Cupones activos / total")
                    ->descriptionIcon("heroicon-m-ticket")
                    ->color("success"),
            ];
        }

        return [];
    }
}
