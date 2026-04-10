<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerNotification extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'icon',
        'color',
        'action_url',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForRestaurant($query, int $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now(), 'is_read' => true]);
        }
    }

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }

    // Helper methods to create notifications
    public static function notifyNewReview(Restaurant $restaurant, Review $review): self
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $restaurant->owner_id,
            'type' => 'review',
            'title' => 'Nueva Resena!',
            'message' => $review->reviewer_name . ' dejo una resena de ' . $review->rating . ' estrellas.',
            'data' => ['review_id' => $review->id, 'rating' => $review->rating],
            'icon' => 'star',
            'color' => $review->rating >= 4 ? 'yellow' : ($review->rating >= 3 ? 'blue' : 'red'),
            'action_url' => '/owner/my-reviews',
        ]);
    }

    public static function notifyViewMilestone(Restaurant $restaurant, int $views): self
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $restaurant->owner_id,
            'type' => 'view_milestone',
            'title' => 'Meta de Vistas Alcanzada!',
            'message' => 'Tu restaurante alcanzo ' . number_format($views) . ' vistas totales!',
            'data' => ['milestone' => $views],
            'icon' => 'eye',
            'color' => 'green',
            'action_url' => '/owner/analytics',
        ]);
    }

    public static function notifyNewFavorite(Restaurant $restaurant, User $user): self
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $restaurant->owner_id,
            'type' => 'favorite',
            'title' => 'Nuevo Favorito!',
            'message' => $user->name . ' agrego tu restaurante a sus favoritos.',
            'data' => ['favorited_by' => $user->id],
            'icon' => 'heart',
            'color' => 'pink',
            'action_url' => null,
        ]);
    }

    public static function notifyResponseNeeded(Restaurant $restaurant, int $pendingCount): self
    {
        return self::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $restaurant->owner_id,
            'type' => 'response_needed',
            'title' => 'Resenas Pendientes',
            'message' => 'Tienes ' . $pendingCount . ' resenas esperando tu respuesta.',
            'data' => ['pending_count' => $pendingCount],
            'icon' => 'chat-bubble-left',
            'color' => 'orange',
            'action_url' => '/owner/my-reviews',
        ]);
    }
}
