<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class DishReview extends Model
{
    protected $fillable = [
        'restaurant_id',
        'menu_item_id',
        'user_id',
        'dish_name',
        'rating',
        'comment',
        'reviewer_name',
        'reviewer_email',
        'is_approved',
        'is_verified_purchase',
        'photos',
        'helpful_count',
    ];

    protected $casts = [
        'rating'               => 'integer',
        'is_approved'          => 'boolean',
        'is_verified_purchase' => 'boolean',
        'photos'               => 'array',
        'helpful_count'        => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->reviewer_name ?? 'Anónimo';
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    public static function getAverageForDish(int $menuItemId): float
    {
        return static::approved()->where('menu_item_id', $menuItemId)->avg('rating') ?? 0;
    }
}
