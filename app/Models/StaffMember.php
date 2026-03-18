<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'role', 'phone', 'email',
        'hourly_rate', 'notes', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    public static array $roles = [
        'manager'    => 'Gerente',
        'cook'       => 'Cocinero',
        'server'     => 'Mesero',
        'host'       => 'Hostess',
        'barista'    => 'Barista',
        'dishwasher' => 'Lavaplatos',
        'other'      => 'Otro',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(StaffShift::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return static::$roles[$this->role] ?? ucfirst($this->role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
