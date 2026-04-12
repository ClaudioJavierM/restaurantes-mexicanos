<?php

namespace App\Livewire\Owner;

use Livewire\Component;

class RestaurantSwitcher extends Component
{
    public int|null $selectedId = null;

    public function mount(): void
    {
        $this->selectedId = session('famer_selected_restaurant')
            ?? auth()->user()->firstAccessibleRestaurant()?->id;
    }

    public function switchRestaurant(int $id): void
    {
        $accessible = auth()->user()->allAccessibleRestaurants()->pluck('id');

        if (!$accessible->contains($id)) {
            return;
        }

        session(['famer_selected_restaurant' => $id]);
        $this->selectedId = $id;

        // Bust the static cache on the User model so firstAccessibleRestaurant()
        // re-resolves on the next request with the newly selected restaurant.
        // We do this by redirecting (new request = fresh static cache).
        $this->redirect(request()->server('HTTP_REFERER', '/owner'));
    }

    public function getRestaurantsProperty()
    {
        return auth()->user()->allAccessibleRestaurants()->get(['id', 'name', 'city', 'state']);
    }

    public function render()
    {
        return view('livewire.owner.restaurant-switcher');
    }
}
