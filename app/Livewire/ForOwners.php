<?php

namespace App\Livewire;

use App\Services\CountryContext;

use App\Models\AnalyticsEvent;
use App\Models\Restaurant;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ForOwners extends Component
{
    public array $stats = [];
    public array $stateStats = [];
    public bool $isMexico = false;

    public function mount()
    {
        $this->isMexico = CountryContext::isMexico();
        $this->loadStats();
    }

    public function loadStats()
    {
        $country = CountryContext::getCountry();

        // Cache stats for 5 minutes (country-specific)
        $this->stats = Cache::remember('platform_stats_for_owners_' . $country, 300, function () use ($country) {
            $firstRecord = AnalyticsEvent::orderBy('created_at', 'asc')->first();

            // Count approved restaurants for current country
            $totalRestaurants = Restaurant::where('status', 'approved')->forCurrentCountry()->count();

            if (!$firstRecord) {
                return [
                    'total_views' => 0,
                    'daily_avg' => 0,
                    'total_restaurants' => $totalRestaurants,
                    'weekly_growth' => 0,
                    'restaurant_growth' => 0,
                    'start_date' => now()->format('M d, Y'),
                ];
            }

            $firstDate = Carbon::parse($firstRecord->created_at);
            $totalDays = max(1, $firstDate->diffInDays(now()) + 1);

            // For Mexico, show only Mexico-specific views if we have them
            // For now, show all views as baseline
            $totalViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)->count();

            // If Mexico and we have restaurant-specific analytics, filter them
            if ($country === 'MX') {
                $mexicoRestaurantIds = Restaurant::where('country', 'MX')->pluck('id');
                $mexicoViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->whereIn('restaurant_id', $mexicoRestaurantIds)
                    ->count();
                // Use Mexico views if we have any, otherwise use a baseline
                $totalViews = $mexicoViews > 0 ? $mexicoViews : 0;
            }

            $avgPerDay = $totalDays > 0 ? $totalViews / $totalDays : 0;

            // Weekly traffic growth
            $thisWeekViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->where('created_at', '>=', Carbon::now()->startOfWeek())
                ->count();
            $lastWeekViews = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
                ->count();

            $weeklyGrowth = $lastWeekViews > 0 ? (($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100 : 0;

            // Restaurant growth - compare to last month
            $restaurantsLastMonth = Restaurant::where('status', 'approved')
                ->forCurrentCountry()
                ->where('created_at', '<', Carbon::now()->subMonth())
                ->count();
            $restaurantGrowth = $restaurantsLastMonth > 0
                ? (($totalRestaurants - $restaurantsLastMonth) / $restaurantsLastMonth) * 100
                : 0;

            return [
                'total_views' => $totalViews,
                'daily_avg' => round($avgPerDay),
                'total_restaurants' => $totalRestaurants,
                'weekly_growth' => round($weeklyGrowth, 1),
                'restaurant_growth' => round($restaurantGrowth, 1),
                'start_date' => $firstDate->format('M d, Y'),
                'google_ads_value' => round($totalViews * 2),
                'google_ads_per_restaurant' => $totalRestaurants > 0 ? round(($totalViews * 2) / $totalRestaurants) : 0,
            ];
        });

        // Top states for current country (cached for 1 hour)
        $this->stateStats = Cache::remember('platform_state_stats_owners_' . $country, 3600, function () use ($country) {
            // Get states for current country
            $countryStates = State::where('country', $country)->pluck('id');

            $stats = AnalyticsEvent::where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                ->join('restaurants', 'analytics_events.restaurant_id', '=', 'restaurants.id')
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->whereIn('states.id', $countryStates)
                ->selectRaw('states.code as state_code, states.name as state_name, COUNT(*) as views')
                ->groupBy('states.code', 'states.name')
                ->orderByDesc('views')
                ->limit(5)
                ->get()
                ->toArray();

            // If no analytics for Mexico yet, show top states by restaurant count
            if (empty($stats) && $country === 'MX') {
                return State::where('country', 'MX')
                    ->withCount(['restaurants' => function($q) {
                        $q->where('status', 'approved');
                    }])
                    ->orderByDesc('restaurants_count')
                    ->limit(5)
                    ->get()
                    ->map(function($state) {
                        return [
                            'state_code' => $state->code,
                            'state_name' => $state->name,
                            'views' => $state->restaurants_count,
                        ];
                    })
                    ->toArray();
            }

            return $stats;
        });
    }

    public function render()
    {
        return view('livewire.for-owners', [
            'isMexico' => $this->isMexico,
        ])->layout('layouts.owners-public', ['title' => __('app.owner_hero_title') . ' ' . __('app.owner_hero_highlight')]);
    }
}
