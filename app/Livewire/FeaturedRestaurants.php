<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FeaturedPlacement;
use App\Models\Restaurant;

class FeaturedRestaurants extends Component
{
    public string $city = '';
    public string $stateCode = '';

    public function getRestaurantsProperty()
    {
        // Get active featured restaurant IDs for this city/state/national
        $cityIds     = FeaturedPlacement::getFeaturedRestaurantIds('city', $this->city);
        $stateIds    = FeaturedPlacement::getFeaturedRestaurantIds('state', $this->stateCode);
        $nationalIds = FeaturedPlacement::getFeaturedRestaurantIds('national');

        $ids = array_unique(array_merge($cityIds, $stateIds, $nationalIds));

        if (empty($ids)) {
            return collect();
        }

        // Track impression for all matched active placements
        FeaturedPlacement::active()->whereIn('restaurant_id', $ids)->increment('impressions');

        return Restaurant::approved()
            ->whereIn('id', $ids)
            ->with('state')
            ->limit(6)
            ->get();
    }

    public function trackClick(int $restaurantId): void
    {
        FeaturedPlacement::active()
            ->where('restaurant_id', $restaurantId)
            ->increment('clicks');
    }

    public function render()
    {
        return view('livewire.featured-restaurants', [
            'featured' => $this->restaurants,
        ]);
    }
}
