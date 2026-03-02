<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformConnection extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'platform',
        'platform_account_id',
        'platform_account_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'connected_at',
        'last_sync_at',
        'last_error',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'connected_at' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    // Statuses
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';
    const STATUS_PENDING = 'pending';

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               ($this->token_expires_at === null || $this->token_expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function needsRefresh(): bool
    {
        return $this->token_expires_at && 
               $this->token_expires_at->subMinutes(5)->isPast();
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    public function recordError(string $error): void
    {
        $this->update(['last_error' => $error]);
    }

    public function recordSync(): void
    {
        $this->update([
            'last_sync_at' => now(),
            'last_error' => null,
        ]);
    }
}
