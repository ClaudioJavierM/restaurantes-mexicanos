<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\ExternalReview;
use App\Models\PlatformConnection;
use App\Models\ReviewResponseTemplate;
use App\Services\GoogleBusinessService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewHubController extends Controller
{
    /**
     * Get review hub dashboard/summary
     */
    public function dashboard(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        // Get platform summaries
        $platforms = ['google', 'yelp', 'tripadvisor', 'facebook'];
        $platformStats = [];

        foreach ($platforms as $platform) {
            $reviews = ExternalReview::where('restaurant_id', $restaurantId)
                ->where('platform', $platform);

            $platformStats[$platform] = [
                'total_reviews' => $reviews->count(),
                'average_rating' => round($reviews->avg('rating') ?? 0, 1),
                'pending_responses' => $reviews->clone()->needsResponse()->count(),
                'negative_unresponded' => $reviews->clone()->negative()->needsResponse()->count(),
                'last_review_at' => $reviews->clone()->max('reviewed_at'),
                'is_connected' => PlatformConnection::where('restaurant_id', $restaurantId)
                    ->where('platform', $platform)
                    ->where('status', 'active')
                    ->exists(),
            ];
        }

        // Overall stats
        $allReviews = ExternalReview::where('restaurant_id', $restaurantId);
        $overall = [
            'total_reviews' => $allReviews->count(),
            'average_rating' => round($allReviews->avg('rating') ?? 0, 1),
            'pending_responses' => $allReviews->clone()->needsResponse()->count(),
            'responded_this_week' => $allReviews->clone()
                ->whereNotNull('owner_response')
                ->where('owner_response_at', '>=', now()->subWeek())
                ->count(),
            'negative_this_month' => $allReviews->clone()
                ->negative()
                ->where('reviewed_at', '>=', now()->subMonth())
                ->count(),
        ];

        // Recent reviews needing attention
        $urgentReviews = ExternalReview::where('restaurant_id', $restaurantId)
            ->needsResponse()
            ->negative()
            ->orderBy('reviewed_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => $overall,
                'platforms' => $platformStats,
                'urgent_reviews' => $urgentReviews,
                'response_rate' => $overall['total_reviews'] > 0
                    ? round((($overall['total_reviews'] - $overall['pending_responses']) / $overall['total_reviews']) * 100)
                    : 0,
            ]
        ]);
    }

    /**
     * Get all external reviews with filters
     */
    public function reviews(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $query = ExternalReview::where('restaurant_id', $restaurantId);

        // Filter by platform
        if ($request->has('platform')) {
            $query->where('platform', $request->platform);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'needs_response') {
                $query->needsResponse();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by sentiment
        if ($request->has('sentiment')) {
            if ($request->sentiment === 'negative') {
                $query->negative();
            } elseif ($request->sentiment === 'positive') {
                $query->positive();
            }
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('reviewed_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('reviewed_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort', 'reviewed_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $reviews = $query->paginate(20);

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
     * Get single review detail
     */
    public function showReview(Request $request, $reviewId): JsonResponse
    {
        $review = ExternalReview::findOrFail($reviewId);
        
        // Verify ownership
        $restaurant = $this->getOwnerRestaurant($request, $review->restaurant_id);

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    /**
     * Respond to an external review
     */
    public function respondToReview(Request $request, $reviewId): JsonResponse
    {
        $review = ExternalReview::findOrFail($reviewId);
        $restaurant = $this->getOwnerRestaurant($request, $review->restaurant_id);

        $validated = $request->validate([
            'response' => 'required|string|min:10|max:2000',
            'sync_to_platform' => 'nullable|boolean',
        ]);

        $review->update([
            'owner_response' => $validated['response'],
            'owner_response_at' => now(),
            'status' => 'responded',
        ]);

        // Sync to platform if requested and possible
        $synced = false;
        if ($request->boolean('sync_to_platform') && $review->canRespondViaApi()) {
            $synced = $this->syncResponseToPlatform($review, $restaurant);
        }

        return response()->json([
            'success' => true,
            'message' => 'Respuesta guardada' . ($synced ? ' y sincronizada' : ''),
            'data' => $review->fresh(),
            'synced' => $synced,
        ]);
    }

    /**
     * Get platform connections status
     */
    public function connections(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $connections = PlatformConnection::where('restaurant_id', $restaurantId)
            ->get()
            ->keyBy('platform');

        $platforms = [
            'google' => [
                'name' => 'Google Business Profile',
                'icon' => 'google',
                'color' => '#4285F4',
                'can_respond' => true,
                'oauth_url' => route('oauth.google.redirect', ['restaurant' => $restaurantId]),
            ],
            'facebook' => [
                'name' => 'Facebook Page',
                'icon' => 'facebook',
                'color' => '#1877F2',
                'can_respond' => true,
                'oauth_url' => route('oauth.facebook.redirect', ['restaurant' => $restaurantId]),
            ],
            'yelp' => [
                'name' => 'Yelp',
                'icon' => 'yelp',
                'color' => '#D32323',
                'can_respond' => false,
                'oauth_url' => null,
                'note' => 'Yelp no permite responder via API',
            ],
            'tripadvisor' => [
                'name' => 'TripAdvisor',
                'icon' => 'tripadvisor',
                'color' => '#00AA6C',
                'can_respond' => false, // Requires partnership
                'oauth_url' => null,
                'note' => 'Requiere partnership con TripAdvisor',
            ],
        ];

        foreach ($platforms as $key => &$platform) {
            $connection = $connections->get($key);
            $platform['connected'] = $connection && $connection->isActive();
            $platform['status'] = $connection?->status ?? 'not_connected';
            $platform['last_sync'] = $connection?->last_sync_at;
            $platform['account_name'] = $connection?->platform_account_name;
        }

        return response()->json([
            'success' => true,
            'data' => $platforms,
        ]);
    }

    /**
     * Disconnect a platform
     */
    public function disconnectPlatform(Request $request, $restaurantId, $platform): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $connection = PlatformConnection::where('restaurant_id', $restaurantId)
            ->where('platform', $platform)
            ->first();

        if ($connection) {
            $connection->update(['status' => 'revoked']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plataforma desconectada',
        ]);
    }

    /**
     * Manually sync reviews from a platform
     */
    public function syncPlatform(Request $request, $restaurantId, $platform): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $connection = PlatformConnection::where('restaurant_id', $restaurantId)
            ->where('platform', $platform)
            ->where('status', 'active')
            ->first();

        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Plataforma no conectada',
            ], 400);
        }

        // Trigger sync based on platform
        $synced = 0;
        try {
            switch ($platform) {
                case 'google':
                    $service = app(GoogleBusinessService::class);
                    $synced = $service->syncReviews($restaurant, $connection);
                    break;
                // Add other platforms here
            }

            $connection->recordSync();

            return response()->json([
                'success' => true,
                'message' => "Sincronizadas {$synced} reseñas de {$platform}",
                'synced_count' => $synced,
            ]);

        } catch (\Exception $e) {
            $connection->recordError($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get response templates
     */
    public function templates(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $templates = ReviewResponseTemplate::where(function($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
              ->orWhere('is_global', true);
        })
        ->orderBy('usage_count', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $templates,
            'placeholders' => ReviewResponseTemplate::getPlaceholders(),
        ]);
    }

    /**
     * Create a response template
     */
    public function createTemplate(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:positive,negative,neutral',
            'template' => 'required|string|max:2000',
        ]);

        $template = ReviewResponseTemplate::create([
            'restaurant_id' => $restaurantId,
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'template' => $validated['template'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plantilla creada',
            'data' => $template,
        ], 201);
    }

    /**
     * Get analytics for reviews
     */
    public function analytics(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = $this->getOwnerRestaurant($request, $restaurantId);

        $period = $request->get('period', '30d');
        $days = match($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30,
        };

        $startDate = now()->subDays($days);

        // Reviews over time by platform
        $reviewsByPlatform = ExternalReview::where('restaurant_id', $restaurantId)
            ->where('reviewed_at', '>=', $startDate)
            ->selectRaw('platform, DATE(reviewed_at) as date, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('platform', 'date')
            ->orderBy('date')
            ->get()
            ->groupBy('platform');

        // Rating distribution
        $ratingDistribution = ExternalReview::where('restaurant_id', $restaurantId)
            ->where('reviewed_at', '>=', $startDate)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Response time average
        $avgResponseTime = ExternalReview::where('restaurant_id', $restaurantId)
            ->whereNotNull('owner_response_at')
            ->where('reviewed_at', '>=', $startDate)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, reviewed_at, owner_response_at)) as avg_hours')
            ->value('avg_hours');

        // Sentiment trend
        $sentimentTrend = ExternalReview::where('restaurant_id', $restaurantId)
            ->where('reviewed_at', '>=', $startDate)
            ->selectRaw('
                DATE(reviewed_at) as date,
                SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as neutral,
                SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'reviews_by_platform' => $reviewsByPlatform,
                'rating_distribution' => [
                    5 => $ratingDistribution[5] ?? 0,
                    4 => $ratingDistribution[4] ?? 0,
                    3 => $ratingDistribution[3] ?? 0,
                    2 => $ratingDistribution[2] ?? 0,
                    1 => $ratingDistribution[1] ?? 0,
                ],
                'avg_response_time_hours' => round($avgResponseTime ?? 0, 1),
                'sentiment_trend' => $sentimentTrend,
            ]
        ]);
    }

    /**
     * Sync response to the original platform
     */
    private function syncResponseToPlatform(ExternalReview $review, Restaurant $restaurant): bool
    {
        try {
            switch ($review->platform) {
                case 'google':
                    $service = app(GoogleBusinessService::class);
                    return $service->replyToReview($restaurant, $review);
                case 'facebook':
                    // TODO: Implement Facebook reply
                    return false;
                default:
                    return false;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to sync response', [
                'review_id' => $review->id,
                'platform' => $review->platform,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get owner's restaurant with ownership verification
     */
    private function getOwnerRestaurant(Request $request, $restaurantId): Restaurant
    {
        return Restaurant::where('id', $restaurantId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
