<?php

namespace App\Events;

use App\Models\Restaurant;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewVoteEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $totalVotesThisMonth;

    public function __construct(public Restaurant $restaurant)
    {
        $this->totalVotesThisMonth = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("restaurant.{$this->restaurant->id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.vote';
    }

    public function broadcastWith(): array
    {
        return [
            'restaurant_id'         => $this->restaurant->id,
            'total_votes_this_month' => $this->totalVotesThisMonth,
        ];
    }
}
