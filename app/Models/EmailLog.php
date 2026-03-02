<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailLog extends Model
{
    protected $fillable = [
        'campaign_id',
        'type',
        'category',
        'to_email',
        'to_name',
        'from_email',
        'from_name',
        'subject',
        'body_preview',
        'mailable_class',
        'template',
        'metadata',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'delivered_at',
        'error_message',
        'message_id',
        'provider',
        'tracking_token',
        'open_count',
        'click_count',
        'user_id',
        'restaurant_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Types
    const TYPE_TRANSACTIONAL = 'transactional';
    const TYPE_CAMPAIGN = 'campaign';
    const TYPE_NOTIFICATION = 'notification';

    // Categories
    const CATEGORY_RESERVATION = 'reservation';
    const CATEGORY_CLAIM = 'claim';
    const CATEGORY_ORDER = 'order';
    const CATEGORY_MARKETING = 'marketing';
    const CATEGORY_TEAM = 'team';
    const CATEGORY_VERIFICATION = 'verification';
    const CATEGORY_REMINDER = 'reminder';
    const CATEGORY_NEWSLETTER = 'newsletter';
    const CATEGORY_FAMER = 'famer';

    // Statuses
    const STATUS_QUEUED = 'queued';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_FAILED = 'failed';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->tracking_token)) {
                $model->tracking_token = Str::random(64);
            }
        });
    }

    // Relationships
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopeTransactional($query)
    {
        return $query->where('type', self::TYPE_TRANSACTIONAL);
    }

    public function scopeCampaigns($query)
    {
        return $query->where('type', self::TYPE_CAMPAIGN);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeOpened($query)
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeClicked($query)
    {
        return $query->whereNotNull('clicked_at');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // Tracking methods
    public function markAsOpened(): void
    {
        $this->increment('open_count');

        if (!$this->opened_at) {
            $this->update([
                'status' => self::STATUS_OPENED,
                'opened_at' => now(),
            ]);
        }

        // Update campaign stats
        if ($this->campaign) {
            $this->campaign->syncStats();
        }
    }

    public function markAsClicked(): void
    {
        $this->increment('click_count');

        if (!$this->clicked_at) {
            $this->update([
                'status' => self::STATUS_CLICKED,
                'clicked_at' => now(),
            ]);
        }

        // Also mark as opened if not already
        if (!$this->opened_at) {
            $this->update(['opened_at' => now()]);
        }

        // Update campaign stats
        if ($this->campaign) {
            $this->campaign->syncStats();
        }
    }

    public function markAsBounced(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_BOUNCED,
            'bounced_at' => now(),
            'error_message' => $reason,
        ]);

        if ($this->campaign) {
            $this->campaign->syncStats();
        }
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);

        if ($this->campaign) {
            $this->campaign->syncStats();
        }
    }

    // Helpers
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            self::STATUS_SENT, self::STATUS_DELIVERED => 'success',
            self::STATUS_OPENED, self::STATUS_CLICKED => 'info',
            self::STATUS_QUEUED => 'warning',
            self::STATUS_BOUNCED, self::STATUS_FAILED => 'danger',
            default => 'gray',
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            self::TYPE_TRANSACTIONAL => 'Transaccional',
            self::TYPE_CAMPAIGN => 'Campaña',
            self::TYPE_NOTIFICATION => 'Notificación',
            default => $this->type,
        };
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            self::CATEGORY_RESERVATION => 'Reservación',
            self::CATEGORY_CLAIM => 'Reclamación',
            self::CATEGORY_ORDER => 'Pedido',
            self::CATEGORY_MARKETING => 'Marketing',
            self::CATEGORY_TEAM => 'Equipo',
            self::CATEGORY_VERIFICATION => 'Verificación',
            self::CATEGORY_REMINDER => 'Recordatorio',
            self::CATEGORY_NEWSLETTER => 'Newsletter',
            self::CATEGORY_FAMER => 'FAMER Score',
            default => $this->category ?? '-',
        };
    }

    public function getTrackingPixelUrl(): string
    {
        return url("/email/track/open/{$this->tracking_token}");
    }

    public function getTrackingClickUrl(string $originalUrl): string
    {
        return url('/email/track/click/' . $this->tracking_token . '?url=' . urlencode($originalUrl));
    }

    // Static helper to log an email
    public static function log(array $data): self
    {
        return self::create(array_merge([
            'sent_at' => now(),
            'status' => self::STATUS_SENT,
        ], $data));
    }

    // Static helper to create a campaign email log
    public static function forCampaign(EmailCampaign $campaign, array $data): self
    {
        return self::create(array_merge([
            'campaign_id' => $campaign->id,
            'type' => self::TYPE_CAMPAIGN,
            'category' => self::CATEGORY_MARKETING,
            'subject' => $campaign->subject,
            'status' => self::STATUS_QUEUED,
        ], $data));
    }
}
