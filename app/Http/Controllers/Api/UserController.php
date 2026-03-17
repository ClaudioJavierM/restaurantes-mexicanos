<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'preferences' => $user->preferences ?? [],
                'favorites_count' => $user->favorites()->count(),
                'reviews_count' => $user->reviews()->count(),
                'reservations_count' => $user->reservations()->count(),
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'preferences' => 'nullable|array',
            'preferences.notifications' => 'nullable|boolean',
            'preferences.language' => 'nullable|in:es,en',
            'preferences.distance_unit' => 'nullable|in:miles,km',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }

    /**
     * Get user favorites
     */
    public function favorites(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favoriteRestaurants()
            ->with(['state:id,name,abbreviation', 'category:id,name,slug'])
            ->select([
                'restaurants.id', 'name', 'slug', 'address', 'city',
                'average_rating', 'total_reviews', 'price_range', 'image',
                'latitude', 'longitude', 'state_id', 'category_id'
            ])
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $favorites->items(),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'total' => $favorites->total(),
            ]
        ]);
    }

    /**
     * Add restaurant to favorites
     */
    public function addFavorite(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $user = $request->user();

        if ($user->favorites()->where('restaurant_id', $restaurantId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El restaurante ya está en favoritos',
            ], 400);
        }

        $user->favorites()->attach($restaurantId);

        // Update fan score
        $fanScore = \App\Models\FanScore::getOrCreate($user->id, $restaurantId);
        $fanScore->addAction('favorite');

        return response()->json([
            'success' => true,
            'message' => 'Restaurante agregado a favoritos',
            'data' => [
                'restaurant_id' => $restaurantId,
                'favorites_count' => $user->favorites()->count(),
            ]
        ]);
    }

    /**
     * Remove restaurant from favorites
     */
    public function removeFavorite(Request $request, $restaurantId): JsonResponse
    {
        $user = $request->user();
        $user->favorites()->detach($restaurantId);

        return response()->json([
            'success' => true,
            'message' => 'Restaurante eliminado de favoritos',
            'data' => [
                'restaurant_id' => $restaurantId,
                'favorites_count' => $user->favorites()->count(),
            ]
        ]);
    }

    /**
     * Check if restaurant is in favorites
     */
    public function isFavorite(Request $request, $restaurantId): JsonResponse
    {
        $isFavorite = $request->user()
            ->favorites()
            ->where('restaurant_id', $restaurantId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_favorite' => $isFavorite,
            ]
        ]);
    }

    /**
     * Get user reservations
     */
    public function reservations(Request $request): JsonResponse
    {
        $status = $request->get('status'); // upcoming, past, cancelled

        $query = $request->user()
            ->reservations()
            ->with(['restaurant:id,name,slug,address,city,phone,image']);

        if ($status === 'upcoming') {
            $query->where('reservation_date', '>=', now()->toDateString())
                  ->whereIn('status', ['pending', 'confirmed']);
        } elseif ($status === 'past') {
            $query->where(function ($q) {
                $q->where('reservation_date', '<', now()->toDateString())
                  ->orWhere('status', 'completed');
            });
        } elseif ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reservations->items(),
            'meta' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'total' => $reservations->total(),
            ]
        ]);
    }

    /**
     * Get user reviews
     */
    public function reviews(Request $request): JsonResponse
    {
        $reviews = $request->user()
            ->reviews()
            ->with(['restaurant:id,name,slug,image'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta',
            ], 400);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta eliminada exitosamente',
        ]);
    }
}
