<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'guests',
        'status',
        'notes',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(RestaurantEvent::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function register(int $eventId, int $userId, int $guests = 1, ?string $notes = null): ?self
    {
        $event = RestaurantEvent::find($eventId);
        
        if (!$event || $event->is_sold_out) {
            return null;
        }

        $registration = self::create([
            'event_id' => $eventId,
            'user_id' => $userId,
            'guests' => $guests,
            'status' => 'confirmed',
            'notes' => $notes,
        ]);

        $event->increment('registered_count', $guests);

        // Award points for registering
        LoyaltyPoints::addPoints($userId, 15, 'event_registration', 'Registro a evento', $registration);

        return $registration;
    }

    public function cancel(): void
    {
        if ($this->status === 'cancelled') return;

        $this->event->decrement('registered_count', $this->guests);
        $this->update(['status' => 'cancelled']);
    }
}
