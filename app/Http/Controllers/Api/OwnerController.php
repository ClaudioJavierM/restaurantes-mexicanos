<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Reservation;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OwnerController extends Controller
{
    /**
     * Get owner's restaurants
     */
    public function restaurants(Request $request): JsonResponse
    {
        $restaurants = $request->user()
            ->restaurants()
            ->with(['state:id,name,abbreviation', 'category:id,name'])
            ->select([
                'id', 'name', 'slug', 'address', 'city', 'phone', 'image',
                'average_rating', 'total_reviews', 'is_claimed', 'status',
                'subscription_plan', 'subscription_status', 'state_id', 'category_id'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $restaurants,
        ]);
    }

    /**
     * Get dashboard summary for a restaurant
     */
    public function dashboard(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        // Get stats for this month
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Current month views
        $monthlyViews = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        // Last month views for comparison
        $lastMonthViews = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'page_view')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        // Phone clicks this month
        $phoneClicks = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'phone_click')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        // Website clicks this month
        $websiteClicks = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'website_click')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        // Direction clicks this month
        $directionClicks = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'direction_click')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        // Pending reservations
        $pendingReservations = Reservation::where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->count();

        // Today's reservations
        $todayReservations = Reservation::where('restaurant_id', $restaurantId)
            ->where('reservation_date', $now->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        // Recent reviews (unread)
        $newReviews = Review::where('restaurant_id', $restaurantId)
            ->whereNull('owner_response')
            ->where('status', 'approved')
            ->count();

        // Views growth percentage
        $viewsGrowth = $lastMonthViews > 0 
            ? round((($monthlyViews - $lastMonthViews) / $lastMonthViews) * 100, 1) 
            : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => $restaurant->only([
                    'id', 'name', 'slug', 'image', 'average_rating', 
                    'total_reviews', 'subscription_plan', 'subscription_status'
                ]),
                'stats' => [
                    'monthly_views' => $monthlyViews,
                    'views_growth' => $viewsGrowth,
                    'phone_clicks' => $phoneClicks,
                    'website_clicks' => $websiteClicks,
                    'direction_clicks' => $directionClicks,
                ],
                'alerts' => [
                    'pending_reservations' => $pendingReservations,
                    'today_reservations' => $todayReservations,
                    'new_reviews' => $newReviews,
                ],
            ]
        ]);
    }

    /**
     * Get detailed analytics
     */
    public function analytics(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $request->validate([
            'period' => 'nullable|in:7d,30d,90d,1y',
        ]);

        $period = $request->get('period', '30d');
        $days = match($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
        };

        $startDate = Carbon::now()->subDays($days);

        // Daily views
        $dailyViews = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill in missing dates with 0
        $filledDailyViews = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $filledDailyViews[$date] = $dailyViews[$date] ?? 0;
        }

        // Event breakdown
        $eventBreakdown = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        // Traffic sources
        $trafficSources = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as count')
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'referrer')
            ->toArray();

        // Device breakdown
        $devices = AnalyticsEvent::where('restaurant_id', $restaurantId)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('device_type')
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'daily_views' => $filledDailyViews,
                'totals' => [
                    'views' => array_sum($filledDailyViews),
                    'phone_clicks' => $eventBreakdown['phone_click'] ?? 0,
                    'website_clicks' => $eventBreakdown['website_click'] ?? 0,
                    'direction_clicks' => $eventBreakdown['direction_click'] ?? 0,
                    'menu_views' => $eventBreakdown['menu_view'] ?? 0,
                    'share_clicks' => $eventBreakdown['share'] ?? 0,
                ],
                'traffic_sources' => $trafficSources,
                'devices' => $devices,
            ]
        ]);
    }

    /**
     * Update restaurant info
     */
    public function updateRestaurant(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:2000',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'hours' => 'sometimes|array',
            'price_range' => 'sometimes|in:$,63652,63652$,6365263652',
            'category_id' => 'sometimes|exists:categories,id',
            // Social media
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            // Features
            'dietary_options' => 'nullable|array',
            'atmosphere' => 'nullable|array',
            'special_features' => 'nullable|array',
            // Reservation settings
            'accepts_reservations' => 'sometimes|boolean',
            'reservation_type' => 'sometimes|in:instant,request',
            'reservation_capacity_per_slot' => 'sometimes|integer|min:1|max:100',
        ]);

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurante actualizado exitosamente',
            'data' => $restaurant->fresh(),
        ]);
    }

    /**
     * Upload restaurant photos
     */
    public function uploadPhotos(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $request->validate([
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'image|max:10240', // 10MB each
            'type' => 'nullable|in:gallery,menu,ambiance',
        ]);

        $type = $request->get('type', 'gallery');
        $uploadedPhotos = [];

        foreach ($request->file('photos') as $photo) {
            $path = $photo->store("restaurants/{$restaurantId}/{$type}", "public");
            
            // Add to media library if using Spatie
            $media = $restaurant->addMedia(Storage::disk('public')->path($path))
                ->preservingOriginal()
                ->toMediaCollection($type);
            
            $uploadedPhotos[] = [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumbnail'),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedPhotos) . ' fotos subidas exitosamente',
            'data' => $uploadedPhotos,
        ]);
    }

    /**
     * Get restaurant reviews (for owner)
     */
    public function reviews(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $query = Review::where('restaurant_id', $restaurantId)
            ->with(['user:id,name,avatar']);

        // Filter by response status
        if ($request->has('responded')) {
            if ($request->boolean('responded')) {
                $query->whereNotNull('owner_response');
            } else {
                $query->whereNull('owner_response');
            }
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
                'unresponded' => Review::where('restaurant_id', $restaurantId)
                    ->whereNull('owner_response')
                    ->count(),
            ]
        ]);
    }

    /**
     * Respond to a review
     */
    public function respondToReview(Request $request, $reviewId): JsonResponse
    {
        $review = Review::with('restaurant')->findOrFail($reviewId);

        // Verify ownership
        if ($review->restaurant->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para responder esta reseña',
            ], 403);
        }

        $validated = $request->validate([
            'response' => 'required|string|min:10|max:1000',
        ]);

        $review->update([
            'owner_response' => $validated['response'],
            'owner_response_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta publicada exitosamente',
            'data' => $review->fresh(),
        ]);
    }

    /**
     * Get restaurant reservations (for owner)
     */
    public function reservations(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $query = Reservation::where('restaurant_id', $restaurantId)
            ->with(['user:id,name,email,phone']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->where('reservation_date', $request->date);
        }

        // Upcoming reservations by default
        if (!$request->has('status') && !$request->has('date') && !$request->has('all')) {
            $query->where('reservation_date', '>=', now()->toDateString())
                  ->whereIn('status', ['pending', 'confirmed']);
        }

        $reservations = $query->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reservations->items(),
            'meta' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'total' => $reservations->total(),
                'pending_count' => Reservation::where('restaurant_id', $restaurantId)
                    ->where('status', 'pending')
                    ->count(),
            ]
        ]);
    }

    /**
     * Update reservation status (confirm, reject, complete)
     */
    public function updateReservationStatus(Request $request, $reservationId): JsonResponse
    {
        $reservation = Reservation::with('restaurant')->findOrFail($reservationId);

        // Verify ownership
        if ($reservation->restaurant->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta reservación',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,rejected,completed,no_show',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        $reservation->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'confirmed_at' => $validated['status'] === 'confirmed' ? now() : $reservation->confirmed_at,
        ]);

        // TODO: Send notification to customer

        return response()->json([
            'success' => true,
            'message' => 'Estado de reservación actualizado',
            'data' => $reservation->fresh(),
        ]);
    }

    /**
     * Get owner's restaurant by ID with ownership check
     */
    private function getOwnerRestaurant(Request $request, $restaurantId): Restaurant
    {
        return Restaurant::where('id', $restaurantId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
