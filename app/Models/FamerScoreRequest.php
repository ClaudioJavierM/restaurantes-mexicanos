<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamerScoreRequest extends Model
{
    protected $fillable = [
        'restaurant_id',
        'famer_score_id',
        'email',
        'name',
        'phone',
        'restaurant_name',
        'restaurant_city',
        'restaurant_state',
        'yelp_id',
        'google_place_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referrer',
        'ip_address',
        'user_agent',
        'status',
        'email_sent_at',
        'email_opened_at',
        'email_clicked_at',
        'marketing_consent',
        'is_owner',
    ];

    protected $casts = [
        'email_sent_at' => 'datetime',
        'email_opened_at' => 'datetime',
        'email_clicked_at' => 'datetime',
        'marketing_consent' => 'boolean',
        'is_owner' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function famerScore(): BelongsTo
    {
        return $this->belongsTo(FamerScore::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'email_sent_at' => now(),
        ]);
    }

    public function markAsOpened(): void
    {
        if ($this->status === 'sent') {
            $this->update([
                'status' => 'opened',
                'email_opened_at' => now(),
            ]);
        }
    }

    public function markAsClicked(): void
    {
        if (in_array($this->status, ['sent', 'opened'])) {
            $this->update([
                'status' => 'clicked',
                'email_clicked_at' => now(),
            ]);
        }
    }

    public function markAsClaimed(): void
    {
        $this->update(['status' => 'claimed']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNotClaimed($query)
    {
        return $query->where('status', '!=', 'claimed');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->restaurant) {
            return $this->restaurant->name;
        }
        return $this->restaurant_name ?? 'Unknown Restaurant';
    }

    public function getDisplayLocationAttribute(): string
    {
        if ($this->restaurant) {
            return "{$this->restaurant->city}, {$this->restaurant->state?->code}";
        }
        if ($this->restaurant_city) {
            return "{$this->restaurant_city}, {$this->restaurant_state}";
        }
        return '';
    }
}
