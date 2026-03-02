<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class RestaurantEvent extends Model
{
    protected $fillable = [
        'restaurant_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'price',
        'capacity',
        'registered_count',
        'image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    const EVENT_TYPES = [
        'live_music' => 'Musica en Vivo',
        'special_dinner' => 'Cena Especial',
        'class' => 'Clase de Cocina',
        'tasting' => 'Degustacion',
        'holiday' => 'Evento Festivo',
        'happy_hour' => 'Happy Hour',
        'other' => 'Otro',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->orderBy('event_date')
            ->orderBy('start_time');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getAvailableSpotsAttribute(): ?int
    {
        if (!$this->capacity) return null;
        return max(0, $this->capacity - $this->registered_count);
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->capacity && $this->registered_count >= $this->capacity;
    }

    public function getEventTypeNameAttribute(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? $this->event_type;
    }
}
