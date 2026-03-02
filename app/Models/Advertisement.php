<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Advertisement extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'link_url',
        'button_text',
        'placement',
        'state_id',
        'is_active',
        'display_order',
        'starts_at',
        'ends_at',
        'clicks_count',
        'views_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    // Placements disponibles
    public static function getPlacements(): array
    {
        return [
            'sidebar' => 'Barra lateral (restaurante)',
            'footer' => 'Pie de página',
            'header' => 'Encabezado',
        ];
    }

    // Relaciones
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeForPlacement($query, string $placement)
    {
        return $query->where('placement', $placement);
    }

    public function scopeForState($query, ?int $stateId)
    {
        return $query->where(function ($q) use ($stateId) {
            $q->whereNull('state_id')
                ->orWhere('state_id', $stateId);
        });
    }

    // Métodos útiles
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }
}
