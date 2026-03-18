<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantWaitlist extends Model
{
    protected $fillable = [
        'restaurant_id', 'user_id', 'name', 'phone', 'email',
        'party_size', 'special_request', 'status', 'position',
        'estimated_wait_minutes', 'called_at', 'seated_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'seated_at' => 'datetime',
    ];

    public static array $statusLabels = [
        'waiting'   => 'Esperando',
        'called'    => 'Llamado',
        'seated'    => 'Sentado',
        'no_show'   => 'No se presentó',
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

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'called']);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    // Recalculate positions for a restaurant's active queue
    public static function recalculatePositions(int $restaurantId): void
    {
        $entries = self::where('restaurant_id', $restaurantId)
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->get();

        foreach ($entries as $index => $entry) {
            $entry->update(['position' => $index + 1]);
        }
    }
}
