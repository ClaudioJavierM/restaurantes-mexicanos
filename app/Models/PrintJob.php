<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintJob extends Model
{
    protected $fillable = ['restaurant_id', 'order_id', 'content', 'status', 'printed_at'];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markPrinting(): void
    {
        $this->update(['status' => 'printing']);
    }

    public function markDone(): void
    {
        $this->update(['status' => 'done', 'printed_at' => now()]);
    }

    public function markFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeForRestaurant($q, $restaurantId)
    {
        return $q->where('restaurant_id', $restaurantId);
    }
}
