<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Review;
use App\Models\ReviewPhoto;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class WriteReview extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;
    public $rating = 0;
    public $serviceRating = 0;
    public $foodRating = 0;
    public $ambianceRating = 0;
    public $title = '';
    public $comment = '';
    public $visitDate;
    public $visitType = 'dine_in';
    public $guestName = '';
    public $guestEmail = '';
    public $photos = [];
    public $showForm = false;

    protected function rules()
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'serviceRating' => 'nullable|integer|min:0|max:5',
            'foodRating' => 'nullable|integer|min:0|max:5',
            'ambianceRating' => 'nullable|integer|min:0|max:5',
            'title' => 'nullable|string|max:200',
            'comment' => 'required|string|min:10|max:5000',
            'photos.*' => 'nullable|image|max:5120',
        ];

        if (!auth()->check()) {
            $rules['guestName'] = 'required|string|max:100';
            $rules['guestEmail'] = 'required|email|max:255';
        }

        return $rules;
    }

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function setServiceRating($rating)
    {
        $this->serviceRating = $rating;
    }

    public function setFoodRating($rating)
    {
        $this->foodRating = $rating;
    }

    public function setAmbianceRating($rating)
    {
        $this->ambianceRating = $rating;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function submitReview()
    {
        $this->validate();

        $userId = auth()->id();

        $review = Review::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => $userId,
            'name' => $userId ? (auth()->user()->name ?? null) : $this->guestName,
            'email' => $userId ? (auth()->user()->email ?? null) : $this->guestEmail,
            'rating' => $this->rating,
            'service_rating' => $this->serviceRating > 0 ? $this->serviceRating : null,
            'food_rating' => $this->foodRating > 0 ? $this->foodRating : null,
            'ambiance_rating' => $this->ambianceRating > 0 ? $this->ambianceRating : null,
            'title' => $this->title,
            'comment' => $this->comment,
            'status' => 'approved',
            'approved_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        if (!empty($this->photos)) {
            foreach ($this->photos as $index => $photo) {
                $path = $photo->store('reviews', 'public');
                ReviewPhoto::create([
                    'review_id' => $review->id,
                    'photo_path' => $path,
                    'display_order' => $index,
                ]);
            }
        }

        $this->restaurant->updateRating();

        $this->reset(['rating', 'serviceRating', 'foodRating', 'ambianceRating', 'title', 'comment', 'visitDate', 'visitType', 'guestName', 'guestEmail', 'photos', 'showForm']);

        session()->flash('review-success', app()->getLocale() === 'en'
            ? 'Thank you! Your review has been submitted successfully.'
            : '¡Gracias! Tu reseña ha sido enviada exitosamente.');

        return redirect()->to(route('restaurants.show', $this->restaurant->slug));
    }

    public function render()
    {
        return view('livewire.write-review');
    }
}
