<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsoredListing extends Model
{
    protected $fillable = [
        'restaurant_id', 'placement', 'starts_at', 'ends_at',
        'amount_paid', 'status', 'notes',
    ];

    protected $casts = [
        'starts_at'   => 'date',
        'ends_at'     => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public static array $placements = [
        'homepage_featured' => '🏠 Destacado en Homepage',
        'search_top'        => '🔍 Top en Búsquedas',
        'city_spotlight'    => '🌆 Spotlight de Ciudad',
    ];

    public static array $prices = [
        'homepage_featured' => 49,
        'search_top'        => 39,
        'city_spotlight'    => 29,
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function isCurrentlyActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at->isPast()
            && $this->ends_at->isFuture();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }
}
