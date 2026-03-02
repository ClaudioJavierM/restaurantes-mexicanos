<?php

namespace App\Http\Controllers;

use App\Models\FlashDeal;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class FlashDealController extends Controller
{
    public function index(Request $request)
    {
        $deals = FlashDeal::active()
            ->with('restaurant:id,name,slug,city,main_image')
            ->orderBy('ends_at', 'asc')
            ->paginate(12);

        return response()->json($deals);
    }

    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|max:100',
        ]);

        $radius = $validated['radius'] ?? 25; // Default 25 miles
        $lat = $validated['latitude'];
        $lng = $validated['longitude'];

        $deals = FlashDeal::active()
            ->whereHas('restaurant', function ($query) use ($lat, $lng, $radius) {
                $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->whereRaw("
                        (3959 * acos(
                            cos(radians(?)) * cos(radians(latitude)) * 
                            cos(radians(longitude) - radians(?)) + 
                            sin(radians(?)) * sin(radians(latitude))
                        )) < ?
                    ", [$lat, $lng, $lat, $radius]);
            })
            ->with('restaurant:id,name,slug,city,latitude,longitude,main_image')
            ->orderBy('ends_at', 'asc')
            ->get();

        return response()->json($deals);
    }

    public function show(FlashDeal $flashDeal)
    {
        if (!$flashDeal->isAvailable()) {
            return response()->json(['error' => 'Esta oferta ya no esta disponible'], 404);
        }

        return response()->json($flashDeal->load('restaurant:id,name,slug,address,city,phone'));
    }

    public function forRestaurant(Restaurant $restaurant)
    {
        $deals = $restaurant->flashDeals()
            ->active()
            ->orderBy('ends_at', 'asc')
            ->get();

        return response()->json($deals);
    }
}
