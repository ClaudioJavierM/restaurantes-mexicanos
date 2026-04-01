<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\CountryContext;

class CityGuideController extends Controller
{
    /**
     * Show list of all states with restaurant counts
     */
    public function states()
    {
        $country = CountryContext::getCountry();
        $data = Cache::remember('city_guide_states_data_' . $country, 3600, function () use ($country) {
            // Get all states with counts (using whereHas for SQLite compatibility)
            $states = State::where('country', $country)->whereHas('restaurants', function ($query) {
                $query->where('status', 'approved');
            })
            ->withCount(['restaurants' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->get();

            // Add top cities to each state
            foreach ($states as $state) {
                $topCities = Restaurant::where('state_id', $state->id)
                    ->where('status', 'approved')
                    ->selectRaw('city, COUNT(*) as count')
                    ->groupBy('city')
                    ->orderByDesc('count')
                    ->limit(5)
                    ->pluck('city')
                    ->toArray();
                $state->top_cities = $topCities;
            }

            // Calculate totals
            $totalRestaurants = Restaurant::where("status", "approved")->where("country", $country)->count();
            $totalCities = Restaurant::where('status', 'approved')->where('country', $country)
                ->distinct('city')
                ->count('city');
            $claimedCount = Restaurant::where('status', 'approved')->where('country', $country)
                ->where('is_claimed', true)
                ->count();

            return [
                'states' => $states,
                'totalRestaurants' => $totalRestaurants,
                'totalCities' => $totalCities,
                'claimedCount' => $claimedCount,
            ];
        });

        // Top states by restaurant count
        $topStates = $data['states']->sortByDesc('restaurants_count')->take(9);
        // All states alphabetically
        $allStates = $data['states']->sortBy('name');

        return view('city-guides.states', [
            'topStates' => $topStates,
            'allStates' => $allStates,
            'totalRestaurants' => $data['totalRestaurants'],
            'totalStates' => $data['states']->count(),
            'totalCities' => $data['totalCities'],
            'claimedCount' => $data['claimedCount'],
        ]);
    }

    /**
     * Show cities in a state
     */
    public function state($stateSlug)
    {
        $state = State::where('code', strtoupper($stateSlug))
            ->orWhere('slug', $stateSlug)
            ->firstOrFail();

        $data = Cache::remember("city_guide_state_{$state->id}", 3600, function () use ($state) {
            // Get cities with stats
            $cities = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->selectRaw('
                    city,
                    COUNT(*) as count,
                    AVG(average_rating) as avg_rating,
                    COUNT(CASE WHEN is_claimed = 1 THEN 1 END) as claimed_count
                ')
                ->groupBy('city')
                ->having('count', '>', 0)
                ->orderByDesc('count')
                ->get();

            // State stats
            $stats = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(average_rating) as avg_rating,
                    SUM(total_reviews) as total_reviews,
                    COUNT(CASE WHEN is_claimed = 1 THEN 1 END) as claimed_count
                ')
                ->first();

            // Top restaurants in state (6 for clean 2x3 grid)
            $topRestaurants = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->with(['state', 'category', 'media'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->limit(6)
                ->get();

            return [
                'cities' => $cities,
                'stats' => $stats,
                'topRestaurants' => $topRestaurants,
            ];
        });

        return view('city-guides.state', [
            'state' => $state,
            'cities' => $data['cities'],
            'stats' => $data['stats'],
            'topRestaurants' => $data['topRestaurants'],
        ]);
    }

    /**
     * Show city guide with top restaurants
     */
    public function city($stateSlug, $citySlug)
    {
        $state = State::where('code', strtoupper($stateSlug))
            ->orWhere('slug', $stateSlug)
            ->firstOrFail();

        $cityName = Str::title(str_replace('-', ' ', $citySlug));

        // Top 10 list ordered by google reviews count then rating (pure quality signal, no subscription bias)
        $top10Restaurants = Cache::remember("city_guide_top10_{$state->id}_{$citySlug}", 3600, function () use ($state, $cityName) {
            return Restaurant::where('state_id', $state->id)
                ->where('city', 'like', $cityName)
                ->where('status', 'approved')
                ->with(['state', 'category', 'media'])
                ->orderByDesc('google_reviews_count')
                ->orderByDesc('google_rating')
                ->limit(10)
                ->get();
        });

        if ($top10Restaurants->isEmpty()) {
            abort(404);
        }

        // Full paginated list: Elite/Premium first, then by rating (for monetization)
        $restaurants = Restaurant::where('state_id', $state->id)
            ->where('city', 'like', $cityName)
            ->where('status', 'approved')
            ->with(['state', 'category', 'media'])
            ->orderByRaw("CASE
                WHEN subscription_tier = 'elite' THEN 1
                WHEN subscription_tier = 'premium' THEN 2
                ELSE 3
            END")
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->paginate(20);

        // Get stats for the city
        $stats = Cache::remember("city_guide_{$state->id}_{$citySlug}", 3600, function () use ($state, $cityName) {
            return Restaurant::where('state_id', $state->id)
                ->where('city', 'like', $cityName)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(google_rating) as avg_rating,
                    SUM(google_reviews_count) as total_reviews,
                    COUNT(CASE WHEN is_claimed = 1 THEN 1 END) as claimed_count,
                    COUNT(DISTINCT category_id) as category_count
                ')
                ->first();
        });

        // Get top categories in this city
        $topCategories = Restaurant::where('state_id', $state->id)
            ->where('city', 'like', $cityName)
            ->where('status', 'approved')
            ->whereNotNull('category_id')
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('category')
            ->get();

        return view('city-guides.city', [
            'state' => $state,
            'cityName' => $cityName,
            'citySlug' => $citySlug,
            'top10Restaurants' => $top10Restaurants,
            'restaurants' => $restaurants,
            'stats' => $stats,
            'topCategories' => $topCategories,
        ]);
    }
}
