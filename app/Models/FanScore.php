<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FanScore extends Model
{
    // Points per action
    const POINTS_VOTE = 50;
    const POINTS_CHECKIN = 10;
    const POINTS_REVIEW = 20;
    const POINTS_FAVORITE = 15;
    const POINTS_SHARE = 10;
    const POINTS_COUPON = 15;

    // Fan level thresholds
    const LEVEL_FAN = 50;
    const LEVEL_SUPER_FAN = 150;
    const LEVEL_FAN_DESTACADO = 300;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'vote_points',
        'checkin_points',
        'review_points',
        'favorite_points',
        'share_points',
        'coupon_points',
        'total_points',
        'votes_count',
        'checkins_count',
        'reviews_count',
        'coupons_redeemed',
        'shares_count',
        'fan_level',
        'badge_accepted',
        'badge_offered_at',
        'badge_accepted_at',
        'year',
    ];

    protected $casts = [
        'badge_accepted' => 'boolean',
        'badge_offered_at' => 'datetime',
        'badge_accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get or create a fan score for a user+restaurant+year combo.
     */
    public static function getOrCreate(int $userId, int $restaurantId, ?int $year = null): self
    {
        $year = $year ?? now()->year;

        return self::firstOrCreate(
            ['user_id' => $userId, 'restaurant_id' => $restaurantId, 'year' => $year],
            ['total_points' => 0]
        );
    }

    /**
     * Recalculate fan score from all activity sources.
     */
    public static function recalculate(int $userId, int $restaurantId, ?int $year = null): self
    {
        $year = $year ?? now()->year;
        $score = self::getOrCreate($userId, $restaurantId, $year);

        // Count votes
        $votesCount = RestaurantVote::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->where('year', $year)
            ->count();

        // Count check-ins this year
        $checkinsCount = CheckIn::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->count();

        // Count reviews this year
        $reviewsCount = Review::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->whereYear('created_at', $year)
            ->count();

        // Has favorited
        $hasFavorite = Favorite::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->exists();

        // Coupons redeemed
        $couponsCount = 0;
        if (class_exists(\App\Models\UserCoupon::class)) {
            $couponsCount = \App\Models\UserCoupon::where('user_id', $userId)
                ->whereHas('coupon', fn($q) => $q->where('restaurant_id', $restaurantId))
                ->where('status', 'redeemed')
                ->whereYear('redeemed_at', $year)
                ->count();
        }

        $score->update([
            'votes_count' => $votesCount,
            'vote_points' => $votesCount * self::POINTS_VOTE,
            'checkins_count' => $checkinsCount,
            'checkin_points' => $checkinsCount * self::POINTS_CHECKIN,
            'reviews_count' => $reviewsCount,
            'review_points' => $reviewsCount * self::POINTS_REVIEW,
            'favorite_points' => $hasFavorite ? self::POINTS_FAVORITE : 0,
            'coupons_redeemed' => $couponsCount,
            'coupon_points' => $couponsCount * self::POINTS_COUPON,
            'total_points' => ($votesCount * self::POINTS_VOTE) +
                ($checkinsCount * self::POINTS_CHECKIN) +
                ($reviewsCount * self::POINTS_REVIEW) +
                ($hasFavorite ? self::POINTS_FAVORITE : 0) +
                ($couponsCount * self::POINTS_COUPON),
        ]);

        $score->updateFanLevel();

        return $score->fresh();
    }

    /**
     * Add points for a specific action.
     */
    public function addAction(string $action): self
    {
        $pointsMap = [
            'vote' => ['field' => 'vote_points', 'count' => 'votes_count', 'points' => self::POINTS_VOTE],
            'checkin' => ['field' => 'checkin_points', 'count' => 'checkins_count', 'points' => self::POINTS_CHECKIN],
            'review' => ['field' => 'review_points', 'count' => 'reviews_count', 'points' => self::POINTS_REVIEW],
            'favorite' => ['field' => 'favorite_points', 'count' => null, 'points' => self::POINTS_FAVORITE],
            'share' => ['field' => 'share_points', 'count' => 'shares_count', 'points' => self::POINTS_SHARE],
            'coupon' => ['field' => 'coupon_points', 'count' => 'coupons_redeemed', 'points' => self::POINTS_COUPON],
        ];

        if (!isset($pointsMap[$action])) {
            return $this;
        }

        $config = $pointsMap[$action];
        $this->increment($config['field'], $config['points']);
        $this->increment('total_points', $config['points']);

        if ($config['count']) {
            $this->increment($config['count']);
        }

        $this->updateFanLevel();

        return $this->fresh();
    }

    /**
     * Update fan level based on total points.
     */
    public function updateFanLevel(): void
    {
        $newLevel = null;

        if ($this->total_points >= self::LEVEL_FAN_DESTACADO) {
            $newLevel = 'fan_destacado';
        } elseif ($this->total_points >= self::LEVEL_SUPER_FAN) {
            $newLevel = 'super_fan';
        } elseif ($this->total_points >= self::LEVEL_FAN) {
            $newLevel = 'fan';
        }

        if ($this->fan_level !== $newLevel) {
            $this->update(['fan_level' => $newLevel]);

            // Offer badge when reaching fan level for the first time
            if ($newLevel && !$this->badge_offered_at) {
                $this->update(['badge_offered_at' => now()]);
            }
        }
    }

    /**
     * Accept the fan badge.
     */
    public function acceptBadge(): self
    {
        $this->update([
            'badge_accepted' => true,
            'badge_accepted_at' => now(),
        ]);

        return $this->fresh();
    }

    /**
     * Get fan level display info.
     */
    public function getLevelInfoAttribute(): ?array
    {
        $levels = [
            'fan' => [
                'name' => 'Fan',
                'color' => '#CD7F32', // bronze
                'icon' => '⭐',
                'next_level' => 'Super Fan',
                'next_points' => self::LEVEL_SUPER_FAN,
            ],
            'super_fan' => [
                'name' => 'Super Fan',
                'color' => '#C0C0C0', // silver
                'icon' => '🌟',
                'next_level' => 'Fan Destacado',
                'next_points' => self::LEVEL_FAN_DESTACADO,
            ],
            'fan_destacado' => [
                'name' => 'Fan Destacado',
                'color' => '#FFD700', // gold
                'icon' => '🏆',
                'next_level' => null,
                'next_points' => null,
            ],
        ];

        return $levels[$this->fan_level] ?? null;
    }

    /**
     * Get the user's rank among fans of this restaurant.
     */
    public function getRankAttribute(): int
    {
        return self::where('restaurant_id', $this->restaurant_id)
            ->where('year', $this->year)
            ->where('total_points', '>', $this->total_points)
            ->count() + 1;
    }

    // Scopes
    public function scopeForYear($query, ?int $year = null)
    {
        return $query->where('year', $year ?? now()->year);
    }

    public function scopeWithBadge($query)
    {
        return $query->whereNotNull('fan_level');
    }

    public function scopeTopFans($query, int $restaurantId, ?int $year = null, int $limit = 10)
    {
        return $query->where('restaurant_id', $restaurantId)
            ->where('year', $year ?? now()->year)
            ->whereNotNull('fan_level')
            ->orderByDesc('total_points')
            ->limit($limit);
    }
}
