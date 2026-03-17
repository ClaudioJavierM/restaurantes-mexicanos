<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\State;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RestaurantController extends Controller
{
    /**
     * Get list of restaurants with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::query()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->approved()
            ->select([
                'id', 'name', 'slug', 'description', 'address', 'city', 'zip_code',
                'phone', 'website', 'latitude', 'longitude', 'average_rating',
                'total_reviews', 'price_range', 'hours', 'is_featured', 'image',
                'state_id', 'category_id'
            ]);

        // Filter by state
        if ($request->has('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by price range
        if ($request->has('price_range')) {
            $query->where('price_range', $request->price_range);
        }

        // Filter featured only
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Sort options (whitelist to prevent SQL injection)
        $allowedSortColumns = ['average_rating', 'total_reviews', 'name', 'created_at', 'price_range', 'city', 'distance'];
        $sortBy = in_array($request->get('sort', 'average_rating'), $allowedSortColumns)
            ? $request->get('sort', 'average_rating')
            : 'average_rating';
        $sortDir = in_array(strtolower($request->get('direction', 'desc')), ['asc', 'desc'])
            ? strtolower($request->get('direction', 'desc'))
            : 'desc';

        if ($sortBy === 'distance' && $request->has('lat') && $request->has('lng')) {
            // Distance sorting handled separately
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 50);
        $restaurants = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $restaurants->items(),
            'meta' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total(),
            ]
        ]);
    }

    /**
     * Get nearby restaurants based on coordinates
     */
    public function nearby(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50', // miles
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->get('radius', 10); // default 10 miles

        // Get restaurants with lat/lng
        $restaurants = Restaurant::query()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->approved()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select([
                'id', 'name', 'slug', 'description', 'address', 'city', 'zip_code',
                'phone', 'website', 'latitude', 'longitude', 'average_rating',
                'total_reviews', 'price_range', 'hours', 'is_featured', 'image',
                'state_id', 'category_id'
            ])
            ->get();

        // Calculate distances and filter
        $nearbyRestaurants = $restaurants->map(function ($restaurant) use ($lat, $lng) {
            $distance = $this->calculateDistance($lat, $lng, $restaurant->latitude, $restaurant->longitude);
            $restaurant->distance = round($distance, 2);
            return $restaurant;
        })
        ->filter(function ($restaurant) use ($radius) {
            return $restaurant->distance <= $radius;
        })
        ->sortBy('distance')
        ->take($request->get('limit', 20))
        ->values();

        return response()->json([
            'success' => true,
            'data' => $nearbyRestaurants,
            'meta' => [
                'center' => ['lat' => $lat, 'lng' => $lng],
                'radius_miles' => $radius,
                'count' => $nearbyRestaurants->count(),
            ]
        ]);
    }

    /**
     * Get single restaurant details
     */
    public function show($id): JsonResponse
    {
        $restaurant = Restaurant::with([
            'state:id,name,abbreviation',
            'category:id,name,slug',
            'approvedReviews' => function ($query) {
                $query->with('user:id,name')
                    ->latest()
                    ->take(10);
            },
            'availableMenuItems',
            'activeCoupons',
        ])->findOrFail($id);

        // Increment view count
        $restaurant->increment('profile_views');

        return response()->json([
            'success' => true,
            'data' => $restaurant,
        ]);
    }

    /**
     * Get featured restaurants
     */
    public function featured(Request $request): JsonResponse
    {
        $restaurants = Restaurant::query()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->approved()
            ->featured()
            ->select([
                'id', 'name', 'slug', 'description', 'address', 'city',
                'average_rating', 'total_reviews', 'price_range', 'image',
                'state_id', 'category_id'
            ])
            ->orderBy('average_rating', 'desc')
            ->take($request->get('limit', 10))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $restaurants,
        ]);
    }

    /**
     * Get popular restaurants (most reviewed)
     */
    public function popular(Request $request): JsonResponse
    {
        $restaurants = Restaurant::query()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->approved()
            ->where('total_reviews', '>', 0)
            ->select([
                'id', 'name', 'slug', 'description', 'address', 'city',
                'average_rating', 'total_reviews', 'price_range', 'image',
                'state_id', 'category_id'
            ])
            ->orderBy('total_reviews', 'desc')
            ->orderBy('average_rating', 'desc')
            ->take($request->get('limit', 10))
            ->get();

        return response()->json([
            'success' => true,
            'data' => $restaurants,
        ]);
    }

    /**
     * Search restaurants
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = $request->q;

        $restaurants = Restaurant::query()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->select([
                'id', 'name', 'slug', 'address', 'city',
                'average_rating', 'total_reviews', 'price_range', 'image',
                'state_id', 'category_id'
            ])
            ->orderBy('average_rating', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $restaurants,
            'meta' => [
                'query' => $query,
                'count' => $restaurants->count(),
            ]
        ]);
    }

    /**
     * Get categories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['restaurants' => function ($query) {
                $query->approved();
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get states
     */
    public function states(): JsonResponse
    {
        $states = State::query()
            ->withCount(['restaurants' => function ($query) {
                $query->approved();
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'abbreviation', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }

    /**
     * GET /v1/restaurants/{id}/menu
     * Returns the public menu for a restaurant, grouped by category.
     */
    public function menu(Request $request, int $id): JsonResponse
    {
        $restaurant = Restaurant::approved()->findOrFail($id);

        $categories = $restaurant->menuCategories()
            ->active()
            ->ordered()
            ->with(['items' => function ($q) {
                $q->where('is_available', true)->orderBy('sort_order')->orderBy('name');
            }])
            ->whereHas('items', fn($q) => $q->where('is_available', true))
            ->get();

        $popularItems = $restaurant->menuItems()
            ->where('is_available', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'restaurant_id' => $restaurant->id,
                'categories'    => $categories,
                'popular_items' => $popularItems,
            ],
        ]);
    }

    /**
     * GET /v1/restaurants/{id}/photos
     * Returns a list of photo URLs for a restaurant.
     */
    public function photos(Request $request, int $id): JsonResponse
    {
        $restaurant = Restaurant::approved()->findOrFail($id);

        $photos = [];

        if ($restaurant->image) {
            $photos[] = ['url' => $restaurant->image, 'type' => 'main'];
        }

        foreach (['gallery', 'ambiance', 'menu', 'default'] as $collection) {
            try {
                foreach ($restaurant->getMedia($collection) as $media) {
                    $photos[] = ['url' => $media->getUrl(), 'type' => $collection];
                }
            } catch (\Exception $e) {
                // collection may not exist
            }
        }

        if (!empty($restaurant->yelp_photos)) {
            $yelpPhotos = is_array($restaurant->yelp_photos)
                ? $restaurant->yelp_photos
                : json_decode($restaurant->yelp_photos, true);
            if (is_array($yelpPhotos)) {
                foreach ($yelpPhotos as $url) {
                    $photos[] = ['url' => $url, 'type' => 'yelp'];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $photos,
            'meta'    => ['count' => count($photos)],
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 3959; // miles

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $lon1 = deg2rad($lon1);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
