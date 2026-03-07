<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckInController extends Controller
{
    /**
     * POST /v1/restaurants/{id}/check-in
     * Records a user check-in at a restaurant.
     */
    public function checkIn(Request $request, int $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::approved()->findOrFail($restaurantId);

        $request->validate([
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Prevent duplicate check-ins within 1 hour
        $recentCheckIn = CheckIn::where('user_id', $request->user()->id)
            ->where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if ($recentCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Ya hiciste check-in aquí recientemente',
            ], 422);
        }

        $checkIn = CheckIn::createCheckIn(
            $request->user()->id,
            $restaurantId,
            $request->latitude,
            $request->longitude,
        );

        return response()->json([
            'success'      => true,
            'message'      => '¡Check-in registrado! Ganaste 10 puntos',
            'data'         => [
                'id'            => $checkIn->id,
                'restaurant_id' => $restaurantId,
                'verified'      => $checkIn->verified,
                'points_earned' => $checkIn->points_earned,
                'created_at'    => $checkIn->created_at,
            ],
        ], 201);
    }

    /**
     * GET /v1/user/check-ins
     * Returns the authenticated user's check-in history.
     */
    public function userCheckIns(Request $request): JsonResponse
    {
        $checkIns = CheckIn::where('user_id', $request->user()->id)
            ->with(['restaurant:id,name,slug,image,city'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $checkIns->items(),
            'meta'    => [
                'current_page' => $checkIns->currentPage(),
                'last_page'    => $checkIns->lastPage(),
                'total'        => $checkIns->total(),
            ],
        ]);
    }

    /**
     * GET /v1/restaurants/{id}/check-ins/count
     * Returns the total check-in count for a restaurant.
     */
    public function count(Request $request, int $restaurantId): JsonResponse
    {
        $count = CheckIn::where('restaurant_id', $restaurantId)->count();

        return response()->json([
            'success' => true,
            'data'    => ['count' => $count],
        ]);
    }
}
