<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticityBadgeRequest extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'badge_id',
        'status',
        'evidence',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ──────────────────────────────────────────────
    // Badge catalog — single source of truth
    // ──────────────────────────────────────────────
    public static array $catalog = [
        'family_owned' => [
            'id'          => 'family_owned',
            'icon'        => '👨‍👩‍👧',
            'name'        => 'Negocio Familiar',
            'name_en'     => 'Family Business',
            'description' => 'Restaurante de segunda o tercera generación',
            'color'       => 'amber',
        ],
        'regional_recipe' => [
            'id'          => 'regional_recipe',
            'icon'        => '🏔️',
            'name'        => 'Receta Regional',
            'name_en'     => 'Regional Recipe',
            'description' => 'Receta auténtica de una región mexicana',
            'color'       => 'orange',
        ],
        'imported_ingredients' => [
            'id'          => 'imported_ingredients',
            'icon'        => '🌶️',
            'name'        => 'Ingredientes Importados',
            'name_en'     => 'Imported Ingredients',
            'description' => 'Usa ingredientes importados directamente de México',
            'color'       => 'red',
        ],
        'mexican_chef' => [
            'id'          => 'mexican_chef',
            'icon'        => '🇲🇽',
            'name'        => 'Chef Mexicano',
            'name_en'     => 'Mexican Chef',
            'description' => 'El chef principal es originario de México',
            'color'       => 'green',
        ],
        'over_10_years' => [
            'id'          => 'over_10_years',
            'icon'        => '🥇',
            'name'        => 'Más de 10 Años',
            'name_en'     => 'Over 10 Years',
            'description' => 'El restaurante lleva más de 10 años en operación',
            'color'       => 'yellow',
        ],
        'no_preservatives' => [
            'id'          => 'no_preservatives',
            'icon'        => '🌿',
            'name'        => 'Sin Conservadores',
            'name_en'     => 'No Preservatives',
            'description' => 'Cocina sin conservadores artificiales',
            'color'       => 'emerald',
        ],
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────
    public function getBadgeDefinition(): ?array
    {
        return self::$catalog[$this->badge_id] ?? null;
    }
}
