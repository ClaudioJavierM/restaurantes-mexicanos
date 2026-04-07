<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\State;
use App\Services\CountryContext;
use App\Services\GeoLocationService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Home extends Component
{
    public $search = '';
    public $selectedState = '';
    public $selectedCategory = '';

    // Location properties
    public $locationQuery = '';
    public $detectedLocation = null;
    public $locationSuggestions = [];
    public $showLocationSuggestions = false;
    public $userLat = null;
    public $userLng = null;
    public $locationSource = null;

    public function mount()
    {
        // Skip IP location detection for Mexico
        if (CountryContext::isMexico()) {
            return;
        }

        if (session()->has('user_location')) {
            $location = session('user_location');
            $this->userLat = $location['lat'];
            $this->userLng = $location['lng'];
            $this->locationSource = 'browser';
            $this->locationQuery = $location['city'] ?? "Mi ubicacion";
        } else {
            $this->detectLocationFromIp();
        }
    }

    public function detectLocationFromIp()
    {
        $geoService = app(GeoLocationService::class);
        $location = $geoService->getLocationFromIp();

        if ($location && $location['country_code'] === 'US') {
            $this->detectedLocation = $location;
            $this->userLat = $location['lat'];
            $this->userLng = $location['lng'];
            $this->locationSource = 'ip';
            $this->locationQuery = $location['city'] . ', ' . $location['state_code'];
            $this->autoSelectState($location['state']);
        }
    }

    public function updatedLocationQuery()
    {
        if (is_string($this->locationQuery) && strlen($this->locationQuery) >= 2) {
            $geoService = app(GeoLocationService::class);
            $this->locationSuggestions = $geoService->searchLocations($this->locationQuery);
            $this->showLocationSuggestions = count($this->locationSuggestions) > 0;
        } else {
            $this->locationSuggestions = [];
            $this->showLocationSuggestions = false;
        }
    }

    public function selectLocation($index)
    {
        if (isset($this->locationSuggestions[$index])) {
            $location = $this->locationSuggestions[$index];
            $this->locationQuery = $location['display'];
            $this->userLat = $location['lat'];
            $this->userLng = $location['lng'];
            $this->locationSource = 'manual';
            $this->detectedLocation = $location;
            $this->showLocationSuggestions = false;
            $this->autoSelectState($location['state']);

            $this->dispatch('locationSelected', [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'display' => $location['display'],
                'city' => $location['city'] ?? null,
                'state' => $location['state'] ?? null,
                'state_code' => $location['state_code'] ?? null,
            ]);
        }
    }

    public function clearLocation()
    {
        $this->locationQuery = '';
        $this->detectedLocation = null;
        $this->userLat = null;
        $this->userLng = null;
        $this->locationSource = null;
        $this->selectedState = '';
        $this->showLocationSuggestions = false;
        $this->dispatch('locationCleared');
    }

    public function useCurrentLocation()
    {
        $this->dispatch('requestBrowserLocation');
    }

    public function setBrowserLocation($lat, $lng, $city = null, $state = null, $stateCode = null)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->locationSource = 'browser';

        $locationDisplay = "Mi ubicacion";

        if ($city && $stateCode) {
            $locationDisplay = $city . ', ' . $stateCode;
            $this->locationQuery = $locationDisplay;
            $this->detectedLocation = [
                'city' => $city,
                'state' => $state,
                'state_code' => $stateCode,
                'lat' => $lat,
                'lng' => $lng,
            ];
            $this->autoSelectState($state);
        } else {
            $this->locationQuery = "Mi ubicacion ({$lat}, {$lng})";
        }

        session(['user_location' => [
            'lat' => $lat,
            'lng' => $lng,
            'city' => $locationDisplay,
        ]]);
    }

    protected function autoSelectState($stateName)
    {
        if (!$stateName) return;

        $states = Cache::remember('home_states_' . CountryContext::getCountry(), 3600, function () {
            return State::where('is_active', true)->forCurrentCountry()
                ->withCount(['restaurants' => fn($q) => $q->where('status', 'approved')])
                ->orderBy('name')
                ->get()
                ->map(fn($s) => (object)[
                    'id' => $s->id,
                    'name' => $s->name,
                    'code' => $s->code,
                    'country' => $s->country,
                    'restaurants_count' => $s->restaurants_count
                ]);
        });

        $state = $states->first(function ($s) use ($stateName) {
            return strtolower($s->name) === strtolower($stateName) ||
                   strtolower($s->code) === strtolower($stateName);
        });

        if ($state) {
            $this->selectedState = $state->name;
        }
    }

    /**
     * Calculate distance between two points using Haversine formula (in PHP, not SQL)
     */
    protected function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        if (!$lat1 || !$lng1 || !$lat2 || !$lng2) return null;

        $earthRadius = 3959; // miles

        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get featured restaurants - either subscribed/featured or highest rated nearby
     */
    protected function getFeaturedRestaurants($isMexico)
    {
        // For Mexico, always show by rating
        if ($isMexico) {
            return Restaurant::approved()
                ->forCurrentCountry()
                ->with(['state', 'category', 'media'])
                ->orderByDesc('google_rating')
                ->orderByDesc('total_reviews')
                ->limit(6)
                ->get();
        }

        // If user has a detected state from IP/browser, prioritize local restaurants
        if ($this->detectedLocation && isset($this->detectedLocation['state_code'])) {
            $stateCode = strtoupper(trim($this->detectedLocation['state_code']));

            // 1. First try featured restaurants in the user's state
            $stateFeatured = Restaurant::approved()
                ->forCurrentCountry()
                ->with(['state', 'category', 'media'])
                ->featured()
                ->whereHas('state', fn($q) => $q->whereRaw('UPPER(code) = ?', [$stateCode]))
                ->limit(6)
                ->get();

            if ($stateFeatured->count() >= 3) {
                // Sort featured by distance if lat/lng available
                if ($this->userLat && $this->userLng) {
                    $stateFeatured = $stateFeatured->map(function($restaurant) {
                        $restaurant->distance = $this->calculateDistance(
                            $this->userLat,
                            $this->userLng,
                            $restaurant->latitude,
                            $restaurant->longitude
                        );
                        return $restaurant;
                    })->sortBy('distance')->values();
                }
                return $stateFeatured;
            }

            // 2. Get all restaurants from the user's state, sorted by rating
            $stateRestaurants = Restaurant::approved()
                ->forCurrentCountry()
                ->with(['state', 'category', 'media'])
                ->whereHas('state', fn($q) => $q->whereRaw('UPPER(code) = ?', [$stateCode]))
                ->orderByDesc('google_rating')
                ->orderByDesc('google_reviews_count')
                ->limit(12)
                ->get();

            // If we have lat/lng, sort by distance from user
            if ($stateRestaurants->count() > 0 && $this->userLat && $this->userLng) {
                $stateRestaurants = $stateRestaurants->map(function($restaurant) {
                    $restaurant->distance = $this->calculateDistance(
                        $this->userLat,
                        $this->userLng,
                        $restaurant->latitude,
                        $restaurant->longitude
                    );
                    return $restaurant;
                })->sortBy('distance')->take(6)->values();

                return $stateRestaurants;
            }

            // 3. If we have state restaurants but no lat/lng, return by rating
            if ($stateRestaurants->count() >= 3) {
                return $stateRestaurants->take(6);
            }
        }

        // 4. No location detected or not enough local results — try national featured
        $featured = Restaurant::approved()
            ->forCurrentCountry()
            ->with(['state', 'category', 'media'])
            ->featured()
            ->limit(6)
            ->get();

        if ($featured->count() >= 3) {
            return $featured;
        }

        // 5. Final fallback: highest rated restaurants nationwide
        return Restaurant::approved()
            ->forCurrentCountry()
            ->with(['state', 'category', 'media'])
            ->orderByDesc('google_rating')
            ->orderByDesc('google_reviews_count')
            ->limit(6)
            ->get();
    }

    /**
     * Get recent platform activity (reviews + new restaurants), optionally filtered by state.
     */
    protected function getRecentActivity()
    {
        $stateId = null;

        // If user has a detected state, resolve its ID for filtering
        if ($this->detectedLocation && isset($this->detectedLocation['state_code'])) {
            $stateCode = strtoupper(trim($this->detectedLocation['state_code']));
            $state = State::where('is_active', true)
                ->forCurrentCountry()
                ->whereRaw('UPPER(code) = ?', [$stateCode])
                ->first();
            if ($state) {
                $stateId = $state->id;
            }
        }

        // Recent approved reviews
        $reviewQuery = Review::with(['restaurant.state', 'restaurant.media'])
            ->where('status', 'approved')
            ->whereHas('restaurant', fn($q) => $q->where('status', 'approved')->where('is_active', true));

        if ($stateId) {
            $reviewQuery->whereHas('restaurant', fn($q) => $q->where('state_id', $stateId));
        }

        $recentReviews = $reviewQuery->latest()->limit(6)->get()->map(function ($review) {
            $r = $review->restaurant;
            $imgSrc = null;
            if ($r) {
                if ($r->image) {
                    $imgSrc = str_starts_with($r->image, 'http') ? $r->image : asset('storage/' . $r->image);
                } elseif ($r->getFirstMediaUrl('images')) {
                    $imgSrc = $r->getFirstMediaUrl('images');
                } elseif (is_array($r->yelp_photos) && count($r->yelp_photos) > 0) {
                    $imgSrc = $r->yelp_photos[0];
                }
            }
            return (object) [
                'type' => 'review',
                'restaurant_name' => $r->name ?? 'Unknown',
                'restaurant_slug' => $r->slug ?? '#',
                'city' => $r->city ?? '',
                'state_name' => $r->state->name ?? '',
                'state_code' => $r->state->code ?? '',
                'rating' => $review->rating,
                'snippet' => $review->comment ? \Illuminate\Support\Str::limit($review->comment, 80) : null,
                'reviewer' => $review->reviewer_name,
                'time_ago' => $review->created_at->diffForHumans(),
                'created_at' => $review->created_at,
                'image' => $imgSrc,
            ];
        });

        // Recently added restaurants
        $restaurantQuery = Restaurant::approved()
            ->forCurrentCountry()
            ->with('state');

        if ($stateId) {
            $restaurantQuery->where('state_id', $stateId);
        }

        $recentRestaurants = $restaurantQuery->with('media')->latest()->limit(6)->get()->map(function ($restaurant) {
            $imgSrc = null;
            if ($restaurant->image) {
                $imgSrc = str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image);
            } elseif ($restaurant->getFirstMediaUrl('images')) {
                $imgSrc = $restaurant->getFirstMediaUrl('images');
            } elseif (is_array($restaurant->yelp_photos) && count($restaurant->yelp_photos) > 0) {
                $imgSrc = $restaurant->yelp_photos[0];
            }
            return (object) [
                'type' => 'new_restaurant',
                'restaurant_name' => $restaurant->name,
                'restaurant_slug' => $restaurant->slug ?? '#',
                'city' => $restaurant->city ?? '',
                'state_name' => $restaurant->state->name ?? '',
                'state_code' => $restaurant->state->code ?? '',
                'rating' => $restaurant->google_rating,
                'snippet' => null,
                'reviewer' => null,
                'time_ago' => $restaurant->created_at->diffForHumans(),
                'created_at' => $restaurant->created_at,
                'image' => $imgSrc,
            ];
        });

        // Merge and sort by most recent, take 6
        return $recentReviews->concat($recentRestaurants)
            ->sortByDesc('created_at')
            ->take(6)
            ->values();
    }

    public function render()
    {
        $isMexico = CountryContext::isMexico();

        $categories = Cache::remember('home_categories', 3600, function () {
            return Category::where('is_active', true)
                ->withCount(['restaurants' => function ($query) {
                    $query->where('status', 'approved')->where('is_active', true);
                }])
                ->get()
                ->filter(fn ($cat) => $cat->restaurants_count > 0)
                ->sortByDesc('restaurants_count')
                ->values();
        });

        $states = Cache::remember('home_states_' . CountryContext::getCountry(), 3600, function () {
            return State::where('is_active', true)->forCurrentCountry()
                ->withCount(['restaurants' => function ($query) {
                    $query->where('status', 'approved');
                }])
                ->orderBy('name')
                ->get()
                ->map(fn($s) => (object)[
                    'id' => $s->id,
                    'name' => $s->name,
                    'code' => $s->code,
                    'country' => $s->country,
                    'restaurants_count' => $s->restaurants_count
                ]);
        });

        // Get featured restaurants with smart fallback
        $featuredRestaurants = $this->getFeaturedRestaurants($isMexico);

        $stats = Cache::remember('home_stats_' . CountryContext::getCountry(), 300, function () {
            return [
                'total_restaurants' => Restaurant::approved()->forCurrentCountry()->count(),
                'total_states' => State::forCurrentCountry()->has('restaurants')->count(),
                'total_categories' => Category::has('restaurants')->count(),
            ];
        });

        // Get recent activity for USA homepage
        $recentActivity = $isMexico ? collect() : $this->getRecentActivity();

        $isEn = app()->getLocale() === 'en';
        $seoTitle = $isEn
            ? 'Best Mexican Restaurants in the USA | FAMER — Famous Mexican Restaurants'
            : 'Los Mejores Restaurantes Mexicanos | FAMER — Restaurantes Mexicanos Famosos';
        $seoDesc = $isEn
            ? 'Discover the best authentic Mexican restaurants near you. Browse 25,000+ restaurants across the USA — verified ratings, menus, hours, and reviews. Find birria, tacos, tamales & more.'
            : 'Descubre los mejores restaurantes mexicanos auténticos cerca de ti. Más de 25,000 restaurantes en EE.UU. — calificaciones verificadas, menús, horarios y reseñas. Encuentra birria, tacos, tamales y más.';

        return view('livewire.home', [
            'categories' => $categories,
            'states' => $states,
            'featuredRestaurants' => $featuredRestaurants,
            'stats' => $stats,
            'isMexico' => $isMexico,
            'userLocation' => $this->detectedLocation,
            'recentActivity' => $recentActivity,
        ])->layout('layouts.app', [
            'title'           => $seoTitle,
            'metaDescription' => $seoDesc,
        ]);
    }

    public function searchRestaurants()
    {
        $params = [
            'search' => $this->search,
            'state' => $this->selectedState,
            'category' => $this->selectedCategory,
        ];

        if ($this->userLat && $this->userLng) {
            $params['lat'] = $this->userLat;
            $params['lng'] = $this->userLng;
            $params['near'] = $this->locationQuery;
        }

        $query = http_build_query(array_filter($params));

        return redirect('/restaurantes?' . $query);
    }
}
