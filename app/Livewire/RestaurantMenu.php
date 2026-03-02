<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\MenuCategory;
use Livewire\Component;

class RestaurantMenu extends Component
{
    public Restaurant $restaurant;
    public $activeCategory = null;
    public $showQrModal = false;

    public function mount($slug)
    {
        $this->restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();
        
        // Set first category as active
        $firstCategory = $this->restaurant->menuCategories()
            ->active()
            ->ordered()
            ->first();
        
        if ($firstCategory) {
            $this->activeCategory = $firstCategory->id;
        }
    }

    public function setCategory($categoryId)
    {
        $this->activeCategory = $categoryId;
    }

    public function toggleQrModal()
    {
        $this->showQrModal = !$this->showQrModal;
    }

    public function render()
    {
        $categories = $this->restaurant->menuCategories()
            ->active()
            ->ordered()
            ->with(['items' => function ($q) {
                $q->available()->ordered();
            }])
            ->get();

        return view('livewire.restaurant-menu', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
