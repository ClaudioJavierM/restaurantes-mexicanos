<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Reservation;
use App\Models\AnalyticsEvent;
use App\Models\Coupon;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\SmsAutomation;
use App\Models\SmsLog;
use App\Models\RestaurantCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * OwnerAppController — Simplified owner endpoints for the FAMER Owners mobile app.
 * Auto-detects the owner's restaurant from the authenticated token.
 */
class OwnerAppController extends Controller
{
    /**
     * Get the authenticated owner's primary restaurant.
     */
    private function getRestaurant(Request $request): ?Restaurant
    {
        return $request->user()->restaurants()->first();
    }

    /**
     * Returns a 403 response if the restaurant's tier is not in the allowed list.
     * Returns null if the tier is allowed (pass-through).
     */
    private function requireTier(Restaurant $restaurant, array $tiers): ?JsonResponse
    {
        if (!in_array($restaurant->subscription_tier ?? 'free', $tiers)) {
            $tierNames = ['premium' => 'Premium ($29/mo)', 'elite' => 'Elite ($79/mo)'];
            $required  = implode(' o ', array_map(fn($t) => $tierNames[$t] ?? ucfirst($t), $tiers));
            return response()->json([
                'success'          => false,
                'upgrade_required' => true,
                'required_tier'    => $tiers[0],
                'message'          => "Esta función requiere plan $required. Actualiza tu suscripción para acceder.",
                'upgrade_url'      => url('/owner/upgrade-subscription'),
            ], 403);
        }
        return null;
    }

    /**
     * Returns a map of feature availability based on subscription tier.
     */
    private function getTierFeatures(string $tier): array
    {
        $isPremium = in_array($tier, ['premium', 'elite']);
        $isElite   = $tier === 'elite';
        return [
            'analytics'        => $isPremium,
            'coupons'          => $isPremium,
            'menu_edit'        => $isPremium,
            'chatbot'          => $isPremium,
            'reservations'     => $isPremium,
            'orders'           => $isElite,
            'sms_marketing'    => $isElite,
            'team_management'  => $isElite,
            'white_label'      => $isElite,
            'priority_support' => $isElite,
            'unlimited_photos' => $isElite,
            'email_marketing'  => false,   // Coming soon
            'flash_deals'      => false,   // Coming soon
            'loyalty_program'  => false,   // Coming soon
            'widget_embed'     => false,   // Coming soon
            'photo_limit'      => $isElite ? null : ($isPremium ? 25 : 5),
        ];
    }

