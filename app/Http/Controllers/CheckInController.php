<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function store(Request $request, Restaurant $restaurant)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Debes iniciar sesion'], 401);
        }

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Check if user already checked in today
        $existingCheckIn = CheckIn::where('user_id', Auth::id())
            ->where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'error' => 'Ya hiciste check-in hoy en este restaurante',
                'check_in' => $existingCheckIn
            ], 422);
        }

        $checkIn = CheckIn::createCheckIn(
            Auth::id(),
            $restaurant->id,
            $validated['latitude'] ?? null,
            $validated['longitude'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => $checkIn->verified 
                ? 'Check-in verificado! +10 puntos' 
                : 'Check-in registrado! +10 puntos',
            'check_in' => $checkIn,
            'points_earned' => 10,
        ]);
    }

    public function userCheckIns()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $checkIns = CheckIn::where('user_id', Auth::id())
            ->with('restaurant:id,name,slug,main_image')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($checkIns);
    }
}
