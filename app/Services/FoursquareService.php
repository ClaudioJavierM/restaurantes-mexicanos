<?php

namespace App\Services;

use App\Models\ApiCallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FoursquareService
{
    protected ?string $apiKey = null;
    protected string $baseUrl = 'https://places-api.foursquare.com';
    protected int $dailyLimit;
    protected string $cacheKey = 'foursquare_daily_calls';

    public function __construct()
    {
        $this->apiKey = config('services.foursquare.api_key', '');
        $this->dailyLimit = (int) config('services.foursquare.daily_limit', 500);
    }

    public function canMakeCall(): bool
    {
        $calls = Cache::get($this->cacheKey, 0);
        return $calls < $this->dailyLimit;
    }

    public function getRemainingCalls(): int
    {
        $calls = Cache::get($this->cacheKey, 0);
        return max(0, $this->dailyLimit - $calls);
    }

    protected function incrementCallCount(): void
    {
        $calls = Cache::get($this->cacheKey, 0);
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
                'service' => 'foursquare',
                'endpoint' => $endpoint,
                'status_code' => $statusCode ?? ($success ? 200 : 500),
                'success' => $success,
                'cost' => 0, // Foursquare free tier
                'params' => $params,
                'error_message' => $error,
                'called_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if logging fails
        }
    }

    protected function makeRequest(string $endpoint, array $params = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'X-Places-Api-Version' => '2025-06-17',
            ])->get($this->baseUrl . $endpoint, $params);

            $this->incrementCallCount();
            
            // Log to api_call_logs
            $this->logApiCall($endpoint, $response->successful(), $response->status(), $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Foursquare API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Foursquare API exception: ' . $e->getMessage());
            $this->logApiCall($endpoint, false, 500, $params, $e->getMessage());
            return null;
        }
    }

    public function searchPlace(string $name, ?string $address = null, ?string $city = null, ?string $state = null, ?float $lat = null, ?float $lng = null): ?array
    {
        if (!$this->canMakeCall()) {
            Log::warning('Foursquare: Daily limit reached');
            return null;
        }

        $params = [
            'query' => $name,
            'categories' => '13065', // Restaurants
            'limit' => 5,
        ];

        if ($lat && $lng) {
            $params['ll'] = "{$lat},{$lng}";
            $params['radius'] = 5000;
        } elseif ($city && $state) {
            $params['near'] = "{$city}, {$state}";
        }

        $data = $this->makeRequest('/places/search', $params);

        if ($data && !empty($data['results'])) {
            return $this->findBestMatch($name, $address, $data['results']);
        }

        return null;
    }

    public function getPlaceDetails(string $fsqId): ?array
    {
        if (!$this->canMakeCall()) {
            return null;
        }

        return $this->makeRequest('/places/' . $fsqId, [
            'fields' => 'fsq_place_id,name,location,rating,stats,price,hours,website,tel,categories',
        ]);
    }

    protected function findBestMatch(string $name, ?string $address, array $results): ?array
    {
        $name = strtolower(trim($name));
        $bestMatch = null;
        $bestScore = 0;

        foreach ($results as $result) {
            $score = 0;
            $resultName = strtolower($result['name'] ?? '');

            if ($resultName === $name) {
                $score += 100;
            } elseif (str_contains($resultName, $name) || str_contains($name, $resultName)) {
                $score += 50;
            } else {
                similar_text($name, $resultName, $percent);
                if ($percent > 60) {
                    $score += $percent * 0.5;
                }
            }

            if ($address && isset($result['location']['address'])) {
                $resultAddr = strtolower($result['location']['address']);
                $addrFirst = strtolower(explode(' ', $address)[0] ?? '');
                if (str_contains($resultAddr, $addrFirst)) {
                    $score += 30;
                }
            }

            if (!empty($result['rating'])) {
                $score += 10;
            }

            if ($score > $bestScore && $score >= 40) {
                $bestScore = $score;
                $bestMatch = $result;
            }
        }

        return $bestMatch;
    }

    public function enrichRestaurant($restaurant): ?array
    {
        $place = $this->searchPlace(
            $restaurant->name,
            $restaurant->address,
            $restaurant->city,
            $restaurant->state?->code,
            $restaurant->latitude,
            $restaurant->longitude
        );

        if (!$place) {
            return null;
        }

        $fsqId = $place['fsq_place_id'];
        $details = $this->getPlaceDetails($fsqId);

        $data = [
            'foursquare_id' => $fsqId,
        ];

        if ($details) {
            $data['foursquare_rating'] = isset($details['rating']) ? round($details['rating'], 1) : null;
            $data['foursquare_checkins'] = $details['stats']['total_checkins'] ?? null;
            $data['foursquare_tips_count'] = $details['stats']['total_tips'] ?? null;
            $data['foursquare_price'] = $details['price'] ?? null;

            if (empty($restaurant->phone) && !empty($details['tel'])) {
                $data['phone'] = $details['tel'];
            }

            if (empty($restaurant->website) && !empty($details['website'])) {
                $data['website'] = $details['website'];
            }
        }

        return $data;
    }
}
