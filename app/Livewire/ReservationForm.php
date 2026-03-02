<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Reservation;
use App\Services\ReservationNotificationService;
use Livewire\Component;

class ReservationForm extends Component
{
    public Restaurant $restaurant;
    public $showForm = false;
    public $showConfirmation = false;
    public $confirmationCode = null;

    // Form fields
    public $reservationDate;
    public $reservationTime;
    public $partySize = 2;
    public $occasion = 'none';
    public $specialRequests = '';
    public $guestName = '';
    public $guestEmail = '';
    public $guestPhone = '';

    protected function rules()
    {
        return [
            'reservationDate' => 'required|date|after_or_equal:today',
            'reservationTime' => 'required|string',
            'partySize' => 'required|integer|min:1|max:20',
            'occasion' => 'nullable|string',
            'specialRequests' => 'nullable|string|max:500',
            'guestName' => auth()->check() ? 'nullable' : 'required|string|max:100',
            'guestEmail' => auth()->check() ? 'nullable' : 'required|email|max:255',
            'guestPhone' => 'required|string|max:20',
        ];
    }

    protected $messages = [
        'reservationDate.required' => 'Por favor selecciona una fecha.',
        'reservationDate.after_or_equal' => 'La fecha debe ser hoy o en el futuro.',
        'reservationTime.required' => 'Por favor selecciona una hora.',
        'partySize.required' => 'Por favor indica el número de personas.',
        'partySize.min' => 'Debe haber al menos 1 persona.',
        'partySize.max' => 'Para grupos mayores de 20, contacta al restaurante directamente.',
        'guestName.required' => 'Por favor ingresa tu nombre.',
        'guestEmail.required' => 'Por favor ingresa tu email.',
        'guestPhone.required' => 'Por favor ingresa tu teléfono.',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->reservationDate = now()->addDay()->format('Y-m-d');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->showConfirmation = false;
    }

    public function getAvailableTimesProperty()
    {
        // Get time slot interval from restaurant settings, default 30 minutes
        $interval = $this->restaurant->getReservationSetting('time_slot_interval', 30);

        // Generate time slots from 11:00 to 22:00
        $times = [];
        $start = 11 * 60; // 11:00 in minutes
        $end = 22 * 60;   // 22:00 in minutes

        for ($time = $start; $time <= $end; $time += $interval) {
            $hours = floor($time / 60);
            $minutes = $time % 60;
            $formatted = sprintf('%02d:%02d', $hours, $minutes);
            $display = sprintf('%d:%02d %s',
                $hours > 12 ? $hours - 12 : $hours,
                $minutes,
                $hours >= 12 ? 'PM' : 'AM'
            );
            $times[$formatted] = $display;
        }

        return $times;
    }

    public function getReservationTypeProperty()
    {
        return $this->restaurant->reservation_type ?? 'none';
    }

    public function getExternalUrlProperty()
    {
        return $this->restaurant->reservation_external_url;
    }

    public function getPlatformNameProperty()
    {
        $platforms = \App\Models\Restaurant::getReservationPlatforms();
        return $platforms[$this->restaurant->reservation_platform] ?? 'Reservaciones';
    }

    public function getPartySizesProperty()
    {
        $sizes = [];
        for ($i = 1; $i <= 20; $i++) {
            $sizes[$i] = $i . ($i === 1 ? ' persona' : ' personas');
        }
        return $sizes;
    }

    public function submitReservation()
    {
        $this->validate();

        $reservation = Reservation::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : $this->guestName,
            'guest_email' => auth()->check() ? null : $this->guestEmail,
            'guest_phone' => $this->guestPhone,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'party_size' => $this->partySize,
            'occasion' => $this->occasion,
            'special_requests' => $this->specialRequests,
            'status' => Reservation::STATUS_PENDING,
            'ip_address' => request()->ip(),
        ]);

        // Send notification to restaurant
        try {
            $notificationService = app(ReservationNotificationService::class);
            $notificationService->notifyRestaurantNewReservation($reservation);
        } catch (\Exception $e) {
            \Log::error('Failed to send reservation notification: ' . $e->getMessage());
        }

        // Send pending confirmation email to customer
        try {
            $notificationService = app(ReservationNotificationService::class);
            $notificationService->notifyCustomerPending($reservation);
        } catch (\Exception $e) {
            \Log::error('Failed to send customer pending notification: ' . $e->getMessage());
        }

        $this->confirmationCode = $reservation->confirmation_code;
        $this->showConfirmation = true;
        $this->showForm = false;

        // Reset form
        $this->reset(['reservationTime', 'partySize', 'occasion', 'specialRequests', 'guestName', 'guestEmail', 'guestPhone']);
        $this->partySize = 2;
        $this->reservationDate = now()->addDay()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reservation-form', [
            'availableTimes' => $this->availableTimes,
            'partySizes' => $this->partySizes,
            'occasions' => Reservation::getOccasions(),
        ]);
    }
}
