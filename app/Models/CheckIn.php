<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_id',
        'latitude',
        'longitude',
        'verified',
        'points_earned',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function createCheckIn(int $userId, int $restaurantId, ?float $lat = null, ?float $lng = null): self
    {
        $checkIn = self::create([
            'user_id' => $userId,
            'restaurant_id' => $restaurantId,
            'latitude' => $lat,
            'longitude' => $lng,
            'verified' => $lat && $lng ? self::verifyLocation($restaurantId, $lat, $lng) : false,
            'points_earned' => 10,
        ]);

        // Award points
        LoyaltyPoints::addPoints($userId, 10, 'check_in', 'Check-in en restaurante', $checkIn);

        return $checkIn;
    }

    public static function verifyLocation(int $restaurantId, float $lat, float $lng): bool
    {
        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant || !$restaurant->latitude || !$restaurant->longitude) {
            return false;
        }

        // Calculate distance in meters (Haversine formula simplified)
        $earthRadius = 6371000; // meters
        $latDiff = deg2rad($lat - $restaurant->latitude);
        $lngDiff = deg2rad($lng - $restaurant->longitude);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
              cos(deg2rad($restaurant->latitude)) * cos(deg2rad($lat)) *
              sin($lngDiff / 2) * sin($lngDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Within 100 meters is considered verified
        return $distance <= 100;
    }
}
