<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantReport extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'email',
        'issue_type',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Tipos de problemas disponibles
    public static function getIssueTypes(): array
    {
        return [
            'incorrect_info' => 'Información incorrecta',
            'closed' => 'Restaurante cerrado',
            'wrong_location' => 'Ubicación incorrecta',
            'wrong_phone' => 'Teléfono incorrecto',
            'wrong_hours' => 'Horario incorrecto',
            'duplicate' => 'Duplicado',
            'other' => 'Otro',
        ];
    }

    // Estados disponibles
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pendiente',
            'reviewed' => 'Revisado',
            'resolved' => 'Resuelto',
        ];
    }

    // Relaciones
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }
}
