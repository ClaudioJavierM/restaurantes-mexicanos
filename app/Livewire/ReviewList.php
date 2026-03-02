<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewList extends Component
{
    use WithPagination;

    public Restaurant $restaurant;
    public $sortBy = 'recent'; // recent, helpful, rating_high, rating_low
    public $filterRating = null; // 1-5 or null for all

    protected $queryString = [
        'sortBy' => ['except' => 'recent'],
        'filterRating' => ['except' => null],
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }

    public function setFilterRating($rating)
    {
        $this->filterRating = $rating == $this->filterRating ? null : $rating;
        $this->resetPage();
    }

    public function voteHelpful($reviewId)
    {
        if (!auth()->check()) {
            session()->flash('vote-error', app()->getLocale() === 'en'
                ? 'Please login to vote on reviews.'
                : 'Por favor inicia sesión para votar en reseñas.');
            return;
        }

        $review = Review::findOrFail($reviewId);
        $review->toggleVote(true);

        session()->flash('vote-success', app()->getLocale() === 'en'
            ? 'Thank you for your feedback!'
            : '¡Gracias por tu opinión!');
    }

    public function voteNotHelpful($reviewId)
    {
        if (!auth()->check()) {
            session()->flash('vote-error', app()->getLocale() === 'en'
                ? 'Please login to vote on reviews.'
                : 'Por favor inicia sesión para votar en reseñas.');
            return;
        }

        $review = Review::findOrFail($reviewId);
        $review->toggleVote(false);

        session()->flash('vote-success', app()->getLocale() === 'en'
            ? 'Thank you for your feedback!'
            : '¡Gracias por tu opinión!');
    }

    public function render()
    {
        $query = $this->restaurant->reviews()->approved();

        // Apply rating filter
        if ($this->filterRating) {
            $query->where('rating', $this->filterRating);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'rating_high':
                $query->orderByDesc('rating')->orderByDesc('created_at');
                break;
            case 'rating_low':
                $query->orderBy('rating')->orderByDesc('created_at');
                break;
            default: // recent
                $query->recent();
                break;
        }

        $reviews = $query->with(['user', 'photos', 'votes'])->paginate(10);

        // Get rating distribution
        $ratingDistribution = $this->restaurant->reviews()
            ->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->get()
            ->pluck('count', 'rating');

        return view('livewire.review-list', [
            'reviews' => $reviews,
            'ratingDistribution' => $ratingDistribution,
        ]);
    }
}
