<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_photo_id',
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
    const REASON_INAPPROPRIATE = 'inappropriate';
    const REASON_NOT_RESTAURANT = 'not_restaurant';
    const REASON_DUPLICATE = 'duplicate';
    const REASON_SPAM = 'spam';
    const REASON_OTHER = 'other';

    // Report statuses
    const STATUS_PENDING = 'pending';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    // Relationships
    public function photo()
    {
        return $this->belongsTo(UserPhoto::class, 'user_photo_id');
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
            self::REASON_INAPPROPRIATE => 'Contenido Inapropiado',
            self::REASON_NOT_RESTAURANT => 'No es del Restaurante',
            self::REASON_DUPLICATE => 'Duplicado',
            self::REASON_SPAM => 'Spam',
            default => 'Otro',
        };
    }
}
