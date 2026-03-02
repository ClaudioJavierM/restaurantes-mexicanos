<?php

namespace App\Livewire;

use App\Models\Review;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ReviewForm extends Component
{
    public $restaurant;

    #[Validate('required|integer|min:1|max:5')]
    public $rating = 5;

    #[Validate('required|min:5|max:100')]
    public $title = '';

    #[Validate('required|min:10|max:1000')]
    public $comment = '';

    #[Validate('required|min:2', as: 'nombre')]
    public $guest_name = '';

    #[Validate('required|email', as: 'email')]
    public $guest_email = '';

    public function mount($restaurant)
    {
        $this->restaurant = $restaurant;

        // Pre-fill if user is logged in
        if (auth()->check()) {
            $this->guest_name = auth()->user()->name;
            $this->guest_email = auth()->user()->email;
        }
    }

    public function submit()
    {
        $this->validate();

        Review::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : $this->guest_name,
            'guest_email' => auth()->check() ? null : $this->guest_email,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        session()->flash('review_success', '¡Gracias por tu reseña! Será publicada después de ser revisada.');

        $this->reset(['rating', 'title', 'comment']);
        $this->rating = 5;

        $this->dispatch('review-submitted');
    }

    public function render()
    {
        return view('livewire.review-form');
    }
}
