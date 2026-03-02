<?php

namespace App\Livewire;

use App\Models\AnalyticsEvent;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PlatformStats extends Component
{
    public array $stats = [];
    public array $weeklyGrowth = [];
    public array $stateStats = [];
    public array $topRestaurants = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Cache stats for 5 minutes to avoid heavy queries on every page load
        $this->stats = Cache::remember('platform_stats', 300, function () {
            $firstRecord = AnalyticsEvent::orderBy('created_at', 'asc')->first();
            $lastRecord = AnalyticsEvent::orderBy('created_at', 'desc')->first();

            if (!$firstRecord) {
                return [
                    'total_views' => 0,
                    'daily_avg' => 0,
                    'monthly_avg' => 0,
                    'yearly_projection' => 0,
                    'total_restaurants' => 0,
                    'unique_restaurants_viewed' => 0,
                    'days_tracking' => 0,
                    'weekly_growth' => 0,
                    'peak_day_views' => 0,
                    'peak_day_date' => null,
                    'ad_value' => 0,
                    'google_ads_equivalent' => 0,
                    'today_views' => 0,
                    'this_month_views' => 0,
                ];
            }

            $firstDate = Carbon::parse($firstRecord->created_at);
            $lastDate = Carbon::parse($lastRecord->created_at);
            $totalDays = max(1, $firstDate->diffInDays($lastDate) + 1);
            $totalMonths = max(1, $firstDate->diffInMonths($lastDate) + 1);

            $totalViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)->count();
            $avgPerDay = $totalViews / $totalDays;
            $avgPerMonth = $totalViews / $totalMonths;

            // Peak day
            $peakDay = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderByDesc('count')
                ->first();

            // Weekly growth calculation
            $lastWeekViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->whereBetween('created_at', [Carbon::now()->subWeeks(1)->startOfWeek(), Carbon::now()->subWeeks(1)->endOfWeek()])
                ->count();
            $prevWeekViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->whereBetween('created_at', [Carbon::now()->subWeeks(2)->startOfWeek(), Carbon::now()->subWeeks(2)->endOfWeek()])
                ->count();
            $weeklyGrowth = $prevWeekViews > 0 ? (($lastWeekViews - $prevWeekViews) / $prevWeekViews) * 100 : 0;

            $uniqueRestaurants = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->distinct('restaurant_id')
                ->count('restaurant_id');

            return [
                'total_views' => $totalViews,
                'daily_avg' => round($avgPerDay),
                'monthly_avg' => round($avgPerMonth),
                'yearly_projection' => round($avgPerDay * 365),
                'total_restaurants' => Restaurant::count(),
                'unique_restaurants_viewed' => $uniqueRestaurants,
                'days_tracking' => round($totalDays),
                'weekly_growth' => round($weeklyGrowth, 1),
                'peak_day_views' => $peakDay->count ?? 0,
                'peak_day_date' => $peakDay ? Carbon::parse($peakDay->date)->format('M d, Y') : null,
                'ad_value' => round($totalViews * 0.75),
                'google_ads_equivalent' => round($totalViews * 2),
                'today_views' => AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->whereDate('created_at', Carbon::today())->count(),
                'this_month_views' => AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
                'start_date' => $firstDate->format('M d, Y'),
            ];
        });

        // State stats (cached for 1 hour)
        $this->stateStats = Cache::remember('platform_state_stats', 3600, function () {
            return AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->join('restaurants', 'analytics_events.restaurant_id', '=', 'restaurants.id')
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->selectRaw('states.code as state_code, states.name as state_name, COUNT(*) as views')
                ->groupBy('states.code', 'states.name')
                ->orderByDesc('views')
                ->limit(15)
                ->get()
                ->toArray();
        });

        // Weekly growth chart data
        $this->weeklyGrowth = Cache::remember('platform_weekly_growth', 300, function () {
            $data = [];
            for ($i = 7; $i >= 0; $i--) {
                $start = Carbon::now()->subWeeks($i)->startOfWeek();
                $end = Carbon::now()->subWeeks($i)->endOfWeek();
                $views = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $data[] = [
                    'week' => $start->format('M d'),
                    'views' => $views,
                ];
            }
            return $data;
        });

        // Top restaurants
        $this->topRestaurants = Cache::remember('platform_top_restaurants', 3600, function () {
            return AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->join('restaurants', 'analytics_events.restaurant_id', '=', 'restaurants.id')
                ->selectRaw('restaurants.id, restaurants.name, restaurants.city, COUNT(*) as views')
                ->groupBy('restaurants.id', 'restaurants.name', 'restaurants.city')
                ->orderByDesc('views')
                ->limit(5)
                ->get()
                ->toArray();
        });
    }

    public function render()
    {
        return view('livewire.platform-stats')
            ->layout('layouts.app', [
                'title' => 'FAMER Stats - The #1 Mexican Restaurant Directory',
                'description' => 'See why thousands of Mexican restaurants trust FAMER. Over ' . number_format($this->stats['total_views'] ?? 0) . ' views and growing.',
            ]);
    }
}
