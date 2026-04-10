<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class UserCollection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'cover_restaurant_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coverRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'cover_restaurant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserCollectionItem::class, 'collection_id')->orderBy('sort_order');
    }

    public function restaurants()
    {
        return $this->hasManyThrough(
            Restaurant::class,
            UserCollectionItem::class,
            'collection_id',    // FK on user_collection_items → user_collections
            'id',               // FK on restaurants
            'id',               // local key on user_collections
            'restaurant_id'     // local key on user_collection_items
        );
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────────

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Returns the cover image URL: coverRestaurant image, or the first item's restaurant image.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->coverRestaurant) {
            return $this->coverRestaurant->getDisplayImageUrl();
        }

        $firstItem = $this->items()->with('restaurant')->first();
        if ($firstItem && $firstItem->restaurant) {
            return $firstItem->restaurant->getDisplayImageUrl();
        }

        return null;
    }
}
