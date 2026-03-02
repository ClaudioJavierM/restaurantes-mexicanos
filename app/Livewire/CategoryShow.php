<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Restaurant;
use App\Models\FoodTag;
use App\Models\Feature;
use App\Models\State;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryShow extends Component
{
    use WithPagination;

    public Category $category;
    public string $sortBy = 'rating';
    
    // Filters
    public $selectedState = '';
    public $selectedPriceRange = '';
    public $selectedBusinessType = '';
    public $selectedFoodTags = [];
    public $selectedFeatures = [];
    public $search = '';

    protected $queryString = [
        'sortBy' => ['except' => 'rating'],
        'selectedState' => ['except' => '', 'as' => 'state'],
        'selectedPriceRange' => ['except' => '', 'as' => 'price'],
        'selectedBusinessType' => ['except' => '', 'as' => 'type'],
        'search' => ['except' => ''],
    ];

    public function mount($slug)
    {
        $this->category = Category::where('slug', $slug)->firstOrFail();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
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

    public function toggleFeature($slug)
    {
        if (in_array($slug, $this->selectedFeatures)) {
            $this->selectedFeatures = array_values(array_diff($this->selectedFeatures, [$slug]));
        } else {
            $this->selectedFeatures[] = $slug;
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['selectedState', 'selectedPriceRange', 'selectedBusinessType', 'selectedFoodTags', 'selectedFeatures', 'search']);
        $this->resetPage();
    }

    public function render()
    {
        // Get filter options
        $states = Cache::remember('category_states_' . $this->category->id, 1800, function () {
            return State::whereHas('restaurants', function($q) {
                $q->where('category_id', $this->category->id)->where('status', 'approved');
            })->orderBy('name')->get();
        });

        $foodTags = FoodTag::where('is_active', true)->orderBy('name')->get();
        $features = Feature::where('is_active', true)->orderBy('category')->orderBy('name')->get();

        $businessTypes = [
            'independent' => '🏠 Independiente',
            'franchise' => '🏪 Franquicia',
            'sports_bar' => '📺 Sports Bar',
            'cafeteria' => '☕ Cafetería',
            'food_truck' => '🚚 Food Truck',
            'bakery' => '🥐 Panadería',
            'ice_cream' => '🍦 Heladería',
            'fast_food' => '🍔 Comida Rápida',
            'fine_dining' => '🍷 Alta Cocina',
        ];

        // Build query
        $query = $this->category->restaurants()
            ->where('status', 'approved');

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        // State filter
        if ($this->selectedState) {
            $query->where('state_id', $this->selectedState);
        }

        // Price filter
        if ($this->selectedPriceRange) {
            $query->where('price_range', $this->selectedPriceRange);
        }

        // Business type filter
        if ($this->selectedBusinessType) {
            $query->where('business_type', $this->selectedBusinessType);
        }

        // Food tags filter
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

        // Sorting
        switch ($this->sortBy) {
            case 'rating':
                $query->orderByDesc('average_rating');
                break;
            case 'reviews':
                $query->orderByDesc('total_reviews');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'newest':
                $query->latest();
                break;
        }

        $restaurants = $query->paginate(24);

        // Count active filters
        $activeFilters = 0;
        if ($this->selectedState) $activeFilters++;
        if ($this->selectedPriceRange) $activeFilters++;
        if ($this->selectedBusinessType) $activeFilters++;
        if (!empty($this->selectedFoodTags)) $activeFilters += count($this->selectedFoodTags);
        if (!empty($this->selectedFeatures)) $activeFilters += count($this->selectedFeatures);

        return view('livewire.category-show', [
            'restaurants' => $restaurants,
            'states' => $states,
            'foodTags' => $foodTags,
            'features' => $features,
            'businessTypes' => $businessTypes,
            'activeFilters' => $activeFilters,
        ])->layout('layouts.app');
    }
}
