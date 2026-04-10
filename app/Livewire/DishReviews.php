<?php

namespace App\Livewire;

use App\Models\DishReview;
use Livewire\Component;

class DishReviews extends Component
{
    public int $restaurantId;
    public string $sortBy = 'recent'; // recent, highest, lowest, helpful

    public function getReviewsProperty()
    {
        $query = DishReview::approved()
            ->forRestaurant($this->restaurantId)
            ->with(['user', 'menuItem']);

        return match ($this->sortBy) {
            'highest' => $query->orderByDesc('rating'),
            'lowest'  => $query->orderBy('rating'),
            'helpful' => $query->orderByDesc('helpful_count'),
            default   => $query->orderByDesc('created_at'),
        }->limit(20)->get();
    }

    public function markHelpful(int $reviewId): void
    {
        DishReview::where('id', $reviewId)->increment('helpful_count');
    }

    public function render()
    {
        return view('livewire.dish-reviews', [
            'reviews'      => $this->reviews,
            'totalReviews' => DishReview::approved()->forRestaurant($this->restaurantId)->count(),
            'avgRating'    => DishReview::approved()->forRestaurant($this->restaurantId)->avg('rating') ?? 0,
        ]);
    }
}
