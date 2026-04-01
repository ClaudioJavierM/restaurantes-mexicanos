<?php
namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\View\View;

class NearMeController extends Controller
{
    public function index(): View
    {
        // Top cities by restaurant count for static fallback / SEO content
        $topCities = Restaurant::query()
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_active', true)
            ->whereNull('restaurants.deleted_at')
            ->select('restaurants.city', 'states.code as state_code', 'states.name as state_name')
            ->selectRaw('COUNT(*) as restaurant_count')
            ->groupBy('restaurants.city', 'states.code', 'states.name')
            ->having('restaurant_count', '>=', 3)
            ->orderByDesc('restaurant_count')
            ->limit(20)
            ->get();

        $featuredRestaurants = Restaurant::approved()
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(12)
            ->get();

        return view('near-me.index', compact('topCities', 'featuredRestaurants'));
    }
}
