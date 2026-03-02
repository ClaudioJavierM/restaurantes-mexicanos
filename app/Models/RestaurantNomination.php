<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantNomination extends Model
{
    protected $fillable = [
        'user_id',
        'restaurant_name',
        'address',
        'city',
        'state_code',
        'phone',
        'website',
        'google_maps_url',
        'yelp_url',
        'why_nominate',
        'nominator_name',
        'nominator_email',
        'nominator_ip',
        'status',
        'created_restaurant_id',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'created_restaurant_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function approve(int $restaurantId = null): void
    {
        $this->update([
            'status' => 'approved',
            'created_restaurant_id' => $restaurantId,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
            'reviewed_at' => now(),
        ]);
    }
}
