<?php

namespace App\Services;

use App\Models\ApiCallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FoursquareService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl = 'https://api.foursquare.com/v2';
    protected string $apiVersion = '20231010';
    protected int $dailyLimit;
    protected string $cacheKey = 'foursquare_daily_calls';

    public function __construct()
    {
        $this->clientId     = config('services.foursquare.client_id', '');
        $this->clientSecret = config('services.foursquare.client_secret', '');
        $this->dailyLimit   = (int) config('services.foursquare.daily_limit', 500);
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
        Cache::put($this->cacheKey, $calls + 1, strtotime('tomorrow') - time());
    }

    protected function logApiCall(string $endpoint, bool $success, ?int $statusCode = null, array $params = [], ?string $error = null): void
    {
        try {
            ApiCallLog::create([
                'service'       => 'foursquare',
                'endpoint'      => $endpoint,
                'status_code'   => $statusCode ?? ($success ? 200 : 500),
                'success'       => $success,
                'cost'          => 0,
                'params'        => $params,
                'error_message' => $error,
                'called_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // silent
        }
    }

    protected function makeRequest(string $endpoint, array $params = []): ?array
    {
        $params['client_id']     = $this->clientId;
        $params['client_secret'] = $this->clientSecret;
        $params['v']             = $this->apiVersion;

        try {
            $response = Http::get($this->baseUrl . $endpoint, $params);

            $this->incrementCallCount();
            $this->logApiCall($endpoint, $response->successful(), $response->status(), array_diff_key($params, array_flip(['client_id', 'client_secret'])));

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Foursquare API error', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Foursquare API exception: ' . $e->getMessage());
            $this->logApiCall($endpoint, false, 500, [], $e->getMessage());
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
            'query'      => $name,
            'categoryId' => '4d4b7105d754a06374d81259', // Food category
            'limit'      => 5,
            'intent'     => 'match',
        ];

        if ($lat && $lng) {
            $params['ll']     = "{$lat},{$lng}";
            $params['radius'] = 5000;
        } elseif ($city && $state) {
            $params['near'] = "{$city}, {$state}";
        }

        $data = $this->makeRequest('/venues/search', $params);

        if ($data && !empty($data['response']['venues'])) {
            return $this->findBestMatch($name, $address, $data['response']['venues']);
        }

        return null;
    }

    public function getPlaceDetails(string $venueId): ?array
    {
        if (!$this->canMakeCall()) {
            return null;
        }

        $data = $this->makeRequest('/venues/' . $venueId);

        return $data['response']['venue'] ?? null;
    }

    protected function findBestMatch(string $name, ?string $address, array $venues): ?array
    {
        $name      = strtolower(trim($name));
        $bestMatch = null;
        $bestScore = 0;

        foreach ($venues as $venue) {
            $score      = 0;
            $venueName  = strtolower($venue['name'] ?? '');

            if ($venueName === $name) {
                $score += 100;
            } elseif (str_contains($venueName, $name) || str_contains($name, $venueName)) {
                $score += 50;
            } else {
                similar_text($name, $venueName, $percent);
                if ($percent > 60) {
                    $score += $percent * 0.5;
                }
            }

            if ($address && !empty($venue['location']['address'])) {
                $venueAddr = strtolower($venue['location']['address']);
                $addrFirst = strtolower(explode(' ', $address)[0] ?? '');
                if (str_contains($venueAddr, $addrFirst)) {
                    $score += 30;
                }
            }

            if (!empty($venue['rating'])) {
                $score += 10;
            }

            if ($score > $bestScore && $score >= 40) {
                $bestScore = $score;
                $bestMatch = $venue;
            }
        }

        return $bestMatch;
    }

    public function enrichRestaurant($restaurant): ?array
    {
        $venue = $this->searchPlace(
            $restaurant->name,
            $restaurant->address,
            $restaurant->city,
            $restaurant->state?->code,
            $restaurant->latitude,
            $restaurant->longitude
        );

        if (!$venue) {
            return null;
        }

        $venueId = $venue['id'];
        $details = $this->getPlaceDetails($venueId);

        $data = [
            'foursquare_id' => $venueId,
        ];

        $source = $details ?? $venue;

        $data['foursquare_rating']      = isset($source['rating']) ? round($source['rating'], 1) : null;
        $data['foursquare_checkins']    = $source['stats']['checkinsCount'] ?? null;
        $data['foursquare_tips_count']  = $source['stats']['tipCount'] ?? null;
        $data['foursquare_price']       = $source['price']['tier'] ?? null;

        if (empty($restaurant->phone) && !empty($source['contact']['phone'])) {
            $data['phone'] = $source['contact']['phone'];
        }

        if (empty($restaurant->website) && !empty($source['url'])) {
            $data['website'] = $source['url'];
        }

        return $data;
    }
}
