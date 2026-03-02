<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalRating extends Model
{
    protected $fillable = [
        'restaurant_id',
        'platform',
        'platform_id',
        'rating',
        'review_count',
        'price_level',
        'platform_url',
        'extra_data',
        'last_synced_at',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'extra_data' => 'array',
        'last_synced_at' => 'datetime',
    ];

    const PLATFORMS = [
        'google' => 'Google',
        'yelp' => 'Yelp',
        'facebook' => 'Facebook',
        'tripadvisor' => 'TripAdvisor',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function getNormalizedScoreAttribute(): float
    {
        if (!$this->rating) return 0;
        
        // Normalize to 0-100 scale
        // Rating is 0-5, so multiply by 20
        $baseScore = $this->rating * 20;
        
        // Bonus for review count (up to 10 extra points)
        $reviewBonus = min(10, $this->review_count / 100);
        
        return min(100, $baseScore + $reviewBonus);
    }

    public static function syncFromGoogle(Restaurant $restaurant, array $data): self
    {
        return self::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'platform' => 'google'],
            [
                'platform_id' => $data['place_id'] ?? null,
                'rating' => $data['rating'] ?? null,
                'review_count' => $data['user_ratings_total'] ?? 0,
                'price_level' => isset($data['price_level']) ? str_repeat('$', $data['price_level']) : null,
                'platform_url' => $data['url'] ?? null,
                'extra_data' => $data,
                'last_synced_at' => now(),
            ]
        );
    }

    public static function syncFromYelp(Restaurant $restaurant, array $data): self
    {
        return self::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'platform' => 'yelp'],
            [
                'platform_id' => $data['id'] ?? null,
                'rating' => $data['rating'] ?? null,
                'review_count' => $data['review_count'] ?? 0,
                'price_level' => $data['price'] ?? null,
                'platform_url' => $data['url'] ?? null,
                'extra_data' => $data,
                'last_synced_at' => now(),
            ]
        );
    }
}
