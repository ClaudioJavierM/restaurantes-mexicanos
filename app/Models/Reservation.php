<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        // Guest info (for non-authenticated users)
        'guest_name',
        'guest_email',
        'guest_phone',
        // Reservation details
        'reservation_date',
        'reservation_time',
        'party_size',
        'special_requests',
        'occasion',
        // Status
        'status',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        // Tracking
        'confirmation_code',
        'reminder_sent_at',
        'ip_address',
        // Owner notes
        'internal_notes',
        'table_assigned',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NO_SHOW = 'no_show';

    // Occasions
    const OCCASION_NONE = 'none';
    const OCCASION_BIRTHDAY = 'birthday';
    const OCCASION_ANNIVERSARY = 'anniversary';
    const OCCASION_DATE = 'date';
    const OCCASION_BUSINESS = 'business';
    const OCCASION_CELEBRATION = 'celebration';
    const OCCASION_OTHER = 'other';

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('reservation_date', $date);
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    // Actions
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        // Send confirmation notification to customer
        try {
            $notificationService = app(\App\Services\ReservationNotificationService::class);
            $notificationService->notifyCustomerConfirmation($this);
        } catch (\Exception $e) {
            \Log::error('Failed to send confirmation notification: ' . $e->getMessage());
        }
    }

    public function cancel(?string $reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // Send cancellation notification to customer
        try {
            $notificationService = app(\App\Services\ReservationNotificationService::class);
            $notificationService->notifyCustomerCancellation($this, $reason);
        } catch (\Exception $e) {
            \Log::error('Failed to send cancellation notification: ' . $e->getMessage());
        }
    }

    public function complete()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    public function markNoShow()
    {
        $this->update([
            'status' => self::STATUS_NO_SHOW,
        ]);
    }

    // Helpers
    public function getContactName(): string
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    public function getContactEmail(): string
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    public function getContactPhone(): ?string
    {
        return $this->user?->phone ?? $this->guest_phone;
    }

    public function getFormattedDateTime(): string
    {
        return $this->reservation_date->format('d/m/Y') . ' a las ' . $this->reservation_time;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_NO_SHOW => 'No se presentó',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_NO_SHOW => 'gray',
            default => 'gray',
        };
    }

    public function getOccasionLabel(): string
    {
        return match($this->occasion) {
            self::OCCASION_BIRTHDAY => 'Cumpleaños',
            self::OCCASION_ANNIVERSARY => 'Aniversario',
            self::OCCASION_DATE => 'Cita romántica',
            self::OCCASION_BUSINESS => 'Reunión de negocios',
            self::OCCASION_CELEBRATION => 'Celebración',
            self::OCCASION_OTHER => 'Otro',
            default => 'Ninguna ocasión especial',
        };
    }

    public static function getOccasions(): array
    {
        return [
            self::OCCASION_NONE => 'Ninguna ocasión especial',
            self::OCCASION_BIRTHDAY => 'Cumpleaños',
            self::OCCASION_ANNIVERSARY => 'Aniversario',
            self::OCCASION_DATE => 'Cita romántica',
            self::OCCASION_BUSINESS => 'Reunión de negocios',
            self::OCCASION_CELEBRATION => 'Celebración',
            self::OCCASION_OTHER => 'Otro',
        ];
    }

    // Generate unique confirmation code
    public static function generateConfirmationCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('confirmation_code', $code)->exists());

        return $code;
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->confirmation_code)) {
                $reservation->confirmation_code = self::generateConfirmationCode();
            }
        });
    }
}
