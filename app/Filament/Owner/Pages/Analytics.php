<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics';
    protected static ?string $title = 'Analytics y Estadísticas';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 4;
    
    protected static string $view = 'filament.owner.pages.analytics';

    public $restaurant;
    public $period = '30'; // days
    public $stats = [];
    public $chartData = [];
    public $comparison = [];
    public $isPremium = false;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $teamMember = \App\Models\RestaurantTeamMember::where('user_id', $user->id)
            ->where('status', 'active')->first();
        if ($teamMember && $teamMember->role !== 'admin') {
            $permissions = $teamMember->permissions ?? [];
            if (!($permissions['analytics'] ?? false)) {
                return false;
            }
        }
        return true;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        $this->isPremium = $this->restaurant && in_array($this->restaurant->subscription_tier, ['premium', 'elite']);
        $this->loadStats();
        $this->loadComparison();
    }

    public function loadStats(): void
    {
        if (!$this->restaurant) {
            return;
        }

        $days = (int) $this->period;
        $startDate = Carbon::now()->subDays($days);
        $previousStart = Carbon::now()->subDays($days * 2);
        $previousEnd = Carbon::now()->subDays($days);

        // Current period stats
        $currentViews = $this->restaurant->profile_views ?? 0;
        $currentPhoneClicks = $this->restaurant->phone_clicks ?? 0;
        $currentWebClicks = $this->restaurant->website_clicks ?? 0;
        
        // Reviews stats
        $currentReviews = Review::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->count();

        $previousReviews = Review::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'approved')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        // Average rating
        $avgRating = Review::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'approved')
            ->avg('rating') ?? 0;

        // Reviews by rating
        $reviewsByRating = Review::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'approved')
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();

        // Reviews over time (last 30 days)
        $reviewsOverTime = Review::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $this->stats = [
            'views' => [
                'current' => $currentViews,
                'change' => 0, // Would need historical data
                'icon' => 'eye',
                'color' => 'blue',
            ],
            'phone_clicks' => [
                'current' => $currentPhoneClicks,
                'change' => 0,
                'icon' => 'phone',
                'color' => 'green',
            ],
            'website_clicks' => [
                'current' => $currentWebClicks,
                'change' => 0,
                'icon' => 'globe-alt',
                'color' => 'purple',
            ],
            'reviews' => [
                'current' => $currentReviews,
                'previous' => $previousReviews,
                'change' => $previousReviews > 0 
                    ? round((($currentReviews - $previousReviews) / $previousReviews) * 100) 
                    : 0,
                'icon' => 'star',
                'color' => 'yellow',
            ],
            'avg_rating' => round($avgRating, 1),
            'reviews_by_rating' => $reviewsByRating,
            'reviews_over_time' => $reviewsOverTime,
            'total_reviews' => Review::where('restaurant_id', $this->restaurant->id)
                ->where('status', 'approved')
                ->count(),
        ];
    }

    public function updatedPeriod(): void
    {
        $this->loadStats();
        $this->loadComparison();
    }



    public function loadComparison(): void
    {
        if (!$this->restaurant) {
            return;
        }

        // Get restaurants in the same city
        $localRestaurants = Restaurant::where("city", $this->restaurant->city)
            ->where("id", "!=", $this->restaurant->id)
            ->where("is_claimed", true)
            ->get();

        $localCount = $localRestaurants->count();
        
        if ($localCount === 0) {
            $this->comparison = ["no_data" => true];
            return;
        }

        // Calculate local averages
        $localAvgRating = $localRestaurants->avg("average_rating") ?? 0;
        $localAvgReviews = $localRestaurants->avg(function($r) {
            return $r->reviews()->where("status", "approved")->count();
        }) ?? 0;
        $localAvgViews = $localRestaurants->avg("profile_views") ?? 0;
        $localAvgMenuItems = $localRestaurants->avg(function($r) {
            return $r->menuItems()->count();
        }) ?? 0;
        $localAvgPhotos = $localRestaurants->avg(function($r) {
            return $r->userPhotos()->count();
        }) ?? 0;

        // Your restaurant stats
        $yourRating = $this->restaurant->average_rating ?? 0;
        $yourReviews = $this->restaurant->reviews()->where("status", "approved")->count();
        $yourViews = $this->restaurant->profile_views ?? 0;
        $yourMenuItems = $this->restaurant->menuItems()->count();
        $yourPhotos = $this->restaurant->userPhotos()->count();

        // Calculate percentile ranks
        $betterRating = $localRestaurants->filter(fn($r) => ($r->average_rating ?? 0) < $yourRating)->count();
        $betterReviews = $localRestaurants->filter(fn($r) => $r->reviews()->where("status", "approved")->count() < $yourReviews)->count();
        $betterViews = $localRestaurants->filter(fn($r) => ($r->profile_views ?? 0) < $yourViews)->count();

        $this->comparison = [
            "local_count" => $localCount,
            "city" => $this->restaurant->city,
            "metrics" => [
                [
                    "name" => "Calificacion",
                    "your_value" => number_format($yourRating, 1),
                    "local_avg" => number_format($localAvgRating, 1),
                    "difference" => round($yourRating - $localAvgRating, 1),
                    "percentile" => $localCount > 0 ? round(($betterRating / $localCount) * 100) : 0,
                    "is_better" => $yourRating >= $localAvgRating,
                    "icon" => "star",
                    "unit" => "★",
                ],
                [
                    "name" => "Resenas",
                    "your_value" => $yourReviews,
                    "local_avg" => round($localAvgReviews),
                    "difference" => $yourReviews - round($localAvgReviews),
                    "percentile" => $localCount > 0 ? round(($betterReviews / $localCount) * 100) : 0,
                    "is_better" => $yourReviews >= $localAvgReviews,
                    "icon" => "chat-bubble-left",
                    "unit" => "",
                ],
                [
                    "name" => "Vistas",
                    "your_value" => number_format($yourViews),
                    "local_avg" => number_format(round($localAvgViews)),
                    "difference" => $yourViews - round($localAvgViews),
                    "percentile" => $localCount > 0 ? round(($betterViews / $localCount) * 100) : 0,
                    "is_better" => $yourViews >= $localAvgViews,
                    "icon" => "eye",
                    "unit" => "",
                ],
                [
                    "name" => "Items Menu",
                    "your_value" => $yourMenuItems,
                    "local_avg" => round($localAvgMenuItems),
                    "difference" => $yourMenuItems - round($localAvgMenuItems),
                    "is_better" => $yourMenuItems >= $localAvgMenuItems,
                    "icon" => "clipboard-document-list",
                    "unit" => "",
                ],
                [
                    "name" => "Fotos",
                    "your_value" => $yourPhotos,
                    "local_avg" => round($localAvgPhotos),
                    "difference" => $yourPhotos - round($localAvgPhotos),
                    "is_better" => $yourPhotos >= $localAvgPhotos,
                    "icon" => "photo",
                    "unit" => "",
                ],
            ],
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;
        
        $restaurant = $user->allAccessibleRestaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_tier, ["premium", "elite"])) {
            return "PRO";
        }
        return null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return "warning";
    }
}