<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use App\Models\FoodTag;
use App\Models\Feature;
use App\Services\GeoLocationService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\CountryContext;

class RestaurantList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedState = '';
    public $selectedCategory = '';
    public $sortBy = 'rating';

    // Advanced Filters
    public $showAdvancedFilters = false;
    public $selectedPriceRange = '';
    public $selectedSpiceLevel = [];
    public $selectedRegion = '';
    public $selectedDietaryOptions = [];
    public $selectedAtmosphere = [];
    public $selectedFeatures = [];
    public $authenticOnly = false;

    // Business Type & Food Tags
    public $selectedBusinessType = '';
    public $selectedFoodTags = [];

    // Specialty Filters - Bebidas y Básicos
    public $hasCafeDeOlla = false;
    public $hasFreshTortillas = false;
    public $hasHandmadeTortillas = false;
    public $hasAguasFrescas = false;

    // Specialty Filters - Preparaciones Caseras
    public $hasHomemadeSalsa = false;
    public $hasHomemadeMole = false;

    // Specialty Filters - Métodos Tradicionales
    public $hasCharcoalGrill = false;
    public $hasComal = false;

    // Specialty Filters - Platillos Tradicionales
    public $hasBirria = false;
    public $hasCarnitas = false;
    public $hasPozoleMenudo = false;
    public $hasBarbacoa = false;
    public $hasTamales = false;

    // Specialty Filters - Panadería y Postres
    public $hasPanDulce = false;
    public $hasChurros = false;

    // Specialty Filters - Bebidas
    public $hasMezcalTequila = false;
    public $hasMicheladas = false;

    // Specialty Filters - Extras
    public $hasMexicanCandy = false;
    public $hasImportedProducts = false;

    // Location
    public $userLatitude = null;
    public $userLongitude = null;
    public $locationSource = null;
    public $showLocationBanner = true;

    protected $listeners = ['locationUpdated' => 'setUserLocation'];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedState' => ['except' => '', 'as' => 'state'],
        'selectedCategory' => ['except' => '', 'as' => 'category'],
        'sortBy' => ['except' => 'rating', 'as' => 'sort'],
        'selectedPriceRange' => ['except' => '', 'as' => 'price'],
        'selectedRegion' => ['except' => '', 'as' => 'region'],
        'selectedBusinessType' => ['except' => '', 'as' => 'type'],
    ];

    public function mount()
    {
        if (session()->has('user_location')) {
            $location = session('user_location');
            $this->userLatitude = $location['lat'];
            $this->userLongitude = $location['lng'];
            $this->locationSource = 'browser';
            $this->showLocationBanner = false;
            $this->sortBy = 'nearby';
        } else {
            $this->detectLocationFromIp();
        }
    }

    public function detectLocationFromIp()
    {
        $geoService = app(GeoLocationService::class);
        $location = $geoService->getLocationFromIp();

        if ($location && isset($location['lat']) && isset($location['lng'])) {
            $this->userLatitude = $location['lat'];
            $this->userLongitude = $location['lng'];
            $this->locationSource = 'ip';
            $this->sortBy = 'nearby';
        }
    }

    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->locationSource = 'browser';
        $this->showLocationBanner = false;
        $this->sortBy = 'nearby';

        session(['user_location' => [
            'lat' => $latitude,
            'lng' => $longitude,
        ]]);

        $this->resetPage();
    }

    public function dismissLocationBanner()
    {
        $this->showLocationBanner = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedState()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedState',
            'selectedCategory',
            'selectedPriceRange',
            'selectedSpiceLevel',
            'selectedRegion',
            'selectedDietaryOptions',
            'selectedAtmosphere',
            'selectedFeatures',
            'authenticOnly',
            'selectedBusinessType',
            'selectedFoodTags',
            // Specialty Filters
            'hasCafeDeOlla',
            'hasFreshTortillas',
            'hasHandmadeTortillas',
            'hasAguasFrescas',
            'hasHomemadeSalsa',
            'hasHomemadeMole',
            'hasCharcoalGrill',
            'hasComal',
            'hasBirria',
            'hasCarnitas',
            'hasPozoleMenudo',
            'hasBarbacoa',
            'hasTamales',
            'hasPanDulce',
            'hasChurros',
            'hasMezcalTequila',
            'hasMicheladas',
            'hasMexicanCandy',
            'hasImportedProducts',
        ]);
        $this->sortBy = ($this->userLatitude && $this->userLongitude) ? 'nearby' : 'rating';
        $this->resetPage();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function toggleSpiceLevel($level)
    {
        if (in_array($level, $this->selectedSpiceLevel)) {
            $this->selectedSpiceLevel = array_values(array_diff($this->selectedSpiceLevel, [$level]));
        } else {
            $this->selectedSpiceLevel[] = $level;
        }
        $this->resetPage();
    }

    public function toggleFoodTag($tagId)
    {
        if (in_array($tagId, $this->selectedFoodTags)) {
            $this->selectedFoodTags = array_values(array_diff($this->selectedFoodTags, [$tagId]));
        } else {
            $this->selectedFoodTags[] = $tagId;
        }
        $this->resetPage();
    }

    public function render()
    {
        $states = Cache::remember('restaurant_states', 1800, function () {
            return State::forCurrentCountry()->has('restaurants')->orderBy('name')->get();
        });

        $categories = Cache::remember('restaurant_categories', 1800, function () {
            return Category::has('restaurants')->orderBy('name')->get();
        });

        // Get food tags and features for filters
        $foodTags = Cache::remember('food_tags_active', 1800, function () {
            return FoodTag::where('is_active', true)->orderBy('name')->get();
        });

        $features = Cache::remember('features_active', 1800, function () {
            return Feature::where('is_active', true)->orderBy('category')->orderBy('sort_order')->get();
        });

        $query = Restaurant::query()
            ->approved()->forCurrentCountry()
            ->with(['state', 'category', 'media']);

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        // State filter
        if ($this->selectedState) {
            $query->whereHas('state', function($q) {
                $q->where('name', $this->selectedState);
            });
        }

        // Category filter
        if ($this->selectedCategory) {
            $query->whereHas('category', function($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        // Business Type filter
        if ($this->selectedBusinessType) {
            $query->where('business_type', $this->selectedBusinessType);
        }

        // Food Tags filter
        if (!empty($this->selectedFoodTags)) {
            $query->whereHas('foodTags', function($q) {
                $q->whereIn('food_tags.id', $this->selectedFoodTags);
            });
        }

        // Features filter
        if (!empty($this->selectedFeatures)) {
            $query->whereHas('features', function($q) {
                $q->whereIn('features.slug', $this->selectedFeatures);
            });
        }

        // Advanced Filters
        if ($this->selectedPriceRange) {
            $query->where('price_range', $this->selectedPriceRange);
        }

        if ($this->selectedRegion) {
            $query->where('mexican_region', $this->selectedRegion);
        }

        if ($this->authenticOnly) {
            $query->where(function($q) {
                $q->where('chef_certified', true)
                  ->orWhere('traditional_recipes', true)
                  ->orWhere('imported_ingredients', true);
            });
        }

        // Specialty Filters - Básicos
        if ($this->hasCafeDeOlla) {
            $query->where('has_cafe_de_olla', true);
        }
        if ($this->hasFreshTortillas) {
            $query->where('has_fresh_tortillas', true);
        }
        if ($this->hasHandmadeTortillas) {
            $query->where('has_handmade_tortillas', true);
        }
        if ($this->hasAguasFrescas) {
            $query->where('has_aguas_frescas', true);
        }

        // Specialty Filters - Preparaciones Caseras
        if ($this->hasHomemadeSalsa) {
            $query->where('has_homemade_salsa', true);
        }
        if ($this->hasHomemadeMole) {
            $query->where('has_homemade_mole', true);
        }

        // Specialty Filters - Métodos Tradicionales
        if ($this->hasCharcoalGrill) {
            $query->where('has_charcoal_grill', true);
        }
        if ($this->hasComal) {
            $query->where('has_comal', true);
        }

        // Specialty Filters - Platillos Tradicionales
        if ($this->hasBirria) {
            $query->where('has_birria', true);
        }
        if ($this->hasCarnitas) {
            $query->where('has_carnitas', true);
        }
        if ($this->hasPozoleMenudo) {
            $query->where('has_pozole_menudo', true);
        }
        if ($this->hasBarbacoa) {
            $query->where('has_barbacoa', true);
        }
        if ($this->hasTamales) {
            $query->where('has_tamales', true);
        }

        // Specialty Filters - Panadería y Postres
        if ($this->hasPanDulce) {
            $query->where('has_pan_dulce', true);
        }
        if ($this->hasChurros) {
            $query->where('has_churros', true);
        }

        // Specialty Filters - Bebidas
        if ($this->hasMezcalTequila) {
            $query->where('has_mezcal_tequila', true);
        }
        if ($this->hasMicheladas) {
            $query->where('has_micheladas', true);
        }

        // Specialty Filters - Extras
        if ($this->hasMexicanCandy) {
            $query->where('has_mexican_candy', true);
        }
        if ($this->hasImportedProducts) {
            $query->where('has_imported_products', true);
        }

        // Sorting
        // Priority: Elite first, then Premium, then others
        $query->orderByRaw("CASE
            WHEN subscription_tier = 'elite' THEN 0
            WHEN subscription_tier = 'premium' THEN 1
            ELSE 2
        END");

        switch ($this->sortBy) {
            case 'nearby':
                if ($this->userLatitude && $this->userLongitude) {
                    $query->select('restaurants.*')
                        ->selectRaw('((latitude - ?) * (latitude - ?) + (longitude - ?) * (longitude - ?)) AS distance', [$this->userLatitude, $this->userLatitude, $this->userLongitude, $this->userLongitude])
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->orderBy('distance');
                } else {
                    $query->orderByDesc('average_rating')->orderBy('name');
                }
                break;
            case 'rating':
                $query->orderByDesc('average_rating')->orderBy('name');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('name');
                break;
        }

        $restaurants = $query->paginate(12);

        // Get business types for filter dropdown
        $businessTypes = [
            'independent' => 'Independiente',
            'franchise' => 'Franquicia',
            'sports_bar' => 'Cantina/Sports Bar',
            'cafeteria' => 'Cafeteria',
            'food_truck' => 'Food Truck',
            'bakery' => 'Panaderia',
            'ice_cream' => 'Paleteria/Heladeria',
            'fast_food' => 'Comida Rapida',
            'fine_dining' => 'Alta Cocina',
            'buffet' => 'Buffet',
            'market' => 'Mercado/Tienda',
            'tortilleria' => 'Tortilleria',
            'catering' => 'Catering',
            'food_stand' => 'Puesto de Comida',
        ];

        return view('livewire.restaurant-list', [
            'restaurants' => $restaurants,
            'states' => $states,
            'categories' => $categories,
            'foodTags' => $foodTags,
            'features' => $features,
            'businessTypes' => $businessTypes,
            'userLatitude' => $this->userLatitude,
            'userLongitude' => $this->userLongitude,
            'locationSource' => $this->locationSource,
        ])->layout('layouts.app', ['title' => 'Restaurantes Mexicanos']);
    }

    public function getDistanceToRestaurant($restaurant): ?float
    {
        if (!$this->userLatitude || !$this->userLongitude) {
            return null;
        }

        if (!$restaurant->latitude || !$restaurant->longitude) {
            return null;
        }

        $geoService = app(GeoLocationService::class);
        return $geoService->calculateDistance(
            $this->userLatitude,
            $this->userLongitude,
            $restaurant->latitude,
            $restaurant->longitude
        );
    }
}
