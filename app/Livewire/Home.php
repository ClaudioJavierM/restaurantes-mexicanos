<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Restaurant;
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
        if (strlen($this->locationQuery) >= 2) {
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

        // For USA, try to get featured/subscribed restaurants first
        $featured = Restaurant::approved()
            ->forCurrentCountry()
            ->with(['state', 'category', 'media'])
            ->featured()
            ->limit(6)
            ->get();

        // If we have featured restaurants, return them
        if ($featured->count() >= 3) {
            return $featured;
        }

        // If user has a detected state from IP, show from that state
        if ($this->detectedLocation && isset($this->detectedLocation['state_code'])) {
            $stateCode = $this->detectedLocation['state_code'];
            $stateRestaurants = Restaurant::approved()
                ->forCurrentCountry()
                ->with(['state', 'category', 'media'])
                ->whereHas('state', fn($q) => $q->where('code', $stateCode))
                ->orderByDesc('google_rating')
                ->orderByDesc('google_reviews_count')
                ->limit(12)
                ->get();

            // If we have location, calculate distances in PHP and sort
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

            if ($stateRestaurants->count() >= 3) {
                return $stateRestaurants->take(6);
            }
        }

        // Fallback: Get highest rated restaurants nationwide
        return Restaurant::approved()
            ->forCurrentCountry()
            ->with(['state', 'category', 'media'])
            ->orderByDesc('google_rating')
            ->orderByDesc('google_reviews_count')
            ->limit(6)
            ->get();
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

        return view('livewire.home', [
            'categories' => $categories,
            'states' => $states,
            'featuredRestaurants' => $featuredRestaurants,
            'stats' => $stats,
            'isMexico' => $isMexico,
            'userLocation' => $this->detectedLocation,
        ])->layout('layouts.app', ['title' => 'Inicio']);
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
