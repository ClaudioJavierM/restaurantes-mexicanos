<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_restaurant_id',
        'referred_restaurant_id',
        'referral_code',
        'referred_email',
        'status',
        'reward_type',
        'reward_value',
        'claimed_at',
        'subscribed_at',
        'rewarded_at',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'claimed_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(Restaurant::class, 'referrer_restaurant_id');
    }

    public function referred()
    {
        return $this->belongsTo(Restaurant::class, 'referred_restaurant_id');
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 8));
        } while (Restaurant::where('referral_code', $code)->exists());

        return $code;
    }
}
