<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Reservation;

class MyReservations extends Component
{
    use WithPagination;

    public function cancelReservation($reservationId)
    {
        $reservation = auth()->user()->reservations()->findOrFail($reservationId);

        if (! in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])) {
            session()->flash('error', 'Esta reservación no puede cancelarse.');
            return;
        }

        $reservation->cancel('Cancelado por el cliente');

        session()->flash('success', 'Tu reservación ha sido cancelada.');
    }

    public function render()
    {
        $reservations = auth()->user()
            ->reservations()
            ->with('restaurant')
            ->orderByRaw("CASE WHEN status IN ('pending','confirmed') AND reservation_date >= CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('reservation_date', 'asc')
            ->paginate(12);

        return view('livewire.my-reservations', [
            'reservations' => $reservations,
        ])->layout('layouts.app', ['title' => 'Mis Reservaciones']);
    }
}
