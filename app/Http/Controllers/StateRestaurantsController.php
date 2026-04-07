<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StateRestaurantsController extends Controller
{
    /**
     * List all states with at least 1 restaurant, grouped by country.
     */
    public function index()
    {
        $states = State::select('states.*')
            ->join('restaurants', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_active', true)
            ->whereNull('restaurants.deleted_at')
            ->groupBy('states.id', 'states.name', 'states.code', 'states.country', 'states.is_active', 'states.created_at', 'states.updated_at')
            ->selectRaw('COUNT(restaurants.id) as restaurants_count')
            ->orderBy('states.name')
            ->get();

        $grouped = $states->groupBy('country');

        $isEn = str_contains(request()->getHost(), 'famousmexicanrestaurants.com');

        $title = $isEn
            ? 'Mexican Restaurants by State | FAMER'
            : 'Restaurantes Mexicanos por Estado | FAMER';

        $metaDescription = $isEn
            ? 'Browse authentic Mexican restaurants by state. Find the best tacos, birria, tamales, and more across the US and Mexico.'
            : 'Explora los mejores restaurantes mexicanos por estado en EUA y México. Birria, tacos, tamales y más.';

        return view('states.index', compact('grouped', 'isEn'))
            ->with('title', $title)
            ->with('metaDescription', $metaDescription);
    }

    /**
     * Show state landing page with top restaurants and cities.
     */
    public function show(string $stateSlug)
    {
        // Normalize slug: hyphens → spaces
        $stateName = str_replace('-', ' ', $stateSlug);

        // Find state by name (case-insensitive) or by code
        $state = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])
            ->orWhereRaw('LOWER(code) = ?', [strtolower($stateSlug)])
            ->first();

        if (!$state) {
            abort(404);
        }

        // Total restaurant count in state
        $total = Restaurant::where('state_id', $state->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->count();

        if ($total === 0) {
            abort(404);
        }

        // Top 10 restaurants ordered by Google rating then review count
        $restaurants = Restaurant::where('state_id', $state->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderByRaw('COALESCE(google_rating, average_rating, 0) DESC')
            ->orderByRaw('COALESCE(google_reviews_count, total_reviews, 0) DESC')
            ->limit(10)
            ->get();

        // Top 5 cities by restaurant count
        $cities = Restaurant::select('city', DB::raw('COUNT(*) as count'))
            ->where('state_id', $state->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Average rating for stats bar
        $avgRating = Restaurant::where('state_id', $state->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('google_rating')
            ->avg('google_rating');

        $topCity = $cities->first()?->city ?? '';

        $isEn = str_contains(request()->getHost(), 'famousmexicanrestaurants.com');

        $seoTitle = $isEn
            ? "Best Mexican Restaurants in {$state->name} — Top {$total} | FAMER"
            : "Restaurantes Mexicanos en {$state->name} — Top {$total} | FAMER";

        $seoDescription = $isEn
            ? "Discover the {$total} best Mexican restaurants in {$state->name}. Verified reviews, photos and menus."
            : "Descubre los {$total} mejores restaurantes mexicanos en {$state->name}. Reseñas verificadas, fotos y menús.";

        return view('states.show', compact(
            'state',
            'restaurants',
            'cities',
            'total',
            'avgRating',
            'topCity',
            'isEn'
        ))->with('title', $seoTitle)
          ->with('metaDescription', $seoDescription);
    }
}
