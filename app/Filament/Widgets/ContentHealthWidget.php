<?php

namespace App\Filament\Widgets;

use App\Models\ApiCallLog;
use App\Models\BlogPost;
use App\Models\Restaurant;
use App\Models\Review;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentHealthWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // 1. Pending Restaurants
        try {
            $pendingRestaurants = Restaurant::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            $pendingRestaurants = 0;
        }

        // 2. Pending Reviews
        try {
            $pendingReviews = Review::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            $pendingReviews = 0;
        }

        // 3. Published Blog Posts
        try {
            $publishedPosts = BlogPost::where('status', 'published')->count();
        } catch (\Throwable $e) {
            $publishedPosts = 0;
        }

        // 4. Without AI Description
        try {
            $withoutDescription = Restaurant::where('status', 'approved')
                ->whereNull('ai_description')
                ->count();
        } catch (\Throwable $e) {
            $withoutDescription = 0;
        }

        // 5. Enrichment Queue
        try {
            $enrichmentQueue = Restaurant::whereNotNull('yelp_id')
                ->whereNull('yelp_enriched_at')
                ->count();
        } catch (\Throwable $e) {
            $enrichmentQueue = 0;
        }

        // 6. API Errors (24h)
        try {
            $apiErrors = ApiCallLog::where('success', false)
                ->where('called_at', '>=', now()->subDay())
                ->count();
        } catch (\Throwable $e) {
            $apiErrors = 0;
        }

        return [
            Stat::make('Pendientes Aprobación', number_format($pendingRestaurants))
                ->description('Restaurantes por revisar')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRestaurants > 100 ? 'danger' : ($pendingRestaurants > 10 ? 'warning' : 'success')),

            Stat::make('Reviews Pendientes', number_format($pendingReviews))
                ->description('Sin moderar')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color($pendingReviews > 50 ? 'danger' : ($pendingReviews > 0 ? 'warning' : 'success')),

            Stat::make('Blog Posts Publicados', number_format($publishedPosts))
                ->description('Posts activos en el blog')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Sin Descripción IA', number_format($withoutDescription))
                ->description('Restaurantes aprobados sin IA')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($withoutDescription > 5000 ? 'danger' : ($withoutDescription > 1000 ? 'warning' : 'success')),

            Stat::make('Cola de Enriquecimiento', number_format($enrichmentQueue))
                ->description('Con Yelp ID, sin enriquecer')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($enrichmentQueue > 10000 ? 'warning' : 'success'),

            Stat::make('Errores API (24h)', number_format($apiErrors))
                ->description('Fallos en últimas 24 horas')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($apiErrors > 0 ? 'danger' : 'success'),
        ];
    }
}
