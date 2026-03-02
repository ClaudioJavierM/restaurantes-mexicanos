<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OwnerCampaignSend extends Model
{
    protected $fillable = [
        'campaign_id',
        'customer_id',
        'tracking_token',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'open_count',
        'click_count',
        'error_message',
        'message_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($send) {
            if (empty($send->tracking_token)) {
                $send->tracking_token = Str::random(64);
            }
        });
    }

    // Relationships
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OwnerCampaign::class, 'campaign_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(RestaurantCustomer::class, 'customer_id');
    }

    // Scopes
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->whereNotNull('opened_at');
    }

    public function scopeClicked($query)
    {
        return $query->whereNotNull('clicked_at');
    }

    public function scopeBounced($query)
    {
        return $query->where('status', 'bounced');
    }

    // Track events
    public function markSent(string $messageId = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'message_id' => $messageId,
        ]);
    }

    public function markOpened(): void
    {
        if (!$this->opened_at) {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);
            $this->campaign->increment('opened_count');
        }
        $this->increment('open_count');
    }

    public function markClicked(): void
    {
        if (!$this->clicked_at) {
            $this->update([
                'status' => 'clicked',
                'clicked_at' => now(),
            ]);
            $this->campaign->increment('clicked_count');
        }
        $this->increment('click_count');

        // Also mark as opened if not already
        if (!$this->opened_at) {
            $this->markOpened();
        }
    }

    public function markBounced(string $message = null): void
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $message,
        ]);
        $this->campaign->increment('bounced_count');
    }

    public function markFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }

    // URLs
    public function getTrackingPixelUrl(): string
    {
        return route('owner-email.track.open', ['token' => $this->tracking_token]);
    }

    public function getClickTrackingUrl(string $url): string
    {
        return route('owner-email.track.click', [
            'token' => $this->tracking_token,
            'url' => base64_encode($url),
        ]);
    }

    public function getUnsubscribeUrl(): string
    {
        return route('owner-email.unsubscribe', ['token' => $this->tracking_token]);
    }
}
