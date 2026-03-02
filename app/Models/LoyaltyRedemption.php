<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LoyaltyRedemption extends Model
{
    protected $fillable = [
        'customer_id',
        'reward_id',
        'points_spent',
        'redemption_code',
        'status',
        'used_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($redemption) {
            if (empty($redemption->redemption_code)) {
                $redemption->redemption_code = strtoupper(Str::random(8));
            }
            if (empty($redemption->expires_at)) {
                $redemption->expires_at = now()->addDays(30);
            }
        });

        static::created(function ($redemption) {
            // Deduct points from customer
            $redemption->customer->decrement('points', $redemption->points_spent);
            // Increment redemption count on reward
            $redemption->reward->increment('redemption_count');
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(RestaurantCustomer::class, 'customer_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReward::class, 'reward_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function isValid(): bool
    {
        if ($this->status !== 'pending') return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    public function markUsed(): void
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        if ($this->status === 'pending') {
            // Refund points
            $this->customer->increment('points', $this->points_spent);
            $this->reward->decrement('redemption_count');
            $this->update(['status' => 'cancelled']);
        }
    }
}
