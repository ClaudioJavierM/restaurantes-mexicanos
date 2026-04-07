<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Support\Str;

class CityRestaurantsController extends Controller
{
    public function show(string $citySlug)
    {
        // Convert "dallas-tx" → city="Dallas", stateCode="TX"
        // URL format: /restaurantes-mexicanos-en-dallas-tx
        // Split on last "-" to get state code (2-letter suffix)
        $parts = explode('-', $citySlug);
        $stateCode = strtoupper(array_pop($parts));
        $cityName = ucwords(implode(' ', $parts));

        $state = State::where('code', $stateCode)->first();

        // Top restaurants in this city (ordered by Google rating then review count)
        $restaurants = Restaurant::where('city', 'LIKE', $cityName)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->when($state, fn($q) => $q->where('state_id', $state->id))
            ->orderByRaw('COALESCE(google_rating, average_rating, 0) DESC')
            ->orderByRaw('COALESCE(google_reviews_count, total_reviews, 0) DESC')
            ->limit(12)
            ->get();

        if ($restaurants->isEmpty()) {
            // Try fuzzy match (drop state filter for broader results)
            $restaurants = Restaurant::where('city', 'LIKE', "%{$cityName}%")
                ->where('status', 'approved')
                ->where('is_active', true)
                ->orderByRaw('COALESCE(google_rating, average_rating, 0) DESC')
                ->orderByRaw('COALESCE(google_reviews_count, total_reviews, 0) DESC')
                ->limit(12)
                ->get();
        }

        abort_if($restaurants->isEmpty(), 404);

        $total = Restaurant::where('city', 'LIKE', $cityName)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->when($state, fn($q) => $q->where('state_id', $state->id))
            ->count();

        $avgRating = Restaurant::where('city', 'LIKE', $cityName)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->when($state, fn($q) => $q->where('state_id', $state->id))
            ->whereNotNull('google_rating')
            ->avg('google_rating');

        // Build state slug for back link (e.g. "TX" → look up state name → "texas")
        $stateSlug = $state ? Str::slug($state->name) : strtolower($stateCode);

        $isEn = str_contains(request()->getHost(), 'famousmexicanrestaurants.com');

        $seoTitle = $isEn
            ? "Best Mexican Restaurants in {$cityName}, {$stateCode} — Top {$total} | FAMER"
            : "Restaurantes Mexicanos en {$cityName}, {$stateCode} — Top {$total} | FAMER";

        $seoDescription = $isEn
            ? "Discover the best {$total} Mexican restaurants in {$cityName}, {$stateCode}. Verified reviews, photos and menus."
            : "Descubre los {$total} mejores restaurantes mexicanos en {$cityName}, {$stateCode}. Reseñas verificadas, fotos y menús.";

        return view('cities.show', compact(
            'restaurants',
            'cityName',
            'stateCode',
            'state',
            'total',
            'avgRating',
            'isEn',
            'citySlug',
            'stateSlug'
        ))->with('title', $seoTitle)
          ->with('metaDescription', $seoDescription);
    }
}
