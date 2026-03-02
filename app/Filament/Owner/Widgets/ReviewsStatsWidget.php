<?php

namespace App\Filament\Owner\Widgets;

use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReviewsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $restaurantIds = auth()->user()->restaurants()->pluck('id');
        
        $totalReviews = Review::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->count();
            
        $pendingResponses = Review::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->whereNull('owner_response')
            ->count();
            
        $avgRating = Review::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->avg('rating');
            
        $thisMonth = Review::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Reseñas', $totalReviews)
                ->description('Reseñas aprobadas')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Sin Responder', $pendingResponses)
                ->description('Requieren atención')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($pendingResponses > 0 ? 'warning' : 'success'),

            Stat::make('Calificación Promedio', number_format($avgRating ?? 0, 1) . ' ⭐')
                ->description('De 5 estrellas')
                ->descriptionIcon('heroicon-m-trophy')
                ->color(($avgRating ?? 0) >= 4 ? 'success' : (($avgRating ?? 0) >= 3 ? 'warning' : 'danger')),

            Stat::make('Este Mes', $thisMonth)
                ->description('Nuevas reseñas')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
