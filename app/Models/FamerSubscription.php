<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamerSubscription extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'year',
        'status',
        'wants_notifications',
        'allows_promotion',
        'contact_email',
        'contact_phone',
        'goals',
        'subscribed_at',
    ];

    protected $casts = [
        'wants_notifications' => 'boolean',
        'allows_promotion' => 'boolean',
        'subscribed_at' => 'datetime',
        'year' => 'integer',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'subscribed_at' => now(),
        ]);
    }
}
