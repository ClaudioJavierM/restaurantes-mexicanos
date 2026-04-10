<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class ReplyToReview extends Component
{
    use WithPagination;

    protected static bool $isLazy = true;

    public int $restaurantId;
    public string $replyText = '';
    public ?int $replyingToId = null;

    protected $listeners = ['reply-saved' => '$refresh'];

    public function mount(int $restaurantId): void
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        if ($restaurant->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para gestionar este restaurante.');
        }

        $this->restaurantId = $restaurantId;
    }

    public function startReply(int $reviewId): void
    {
        $this->replyingToId = $reviewId;
        $this->replyText = '';
    }

    public function cancelReply(): void
    {
        $this->replyingToId = null;
        $this->replyText = '';
    }

    public function submitReply(): void
    {
        $this->validate([
            'replyText' => 'required|min:10|max:500',
        ], [
            'replyText.required' => 'La respuesta no puede estar vacía.',
            'replyText.min'      => 'La respuesta debe tener al menos 10 caracteres.',
            'replyText.max'      => 'La respuesta no puede superar los 500 caracteres.',
        ]);

        $review = Review::where('id', $this->replyingToId)
            ->where('restaurant_id', $this->restaurantId)
            ->firstOrFail();

        $review->update([
            'owner_reply'      => trim($this->replyText),
            'owner_replied_at' => now(),
        ]);

        $this->cancelReply();
        $this->dispatch('reply-saved');
    }

    public function getReviewsProperty()
    {
        return Review::where('restaurant_id', $this->restaurantId)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->with('user')
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.owner.reply-to-review', [
            'reviews' => $this->reviews,
        ])->layout('layouts.owner');
    }
}
