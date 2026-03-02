<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OwnerCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'created_by',
        'name',
        'subject',
        'preview_text',
        'type',
        'content',
        'template',
        'audience_filter',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'coupon_config',
    ];

    protected $casts = [
        'audience_filter' => 'array',
        'coupon_config' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Types
    const TYPE_PROMO = 'promo';
    const TYPE_ANNOUNCEMENT = 'announcement';
    const TYPE_EVENT = 'event';
    const TYPE_BIRTHDAY = 'birthday';
    const TYPE_LOYALTY = 'loyalty';
    const TYPE_REACTIVATION = 'reactivation';
    const TYPE_NEWSLETTER = 'newsletter';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_PROMO => 'Promoción',
            self::TYPE_ANNOUNCEMENT => 'Anuncio',
            self::TYPE_EVENT => 'Evento',
            self::TYPE_BIRTHDAY => 'Cumpleaños',
            self::TYPE_LOYALTY => 'Lealtad',
            self::TYPE_REACTIVATION => 'Reactivación',
            self::TYPE_NEWSLETTER => 'Newsletter',
        ];
    }

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(OwnerCampaignSend::class, 'campaign_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    // Stats
    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 1);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->clicked_count / $this->sent_count) * 100, 1);
    }

    public function getBounceRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->bounced_count / $this->sent_count) * 100, 1);
    }

    public function getUnsubscribeRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->unsubscribed_count / $this->sent_count) * 100, 1);
    }

    // Status checks
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function canSend(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['scheduled', 'sending']);
    }

    // Actions
    public function schedule(\DateTime $date): void
    {
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $date,
        ]);
    }

    public function sendNow(): void
    {
        $this->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'sent',
            'completed_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    // Get audience based on filters
    public function getAudience()
    {
        $query = RestaurantCustomer::where('restaurant_id', $this->restaurant_id)
            ->subscribed();

        $filters = $this->audience_filter ?? [];

        if (!empty($filters['source'])) {
            $query->bySource($filters['source']);
        }

        if (!empty($filters['min_visits'])) {
            $query->where('visits_count', '>=', $filters['min_visits']);
        }

        if (!empty($filters['min_spent'])) {
            $query->where('total_spent', '>=', $filters['min_spent']);
        }

        if (!empty($filters['inactive_days'])) {
            $query->inactive($filters['inactive_days']);
        }

        if (!empty($filters['has_birthday'])) {
            $query->withBirthday();
        }

        if (!empty($filters['birthday_this_month'])) {
            $query->birthdayThisMonth();
        }

        if (!empty($filters['tags'])) {
            foreach ($filters['tags'] as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        return $query;
    }

    public function getAudienceCount(): int
    {
        return $this->getAudience()->count();
    }
}
