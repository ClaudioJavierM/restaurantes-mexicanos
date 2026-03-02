<?php

namespace App\Services\ExternalRatings;

use App\Models\Restaurant;
use App\Models\RestaurantScore;
use App\Models\RestaurantRanking;
use App\Models\ExternalRating;
use App\Models\State;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExternalRatingsService
{
    protected GooglePlacesService $googleService;
    protected YelpService $yelpService;

    public function __construct(
        GooglePlacesService $googleService,
        YelpService $yelpService
    ) {
        $this->googleService = $googleService;
        $this->yelpService = $yelpService;
    }

    /**
     * Sync all external ratings for a restaurant
     */
    public function syncAllRatings(Restaurant $restaurant): array
    {
        $results = [
            'google' => null,
            'yelp' => null,
        ];

        if ($this->googleService->isConfigured()) {
            try {
                $results['google'] = $this->googleService->syncRating($restaurant);
            } catch (\Exception $e) {
                Log::error('Google sync failed for restaurant ' . $restaurant->id . ': ' . $e->getMessage());
            }
        }

        if ($this->yelpService->isConfigured()) {
            try {
                $results['yelp'] = $this->yelpService->syncRating($restaurant);
            } catch (\Exception $e) {
                Log::error('Yelp sync failed for restaurant ' . $restaurant->id . ': ' . $e->getMessage());
            }
        }

        $this->calculateScore($restaurant);
        return $results;
    }

    /**
     * Calculate comprehensive score for a restaurant
     */
    public function calculateScore(Restaurant $restaurant): RestaurantScore
    {
        $externalRatings = $restaurant->externalRatings()->get();
        
        $scores = [
            'google_score' => 0,
            'yelp_score' => 0,
            'facebook_score' => 0,
            'tripadvisor_score' => 0,
            'famer_rating_score' => 0,
            'review_count_score' => 0,
            'verified_reviews_score' => 0,
            'response_rate_score' => 0,
            'photo_count_score' => 0,
            'completeness_score' => 0,
            'activity_score' => 0,
        ];

        foreach ($externalRatings as $rating) {
            $normalizedScore = $rating->normalized_score;
            switch ($rating->platform) {
                case 'google': $scores['google_score'] = $normalizedScore; break;
                case 'yelp': $scores['yelp_score'] = $normalizedScore; break;
                case 'facebook': $scores['facebook_score'] = $normalizedScore; break;
                case 'tripadvisor': $scores['tripadvisor_score'] = $normalizedScore; break;
            }
        }

        $scores['famer_rating_score'] = $this->calculateFamerScore($restaurant);
        $scores['review_count_score'] = $this->calculateReviewCountScore($restaurant);
        $scores['verified_reviews_score'] = $this->calculateVerifiedReviewsScore($restaurant);
        $scores['response_rate_score'] = $this->calculateResponseRateScore($restaurant);
        $scores['photo_count_score'] = $this->calculatePhotoScore($restaurant);
        $scores['completeness_score'] = $this->calculateCompletenessScore($restaurant);
        $scores['activity_score'] = $this->calculateActivityScore($restaurant);

        $weights = $this->getWeights();
        $totalScore = 0;
        $totalWeight = 0;

        foreach ($scores as $key => $score) {
            $weight = $weights[$key] ?? 0;
            if ($score > 0) {
                $totalScore += $score * $weight;
                $totalWeight += $weight;
            }
        }

        $finalScore = $totalWeight > 0 ? ($totalScore / $totalWeight) * 100 : 0;

        return RestaurantScore::updateOrCreate(
            ['restaurant_id' => $restaurant->id],
            array_merge($scores, ['total_score' => round($finalScore, 2)])
        );
    }

    /**
     * Calculate rankings - OPTIMIZED VERSION
     * Top 10 per city, Top 10 per state, Top 100 national
     */
    public function calculateRankings(?int $year = null): array
    {
        $year = $year ?? now()->year;
        
        $results = ['city' => 0, 'state' => 0, 'national' => 0];

        // Delete old rankings for this year first
        RestaurantRanking::where('year', $year)->delete();

        // CITY RANKINGS - Top 10 per city (using raw queries for efficiency)
        $cityScores = DB::table('restaurants as r')
            ->join('restaurant_scores as s', 'r.id', '=', 's.restaurant_id')
            ->join('states as st', 'r.state_id', '=', 'st.id')
            ->where('r.is_active', true)
            ->where('r.status', 'approved')
            ->whereNotNull('r.city')
            ->select('r.id', 'r.city', 'r.state_id', 'st.code as state_code', 's.total_score')
            ->orderByDesc('s.total_score')
            ->get();

        $cityCounts = [];
        foreach ($cityScores as $row) {
            $cityKey = $row->city . '_' . $row->state_id;
            $cityCounts[$cityKey] = ($cityCounts[$cityKey] ?? 0) + 1;
            
            if ($cityCounts[$cityKey] <= 10) {
                RestaurantRanking::create([
                    'restaurant_id' => $row->id,
                    'year' => $year,
                    'ranking_type' => 'city',
                    'ranking_scope' => $row->city,
                    'position' => $cityCounts[$cityKey],
                    'final_score' => $row->total_score,
                    'badge_name' => $this->generateBadgeName($cityCounts[$cityKey], 'city', $row->city, $year),
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                $results['city']++;
            }
        }
        unset($cityScores, $cityCounts);

        // STATE RANKINGS - Top 10 per state
        $states = State::pluck('name', 'code');
        
        foreach ($states as $code => $name) {
            $stateTop10 = DB::table('restaurants as r')
                ->join('restaurant_scores as s', 'r.id', '=', 's.restaurant_id')
                ->join('states as st', 'r.state_id', '=', 'st.id')
                ->where('st.code', $code)
                ->where('r.is_active', true)
                ->where('r.status', 'approved')
                ->select('r.id', 's.total_score')
                ->orderByDesc('s.total_score')
                ->limit(10)
                ->get();

            $position = 1;
            foreach ($stateTop10 as $row) {
                RestaurantRanking::create([
                    'restaurant_id' => $row->id,
                    'year' => $year,
                    'ranking_type' => 'state',
                    'ranking_scope' => $code,
                    'position' => $position,
                    'final_score' => $row->total_score,
                    'badge_name' => $this->generateBadgeName($position, 'state', $name, $year),
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                $results['state']++;
                $position++;
            }
        }

        // NATIONAL RANKINGS - Top 100
        $nationalTop100 = DB::table('restaurants as r')
            ->join('restaurant_scores as s', 'r.id', '=', 's.restaurant_id')
            ->where('r.is_active', true)
            ->where('r.status', 'approved')
            ->select('r.id', 's.total_score')
            ->orderByDesc('s.total_score')
            ->limit(100)
            ->get();

        $position = 1;
        foreach ($nationalTop100 as $row) {
            RestaurantRanking::create([
                'restaurant_id' => $row->id,
                'year' => $year,
                'ranking_type' => 'national',
                'ranking_scope' => 'usa',
                'position' => $position,
                'final_score' => $row->total_score,
                'badge_name' => $this->generateBadgeName($position, 'national', 'USA', $year),
                'is_published' => true,
                'published_at' => now(),
            ]);
            $results['national']++;
            $position++;
        }

        return $results;
    }

    protected function generateBadgeName(int $position, string $scope, ?string $location, int $year): string
    {
        if ($position == 1) {
            return "#1 Mejor Restaurante Mexicano - " . $location . " " . $year;
        } elseif ($position <= 3) {
            return "Top 3 " . $location . " " . $year;
        } elseif ($position <= 5) {
            return "Top 5 " . $location . " " . $year;
        } elseif ($position <= 10) {
            return "Top 10 " . $location . " " . $year;
        } elseif ($position <= 25) {
            return "Top 25 " . $location . " " . $year;
        } elseif ($position <= 50) {
            return "Top 50 " . $location . " " . $year;
        } else {
            return "Top 100 " . $location . " " . $year;
        }
    }

    protected function getWeights(): array
    {
        return [
            'google_score' => 0.12,
            'yelp_score' => 0.10,
            'facebook_score' => 0.08,
            'tripadvisor_score' => 0.10,
            'famer_rating_score' => 0.15,
            'review_count_score' => 0.08,
            'verified_reviews_score' => 0.08,
            'response_rate_score' => 0.07,
            'photo_count_score' => 0.07,
            'completeness_score' => 0.08,
            'activity_score' => 0.07,
        ];
    }

    protected function calculateFamerScore(Restaurant $restaurant): float
    {
        return ($restaurant->average_rating ?? 0) * 20;
    }

    protected function calculateReviewCountScore(Restaurant $restaurant): float
    {
        $reviews = $restaurant->total_reviews ?? 0;
        return min(100, log10($reviews + 1) * 50);
    }

    protected function calculateVerifiedReviewsScore(Restaurant $restaurant): float
    {
        $total = $restaurant->reviews()->count();
        if ($total == 0) return 0;
        $verified = $restaurant->reviews()->whereHas('checkIn')->count();
        return ($verified / $total) * 100;
    }

    protected function calculateResponseRateScore(Restaurant $restaurant): float
    {
        $total = $restaurant->reviews()->count();
        if ($total == 0) return 0;
        $responded = $restaurant->reviews()->whereNotNull('owner_response')->count();
        return ($responded / $total) * 100;
    }

    protected function calculatePhotoScore(Restaurant $restaurant): float
    {
        $ownerPhotos = $restaurant->getMedia('images')->count();
        $userPhotos = $restaurant->approvedPhotos()->count();
        return min(100, ($ownerPhotos + $userPhotos) * 5);
    }

    protected function calculateCompletenessScore(Restaurant $restaurant): float
    {
        $fields = [
            'description' => 15, 'phone' => 10, 'email' => 5, 'website' => 10,
            'address' => 10, 'hours' => 15, 'price_range' => 5, 'mexican_region' => 5,
            'dietary_options' => 5, 'special_features' => 10, 'image' => 10,
        ];

        $score = 0;
        foreach ($fields as $field => $weight) {
            if ($field === 'hours') {
                $score += (!empty($restaurant->hours) ? $weight : 0);
            } elseif ($field === 'dietary_options' || $field === 'special_features') {
                $score += (!empty($restaurant->$field) && count($restaurant->$field) > 0 ? $weight : 0);
            } elseif ($field === 'image') {
                $score += ($restaurant->getMedia('logo')->count() > 0 ? $weight : 0);
            } else {
                $score += (!empty($restaurant->$field) ? $weight : 0);
            }
        }
        return $score;
    }

    protected function calculateActivityScore(Restaurant $restaurant): float
    {
        $score = 0;
        try { if (method_exists($restaurant, "activeCoupons") && $restaurant->activeCoupons()->count() > 0) $score += 25; } catch (\Exception $e) {}
        if ($restaurant->accepts_reservations) $score += 25;
        try { if (method_exists($restaurant, "menuCategories") && $restaurant->menuCategories()->count() > 0) $score += 25; } catch (\Exception $e) {}
        if ($restaurant->updated_at && $restaurant->updated_at->diffInDays(now()) <= 30) $score += 25;
        return $score;
    }
}
