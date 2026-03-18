<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffShift extends Model
{
    protected $fillable = [
        'restaurant_id', 'staff_member_id', 'shift_date',
        'start_time', 'end_time', 'notes', 'status',
    ];

    protected $casts = [
        'shift_date' => 'date',
    ];

    public static array $statusLabels = [
        'scheduled' => 'Programado',
        'completed' => 'Completado',
        'absent'    => 'Ausente',
        'cancelled' => 'Cancelado',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function getDurationHoursAttribute(): float
    {
        [$sh, $sm] = explode(':', $this->start_time);
        [$eh, $em] = explode(':', $this->end_time);
        $minutes = ($eh * 60 + $em) - ($sh * 60 + $sm);
        return round($minutes / 60, 1);
    }
}
