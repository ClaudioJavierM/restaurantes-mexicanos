<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referred_restaurant_id',
        'referral_code',
        'type',
        'status',
        'reward_points',
        'reward_type',
    ];

    const REWARDS = [
        'user' => ['points' => 100, 'type' => 'points'],
        'restaurant' => ['points' => 500, 'type' => 'free_month'],
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function referredRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'referred_restaurant_id');
    }

    public static function generateCode(): string
    {
        do {
            $code = 'REF-' . strtoupper(Str::random(6));
        } while (self::where('referral_code', $code)->exists());
        
        return $code;
    }

    public static function createForUser(int $referrerId): self
    {
        return self::create([
            'referrer_id' => $referrerId,
            'referral_code' => self::generateCode(),
            'type' => 'user',
            'status' => 'pending',
            'reward_points' => self::REWARDS['user']['points'],
            'reward_type' => self::REWARDS['user']['type'],
        ]);
    }

    public function complete(int $referredId): void
    {
        $this->update([
            'referred_id' => $referredId,
            'status' => 'completed',
        ]);

        // Award points to referrer
        LoyaltyPoints::addPoints(
            $this->referrer_id,
            $this->reward_points,
            'referral',
            'Referido completado: nuevo usuario',
            $this
        );

        // Award points to referred user too
        LoyaltyPoints::addPoints(
            $referredId,
            50, // Welcome bonus
            'referral',
            'Bono de bienvenida por referido',
            $this
        );

        $this->update(['status' => 'rewarded']);
    }
}
