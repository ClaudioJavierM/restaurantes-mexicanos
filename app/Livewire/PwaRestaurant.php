<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantBranding;
use App\Models\MenuCategory;
use Livewire\Component;

class PwaRestaurant extends Component
{
    public Restaurant $restaurant;
    public ?RestaurantBranding $branding = null;
    public $activeTab = 'menu';
    public $cartCount = 0;

    public function mount(string $slug)
    {
        $this->restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->with(['menuCategories' => function ($q) {
                $q->active()->ordered()->with(['items' => function ($q) {
                    $q->available()->ordered();
                }]);
            }])
            ->firstOrFail();

        $this->branding = RestaurantBranding::getForRestaurant($this->restaurant->id);
        $this->loadCartCount();
    }

    public function loadCartCount()
    {
        $cart = session()->get('cart', []);
        if (($cart['restaurant_id'] ?? null) === $this->restaurant->id) {
            $this->cartCount = collect($cart['items'] ?? [])->sum('quantity');
        } else {
            $this->cartCount = 0;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.pwa-restaurant', [
            'categories' => $this->restaurant->menuCategories,
        ])->layout('layouts.pwa', [
            'title' => $this->branding->app_name ?? $this->restaurant->name,
            'branding' => $this->branding,
            'restaurant' => $this->restaurant,
        ]);
    }
}
