<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCoupon extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'claimed_at',
        'used_at',
        'is_used',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'used_at'    => 'datetime',
        'is_used'    => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
