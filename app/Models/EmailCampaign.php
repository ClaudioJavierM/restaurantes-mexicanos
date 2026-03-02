<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'preview_text',
        'type',
        'content',
        'variables',
        'audience_filter',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'bounced_count',
        'unsubscribed_count',
        'failed_count',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'variables' => 'array',
        'audience_filter' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Campaign types
    const TYPE_INVITATION = 'invitation';
    const TYPE_FOLLOW_UP = 'follow_up';
    const TYPE_STATS = 'stats';
    const TYPE_URGENCY = 'urgency';
    const TYPE_WELCOME = 'welcome';
    const TYPE_UPGRADE = 'upgrade';
    const TYPE_NEWSLETTER = 'newsletter';
    const TYPE_PROMO = 'promo';
    const TYPE_FAMER_REPORT = 'famer_report';

    // Campaign statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';

    public static function getTypes(): array
    {
        return [
            self::TYPE_INVITATION => 'Invitación',
            self::TYPE_FOLLOW_UP => 'Seguimiento',
            self::TYPE_STATS => 'Estadísticas',
            self::TYPE_URGENCY => 'Urgencia',
            self::TYPE_WELCOME => 'Bienvenida',
            self::TYPE_UPGRADE => 'Upgrade',
            self::TYPE_NEWSLETTER => 'Newsletter',
            self::TYPE_PROMO => 'Promoción',
            self::TYPE_FAMER_REPORT => 'FAMER Report',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Borrador',
            self::STATUS_SCHEDULED => 'Programada',
            self::STATUS_SENDING => 'Enviando',
            self::STATUS_SENT => 'Enviada',
            self::STATUS_PAUSED => 'Pausada',
            self::STATUS_CANCELLED => 'Cancelada',
        ];
    }

    // Relationships
    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Statistics Accessors
    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function getClickToOpenRateAttribute(): float
    {
        if ($this->opened_count === 0) return 0;
        return round(($this->clicked_count / $this->opened_count) * 100, 2);
    }

    public function getBounceRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->bounced_count / $this->sent_count) * 100, 2);
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->delivered_count / $this->sent_count) * 100, 2);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_recipients === 0) return 0;
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    // Status helpers
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_SCHEDULED => 'info',
            self::STATUS_SENDING => 'warning',
            self::STATUS_SENT => 'success',
            self::STATUS_PAUSED => 'warning',
            self::STATUS_CANCELLED => 'danger',
            default => 'gray',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INVITATION => 'primary',
            self::TYPE_FOLLOW_UP => 'info',
            self::TYPE_WELCOME => 'success',
            self::TYPE_UPGRADE => 'warning',
            self::TYPE_PROMO => 'danger',
            self::TYPE_NEWSLETTER => 'gray',
            default => 'gray',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_SENDING]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }

    // Actions
    public function start(): void
    {
        $this->update([
            'status' => self::STATUS_SENDING,
            'started_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'completed_at' => now(),
        ]);
    }

    public function pause(): void
    {
        $this->update(['status' => self::STATUS_PAUSED]);
    }

    public function resume(): void
    {
        $this->update(['status' => self::STATUS_SENDING]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function schedule(\DateTime $at): void
    {
        $this->update([
            'status' => self::STATUS_SCHEDULED,
            'scheduled_at' => $at,
        ]);
    }

    // Update stats from logs
    public function syncStats(): void
    {
        $stats = $this->emailLogs()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status != "queued" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status IN ("delivered", "opened", "clicked") THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status IN ("opened", "clicked") THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN status = "clicked" THEN 1 ELSE 0 END) as clicked,
                SUM(CASE WHEN status = "bounced" THEN 1 ELSE 0 END) as bounced,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
            ->first();

        $this->update([
            'total_recipients' => $stats->total ?? 0,
            'sent_count' => $stats->sent ?? 0,
            'delivered_count' => $stats->delivered ?? 0,
            'opened_count' => $stats->opened ?? 0,
            'clicked_count' => $stats->clicked ?? 0,
            'bounced_count' => $stats->bounced ?? 0,
            'failed_count' => $stats->failed ?? 0,
        ]);
    }

    // Get available merge tags
    public static function getAvailableMergeTags(): array
    {
        return [
            '{{restaurant_name}}' => 'Nombre del restaurante',
            '{{owner_name}}' => 'Nombre del dueño',
            '{{owner_email}}' => 'Email del dueño',
            '{{restaurant_city}}' => 'Ciudad del restaurante',
            '{{restaurant_state}}' => 'Estado del restaurante',
            '{{famer_score}}' => 'FAMER Score',
            '{{famer_grade}}' => 'FAMER Grade (letra)',
            '{{claim_url}}' => 'URL para reclamar',
            '{{dashboard_url}}' => 'URL del dashboard',
            '{{unsubscribe_url}}' => 'URL para desuscribirse',
        ];
    }

    // Replace merge tags in content
    public function renderContent(array $data): string
    {
        $content = $this->content;
        
        foreach ($data as $key => $value) {
            $content = str_replace("{{$key}}", $value ?? '', $content);
        }
        
        return $content;
    }
}
