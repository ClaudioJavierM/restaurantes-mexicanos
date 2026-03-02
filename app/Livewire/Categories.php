<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class Categories extends Component
{
    public function render()
    {
        $categories = Category::withCount('restaurants')
            ->orderBy('restaurants_count', 'desc')
            ->get();
        
        return view('livewire.categories', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
