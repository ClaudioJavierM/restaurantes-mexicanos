<?php

namespace App\Services\ExternalRatings;

use App\Models\Restaurant;
use App\Models\ExternalRating;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://maps.googleapis.com/maps/api/place';

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key', '');
    }

    /**
     * Find a place by name and address
     */
    public function findPlace(string $name, string $address, ?string $city = null): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Google Places API key not configured');
            return null;
        }

        $query = $name . ' ' . $address;
        if ($city) {
            $query .= ' ' . $city;
        }

        try {
            $response = Http::get($this->baseUrl . '/findplacefromtext/json', [
                'input' => $query,
                'inputtype' => 'textquery',
                'fields' => 'place_id,name,formatted_address,rating,user_ratings_total,business_status,types',
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates']) && count($data['candidates']) > 0) {
                    return $data['candidates'][0];
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Places API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get place details by place_id
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::get($this->baseUrl . '/details/json', [
                'place_id' => $placeId,
                'fields' => 'place_id,name,formatted_address,rating,user_ratings_total,reviews,business_status,url,website,formatted_phone_number,price_level,opening_hours',
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result'])) {
                    return $data['result'];
                }
            }
        } catch (\Exception $e) {
            Log::error('Google Places Details API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Sync Google rating for a restaurant
     */
    public function syncRating(Restaurant $restaurant): ?ExternalRating
    {
        // First try to use existing place_id
        $placeId = $restaurant->google_place_id;

        // If no place_id, try to find the place
        if (empty($placeId)) {
            $place = $this->findPlace(
                $restaurant->name,
                $restaurant->address,
                $restaurant->city
            );

            if ($place && isset($place['place_id'])) {
                $placeId = $place['place_id'];
                
                // Save the place_id for future use
                $restaurant->update(['google_place_id' => $placeId]);
            }
        }

        if (empty($placeId)) {
            return null;
        }

        // Get full details
        $details = $this->getPlaceDetails($placeId);

        if (!$details) {
            return null;
        }

        // Create or update external rating
        return ExternalRating::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'platform' => 'google',
            ],
            [
                'external_id' => $placeId,
                'rating' => $details['rating'] ?? null,
                'review_count' => $details['user_ratings_total'] ?? 0,
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
                $lastRating = $restaurant->getExternalRating('google');
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

            // Rate limiting - Google allows ~10 QPS for Places API
            usleep(150000); // 150ms delay
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
