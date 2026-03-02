<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class MyFavorites extends Component
{
    use WithPagination;

    public function removeFavorite($restaurantId)
    {
        auth()->user()->favorites()
            ->where('restaurant_id', $restaurantId)
            ->delete();

        session()->flash('success', __('Restaurant removed from favorites'));
    }

    public function render()
    {
        $favorites = auth()->user()
            ->favoriteRestaurants()
            ->with(['state', 'category'])
            ->withCount('reviews')
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('livewire.my-favorites', [
            'favorites' => $favorites,
        ])->layout('layouts.app', ['title' => __('My Favorites')]);
    }
}
