<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Restaurant;
use Livewire\Component;

class FavoriteButton extends Component
{
    public Restaurant $restaurant;
    public bool $isFavorited = false;
    public int $favoritesCount = 0;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->checkFavoriteStatus();
    }

    public function checkFavoriteStatus()
    {
        if (auth()->check()) {
            $this->isFavorited = auth()->user()->hasFavorited($this->restaurant);
        }

        $this->favoritesCount = $this->restaurant->favorites()->count();
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) {
            return $this->redirect(route('login', ['redirect' => url()->current()]));
        }

        if ($this->isFavorited) {
            // Remove from favorites
            Favorite::where('user_id', auth()->id())
                ->where('restaurant_id', $this->restaurant->id)
                ->delete();

            $this->isFavorited = false;
            $this->favoritesCount = max(0, $this->favoritesCount - 1);
        } else {
            // Add to favorites
            Favorite::create([
                'user_id' => auth()->id(),
                'restaurant_id' => $this->restaurant->id,
            ]);

            $this->isFavorited = true;
            $this->favoritesCount++;
        }

        $this->dispatch('favorite-toggled');
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
