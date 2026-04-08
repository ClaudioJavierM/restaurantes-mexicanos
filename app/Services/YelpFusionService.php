<?php

namespace App\Services;

use App\Models\ApiCallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YelpFusionService
{
    protected $apiKey;
    protected $apiKeys = [];
    protected $currentKeyIndex = 0;
    protected $baseUrl = 'https://api.yelp.com/v3';

    public function __construct()
    {
        $this->apiKeys = config('services.yelp.api_keys', []);

        if (empty($this->apiKeys)) {
            $singleKey = config('services.yelp.api_key');
            if ($singleKey) {
                $this->apiKeys = [$singleKey];
            }
        }

        $this->apiKey = $this->apiKeys[0] ?? null;

        if (count($this->apiKeys) > 1) {
            $this->currentKeyIndex = rand(0, count($this->apiKeys) - 1);
            $this->apiKey = $this->apiKeys[$this->currentKeyIndex];
        }

        Log::debug('YelpFusionService initialized with ' . count($this->apiKeys) . ' API keys');
    }

    protected function rotateApiKey(): bool
    {
        if (count($this->apiKeys) <= 1) {
            return false;
        }

        $this->currentKeyIndex = ($this->currentKeyIndex + 1) % count($this->apiKeys);
        $this->apiKey = $this->apiKeys[$this->currentKeyIndex];

        Log::info('Rotated to Yelp API key index: ' . $this->currentKeyIndex);
        return true;
    }

    /**
     * Guard: checks per-key monthly budget (5,000 per trial key).
     * Auto-rotates to a key with remaining budget before throwing.
     * Called before every API request so imports auto-stop at the limit.
     */
    protected function checkMonthlyBudget(): void
    {
        $perKeyLimit = (int) config('services.yelp.monthly_limit', 5000);

        // Check current key's usage
        $used = $this->getKeyUsage($this->currentKeyIndex);

        if ($used >= $perKeyLimit) {
            Log::warning("Yelp key [{$this->currentKeyIndex}] monthly limit reached ({$used}/{$perKeyLimit}), rotating...");

            // Try to rotate to a key with remaining budget
            if (!$this->rotateToAvailableKey($perKeyLimit)) {
                $total = count($this->apiKeys) * $perKeyLimit;
                throw new \RuntimeException(
                    "All {$this->getApiKeyCount()} Yelp API keys exhausted for this month. " .
                    "Total budget: {$total} calls. Resets on " . now()->startOfNextMonth()->toDateString() . '.'
                );
            }

            // Re-check the new key
            $used = $this->getKeyUsage($this->currentKeyIndex);
        }

        // Warn at 80%
        if ($used >= (int) ($perKeyLimit * 0.80)) {
            Log::warning("Yelp key [{$this->currentKeyIndex}] at " . round(($used / $perKeyLimit) * 100) . '%', [
                'used' => $used,
                'limit' => $perKeyLimit,
                'remaining' => $perKeyLimit - $used,
            ]);
        }
    }

    /**
     * Count this month's API calls for a specific key index.
     */
    protected function getKeyUsage(int $keyIndex): int
    {
        return ApiCallLog::where('service', 'yelp')
            ->where('called_at', '>=', now()->startOfMonth())
            ->whereRaw("JSON_EXTRACT(params, '$._key_index') = ?", [$keyIndex])
            ->count();
    }

    /**
     * Rotate to the first key that still has remaining monthly budget.
     * Returns true if a key with budget was found, false if all exhausted.
     */
    protected function rotateToAvailableKey(int $perKeyLimit): bool
    {
        $startIndex = $this->currentKeyIndex;

        for ($i = 1; $i <= count($this->apiKeys); $i++) {
            $nextIndex = ($startIndex + $i) % count($this->apiKeys);

            if ($nextIndex === $startIndex) {
                break; // Checked all keys
            }

            $used = $this->getKeyUsage($nextIndex);
            if ($used < $perKeyLimit) {
                $this->currentKeyIndex = $nextIndex;
                $this->apiKey = $this->apiKeys[$nextIndex];
                Log::info("Rotated to Yelp key [{$nextIndex}] — {$used}/{$perKeyLimit} used this month");
                return true;
            }
        }

        return false;
    }

    /**
     * Log API call to api_call_logs for dashboard visibility.
     * Stores _key_index in params JSON for per-key budget tracking.
     */
    protected function logApiCall(string $endpoint, bool $success, ?int $statusCode = null, array $params = [], ?string $error = null): void
    {
        try {
            ApiCallLog::create([
                'service' => 'yelp',
                'endpoint' => $endpoint,
                'status_code' => $statusCode ?? ($success ? 200 : 500),
                'success' => $success,
                'cost' => 0,
                'params' => array_merge($params, ['_key_index' => $this->currentKeyIndex]),
                'error_message' => $error,
                'called_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if logging fails
        }
    }

    protected function makeRequest(string $method, string $url, array $params = []): ?\Illuminate\Http\Client\Response
    {
        // Stop if monthly budget is reached — lets RuntimeException propagate
        $this->checkMonthlyBudget();

        $attempts = 0;
        $maxAttempts = count($this->apiKeys);
        $endpoint = str_replace($this->baseUrl . '/', '', $url);

        while ($attempts < $maxAttempts) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                ])->{$method}($url, $params);

                // Log the API call
                $this->logApiCall($endpoint, $response->successful(), $response->status(), $params);

                if (in_array($response->status(), [429, 401])) {
                    Log::warning("Yelp API rate limited or unauthorized, rotating key", [
                        'status' => $response->status(),
                        'key_index' => $this->currentKeyIndex,
                    ]);

                    if (!$this->rotateApiKey()) {
                        return $response;
                    }
                    $attempts++;
                    continue;
                }

                return $response;
            } catch (\RuntimeException $e) {
                // Budget exception — don't retry, propagate immediately
                throw $e;
            } catch (\Exception $e) {
                Log::error('Yelp API request exception: ' . $e->getMessage());
                $this->logApiCall($endpoint, false, 500, $params, $e->getMessage());

                if (!$this->rotateApiKey()) {
                    throw $e;
                }
                $attempts++;
            }
        }

        return null;
    }

    public function searchBusiness(string $name, string $city, string $state, ?string $address = null, bool $strictCategory = true): ?array
    {
        if (empty($this->apiKeys)) {
            Log::warning('Yelp API keys not configured');
            return null;
        }

        try {
            $params = [
                'term' => $name,
                'location' => "{$city}, {$state}",
                'limit' => 10,
            ];

            if ($strictCategory) {
                $params['categories'] = 'mexican,restaurants';
            }

            $response = $this->makeRequest('get', "{$this->baseUrl}/businesses/search", $params);

            if ($response && $response->successful()) {
                $data = $response->json();
                $businesses = $data['businesses'] ?? [];

                $searchCity = $this->normalizeCity($city);
                $bestMatch = null;
                $bestScore = 0;

                foreach ($businesses as $business) {
                    $businessCity = $this->normalizeCity($business['location']['city'] ?? '');
                    $citySimilarity = $this->calculateSimilarity($searchCity, $businessCity);

                    if ($citySimilarity < 80) {
                        Log::debug("Yelp: Skipping {$business['name']} - city mismatch: {$businessCity} vs {$searchCity}");
                        continue;
                    }

                    $nameSimilarity = $this->calculateSimilarity($name, $business['name']);
                    $combinedScore = ($nameSimilarity * 0.7) + ($citySimilarity * 0.3);

                    if ($address && !empty($business['location']['address1'])) {
                        $addressSimilarity = $this->calculateSimilarity(
                            $this->normalizeAddress($address),
                            $this->normalizeAddress($business['location']['address1'])
                        );
                        if ($addressSimilarity > 60) {
                            $combinedScore += 10;
                        }
                    }

                    if ($combinedScore > $bestScore && $nameSimilarity >= 65) {
                        $bestScore = $combinedScore;
                        $bestMatch = [
                            'verified' => true,
                            'yelp_id' => $business['id'],
                            'name' => $business['name'],
                            'rating' => $business['rating'] ?? null,
                            'review_count' => $business['review_count'] ?? 0,
                            'phone' => $business['phone'] ?? null,
                            'location' => $business['location'] ?? null,
                            'categories' => $business['categories'] ?? [],
                            'url' => $business['url'] ?? null,
                            'image_url' => $business['image_url'] ?? null,
                            'coordinates' => $business['coordinates'] ?? null,
                            'similarity' => $nameSimilarity,
                            'city_similarity' => $citySimilarity,
                            'combined_score' => $combinedScore,
                        ];
                    }
                }

                if ($bestMatch && $bestScore >= 65) {
                    return $bestMatch;
                }

                return ['verified' => false, 'message' => 'No close match found in same city'];
            }

            if ($response) {
                Log::error('Yelp API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;

        } catch (\RuntimeException $e) {
            Log::warning('Yelp budget limit in searchBusiness: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Yelp API error: ' . $e->getMessage());
            return null;
        }
    }

    protected function normalizeCity(string $city): string
    {
        $city = strtolower(trim($city));
        $city = preg_replace('/[^a-z0-9\s]/', '', $city);
        $city = preg_replace('/\s+(city|town|village)$/i', '', $city);
        return trim($city);
    }

    protected function normalizeAddress(string $address): string
    {
        $address = strtolower(trim($address));
        $replacements = [
            'street' => 'st', 'st.' => 'st',
            'avenue' => 'ave', 'ave.' => 'ave',
            'boulevard' => 'blvd', 'blvd.' => 'blvd',
            'drive' => 'dr', 'dr.' => 'dr',
            'road' => 'rd', 'rd.' => 'rd',
            'lane' => 'ln', 'ln.' => 'ln',
            'highway' => 'hwy', 'hwy.' => 'hwy',
            'north' => 'n', 'n.' => 'n',
            'south' => 's', 's.' => 's',
            'east' => 'e', 'e.' => 'e',
            'west' => 'w', 'w.' => 'w',
        ];
        foreach ($replacements as $from => $to) {
            $address = str_replace($from, $to, $address);
        }
        return preg_replace('/[^a-z0-9\s]/', '', $address);
    }

    protected function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        similar_text($str1, $str2, $percent);
        return round($percent, 2);
    }

    public function getBusinessDetails(string $businessId): ?array
    {
        if (empty($this->apiKeys)) {
            Log::warning('Yelp API keys not configured for getBusinessDetails');
            return null;
        }

        try {
            $response = $this->makeRequest('get', "{$this->baseUrl}/businesses/{$businessId}");

            if ($response && $response->successful()) {
                Log::info("Successfully fetched Yelp business details for ID: {$businessId}");
                return $response->json();
            }

            if ($response) {
                Log::warning("Yelp API getBusinessDetails failed for ID: {$businessId}", [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;

        } catch (\RuntimeException $e) {
            Log::warning('Yelp budget limit in getBusinessDetails: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error("Yelp API get business error for ID: {$businessId} - " . $e->getMessage());
            return null;
        }
    }

    public function getBusinessReviews(string $businessId): ?array
    {
        if (empty($this->apiKeys)) {
            return null;
        }

        try {
            $response = $this->makeRequest('get', "{$this->baseUrl}/businesses/{$businessId}/reviews", [
                'limit' => 3,
                'sort_by' => 'rating',
            ]);

            if ($response && $response->successful()) {
                return $response->json()['reviews'] ?? [];
            }

            return null;

        } catch (\RuntimeException $e) {
            Log::warning('Yelp budget limit in getBusinessReviews: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Yelp API get reviews error: ' . $e->getMessage());
            return null;
        }
    }

    public function searchBusinesses(
        string $term,
        string $city,
        string $state,
        int $limit = 50,
        int $offset = 0
    ): ?array {
        if (empty($this->apiKeys)) {
            Log::warning('Yelp API keys not configured');
            return null;
        }

        try {
            $response = $this->makeRequest('get', "{$this->baseUrl}/businesses/search", [
                'term' => $term,
                'location' => "{$city}, {$state}",
                'categories' => 'mexican,restaurants',
                'limit' => min($limit, 50),
                'offset' => $offset,
            ]);

            if ($response && $response->successful()) {
                return $response->json();
            }

            if ($response) {
                Log::error('Yelp API search businesses failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;

        } catch (\RuntimeException $e) {
            Log::warning('Yelp budget limit in searchBusinesses: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Yelp API search businesses error: ' . $e->getMessage());
            return null;
        }
    }

    public function getApiKeyCount(): int
    {
        return count($this->apiKeys);
    }

    /**
     * Get current month Yelp API usage stats, broken down per key.
     */
    public function getMonthlyUsage(): array
    {
        $perKeyLimit = (int) config('services.yelp.monthly_limit', 5000);
        $keyCount = count($this->apiKeys);
        $totalLimit = $perKeyLimit * $keyCount;

        // Total calls (including legacy entries without _key_index)
        $totalUsed = ApiCallLog::where('service', 'yelp')
            ->where('called_at', '>=', now()->startOfMonth())
            ->count();

        // Per-key breakdown (only counts entries with _key_index logged)
        $perKey = [];
        for ($i = 0; $i < $keyCount; $i++) {
            $used = $this->getKeyUsage($i);
            $perKey[] = [
                'key_index' => $i,
                'used'      => $used,
                'limit'     => $perKeyLimit,
                'remaining' => max(0, $perKeyLimit - $used),
            ];
        }

        // Effective remaining = sum of per-key remaining
        $effectiveRemaining = array_sum(array_column($perKey, 'remaining'));

        return [
            'used'               => $totalUsed,
            'limit'              => $totalLimit,
            'remaining'          => $effectiveRemaining,
            'percentage'         => $totalLimit > 0 ? round(($totalUsed / $totalLimit) * 100, 1) : 0,
            'resets_on'          => now()->startOfNextMonth()->toDateString(),
            'per_key'            => $perKey,
            'key_count'          => $keyCount,
        ];
    }
}
