<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantCustomer extends Model
{
    protected $fillable = [
        'restaurant_id',
        'email',
        'name',
        'phone',
        'birthday',
        'source',
        'email_subscribed',
        'sms_subscribed',
        'visits_count',
        'total_spent',
        'points',
        'last_visit_at',
        'subscribed_at',
        'unsubscribed_at',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'birthday' => 'date',
        'email_subscribed' => 'boolean',
        'sms_subscribed' => 'boolean',
        'total_spent' => 'decimal:2',
        'last_visit_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function campaignSends(): HasMany
    {
        return $this->hasMany(OwnerCampaignSend::class, 'customer_id');
    }

    // Scopes
    public function scopeSubscribed($query)
    {
        return $query->where('email_subscribed', true);
    }

    public function scopeSmsSubscribed($query)
    {
        return $query->where('sms_subscribed', true);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeWithBirthday($query)
    {
        return $query->whereNotNull('birthday');
    }

    public function scopeBirthdayThisMonth($query)
    {
        return $query->whereMonth('birthday', now()->month);
    }

    public function scopeInactive($query, int $days = 90)
    {
        return $query->where(function($q) use ($days) {
            $q->where('last_visit_at', '<', now()->subDays($days))
              ->orWhereNull('last_visit_at');
        });
    }

    // Helpers
    public function recordVisit(float $amount = 0): void
    {
        $this->increment('visits_count');
        $this->increment('total_spent', $amount);
        $this->update(['last_visit_at' => now()]);
    }

    public function addPoints(int $points): void
    {
        $this->increment('points', $points);
    }

    public function redeemPoints(int $points): bool
    {
        if ($this->points >= $points) {
            $this->decrement('points', $points);
            return true;
        }
        return false;
    }

    public function unsubscribe(): void
    {
        $this->update([
            'email_subscribed' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? explode('@', $this->email)[0];
    }

    public function hasBirthdayToday(): bool
    {
        if (!$this->birthday) return false;
        return $this->birthday->format('m-d') === now()->format('m-d');
    }

    public function hasBirthdayThisWeek(): bool
    {
        if (!$this->birthday) return false;
        $bdayThisYear = $this->birthday->copy()->year(now()->year);
        return $bdayThisYear->between(now(), now()->addDays(7));
    }
}