    /**
     * GET /v1/owner/dashboard
     * Returns a flat dashboard summary expected by the Flutter app.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un restaurante registrado',
            ], 404);
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $monthlyViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        $lastMonthViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $pendingReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->count();

        $todayReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $unansweredReviews = Review::where('restaurant_id', $restaurant->id)
            ->where('status', 'approved')
            ->whereNull('owner_response')
            ->count();

        $recentReviews = Review::where('restaurant_id', $restaurant->id)
            ->where('status', 'approved')
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'user_id', 'rating', 'comment', 'owner_response', 'created_at']);

        $upcomingReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->where('reservation_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['user:id,name,phone'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(5)
            ->get();

        $viewsGrowth = $lastMonthViews > 0
            ? round((($monthlyViews - $lastMonthViews) / $lastMonthViews) * 100, 1)
            : ($monthlyViews > 0 ? 100 : 0);

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant'    => $restaurant->only([
                    'id', 'name', 'slug', 'image', 'average_rating',
                    'total_reviews', 'subscription_tier', 'subscription_status',
                    'city', 'phone', 'is_claimed',
                ]),
                'tier_features' => $this->getTierFeatures($restaurant->subscription_tier ?? 'free'),
                'total_reviews' => $restaurant->total_reviews ?? 0,
                'average_rating' => (float) ($restaurant->average_rating ?? 0),
                'pending_reservations' => $pendingReservations,
                'today_reservations' => $todayReservations,
                'unanswered_reviews' => $unansweredReviews,
                'monthly_views' => $monthlyViews,
                'views_growth' => $viewsGrowth,
                'recent_reviews' => $recentReviews,
                'upcoming_reservations' => $upcomingReservations,
            ],
        ]);
    }

    /**
     * GET /v1/owner/restaurant
     * Returns the owner's restaurant.
     */
    public function show(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un restaurante registrado',
            ], 404);
        }

        $restaurant->load(['state:id,name,code', 'category:id,name']);

        return response()->json([
            'success' => true,
            'data' => $restaurant,
        ]);
    }

    /**
     * PUT /v1/owner/restaurant
     * Updates the owner's restaurant.
     */
    public function update(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes un restaurante registrado',
            ], 404);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'description'  => 'sometimes|string|max:2000',
            'phone'        => 'sometimes|string|max:20',
            'email'        => 'sometimes|email|max:255',
            'website'      => 'nullable|url|max:255',
            'address'      => 'sometimes|string|max:255',
            'city'         => 'sometimes|string|max:100',
            'zip_code'     => 'sometimes|string|max:20',
            'hours'        => 'sometimes|array',
            'price_range'  => 'sometimes|string|max:10',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url'=> 'nullable|url|max:255',
            'twitter_url'  => 'nullable|url|max:255',
        ]);

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurante actualizado exitosamente',
            'data'    => $restaurant->fresh(),
        ]);
    }

    /**
     * GET /v1/owner/reviews
     * Returns all reviews for the owner's restaurant.
     */
    public function reviews(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $query = Review::where('restaurant_id', $restaurant->id)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc');

        if ($request->has('responded')) {
            $request->boolean('responded')
                ? $query->whereNotNull('owner_response')
                : $query->whereNull('owner_response');
        }

        if ($request->has('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        $reviews = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $reviews->items(),
            'meta'    => [
                'current_page'   => $reviews->currentPage(),
                'last_page'      => $reviews->lastPage(),
                'total'          => $reviews->total(),
                'unanswered'     => Review::where('restaurant_id', $restaurant->id)
                    ->whereNull('owner_response')
                    ->count(),
            ],
        ]);
    }

    /**
     * POST /v1/owner/reviews/{reviewId}/respond
     * Posts an owner response to a review.
     */
    public function respondToReview(Request $request, int $reviewId): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $review = Review::where('id', $reviewId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        $validated = $request->validate([
            'response' => 'required|string|min:10|max:1000',
        ]);

        $review->update([
            'owner_response'    => $validated['response'],
            'owner_response_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Respuesta publicada exitosamente',
            'data'    => $review->fresh(),
        ]);
    }

    /**
     * GET /v1/owner/reservations
     * Returns reservations for the owner's restaurant.
     */
    public function reservations(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $query = Reservation::where('restaurant_id', $restaurant->id)
            ->with(['user:id,name,email,phone']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->where('reservation_date', $request->date);
        }

        if (!$request->hasAny(['status', 'date', 'all'])) {
            $query->where('reservation_date', '>=', now()->toDateString())
                  ->whereIn('status', ['pending', 'confirmed']);
        }

        $reservations = $query->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $reservations->items(),
            'meta'    => [
                'current_page' => $reservations->currentPage(),
                'last_page'    => $reservations->lastPage(),
                'total'        => $reservations->total(),
                'pending_count'=> Reservation::where('restaurant_id', $restaurant->id)
                    ->where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * PUT /v1/owner/reservations/{id}
     * Updates a reservation status.
     */
    public function updateReservation(Request $request, int $reservationId): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $reservation = Reservation::where('id', $reservationId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        $validated = $request->validate([
            'status'           => 'required|in:confirmed,rejected,completed,no_show',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        $reservation->update([
            'status'           => $validated['status'],
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'confirmed_at'     => $validated['status'] === 'confirmed' ? now() : $reservation->confirmed_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de reservación actualizado',
            'data'    => $reservation->fresh(),
        ]);
    }

    /**
     * GET /v1/owner/photos
     * Returns a list of photo URLs for the owner's restaurant.
     */
    public function photos(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $photos = [];

        // Main image
        if ($restaurant->image) {
            $photos[] = $restaurant->image;
        }

        // Spatie media library collections
        foreach (['gallery', 'menu', 'ambiance', 'default'] as $collection) {
            try {
                foreach ($restaurant->getMedia($collection) as $media) {
                    $photos[] = $media->getUrl();
                }
            } catch (\Exception $e) {
                // Collection may not exist
            }
        }

        // yelp_photos stored as JSON array
        if (!empty($restaurant->yelp_photos)) {
            $yelpPhotos = is_array($restaurant->yelp_photos)
                ? $restaurant->yelp_photos
                : json_decode($restaurant->yelp_photos, true);
            if (is_array($yelpPhotos)) {
                $photos = array_merge($photos, $yelpPhotos);
            }
        }

        return response()->json([
            'success' => true,
            'data'    => array_values(array_unique($photos)),
        ]);
    }

    /**
     * DELETE /v1/owner/photos
     * Deletes a photo by URL from the media library.
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $request->validate([
            'photo_url' => 'required|string',
        ]);

        $photoUrl = $request->input('photo_url');
        $deleted = false;

        foreach (['gallery', 'menu', 'ambiance', 'default'] as $collection) {
            try {
                foreach ($restaurant->getMedia($collection) as $media) {
                    if ($media->getUrl() === $photoUrl) {
                        $media->delete();
                        $deleted = true;
                        break 2;
                    }
                }
            } catch (\Exception $e) {
                // continue
            }
        }

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Foto no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada exitosamente',
        ]);
    }

    /**
     * GET /v1/owner/coupons
     * Returns all coupons for the owner's restaurant.
     */
    public function coupons(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['premium', 'elite']);
        if ($tierError) return $tierError;

        $coupons = Coupon::where('restaurant_id', $restaurant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $coupons,
        ]);
    }

    /**
     * POST /v1/owner/coupons
     * Creates a new coupon for the owner's restaurant.
     */
    public function createCoupon(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['premium', 'elite']);
        if ($tierError) return $tierError;

        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:coupons,code',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string|max:500',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'max_uses'       => 'nullable|integer|min:1',
        ]);

        $coupon = Coupon::create([
            'restaurant_id'   => $restaurant->id,
            'code'            => strtoupper($validated['code']),
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? null,
            'discount_type'   => $validated['discount_type'],
            'discount_value'  => $validated['discount_value'],
            'minimum_purchase'=> $validated['min_order_amount'] ?? null,
            'valid_from'      => $validated['valid_from'] ?? null,
            'valid_until'     => $validated['valid_until'] ?? null,
            'usage_limit'     => $validated['max_uses'] ?? null,
            'is_active'       => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cupón creado exitosamente',
            'data'    => $coupon,
        ], 201);
    }

    /**
     * PUT /v1/owner/coupons/{id}
     * Updates a coupon (e.g., toggle active status).
     */
    public function updateCoupon(Request $request, int $couponId): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['premium', 'elite']);
        if ($tierError) return $tierError;

        $coupon = Coupon::where('id', $couponId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        $validated = $request->validate([
            'is_active'      => 'sometimes|boolean',
            'title'          => 'sometimes|string|max:255',
            'description'    => 'nullable|string|max:500',
            'discount_value' => 'sometimes|numeric|min:0',
            'valid_until'    => 'nullable|date',
        ]);

        $coupon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cupón actualizado exitosamente',
            'data'    => $coupon->fresh(),
        ]);
    }

    /**
     * DELETE /v1/owner/coupons/{id}
     * Deletes a coupon.
     */
    public function deleteCoupon(Request $request, int $couponId): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['premium', 'elite']);
        if ($tierError) return $tierError;

        $coupon = Coupon::where('id', $couponId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cupón eliminado exitosamente',
        ]);
    }

    /**
     * GET /v1/owner/score
     * Calculates and returns the FAMER score for the owner's restaurant.
     */
    public function score(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        // Rating score (0–25): based on average_rating out of 5
        $avgRating = (float) ($restaurant->average_rating ?? 0);
        $ratingScore = round(($avgRating / 5) * 25, 1);

        // Response score (0–20): % of approved reviews with owner response
        $totalApproved = Review::where('restaurant_id', $restaurant->id)->where('status', 'approved')->count();
        $responded = Review::where('restaurant_id', $restaurant->id)
            ->where('status', 'approved')
            ->whereNotNull('owner_response')
            ->count();
        $responseRate = $totalApproved > 0 ? round(($responded / $totalApproved) * 100, 1) : 0;
        $responseScore = round(($responseRate / 100) * 20, 1);

        // Photos score (0–20): up to 10 photos = full score
        $photoCount = 0;
        try {
            $photoCount = $restaurant->getMedia('gallery')->count()
                + $restaurant->getMedia('ambiance')->count()
                + $restaurant->getMedia('menu')->count();
        } catch (\Exception $e) {
            $photoCount = 0;
        }
        if ($restaurant->image) $photoCount++;
        $photosScore = round(min($photoCount / 10, 1) * 20, 1);

        // Profile completeness score (0–20)
        $profileFields = ['name', 'description', 'phone', 'address', 'city', 'website', 'facebook_url', 'instagram_url'];
        $filled = collect($profileFields)->filter(fn($f) => !empty($restaurant->$f))->count();
        $profileCompleteness = round(($filled / count($profileFields)) * 100, 1);
        $profileScore = round(($profileCompleteness / 100) * 20, 1);

        // Hours score (0–15): has hours configured
        $hasHours = !empty($restaurant->hours) || !empty($restaurant->business_hours);
        $hoursScore = $hasHours ? 15.0 : 0.0;

        $totalScore = round($ratingScore + $responseScore + $photosScore + $profileScore + $hoursScore, 1);

        // Level
        if ($totalScore >= 90) {
            $level = 'Restaurante Estrella';
            $levelDescription = 'Tu restaurante es uno de los mejores en FAMER';
        } elseif ($totalScore >= 75) {
            $level = 'Restaurante Destacado';
            $levelDescription = 'Tu restaurante tiene excelente presencia';
        } elseif ($totalScore >= 60) {
            $level = 'Buen Restaurante';
            $levelDescription = 'Tu restaurante está en buen camino';
        } elseif ($totalScore >= 40) {
            $level = 'En Desarrollo';
            $levelDescription = 'Hay oportunidades de mejora';
        } else {
            $level = 'Nuevo';
            $levelDescription = 'Completa tu perfil para mejorar tu puntaje';
        }

        // Improvement tips
        $tips = [];
        if ($ratingScore < 20) $tips[] = 'Invita a tus clientes satisfechos a dejar reseñas positivas';
        if ($responseScore < 15) $tips[] = 'Responde a las reseñas de tus clientes para mejorar tu puntuación';
        if ($photosScore < 15) $tips[] = 'Agrega más fotos de tu restaurante, platillos y ambiente';
        if ($profileScore < 15) $tips[] = 'Completa tu perfil: descripción, sitio web y redes sociales';
        if (!$hasHours) $tips[] = 'Configura tus horarios de atención';

        return response()->json([
            'success' => true,
            'data'    => [
                'total_score'          => $totalScore,
                'rating_score'         => $ratingScore,
                'response_score'       => $responseScore,
                'photos_score'         => $photosScore,
                'profile_score'        => $profileScore,
                'hours_score'          => $hoursScore,
                'total_reviews'        => $totalApproved,
                'response_rate'        => $responseRate,
                'photo_count'          => $photoCount,
                'profile_completeness' => $profileCompleteness,
                'has_hours'            => $hasHours,
                'level'                => $level,
                'level_description'    => $levelDescription,
                'improvement_tips'     => $tips,
            ],
        ]);
    }

    /**
     * GET /v1/owner/analytics
     * Returns analytics data expected by AnalyticsData.fromJson in the Flutter app.
     */
    public function analytics(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['claimed', 'premium', 'elite']);
        if ($tierError) {
            return $tierError;
        }

        $request->validate(['period' => 'nullable|in:7d,30d,90d,1y']);
        $period = $request->get('period', '30d');
        $days = match($period) {
            '7d'  => 7,
            '90d' => 90,
            '1y'  => 365,
            default => 30,
        };

        $startDate = Carbon::now()->subDays($days);

        $totalViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->count();

        $totalReservations = \App\Models\Reservation::where('restaurant_id', $restaurant->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Daily stats: views + reservations per day
        $dailyViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyReservations = \App\Models\Reservation::where('restaurant_id', $restaurant->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $dailyStats = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dailyStats[] = [
                'date'         => $date,
                'views'        => $dailyViews[$date] ?? 0,
                'reservations' => $dailyReservations[$date] ?? 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'total_views'        => $totalViews,
                'total_reviews'      => $restaurant->total_reviews ?? 0,
                'average_rating'     => (float) ($restaurant->average_rating ?? 0),
                'total_reservations' => $totalReservations,
                'daily_stats'        => $dailyStats,
            ],
        ]);
    }

    /**
     * GET /v1/owner/menu
     * Returns the full menu (categories + items) for the owner's restaurant.
     */
    public function menu(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $categories = MenuCategory::where('restaurant_id', $restaurant->id)
            ->with(['items' => fn($q) => $q->orderBy('sort_order')->orderBy('name')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * PUT /v1/owner/menu
     * Saves the full menu structure. Syncs categories and items.
     * Expects: { categories: [ { name, items: [ { name, price, description, ... } ] } ] }
     */
    public function saveMenu(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['premium', 'elite']);
        if ($tierError) return $tierError;

        $request->validate([
            'categories'             => 'required|array',
            'categories.*.name'      => 'required|string|max:100',
            'categories.*.items'     => 'sometimes|array',
            'categories.*.items.*.name'  => 'required|string|max:255',
            'categories.*.items.*.price' => 'required|numeric|min:0',
            'categories.*.items.*.description' => 'nullable|string|max:500',
            'categories.*.items.*.is_available' => 'sometimes|boolean',
            'categories.*.items.*.is_popular'   => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($request, $restaurant) {
            // Remove old menu
            $oldCategoryIds = MenuCategory::where('restaurant_id', $restaurant->id)->pluck('id');
            MenuItem::whereIn('menu_category_id', $oldCategoryIds)->delete();
            MenuCategory::where('restaurant_id', $restaurant->id)->delete();

            // Insert new structure
            foreach ($request->categories as $sortOrder => $catData) {
                $category = MenuCategory::create([
                    'restaurant_id' => $restaurant->id,
                    'name'          => $catData['name'],
                    'sort_order'    => $sortOrder,
                    'is_active'     => true,
                ]);

                foreach ($catData['items'] ?? [] as $itemSort => $itemData) {
                    MenuItem::create([
                        'menu_category_id' => $category->id,
                        'name'             => $itemData['name'],
                        'description'      => $itemData['description'] ?? null,
                        'price'            => $itemData['price'],
                        'is_available'     => $itemData['is_available'] ?? true,
                        'is_popular'       => $itemData['is_popular'] ?? false,
                        'sort_order'       => $itemSort,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Menú guardado exitosamente',
        ]);
    }

    /**
     * PUT /v1/owner/hours
     * Dedicated endpoint to update business hours.
     * Accepts: { hours: { "lunes": "9:00-21:00", "martes": "9:00-21:00", ... } }
     */
    public function updateHours(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $request->validate([
            'hours' => 'required|array',
        ]);

        $restaurant->update(['hours' => $request->hours]);

        return response()->json([
            'success' => true,
            'message' => 'Horarios actualizados exitosamente',
            'data'    => ['hours' => $restaurant->fresh()->hours],
        ]);
    }

    /**
     * GET /v1/owner/tier-features
     * Returns the feature map for the restaurant's current subscription tier.
     */
    public function getTierFeatures(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);

        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tier = $restaurant->subscription_tier ?? 'free';

        $isClaimed = in_array($tier, ['claimed', 'premium', 'elite']);
        $isPremium = in_array($tier, ['premium', 'elite']);
        $isElite = $tier === 'elite';

        return response()->json([
            'success' => true,
            'data' => [
                'tier' => $tier,
                'features' => [
                    'analytics' => $isClaimed,
                    'reservations' => $isClaimed,
                    'menu_edit' => $isClaimed,
                    'team_management' => $isPremium,
                    'coupons' => $isPremium,
                    'featured_listing' => $isPremium,
                    'advanced_analytics' => $isElite,
                    'online_ordering' => $isElite,
                    'priority_support' => $isElite,
                    'photo_limit' => $isElite ? null : ($isPremium ? 25 : ($isClaimed ? 10 : 5)),
                ],
            ],
        ]);
    }

    /**
     * GET /v1/owner/subscription
     * Returns available subscription plans and pricing.
     */
    public function subscription(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        $currentTier = $restaurant?->subscription_tier ?? 'free';

        return response()->json([
            'success' => true,
            'data' => [
                'current_tier' => $currentTier,
                'plans' => [
                    'free' => [
                        'name' => 'Gratuito',
                        'price_monthly' => 0,
                        'features' => ['Perfil básico', 'Aparecer en búsquedas', '5 fotos'],
                    ],
                    'claimed' => [
                        'name' => 'Reclamado',
                        'price_monthly' => 0,
                        'features' => ['Perfil verificado', 'Analytics básicos', 'Reservaciones', 'Editar menú', '10 fotos'],
                    ],
                    'premium' => [
                        'name' => 'Premium',
                        'price_monthly' => 39,
                        'price_display' => '$39/mes',
                        'promo_first_month' => '$9.99',
                        'features' => ['Todo de Reclamado', 'Cupones y promociones', 'Listado destacado', 'Gestión de equipo', '25 fotos'],
                    ],
                    'elite' => [
                        'name' => 'Elite',
                        'price_monthly' => 79,
                        'price_display' => '$79/mes',
                        'features' => ['Todo de Premium', 'Analytics avanzados', 'Pedidos online', 'Fotos ilimitadas', 'Soporte prioritario', 'Badge Elite dorado'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * DELETE /v1/owner/account
     * Deletes the authenticated owner's account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cuenta eliminada exitosamente',
        ]);
    }

    /**
     * GET /v1/owner/orders
     * Returns active orders for the owner's restaurant (Elite only).
     */
    public function orders(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['elite']);
        if ($tierError) return $tierError;

        $query = Order::where('restaurant_id', $restaurant->id)->with(['items']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('all')) {
            $query->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready']);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'total'        => $orders->total(),
                'new_count'    => Order::where('restaurant_id', $restaurant->id)
                    ->where('status', 'pending')->count(),
            ],
        ]);
    }

    /**
     * PUT /v1/owner/orders/{orderId}
     * Updates an order status (Elite only).
     */
    public function updateOrder(Request $request, int $orderId): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['elite']);
        if ($tierError) return $tierError;

        $order = Order::where('id', $orderId)
            ->where('restaurant_id', $restaurant->id)
            ->firstOrFail();

        $validated = $request->validate([
            'status'              => 'required|in:confirmed,preparing,ready,completed,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string|max:500',
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'completed')  $updateData['completed_at'] = now();
        if ($validated['status'] === 'confirmed')   $updateData['confirmed_at'] = now();
        if ($validated['status'] === 'cancelled') {
            $updateData['cancelled_at']       = now();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? null;
        }

        $order->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Pedido actualizado exitosamente',
            'data'    => $order->fresh()->load('items'),
        ]);
    }

    /**
     * GET /v1/owner/subscription
     * Returns subscription plan info and feature availability.
     */
    public function subscription(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tier = $restaurant->subscription_tier ?? 'free';

        $plans = [
            'free'    => ['name' => 'Gratis',   'price' => 0,  'currency' => 'USD', 'interval' => null],
            'premium' => ['name' => 'Premium',  'price' => 29, 'currency' => 'USD', 'interval' => 'month'],
            'elite'   => ['name' => 'Elite',    'price' => 79, 'currency' => 'USD', 'interval' => 'month'],
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'tier'               => $tier,
                'plan'               => $plans[$tier] ?? $plans['free'],
                'status'             => $restaurant->subscription_status ?? 'active',
                'started_at'         => $restaurant->subscription_started_at,
                'expires_at'         => $restaurant->subscription_expires_at,
                'has_stripe'         => !empty($restaurant->stripe_customer_id),
                'features'           => $this->getTierFeatures($tier),
                'upgrade_url'        => url('/owner/upgrade-subscription'),
            ],
        ]);
    }

    /**
     * GET /v1/owner/sms/stats
     * Returns SMS marketing stats for the last 30 days (Elite only).
     */
    public function smsStats(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['elite']);
        if ($tierError) return $tierError;

        $since = Carbon::now()->subDays(30);

        $totalSent      = SmsLog::where('restaurant_id', $restaurant->id)->where('created_at', '>=', $since)->count();
        $totalDelivered = SmsLog::where('restaurant_id', $restaurant->id)->where('created_at', '>=', $since)->where('status', 'delivered')->count();
        $totalClicked   = SmsLog::where('restaurant_id', $restaurant->id)->where('created_at', '>=', $since)->where('clicked', true)->count();

        $subscribedCustomers = RestaurantCustomer::where('restaurant_id', $restaurant->id)
            ->where('sms_opted_in', true)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_sent'           => $totalSent,
                'total_delivered'      => $totalDelivered,
                'delivery_rate'        => $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0,
                'total_clicked'        => $totalClicked,
                'click_rate'           => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0,
                'subscribed_customers' => $subscribedCustomers,
                'period_days'          => 30,
            ],
        ]);
    }

    /**
     * GET /v1/owner/sms/automations
     * Returns SMS automations for the owner's restaurant (Elite only).
     */
    public function smsAutomations(Request $request): JsonResponse
    {
        $restaurant = $this->getRestaurant($request);
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurante no encontrado'], 404);
        }

        $tierError = $this->requireTier($restaurant, ['elite']);
        if ($tierError) return $tierError;

        $automations = SmsAutomation::where('restaurant_id', $restaurant->id)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $automations,
            'trigger_types' => SmsAutomation::triggerTypes(),
        ]);
    }
}
