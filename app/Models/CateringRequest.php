<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringRequest extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'event_type',
        'event_date',
        'guest_count',
        'event_location',
        'budget_range',
        'message',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guest_count' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──────────────────────────────────────────────────

    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {
            'boda'        => 'Boda',
            'quinceañera' => 'Quinceañera',
            'corporativo' => 'Evento Corporativo',
            'cumpleaños'  => 'Cumpleaños',
            'graduacion'  => 'Graduación',
            default       => 'Otro evento',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Pendiente',
            'contacted' => 'Contactado',
            'quoted'    => 'Cotizado',
            'booked'    => 'Reservado',
            'declined'  => 'Declinado',
            default     => 'Pendiente',
        };
    }
}
