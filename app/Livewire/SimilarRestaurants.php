<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;

class SimilarRestaurants extends Component
{
    public int $restaurantId;
    public string $city = '';
    public ?int $stateId = null;

    public function render()
    {
        $similar = Restaurant::where('id', '!=', $this->restaurantId)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('city', $this->city)
                  ->orWhere('state_id', $this->stateId);
            })
            ->where('google_rating', '>=', 3.5)
            ->orderByDesc('google_rating')
            ->orderByDesc('google_reviews_count')
            ->limit(6)
            ->get();

        return view('livewire.similar-restaurants', ['similar' => $similar]);
    }
}
