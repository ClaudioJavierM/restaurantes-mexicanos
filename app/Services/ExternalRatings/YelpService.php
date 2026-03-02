<?php

namespace App\Services\ExternalRatings;

use App\Models\Restaurant;
use App\Models\ExternalRating;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YelpService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.yelp.com/v3';

    public function __construct()
    {
        $this->apiKey = config('services.yelp.api_key', '');
    }

    /**
     * Search for a business by name and location
     */
    public function searchBusiness(string $name, string $address, ?string $city = null, ?string $state = null): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Yelp API key not configured');
            return null;
        }

        $location = $address;
        if ($city) {
            $location .= ', ' . $city;
        }
        if ($state) {
            $location .= ', ' . $state;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/businesses/search', [
                'term' => $name,
                'location' => $location,
                'categories' => 'mexican,restaurants',
                'limit' => 5,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['businesses']) && count($data['businesses']) > 0) {
                    // Find best match by name similarity
                    return $this->findBestMatch($name, $data['businesses']);
                }
            }
        } catch (\Exception $e) {
            Log::error('Yelp API search error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Find the best matching business from search results
     */
    protected function findBestMatch(string $searchName, array $businesses): ?array
    {
        $searchName = strtolower($searchName);
        $bestMatch = null;
        $highestScore = 0;

        foreach ($businesses as $business) {
            $businessName = strtolower($business['name']);
            
            // Calculate similarity score
            similar_text($searchName, $businessName, $percent);
            
            if ($percent > $highestScore && $percent > 50) {
                $highestScore = $percent;
                $bestMatch = $business;
            }
        }

        return $bestMatch ?? $businesses[0]; // Return first result if no good match
    }

    /**
     * Get business details by Yelp ID
     */
    public function getBusinessDetails(string $yelpId): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/businesses/' . $yelpId);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Yelp API details error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Sync Yelp rating for a restaurant
     */
    public function syncRating(Restaurant $restaurant): ?ExternalRating
    {
        // First try to use existing yelp_id
        $yelpId = $restaurant->yelp_id;

        // If no yelp_id, try to find the business
        if (empty($yelpId)) {
            $business = $this->searchBusiness(
                $restaurant->name,
                $restaurant->address,
                $restaurant->city,
                $restaurant->state?->code
            );

            if ($business && isset($business['id'])) {
                $yelpId = $business['id'];
                
                // Save the yelp_id for future use
                $restaurant->update([
                    'yelp_id' => $yelpId,
                    'yelp_url' => $business['url'] ?? null,
                ]);
            }
        }

        if (empty($yelpId)) {
            return null;
        }

        // Get full details
        $details = $this->getBusinessDetails($yelpId);

        if (!$details) {
            return null;
        }

        // Update restaurant with Yelp data
        $restaurant->update([
            'yelp_rating' => $details['rating'] ?? null,
            'yelp_reviews_count' => $details['review_count'] ?? 0,
            'yelp_url' => $details['url'] ?? null,
            'yelp_last_sync' => now(),
        ]);

        // Create or update external rating
        return ExternalRating::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'platform' => 'yelp',
            ],
            [
                'external_id' => $yelpId,
                'rating' => $details['rating'] ?? null,
                'review_count' => $details['review_count'] ?? 0,
                'profile_url' => $details['url'] ?? null,
                'raw_data' => $details,
                'fetched_at' => now(),
                'is_verified' => true,
            ]
        );
    }

    /**
     * Sync ratings for multiple restaurants
     */
    public function syncMultiple(iterable $restaurants, bool $forceRefresh = false): array
    {
        $results = [
            'synced' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        foreach ($restaurants as $restaurant) {
            // Skip if recently synced (within 24 hours) unless force refresh
            if (!$forceRefresh) {
                $lastRating = $restaurant->getExternalRating('yelp');
                if ($lastRating && $lastRating->fetched_at->diffInHours(now()) < 24) {
                    $results['skipped']++;
                    continue;
                }
            }

            $rating = $this->syncRating($restaurant);
            
            if ($rating) {
                $results['synced']++;
            } else {
                $results['failed']++;
            }

            // Rate limiting - Yelp allows ~5000/day (~3.5/min)
            usleep(250000); // 250ms delay
        }

        return $results;
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
