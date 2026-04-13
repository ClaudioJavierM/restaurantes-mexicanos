<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Get location data from IP address using free ip-api.com service
     * No API key required for basic usage (45 requests per minute)
     */
    public function getLocationFromIp(?string $ip = null): ?array
    {
        // When behind Cloudflare, request()->ip() returns the edge server IP (Dallas, TX).
        // CF-Connecting-IP contains the real visitor IP.
        $ip = $ip ?? request()->header('CF-Connecting-IP') ?? request()->ip();

        // Skip local/private IPs
        if ($this->isPrivateIp($ip)) {
            return null;
        }

        // Cache for 6 hours per IP (shorter to avoid stale Cloudflare edge IPs)
        $cacheKey = "geo_location_{$ip}";

        return Cache::remember($cacheKey, 21600, function () use ($ip) {
            try {
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,countryCode,region,regionName,city,zip,lat,lon,timezone',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'country_code' => $data['countryCode'] ?? null,
                            'state' => $data['regionName'] ?? null,
                            'state_code' => $data['region'] ?? null,
                            'city' => $data['city'] ?? null,
                            'zip' => $data['zip'] ?? null,
                            'lat' => $data['lat'] ?? null,
                            'lng' => $data['lon'] ?? null,
                            'timezone' => $data['timezone'] ?? null,
                            'source' => 'ip',
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("GeoLocation IP lookup failed: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Search for cities/locations in USA by query
     */
    public function searchLocations(string $query): array
    {
        $query = trim($query);

        if (strlen($query) < 2) {
            return [];
        }

        // Check if it's a zip code (5 digits)
        if (preg_match('/^\d{5}$/', $query)) {
            return $this->searchByZipCode($query);
        }

        // Search by city name
        return $this->searchByCityName($query);
    }

    /**
     * Search location by ZIP code
     */
    protected function searchByZipCode(string $zipCode): array
    {
        $cacheKey = "geo_zip_{$zipCode}";

        return Cache::remember($cacheKey, 604800, function () use ($zipCode) {
            try {
                // Using Zippopotam.us - free, no API key needed
                $response = Http::timeout(5)->get("https://api.zippopotam.us/us/{$zipCode}");

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['places'][0])) {
                        $place = $data['places'][0];
                        return [[
                            'city' => $place['place name'] ?? null,
                            'state' => $place['state'] ?? null,
                            'state_code' => $place['state code'] ?? null,
                            'zip' => $zipCode,
                            'lat' => (float) ($place['latitude'] ?? 0),
                            'lng' => (float) ($place['longitude'] ?? 0),
                            'display' => ($place['place name'] ?? '') . ', ' . ($place['state code'] ?? '') . ' ' . $zipCode,
                        ]];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ZIP code lookup failed: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Search location by city name
     * Uses our database of states and matches with common US cities
     */
    protected function searchByCityName(string $query): array
    {
        // Major US cities with Mexican restaurant presence
        $cities = $this->getUSCities();

        $results = [];
        $queryLower = strtolower($query);

        foreach ($cities as $city) {
            $cityLower = strtolower($city['city']);
            $stateLower = strtolower($city['state']);

            // Match by city name or state
            if (str_contains($cityLower, $queryLower) ||
                str_contains($stateLower, $queryLower) ||
                str_contains($city['state_code'], strtoupper($query))) {
                $results[] = [
                    'city' => $city['city'],
                    'state' => $city['state'],
                    'state_code' => $city['state_code'],
                    'lat' => $city['lat'],
                    'lng' => $city['lng'],
                    'display' => $city['city'] . ', ' . $city['state_code'],
                ];

                if (count($results) >= 10) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Calculate distance between two coordinates in miles
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 3959; // miles

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if IP is private/local
     */
    protected function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * Get list of major US cities
     */
    protected function getUSCities(): array
    {
        return [
            ['city' => 'Los Angeles', 'state' => 'California', 'state_code' => 'CA', 'lat' => 34.0522, 'lng' => -118.2437],
            ['city' => 'Houston', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 29.7604, 'lng' => -95.3698],
            ['city' => 'San Antonio', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 29.4241, 'lng' => -98.4936],
            ['city' => 'Chicago', 'state' => 'Illinois', 'state_code' => 'IL', 'lat' => 41.8781, 'lng' => -87.6298],
            ['city' => 'Phoenix', 'state' => 'Arizona', 'state_code' => 'AZ', 'lat' => 33.4484, 'lng' => -112.0740],
            ['city' => 'Dallas', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 32.7767, 'lng' => -96.7970],
            ['city' => 'San Diego', 'state' => 'California', 'state_code' => 'CA', 'lat' => 32.7157, 'lng' => -117.1611],
            ['city' => 'San Jose', 'state' => 'California', 'state_code' => 'CA', 'lat' => 37.3382, 'lng' => -121.8863],
            ['city' => 'Austin', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 30.2672, 'lng' => -97.7431],
            ['city' => 'Fort Worth', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 32.7555, 'lng' => -97.3308],
            ['city' => 'Denver', 'state' => 'Colorado', 'state_code' => 'CO', 'lat' => 39.7392, 'lng' => -104.9903],
            ['city' => 'El Paso', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 31.7619, 'lng' => -106.4850],
            ['city' => 'Tucson', 'state' => 'Arizona', 'state_code' => 'AZ', 'lat' => 32.2226, 'lng' => -110.9747],
            ['city' => 'Albuquerque', 'state' => 'New Mexico', 'state_code' => 'NM', 'lat' => 35.0844, 'lng' => -106.6504],
            ['city' => 'Fresno', 'state' => 'California', 'state_code' => 'CA', 'lat' => 36.7378, 'lng' => -119.7871],
            ['city' => 'Sacramento', 'state' => 'California', 'state_code' => 'CA', 'lat' => 38.5816, 'lng' => -121.4944],
            ['city' => 'Las Vegas', 'state' => 'Nevada', 'state_code' => 'NV', 'lat' => 36.1699, 'lng' => -115.1398],
            ['city' => 'Mesa', 'state' => 'Arizona', 'state_code' => 'AZ', 'lat' => 33.4152, 'lng' => -111.8315],
            ['city' => 'Long Beach', 'state' => 'California', 'state_code' => 'CA', 'lat' => 33.7701, 'lng' => -118.1937],
            ['city' => 'Bakersfield', 'state' => 'California', 'state_code' => 'CA', 'lat' => 35.3733, 'lng' => -119.0187],
            ['city' => 'Miami', 'state' => 'Florida', 'state_code' => 'FL', 'lat' => 25.7617, 'lng' => -80.1918],
            ['city' => 'Riverside', 'state' => 'California', 'state_code' => 'CA', 'lat' => 33.9533, 'lng' => -117.3962],
            ['city' => 'Santa Ana', 'state' => 'California', 'state_code' => 'CA', 'lat' => 33.7455, 'lng' => -117.8677],
            ['city' => 'Anaheim', 'state' => 'California', 'state_code' => 'CA', 'lat' => 33.8366, 'lng' => -117.9143],
            ['city' => 'Stockton', 'state' => 'California', 'state_code' => 'CA', 'lat' => 37.9577, 'lng' => -121.2908],
            ['city' => 'Corpus Christi', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 27.8006, 'lng' => -97.3964],
            ['city' => 'Laredo', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 27.5306, 'lng' => -99.4803],
            ['city' => 'McAllen', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 26.2034, 'lng' => -98.2300],
            ['city' => 'Brownsville', 'state' => 'Texas', 'state_code' => 'TX', 'lat' => 25.9017, 'lng' => -97.4975],
            ['city' => 'New York', 'state' => 'New York', 'state_code' => 'NY', 'lat' => 40.7128, 'lng' => -74.0060],
            ['city' => 'Atlanta', 'state' => 'Georgia', 'state_code' => 'GA', 'lat' => 33.7490, 'lng' => -84.3880],
            ['city' => 'Seattle', 'state' => 'Washington', 'state_code' => 'WA', 'lat' => 47.6062, 'lng' => -122.3321],
            ['city' => 'Portland', 'state' => 'Oregon', 'state_code' => 'OR', 'lat' => 45.5155, 'lng' => -122.6789],
            ['city' => 'Oakland', 'state' => 'California', 'state_code' => 'CA', 'lat' => 37.8044, 'lng' => -122.2712],
            ['city' => 'San Francisco', 'state' => 'California', 'state_code' => 'CA', 'lat' => 37.7749, 'lng' => -122.4194],
        ];
    }
}
