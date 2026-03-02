<?php

namespace App\Services;

use App\Models\ApiCallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TripAdvisorService
{
    protected ?string $apiKey = null;
    protected string $baseUrl = 'https://api.content.tripadvisor.com/api/v1';
    protected int $dailyLimit;
    protected string $cacheKey = 'tripadvisor_daily_calls';

    public function __construct()
    {
        $this->apiKey = config('services.tripadvisor.api_key', '');
        $this->dailyLimit = (int) config('services.tripadvisor.daily_limit', 160);
    }

    /**
     * Check if we can make more API calls today
     */
    public function canMakeCall(): bool
    {
        $calls = Cache::get($this->cacheKey, 0);
        return $calls < $this->dailyLimit;
    }

    /**
     * Get remaining calls for today
     */
    public function getRemainingCalls(): int
    {
        $calls = Cache::get($this->cacheKey, 0);
        return max(0, $this->dailyLimit - $calls);
    }

    /**
     * Increment the daily call counter
     */
    protected function incrementCallCount(): void
    {
        $calls = Cache::get($this->cacheKey, 0);
        // Cache until end of day (UTC)
        $secondsUntilMidnight = strtotime('tomorrow') - time();
        Cache::put($this->cacheKey, $calls + 1, $secondsUntilMidnight);
    }

    /**
     * Log API call to api_call_logs for dashboard visibility
     */
    protected function logApiCall(string $endpoint, bool $success, ?int $statusCode = null, array $params = [], ?string $error = null): void
    {
        try {
            ApiCallLog::create([
                'service' => 'tripadvisor',
                'endpoint' => $endpoint,
                'status_code' => $statusCode ?? ($success ? 200 : 500),
                'success' => $success,
                'cost' => 0, // TripAdvisor free tier
                'params' => $params,
                'error_message' => $error,
                'called_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if logging fails
        }
    }

    /**
     * Search for a location by name and address
     */
    public function searchLocation(string $name, string $address, ?string $city = null, ?string $state = null): ?array
    {
        if (!$this->canMakeCall()) {
            Log::warning('TripAdvisor: Daily limit reached');
            return null;
        }

        try {
            $searchQuery = $name;
            if ($city) {
                $searchQuery .= ' ' . $city;
            }
            if ($state) {
                $searchQuery .= ' ' . $state;
            }

            $response = Http::get($this->baseUrl . '/location/search', [
                'key' => $this->apiKey,
                'searchQuery' => $searchQuery,
                'category' => 'restaurants',
                'language' => 'en',
            ]);

            $this->incrementCallCount();
            
            // Log to api_call_logs
            $this->logApiCall('/location/search', $response->successful(), $response->status(), ['query' => $searchQuery]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['data'])) {
                    // Find best match by comparing address
                    foreach ($data['data'] as $location) {
                        $locationAddress = strtolower($location['address_obj']['street1'] ?? '');
                        if (str_contains($locationAddress, strtolower(explode(' ', $address)[0] ?? ''))) {
                            return $location;
                        }
                    }
                    // Return first result if no address match
                    return $data['data'][0];
                }
            }

            Log::debug('TripAdvisor search failed', [
                'query' => $searchQuery,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('TripAdvisor search error: ' . $e->getMessage());
            $this->logApiCall('/location/search', false, 500, ['query' => $name], $e->getMessage());
            return null;
        }
    }

    /**
     * Get location details including rating and review count
     */
    public function getLocationDetails(string $locationId): ?array
    {
        if (!$this->canMakeCall()) {
            Log::warning('TripAdvisor: Daily limit reached');
            return null;
        }

        try {
            $response = Http::get($this->baseUrl . '/location/' . $locationId . '/details', [
                'key' => $this->apiKey,
                'language' => 'en',
                'currency' => 'USD',
            ]);

            $this->incrementCallCount();
            
            // Log to api_call_logs
            $this->logApiCall('/location/details', $response->successful(), $response->status(), ['location_id' => $locationId]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::debug('TripAdvisor details failed', [
                'location_id' => $locationId,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('TripAdvisor details error: ' . $e->getMessage());
            $this->logApiCall('/location/details', false, 500, ['location_id' => $locationId], $e->getMessage());
            return null;
        }
    }

    /**
     * Get reviews for a location (up to 5)
     */
    public function getLocationReviews(string $locationId): ?array
    {
        if (!$this->canMakeCall()) {
            Log::warning('TripAdvisor: Daily limit reached');
            return null;
        }

        try {
            $response = Http::get($this->baseUrl . '/location/' . $locationId . '/reviews', [
                'key' => $this->apiKey,
                'language' => 'en',
            ]);

            $this->incrementCallCount();
            
            // Log to api_call_logs
            $this->logApiCall('/location/reviews', $response->successful(), $response->status(), ['location_id' => $locationId]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('TripAdvisor reviews error: ' . $e->getMessage());
            $this->logApiCall('/location/reviews', false, 500, ['location_id' => $locationId], $e->getMessage());
            return null;
        }
    }

    /**
     * Enrich a restaurant with TripAdvisor data
     * Returns array with tripadvisor data or null
     */
    public function enrichRestaurant($restaurant): ?array
    {
        // Search for the restaurant
        $location = $this->searchLocation(
            $restaurant->name,
            $restaurant->address,
            $restaurant->city,
            $restaurant->state?->code
        );

        if (!$location) {
            return null;
        }

        $locationId = $location['location_id'];

        // Get detailed information
        $details = $this->getLocationDetails($locationId);

        if (!$details) {
            return [
                'tripadvisor_id' => $locationId,
                'tripadvisor_url' => $location['web_url'] ?? null,
            ];
        }

        return [
            'tripadvisor_id' => $locationId,
            'tripadvisor_url' => $details['web_url'] ?? $location['web_url'] ?? null,
            'tripadvisor_rating' => isset($details['rating']) ? (float) $details['rating'] : null,
            'tripadvisor_reviews_count' => isset($details['num_reviews']) ? (int) $details['num_reviews'] : null,
            'tripadvisor_ranking' => $details['ranking_data']['ranking'] ?? null,
            'tripadvisor_price_level' => $details['price_level'] ?? null,
        ];
    }
}
