<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_feature');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function getCategories(): array
    {
        return [
            'service' => 'Servicio',
            'reservations' => 'Reservaciones',
            'ambiance' => 'Ambiente',
            'ideal_for' => 'Ideal Para',
            'facilities' => 'Facilidades',
            'dietary' => 'Opciones Dietéticas',
        ];
    }
}
