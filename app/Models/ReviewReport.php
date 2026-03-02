<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'reason',
        'description',
        'status',
        'resolved_at',
        'resolved_by',
        'ip_address',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Report reasons
    const REASON_SPAM = 'spam';
    const REASON_INAPPROPRIATE = 'inappropriate';
    const REASON_FAKE = 'fake';
    const REASON_OFFENSIVE = 'offensive';
    const REASON_OTHER = 'other';

    // Report statuses
    const STATUS_PENDING = 'pending';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', self::STATUS_DISMISSED);
    }

    // Resolve report
    public function resolve(?int $resolvedBy = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy ?? auth()->id(),
        ]);
    }

    // Dismiss report
    public function dismiss(?int $dismissedBy = null)
    {
        $this->update([
            'status' => self::STATUS_DISMISSED,
            'resolved_at' => now(),
            'resolved_by' => $dismissedBy ?? auth()->id(),
        ]);
    }

    // Get reason label
    public function getReasonLabel(): string
    {
        return match($this->reason) {
            self::REASON_SPAM => app()->getLocale() === 'en' ? 'Spam' : 'Spam',
            self::REASON_INAPPROPRIATE => app()->getLocale() === 'en' ? 'Inappropriate Content' : 'Contenido Inapropiado',
            self::REASON_FAKE => app()->getLocale() === 'en' ? 'Fake Review' : 'Reseña Falsa',
            self::REASON_OFFENSIVE => app()->getLocale() === 'en' ? 'Offensive' : 'Ofensivo',
            default => app()->getLocale() === 'en' ? 'Other' : 'Otro',
        };
    }
}
