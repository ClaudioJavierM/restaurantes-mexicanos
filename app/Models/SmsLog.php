<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'restaurant_id',
        'sms_automation_id',
        'restaurant_customer_id',
        'phone',
        'message',
        'type',
        'trigger_type',
        'status',
        'twilio_sid',
        'error_message',
        'short_url',
        'sent_at',
        'delivered_at',
        'clicked_at',
        'cost',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'clicked_at' => 'datetime',
        'cost' => 'decimal:4',
        'metadata' => 'array',
    ];

    // Types
    const TYPE_AUTOMATION = 'automation';
    const TYPE_MANUAL = 'manual';
    const TYPE_TRANSACTIONAL = 'transactional';

    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED = 'failed';
    const STATUS_CLICKED = 'clicked';

    // Relationships
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(SmsAutomation::class, 'sms_automation_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(RestaurantCustomer::class, 'restaurant_customer_id');
    }

    // Scopes
    public function scopeSent($query)
    {
        return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_DELIVERED, self::STATUS_CLICKED]);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTrigger($query, string $trigger)
    {
        return $query->where('trigger_type', $trigger);
    }

    // Helpers
    public function markAsSent(string $twilioSid): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'twilio_sid' => $twilioSid,
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }

    public function markAsClicked(): void
    {
        $this->update([
            'status' => self::STATUS_CLICKED,
            'clicked_at' => now(),
        ]);

        // Update automation stats
        if ($this->automation) {
            $this->automation->incrementClicks();
        }
    }
}
