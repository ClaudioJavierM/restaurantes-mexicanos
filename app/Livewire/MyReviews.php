<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class MyReviews extends Component
{
    use WithPagination;

    public function render()
    {
        $reviews = auth()->user()
            ->reviews()
            ->with(['restaurant', 'photos'])
            ->latest()
            ->paginate(12);

        return view('livewire.my-reviews', [
            'reviews' => $reviews,
        ])->layout('layouts.app', ['title' => 'Mis Reseñas']);
    }
}
