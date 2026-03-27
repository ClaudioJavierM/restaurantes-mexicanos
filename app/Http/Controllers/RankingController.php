<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\CountryContext;

class RankingController extends Controller
{
    /**
     * Show top Mexican restaurants nationwide
     * URL: /mejores-restaurantes-mexicanos
     */
    public function mejoresNacional()
    {
        $country = CountryContext::getCountry();
        $cacheKey = "ranking_mejores_nacional_{$country}";

        $data = Cache::remember($cacheKey, 3600, function () use ($country) {
            // Get top 50 restaurants by weighted rating
            $restaurants = Restaurant::where('status', 'approved')
                ->where('country', $country)
                ->with(['state', 'category', 'media'])
                ->orderByRaw('(average_rating * 0.7 + LEAST(total_reviews / 100, 1) * 0.3 * 5) DESC')
                ->orderByDesc('total_reviews')
                ->limit(50)
                ->get();

            // Stats
            $totalRestaurants = Restaurant::where('status', 'approved')
                ->where('country', $country)
                ->count();

            $avgRating = Restaurant::where('status', 'approved')
                ->where('country', $country)
                ->avg('average_rating');

            // Top states
            $topStates = Restaurant::where('status', 'approved')
                ->where('country', $country)
                ->selectRaw('state_id, COUNT(*) as count')
                ->groupBy('state_id')
                ->orderByDesc('count')
                ->limit(10)
                ->with('state')
                ->get();

            return [
                'restaurants' => $restaurants,
                'totalRestaurants' => $totalRestaurants,
                'avgRating' => $avgRating,
                'topStates' => $topStates,
            ];
        });

        return view('rankings.mejores-nacional', [
            'restaurants' => $data['restaurants'],
            'totalRestaurants' => $data['totalRestaurants'],
            'avgRating' => $data['avgRating'],
            'topStates' => $data['topStates'],
            'year' => now()->year,
        ]);
    }

    /**
     * Show top 10 Mexican restaurants
     * URL: /top-10-restaurantes-mexicanos
     */
    public function top10Nacional()
    {
        $country = CountryContext::getCountry();
        $cacheKey = "ranking_top10_nacional_{$country}";

        $data = Cache::remember($cacheKey, 3600, function () use ($country) {
            // Get top 10 restaurants
            $restaurants = Restaurant::where('status', 'approved')
                ->where('country', $country)
                ->with(['state', 'category', 'media'])
                ->orderByRaw('(average_rating * 0.7 + LEAST(total_reviews / 100, 1) * 0.3 * 5) DESC')
                ->orderByDesc('total_reviews')
                ->limit(10)
                ->get();

            return [
                'restaurants' => $restaurants,
            ];
        });

        return view('rankings.top-10-nacional', [
            'restaurants' => $data['restaurants'],
            'year' => now()->year,
        ]);
    }

    /**
     * Show best restaurants in a specific city
     * URL: /mejores/{state}/{city}
     */
    public function mejoresCiudad($stateSlug, $citySlug)
    {
        $state = State::where('code', strtoupper($stateSlug))
            ->orWhere('slug', $stateSlug)
            ->firstOrFail();

        $cityName = Str::title(str_replace('-', ' ', $citySlug));
        $cacheKey = "ranking_mejores_ciudad_{$state->id}_{$citySlug}";

        $data = Cache::remember($cacheKey, 3600, function () use ($state, $cityName) {
            // Get top restaurants in city
            $restaurants = Restaurant::where('state_id', $state->id)
                ->where('city', 'like', $cityName)
                ->where('status', 'approved')
                ->with(['state', 'category', 'media'])
                ->orderByRaw('(average_rating * 0.7 + LEAST(total_reviews / 100, 1) * 0.3 * 5) DESC')
                ->orderByDesc('total_reviews')
                ->limit(25)
                ->get();

            // City stats
            $stats = Restaurant::where('state_id', $state->id)
                ->where('city', 'like', $cityName)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(average_rating) as avg_rating,
                    SUM(total_reviews) as total_reviews
                ')
                ->first();

            return [
                'restaurants' => $restaurants,
                'stats' => $stats,
            ];
        });

        if ($data['restaurants']->isEmpty()) {
            abort(404);
        }

        return view('rankings.mejores-ciudad', [
            'state' => $state,
            'cityName' => $cityName,
            'citySlug' => $citySlug,
            'restaurants' => $data['restaurants'],
            'stats' => $data['stats'],
            'year' => now()->year,
        ]);
    }

    /**
     * Show best restaurants in a specific state
     * URL: /mejores/{state}
     */
    public function mejoresEstado($stateSlug)
    {
        $state = State::where('code', strtoupper($stateSlug))
            ->orWhere('slug', $stateSlug)
            ->firstOrFail();

        $cacheKey = "ranking_mejores_estado_{$state->id}";

        $data = Cache::remember($cacheKey, 3600, function () use ($state) {
            // Get top restaurants in state
            $restaurants = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->with(['state', 'category', 'media'])
                ->orderByRaw('(average_rating * 0.7 + LEAST(total_reviews / 100, 1) * 0.3 * 5) DESC')
                ->orderByDesc('total_reviews')
                ->limit(25)
                ->get();

            // State stats
            $stats = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(average_rating) as avg_rating,
                    SUM(total_reviews) as total_reviews
                ')
                ->first();

            // Top cities in state
            $topCities = Restaurant::where('state_id', $state->id)
                ->where('status', 'approved')
                ->selectRaw('city, COUNT(*) as count, AVG(average_rating) as avg_rating')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(10)
                ->get();

            return [
                'restaurants' => $restaurants,
                'stats' => $stats,
                'topCities' => $topCities,
            ];
        });

        return view('rankings.mejores-estado', [
            'state' => $state,
            'restaurants' => $data['restaurants'],
            'stats' => $data['stats'],
            'topCities' => $data['topCities'],
            'year' => now()->year,
        ]);
    }
}
