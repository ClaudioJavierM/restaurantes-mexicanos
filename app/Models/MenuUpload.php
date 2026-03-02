<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuUpload extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'file_path',
        'file_type',
        'original_name',
        'status',
        'ocr_raw_text',
        'ai_extracted_data',
        'error_message',
        'items_extracted',
        'items_approved',
        'processed_at',
        'approved_at',
    ];

    protected $casts = [
        'ai_extracted_data' => 'array',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function needsReview(): bool
    {
        return $this->status === 'needs_review';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '⏳ Pendiente',
            'processing' => '🔄 Procesando',
            'completed' => '✅ Completado',
            'failed' => '❌ Error',
            'needs_review' => '👀 Revisar',
            default => $this->status,
        };
    }

    public function getFileTypeIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => '📄',
            'image' => '🖼️',
            'url' => '🔗',
            default => '📎',
        };
    }
}
