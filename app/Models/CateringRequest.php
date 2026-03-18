<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CateringRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'contact_name',
        'contact_email',
        'contact_phone',
        'event_date',
        'guest_count',
        'event_type',
        'event_location',
        'notes',
        'budget',
        'status',
        'owner_notes',
        'quote_amount',
        'responded_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'budget' => 'decimal:2',
        'quote_amount' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    public static array $eventTypes = [
        'boda' => 'Boda',
        'cumpleanos' => 'Cumpleaños',
        'corporativo' => 'Evento Corporativo',
        'graduacion' => 'Graduación',
        'bautizo' => 'Bautizo / Primera Comunión',
        'xv_anos' => 'XV Años',
        'otro' => 'Otro',
    ];

    public static array $statusLabels = [
        'pending' => 'Pendiente',
        'viewed' => 'Visto',
        'quoted' => 'Cotizado',
        'accepted' => 'Aceptado',
        'declined' => 'Declinado',
        'cancelled' => 'Cancelado',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? ucfirst($this->status);
    }

    public function getEventTypeLabelAttribute(): string
    {
        return self::$eventTypes[$this->event_type] ?? ucfirst($this->event_type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'viewed', 'quoted']);
    }
}
