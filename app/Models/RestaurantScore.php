<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantScore extends Model
{
    protected $fillable = [
        'restaurant_id',
        'google_score', 'yelp_score', 'facebook_score', 'tripadvisor_score',
        'famer_rating_score', 'review_count_score', 'verified_reviews_score',
        'owner_response_score', 'engagement_score', 'subscription_score',
        'seniority_score', 'survey_score', 'total_score',
        'city_rank', 'state_rank', 'national_rank', 'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    // Default weights for score calculation
    const WEIGHTS = [
        // External platforms (40%)
        'google' => 12,
        'yelp' => 10,
        'facebook' => 8,
        'tripadvisor' => 10,
        // FAMER internal (60%)
        'famer_rating' => 15,
        'review_count' => 8,
        'verified_reviews' => 8,
        'owner_response' => 7,
        'engagement' => 5,
        'subscription' => 7,
        'seniority' => 5,
        'survey' => 5,
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function calculateForRestaurant(Restaurant $restaurant): self
    {
        $score = self::firstOrNew(['restaurant_id' => $restaurant->id]);
        
        // Get external ratings
        $externalRatings = $restaurant->externalRatings()->get()->keyBy('platform');
        
        // Calculate external scores
        $score->google_score = $externalRatings->get('google')?->normalized_score ?? 0;
        $score->yelp_score = $externalRatings->get('yelp')?->normalized_score ?? 0;
        $score->facebook_score = $externalRatings->get('facebook')?->normalized_score ?? 0;
        $score->tripadvisor_score = $externalRatings->get('tripadvisor')?->normalized_score ?? 0;
        
        // Calculate FAMER scores
        $score->famer_rating_score = self::calculateFamerRatingScore($restaurant);
        $score->review_count_score = self::calculateReviewCountScore($restaurant);
        $score->verified_reviews_score = self::calculateVerifiedReviewsScore($restaurant);
        $score->owner_response_score = self::calculateOwnerResponseScore($restaurant);
        $score->engagement_score = self::calculateEngagementScore($restaurant);
        $score->subscription_score = self::calculateSubscriptionScore($restaurant);
        $score->seniority_score = self::calculateSeniorityScore($restaurant);
        $score->survey_score = 50; // Default until surveys implemented
        
        // Calculate total weighted score
        $score->total_score = self::calculateTotalScore($score);
        $score->calculated_at = now();
        $score->save();
        
        return $score;
    }

    protected static function calculateFamerRatingScore(Restaurant $restaurant): float
    {
        $rating = $restaurant->rating ?? 0;
        return min(100, $rating * 20); // 5 stars = 100
    }

    protected static function calculateReviewCountScore(Restaurant $restaurant): float
    {
        $count = $restaurant->reviews()->where('status', 'approved')->count();
        // 100+ reviews = 100 score
        return min(100, $count);
    }

    protected static function calculateVerifiedReviewsScore(Restaurant $restaurant): float
    {
        $total = $restaurant->reviews()->where('status', 'approved')->count();
        if ($total === 0) return 0;
        
        $verified = $restaurant->reviews()->where('status', 'approved')->where('is_verified', true)->count();
        return ($verified / $total) * 100;
    }

    protected static function calculateOwnerResponseScore(Restaurant $restaurant): float
    {
        if (!$restaurant->is_claimed) return 0;
        
        $totalReviews = $restaurant->reviews()->where('status', 'approved')->count();
        if ($totalReviews === 0) return 50; // Neutral if no reviews
        
        // Check for owner responses (assuming there's a response field)
        $respondedReviews = $restaurant->reviews()
            ->where('status', 'approved')
            ->whereNotNull('owner_response')
            ->count();
        
        return ($respondedReviews / $totalReviews) * 100;
    }

    protected static function calculateEngagementScore(Restaurant $restaurant): float
    {
        $score = 0;
        
        // Has photos
        if ($restaurant->media()->count() > 0) $score += 30;
        if ($restaurant->media()->count() > 5) $score += 20;
        
        // Has complete profile
        if ($restaurant->description) $score += 15;
        if ($restaurant->phone) $score += 10;
        if ($restaurant->website) $score += 10;
        if ($restaurant->hours) $score += 15;
        
        return min(100, $score);
    }

    protected static function calculateSubscriptionScore(Restaurant $restaurant): float
    {
        return match($restaurant->subscription_plan) {
            'elite' => 100,
            'premium' => 70,
            'free' => 30,
            default => 0,
        };
    }

    protected static function calculateSeniorityScore(Restaurant $restaurant): float
    {
        $monthsOnPlatform = $restaurant->created_at->diffInMonths(now());
        // 24+ months = 100 score
        return min(100, ($monthsOnPlatform / 24) * 100);
    }

    protected static function calculateTotalScore(self $score): float
    {
        $total = 0;
        
        // External (40%)
        $total += $score->google_score * (self::WEIGHTS['google'] / 100);
        $total += $score->yelp_score * (self::WEIGHTS['yelp'] / 100);
        $total += $score->facebook_score * (self::WEIGHTS['facebook'] / 100);
        $total += $score->tripadvisor_score * (self::WEIGHTS['tripadvisor'] / 100);
        
        // Internal (60%)
        $total += $score->famer_rating_score * (self::WEIGHTS['famer_rating'] / 100);
        $total += $score->review_count_score * (self::WEIGHTS['review_count'] / 100);
        $total += $score->verified_reviews_score * (self::WEIGHTS['verified_reviews'] / 100);
        $total += $score->owner_response_score * (self::WEIGHTS['owner_response'] / 100);
        $total += $score->engagement_score * (self::WEIGHTS['engagement'] / 100);
        $total += $score->subscription_score * (self::WEIGHTS['subscription'] / 100);
        $total += $score->seniority_score * (self::WEIGHTS['seniority'] / 100);
        $total += $score->survey_score * (self::WEIGHTS['survey'] / 100);
        
        return round($total, 2);
    }
}
