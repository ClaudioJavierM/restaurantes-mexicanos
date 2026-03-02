<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Get available time slots for a restaurant
     */
    public function availableSlots(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$restaurant->accepts_reservations) {
            return response()->json([
                'success' => false,
                'message' => 'Este restaurante no acepta reservaciones',
            ], 400);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'party_size' => 'required|integer|min:1|max:20',
        ]);

        $date = Carbon::parse($request->date);
        $partySize = $request->party_size;

        // Get restaurant hours for the day
        $dayOfWeek = strtolower($date->format('l'));
        $hours = $restaurant->reservation_hours[$dayOfWeek] ?? $restaurant->hours[$dayOfWeek] ?? null;

        if (!$hours || ($hours['closed'] ?? false)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'El restaurante está cerrado este día',
            ]);
        }

        // Generate time slots (every 30 minutes)
        $slots = [];
        $openTime = Carbon::parse($hours['open'] ?? '11:00');
        $closeTime = Carbon::parse($hours['close'] ?? '21:00')->subHours(1); // Last reservation 1hr before close

        $existingReservations = Reservation::where('restaurant_id', $restaurantId)
            ->where('reservation_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $capacityPerSlot = $restaurant->reservation_capacity_per_slot ?? 20;

        while ($openTime <= $closeTime) {
            $timeSlot = $openTime->format('H:i');
            
            // Count existing reservations for this slot
            $reservedCount = $existingReservations
                ->where('reservation_time', $timeSlot)
                ->sum('party_size');

            $availableCapacity = $capacityPerSlot - $reservedCount;

            if ($availableCapacity >= $partySize) {
                $slots[] = [
                    'time' => $timeSlot,
                    'time_formatted' => $openTime->format('g:i A'),
                    'available' => true,
                    'remaining_capacity' => $availableCapacity,
                ];
            }

            $openTime->addMinutes(30);
        }

        return response()->json([
            'success' => true,
            'data' => $slots,
            'meta' => [
                'date' => $date->toDateString(),
                'party_size' => $partySize,
                'restaurant' => $restaurant->only(['id', 'name']),
            ]
        ]);
    }

    /**
     * Create a reservation
     */
    public function store(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$restaurant->accepts_reservations) {
            return response()->json([
                'success' => false,
                'message' => 'Este restaurante no acepta reservaciones',
            ], 400);
        }

        $validated = $request->validate([
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|date_format:H:i',
            'party_size' => 'required|integer|min:1|max:20',
            'guest_name' => 'required|string|max:100',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'required|email',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Check availability
        $existingCount = Reservation::where('restaurant_id', $restaurantId)
            ->where('reservation_date', $validated['reservation_date'])
            ->where('reservation_time', $validated['reservation_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('party_size');

        $capacityPerSlot = $restaurant->reservation_capacity_per_slot ?? 20;

        if (($existingCount + $validated['party_size']) > $capacityPerSlot) {
            return response()->json([
                'success' => false,
                'message' => 'No hay disponibilidad para este horario',
            ], 400);
        }

        // Generate confirmation code
        $confirmationCode = strtoupper(substr($restaurant->slug, 0, 3)) . '-' . now()->format('md') . '-' . rand(1000, 9999);

        $reservation = Reservation::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $request->user()->id,
            'confirmation_code' => $confirmationCode,
            'reservation_date' => $validated['reservation_date'],
            'reservation_time' => $validated['reservation_time'],
            'party_size' => $validated['party_size'],
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => $restaurant->reservation_type === 'instant' ? 'confirmed' : 'pending',
        ]);

        // Send notification to restaurant owner
        try {
            $notificationService = app(\App\Services\ReservationNotificationService::class);
            $notificationService->notifyRestaurantNewReservation($reservation);
        } catch (\Exception $e) {
            \Log::error('Failed to send reservation notification: ' . $e->getMessage());
        }

        // Send pending confirmation email to customer
        try {
            $notificationService->notifyCustomerPending($reservation);
        } catch (\Exception $e) {
            \Log::error('Failed to send customer pending notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $reservation->status === 'confirmed' 
                ? 'Reservación confirmada' 
                : 'Reservación enviada, pendiente de confirmación',
            'data' => $reservation->load('restaurant:id,name,address,phone'),
        ], 201);
    }

    /**
     * Get reservation details
     */
    public function show(Request $request, $reservationId): JsonResponse
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $request->user()->id)
            ->with(['restaurant:id,name,slug,address,city,phone,image,latitude,longitude'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, $reservationId): JsonResponse
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        // Check if cancellation is allowed (e.g., 2 hours before)
        $reservationDateTime = Carbon::parse($reservation->reservation_date . ' ' . $reservation->reservation_time);
        
        if ($reservationDateTime->diffInHours(now()) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes cancelar con menos de 2 horas de anticipación',
            ], 400);
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Cancelado por el cliente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservación cancelada exitosamente',
        ]);
    }

    /**
     * Modify a reservation
     */
    public function update(Request $request, $reservationId): JsonResponse
    {
        $reservation = Reservation::where('id', $reservationId)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->firstOrFail();

        $validated = $request->validate([
            'reservation_date' => 'sometimes|date|after_or_equal:today',
            'reservation_time' => 'sometimes|date_format:H:i',
            'party_size' => 'sometimes|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // If date/time changed, check availability
        if (isset($validated['reservation_date']) || isset($validated['reservation_time'])) {
            $newDate = $validated['reservation_date'] ?? $reservation->reservation_date;
            $newTime = $validated['reservation_time'] ?? $reservation->reservation_time;
            $partySize = $validated['party_size'] ?? $reservation->party_size;

            $existingCount = Reservation::where('restaurant_id', $reservation->restaurant_id)
                ->where('reservation_date', $newDate)
                ->where('reservation_time', $newTime)
                ->where('id', '!=', $reservationId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->sum('party_size');

            $capacityPerSlot = $reservation->restaurant->reservation_capacity_per_slot ?? 20;

            if (($existingCount + $partySize) > $capacityPerSlot) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay disponibilidad para el nuevo horario',
                ], 400);
            }

            // Reset to pending if date/time changed
            $validated['status'] = 'pending';
        }

        $reservation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reservación modificada exitosamente',
            'data' => $reservation->fresh()->load('restaurant:id,name,address,phone'),
        ]);
    }
}
