<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class FeaturedPlacement extends Model
{
    protected $fillable = [
        'restaurant_id',
        'placement_type',
        'scope',
        'starts_at',
        'ends_at',
        'amount_paid',
        'status',
        'stripe_payment_intent_id',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'starts_at'   => 'date',
        'ends_at'     => 'date',
        'amount_paid' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('status', 'active')
            ->where('starts_at', '<=', $today)
            ->where('ends_at', '>=', $today);
    }

    public function scopeForScope($query, string $type, ?string $scope)
    {
        $query->where('placement_type', $type);

        if ($scope !== null) {
            $query->where('scope', $scope);
        }

        return $query;
    }

    // ─── Static helpers ───────────────────────────────────────────────────────

    public static function getFeaturedRestaurantIds(string $type = 'national', ?string $scope = null): array
    {
        return static::active()
            ->forScope($type, $scope)
            ->pluck('restaurant_id')
            ->toArray();
    }
}
