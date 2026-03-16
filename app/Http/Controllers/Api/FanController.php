<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanScore;
use App\Models\Restaurant;
use App\Models\RestaurantVote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FanController extends Controller
{
    /**
     * Get top fans for a restaurant.
     */
    public function topFans(int $restaurantId): JsonResponse
    {
        $year = now()->year;

        $fans = FanScore::where('restaurant_id', $restaurantId)
            ->where('year', $year)
            ->where('total_points', '>', 0)
            ->orderByDesc('total_points')
            ->with('user:id,name,avatar')
            ->limit(20)
            ->get()
            ->map(fn($fan) => [
                'user_id' => $fan->user_id,
                'user_name' => $fan->user->name ?? 'Anónimo',
                'user_avatar' => $fan->user->avatar ?? null,
                'total_points' => $fan->total_points,
                'fan_level' => $fan->fan_level,
                'level_info' => $fan->level_info,
                'badge_accepted' => $fan->badge_accepted,
                'votes_count' => $fan->votes_count,
                'checkins_count' => $fan->checkins_count,
                'reviews_count' => $fan->reviews_count,
                'rank' => $fan->rank,
            ]);

        return response()->json([
            'success' => true,
            'data' => $fans,
            'total_fans' => FanScore::where('restaurant_id', $restaurantId)
                ->where('year', $year)
                ->whereNotNull('fan_level')
                ->count(),
            'total_voters' => RestaurantVote::where('restaurant_id', $restaurantId)
                ->where('year', $year)
                ->count(),
        ]);
    }

    /**
     * Get the current user's fan score for a restaurant.
     */
    public function myFanScore(Request $request, int $restaurantId): JsonResponse
    {
        $user = $request->user();
        $year = now()->year;

        $score = FanScore::recalculate($user->id, $restaurantId, $year);

        return response()->json([
            'success' => true,
            'data' => [
                'total_points' => $score->total_points,
                'fan_level' => $score->fan_level,
                'level_info' => $score->level_info,
                'badge_accepted' => $score->badge_accepted,
                'rank' => $score->rank,
                'breakdown' => [
                    'votes' => ['count' => $score->votes_count, 'points' => $score->vote_points],
                    'checkins' => ['count' => $score->checkins_count, 'points' => $score->checkin_points],
                    'reviews' => ['count' => $score->reviews_count, 'points' => $score->review_points],
                    'favorite' => ['active' => $score->favorite_points > 0, 'points' => $score->favorite_points],
                    'coupons' => ['count' => $score->coupons_redeemed, 'points' => $score->coupon_points],
                    'shares' => ['count' => $score->shares_count, 'points' => $score->share_points],
                ],
                'next_level' => $score->level_info['next_level'] ?? null,
                'points_to_next' => $score->level_info
                    ? ($score->level_info['next_points'] ? $score->level_info['next_points'] - $score->total_points : 0)
                    : FanScore::LEVEL_FAN - $score->total_points,
            ],
        ]);
    }

    /**
     * Accept the fan badge.
     */
    public function acceptBadge(Request $request, int $restaurantId): JsonResponse
    {
        $user = $request->user();
        $year = now()->year;

        $score = FanScore::getOrCreate($user->id, $restaurantId, $year);

        if (!$score->fan_level) {
            return response()->json([
                'success' => false,
                'message' => 'Aún no tienes un nivel de fan para este restaurante.',
            ], 422);
        }

        $score->acceptBadge();

        return response()->json([
            'success' => true,
            'message' => '¡Insignia aceptada! Ahora aparecerá junto a tu nombre.',
            'data' => [
                'fan_level' => $score->fan_level,
                'level_info' => $score->level_info,
            ],
        ]);
    }

    /**
     * Vote for a restaurant (public endpoint with fingerprint support).
     */
    public function vote(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'vote_type' => 'in:up,favorite,must_visit',
            'fingerprint' => 'nullable|string|max:64',
            'email' => 'nullable|email',
        ]);

        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $year = now()->year;
        $month = now()->month;
        $userId = $request->user()?->id;
        $fingerprint = $request->fingerprint ?? $request->ip();

        // Check if already voted
        if (RestaurantVote::hasVoted($restaurant->id, $userId, $fingerprint, $year, $month)) {
            return response()->json([
                'success' => false,
                'message' => '¡Ya votaste por este restaurante este mes!',
                'already_voted' => true,
            ], 422);
        }

        // Create vote
        $vote = RestaurantVote::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $userId,
            'voter_ip' => $request->ip(),
            'voter_fingerprint' => $fingerprint,
            'voter_email' => $request->email ?? $request->user()?->email,
            'year' => $year,
            'month' => $month,
            'vote_type' => $request->vote_type ?? 'up',
            'is_verified' => (bool) $userId,
        ]);

        // Update fan score if user is authenticated
        if ($userId) {
            $fanScore = FanScore::getOrCreate($userId, $restaurant->id, $year);
            $fanScore->addAction('vote');
        }

        // Count total votes for this restaurant
        $totalVotes = RestaurantVote::where('restaurant_id', $restaurant->id)
            ->where('year', $year)
            ->count();

        return response()->json([
            'success' => true,
            'message' => '¡Gracias por tu voto! Ayudas a que ' . $restaurant->name . ' sea reconocido.',
            'data' => [
                'total_votes' => $totalVotes,
                'fan_score' => $userId ? FanScore::getOrCreate($userId, $restaurant->id, $year)->total_points : null,
                'fan_level' => $userId ? FanScore::getOrCreate($userId, $restaurant->id, $year)->fan_level : null,
            ],
        ]);
    }

    /**
     * Record a share action for fan points.
     */
    public function recordShare(Request $request, int $restaurantId): JsonResponse
    {
        $user = $request->user();
        $year = now()->year;

        $fanScore = FanScore::getOrCreate($user->id, $restaurantId, $year);
        $fanScore->addAction('share');

        return response()->json([
            'success' => true,
            'message' => '¡Puntos de fan sumados por compartir!',
            'data' => [
                'total_points' => $fanScore->fresh()->total_points,
                'fan_level' => $fanScore->fresh()->fan_level,
            ],
        ]);
    }
}
