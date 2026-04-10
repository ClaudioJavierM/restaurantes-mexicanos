<?php

namespace App\Services;

use App\Events\NewOrderEvent;
use App\Events\NewReviewEvent;
use App\Events\NewVoteEvent;
use App\Models\Order;
use App\Models\OwnerNotification;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Log;

class OwnerNotificationService
{
    /**
     * Notify owner of a new order.
     */
    public function notifyNewOrder(Order $order): void
    {
        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            return;
        }

        $this->createNotification(
            restaurantId: $restaurant->id,
            type: 'new_order',
            title: 'Nuevo Pedido Recibido',
            message: "#{$order->order_number} de {$order->customer_name} — $" . number_format($order->total, 2),
            data: [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $order->total,
                'order_type'   => $order->order_type,
            ],
            icon: 'package',
            color: 'blue',
            actionUrl: '/owner/live-orders',
        );

        $this->broadcastIfPusherConfigured(fn () => event(new NewOrderEvent($order)));
    }

    /**
     * Notify owner of a new review.
     */
    public function notifyNewReview(Review $review): void
    {
        $restaurant = $review->restaurant;
        if (!$restaurant) {
            return;
        }

        $stars = str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);

        $this->createNotification(
            restaurantId: $restaurant->id,
            type: 'new_review',
            title: 'Nueva Reseña Recibida',
            message: "{$review->reviewer_name} dejó una reseña de {$review->rating}/5 {$stars}",
            data: [
                'review_id'     => $review->id,
                'rating'        => $review->rating,
                'reviewer_name' => $review->reviewer_name,
                'excerpt'       => mb_substr($review->comment ?? '', 0, 100),
            ],
            icon: 'star',
            color: $review->rating >= 4 ? 'yellow' : ($review->rating >= 3 ? 'blue' : 'red'),
            actionUrl: '/owner/my-reviews',
        );

        $this->broadcastIfPusherConfigured(fn () => event(new NewReviewEvent($restaurant, $review)));
    }

    /**
     * Notify owner of a new vote.
     */
    public function notifyNewVote(Restaurant $restaurant): void
    {
        $votesThisMonth = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();

        $this->createNotification(
            restaurantId: $restaurant->id,
            type: 'new_vote',
            title: '¡Nuevo Voto Recibido!',
            message: "Tu restaurante recibió un voto. Total este mes: {$votesThisMonth} votos.",
            data: [
                'restaurant_id'          => $restaurant->id,
                'total_votes_this_month' => $votesThisMonth,
            ],
            icon: 'trophy',
            color: 'yellow',
            actionUrl: '/owner/analytics',
        );

        $this->broadcastIfPusherConfigured(fn () => event(new NewVoteEvent($restaurant)));
    }

    /**
     * Create a raw notification record.
     */
    public function createNotification(
        int $restaurantId,
        string $type,
        string $title,
        string $message,
        array $data = [],
        ?string $icon = null,
        ?string $color = null,
        ?string $actionUrl = null,
    ): OwnerNotification {
        $restaurant = Restaurant::find($restaurantId);
        $ownerId = $restaurant?->owner_id;

        return OwnerNotification::create([
            'restaurant_id' => $restaurantId,
            'user_id'       => $ownerId,
            'type'          => $type,
            'title'         => $title,
            'message'       => $message,
            'data'          => $data,
            'icon'          => $icon,
            'color'         => $color,
            'action_url'    => $actionUrl,
        ]);
    }

    /**
     * Fire broadcast only when Pusher credentials are present.
     */
    private function broadcastIfPusherConfigured(callable $callback): void
    {
        $key = config('broadcasting.connections.pusher.key');
        $secret = config('broadcasting.connections.pusher.secret');
        $appId = config('broadcasting.connections.pusher.app_id');

        if (empty($key) || empty($secret) || empty($appId)) {
            return;
        }

        try {
            $callback();
        } catch (\Throwable $e) {
            Log::warning('[OwnerNotificationService] Broadcast failed: ' . $e->getMessage());
        }
    }
}
