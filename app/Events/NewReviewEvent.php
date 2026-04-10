<?php

namespace App\Events;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewReviewEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Restaurant $restaurant,
        public Review $review
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("restaurant.{$this->restaurant->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.review';
    }

    public function broadcastWith(): array
    {
        return [
            'reviewer_name' => $this->review->reviewer_name,
            'rating'        => $this->review->rating,
            'excerpt'       => mb_substr($this->review->comment ?? '', 0, 100),
        ];
    }
}
