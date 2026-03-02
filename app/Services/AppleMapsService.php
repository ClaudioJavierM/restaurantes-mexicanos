<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Firebase\JWT\JWT;

class AppleMapsService
{
    protected string $keyId;
    protected string $teamId;
    protected string $privateKeyPath;
    protected string $baseUrl = 'https://maps-api.apple.com/v1';
    protected int $dailyLimit;
    protected string $cacheKey = 'apple_maps_daily_calls';
    protected ?string $token = null;

    public function __construct()
    {
        $this->keyId = config('services.apple_maps.key_id');
        $this->teamId = config('services.apple_maps.team_id');
        $this->privateKeyPath = config('services.apple_maps.private_key_path');
        $this->dailyLimit = (int) config('services.apple_maps.daily_limit', 500);
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

    protected function getToken(): string
    {
        // Cache token for 30 minutes (tokens are valid for 1 hour max)
        $cacheKey = 'apple_maps_jwt_token';
        
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        $privateKey = file_get_contents($this->privateKeyPath);
        
        $header = [
            'alg' => 'ES256',
            'kid' => $this->keyId,
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $this->teamId,
            'iat' => $now,
            'exp' => $now + 3600, // 1 hour
        ];

        $token = JWT::encode($payload, $privateKey, 'ES256', $this->keyId);
        
        Cache::put($cacheKey, $token, 1800); // 30 minutes
        
        return $token;
    }

    protected function makeRequest(string $endpoint, array $params = []): ?array
    {
        try {
            $token = $this->getToken();
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . $endpoint, $params);

            $this->incrementCallCount();

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Apple Maps API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Apple Maps API exception: ' . $e->getMessage());
            return null;
        }
    }

    public function searchPlace(string $name, ?string $address = null, ?string $city = null, ?string $state = null, ?float $lat = null, ?float $lng = null): ?array
    {
        if (!$this->canMakeCall()) {
            Log::warning('Apple Maps: Daily limit reached');
            return null;
        }

        $query = $name;
        if ($city) $query .= ', ' . $city;
        if ($state) $query .= ', ' . $state;

        $params = [
            'q' => $query,
            'limitToCountries' => 'US,MX',
            'resultTypeFilter' => 'Poi',
            'poiFilter' => 'Restaurant',
        ];

        if ($lat && $lng) {
            $params["searchLocation"] = "$lat,$lng";
        }

        $data = $this->makeRequest('/search', $params);

        if ($data && !empty($data['results'])) {
            return $this->findBestMatch($name, $address, $data['results']);
        }

        return null;
    }

    public function getPlaceDetails(string $placeId): ?array
    {
        if (!$this->canMakeCall()) {
            return null;
        }

        return $this->makeRequest('/place/' . $placeId);
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

            if ($address && isset($result['formattedAddressLines'])) {
                $resultAddr = strtolower(implode(' ', $result['formattedAddressLines']));
                $addrFirst = strtolower(explode(' ', $address)[0] ?? '');
                if (str_contains($resultAddr, $addrFirst)) {
                    $score += 30;
                }
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

        $data = [
            'apple_maps_id' => $place['id'] ?? $place['muid'] ?? null,
            'apple_maps_url' => $place['urls']['appleMaps'] ?? null,
        ];

        // Update phone if missing
        if (empty($restaurant->phone) && !empty($place['telephone'])) {
            $data['phone'] = $place['telephone'];
        }

        // Update website if missing
        if (empty($restaurant->website) && !empty($place['urls']['website'])) {
            $data['website'] = $place['urls']['website'];
        }

        return $data;
    }
}
