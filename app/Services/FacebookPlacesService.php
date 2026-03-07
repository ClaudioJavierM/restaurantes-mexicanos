<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FacebookPlacesService
{
    protected string $appId;
    protected string $appSecret;
    protected string $accessToken;
    protected string $apiVersion = 'v18.0';
    protected string $baseUrl = 'https://graph.facebook.com';

    public function __construct()
    {
        $this->appId = config('services.facebook.client_id') ?? '';
        $this->appSecret = config('services.facebook.client_secret') ?? '';
        $this->accessToken = $this->appId . '|' . $this->appSecret;
    }

    /**
     * Search for a Facebook page by restaurant name and location
     */
    public function searchPlace(string $name, ?string $city = null, ?string $state = null, ?float $lat = null, ?float $lng = null): ?array
    {
        $query = $name;
        if ($city) {
            $query .= ' ' . $city;
        }
        if ($state) {
            $query .= ' ' . $state;
        }

        $params = [
            'type' => 'place',
            'q' => $query,
            'fields' => 'id,name,location,phone,website,hours,overall_star_rating,rating_count,category_list,link,is_verified',
            'access_token' => $this->accessToken,
        ];

        // Add center coordinates if available for better results
        if ($lat && $lng) {
            $params['center'] = "{$lat},{$lng}";
            $params['distance'] = 5000; // 5km radius
        }

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$this->apiVersion}/search", $params);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['data'])) {
                    // Try to find the best match
                    return $this->findBestMatch($name, $city, $data['data']);
                }
            } else {
                Log::warning('Facebook Places API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'query' => $query,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Facebook Places API exception', [
                'message' => $e->getMessage(),
                'query' => $query,
            ]);
        }

        return null;
    }

    /**
     * Get detailed information about a Facebook page
     */
    public function getPageDetails(string $pageId): ?array
    {
        $fields = implode(',', [
            'id',
            'name',
            'location',
            'phone',
            'website',
            'hours',
            'overall_star_rating',
            'rating_count',
            'category_list',
            'link',
            'is_verified',
            'single_line_address',
            'about',
            'description',
        ]);

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/{$this->apiVersion}/{$pageId}", [
                'fields' => $fields,
                'access_token' => $this->accessToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::warning('Facebook Page Details API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'page_id' => $pageId,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Facebook Page Details API exception', [
                'message' => $e->getMessage(),
                'page_id' => $pageId,
            ]);
        }

        return null;
    }

    /**
     * Enrich a restaurant with Facebook data
     */
    public function enrichRestaurant(Restaurant $restaurant): bool
    {
        // Skip if already enriched recently (within 30 days)
        if ($restaurant->facebook_enriched_at && $restaurant->facebook_enriched_at->gt(now()->subDays(30))) {
            return false;
        }

        // Search for the restaurant on Facebook
        $result = $this->searchPlace(
            $restaurant->name,
            $restaurant->city,
            $restaurant->state?->code,
            $restaurant->latitude,
            $restaurant->longitude
        );

        if (!$result) {
            // Mark as attempted even if not found
            $restaurant->facebook_enriched_at = now();
            $restaurant->save();
            return false;
        }

        // Update restaurant with Facebook data
        $restaurant->facebook_page_id = $result['id'];
        $restaurant->facebook_url = $result['link'] ?? "https://facebook.com/{$result['id']}";
        $restaurant->facebook_rating = $result['overall_star_rating'] ?? null;
        $restaurant->facebook_review_count = $result['rating_count'] ?? null;
        $restaurant->facebook_hours = $result['hours'] ?? null;
        $restaurant->facebook_enriched_at = now();

        // Update phone if missing
        if (empty($restaurant->phone) && !empty($result['phone'])) {
            $restaurant->phone = $result['phone'];
        }

        // Update website if missing
        if (empty($restaurant->website) && !empty($result['website'])) {
            $restaurant->website = $result['website'];
        }

        $restaurant->save();

        Log::info('Restaurant enriched with Facebook data', [
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'facebook_page_id' => $result['id'],
            'facebook_rating' => $result['overall_star_rating'] ?? null,
        ]);

        return true;
    }

    /**
     * Find the best matching result from search results
     */
    protected function findBestMatch(string $name, ?string $city, array $results): ?array
    {
        $name = strtolower(trim($name));
        $city = $city ? strtolower(trim($city)) : null;

        $bestMatch = null;
        $bestScore = 0;

        foreach ($results as $result) {
            $score = 0;
            $resultName = strtolower($result['name'] ?? '');

            // Exact name match
            if ($resultName === $name) {
                $score += 100;
            }
            // Name contains search term
            elseif (str_contains($resultName, $name) || str_contains($name, $resultName)) {
                $score += 50;
            }
            // Similar name (Levenshtein)
            else {
                $similarity = 1 - (levenshtein($name, $resultName) / max(strlen($name), strlen($resultName)));
                if ($similarity > 0.7) {
                    $score += $similarity * 40;
                }
            }

            // City match
            if ($city && isset($result['location']['city'])) {
                $resultCity = strtolower($result['location']['city']);
                if ($resultCity === $city) {
                    $score += 30;
                } elseif (str_contains($resultCity, $city) || str_contains($city, $resultCity)) {
                    $score += 15;
                }
            }

            // Has rating (indicates active business)
            if (!empty($result['overall_star_rating'])) {
                $score += 10;
            }

            // Is verified
            if (!empty($result['is_verified'])) {
                $score += 20;
            }

            // Check if it's a restaurant category
            if (!empty($result['category_list'])) {
                foreach ($result['category_list'] as $category) {
                    $catName = strtolower($category['name'] ?? '');
                    if (str_contains($catName, 'restaurant') || str_contains($catName, 'mexican') ||
                        str_contains($catName, 'food') || str_contains($catName, 'taco')) {
                        $score += 15;
                        break;
                    }
                }
            }

            if ($score > $bestScore && $score >= 50) { // Minimum threshold
                $bestScore = $score;
                $bestMatch = $result;
            }
        }

        return $bestMatch;
    }

    /**
     * Batch enrich restaurants
     */
    public function enrichBatch(int $limit = 100, bool $onlyWithoutFacebook = true): array
    {
        $query = Restaurant::where('status', 'approved');

        if ($onlyWithoutFacebook) {
            $query->whereNull('facebook_page_id')
                  ->where(function ($q) {
                      $q->whereNull('facebook_enriched_at')
                        ->orWhere('facebook_enriched_at', '<', now()->subDays(30));
                  });
        }

        $restaurants = $query->limit($limit)->get();

        $results = [
            'processed' => 0,
            'enriched' => 0,
            'failed' => 0,
        ];

        foreach ($restaurants as $restaurant) {
            $results['processed']++;

            // Rate limiting - Facebook allows 200 calls per hour per user
            // Being conservative with 1 call per 2 seconds
            usleep(500000); // 0.5 seconds between calls

            try {
                if ($this->enrichRestaurant($restaurant)) {
                    $results['enriched']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                Log::error('Error enriching restaurant', [
                    'restaurant_id' => $restaurant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }
}
