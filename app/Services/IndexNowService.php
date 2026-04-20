<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    private const KEY = 'famer2026mexicanrestaurants';
    private const API_URL = 'https://api.indexnow.org/IndexNow';
    private const MAX_URLS_PER_CALL = 10000;

    /**
     * Notify IndexNow about a single URL across all 3 FAMER domains.
     */
    public static function notifyRestaurant(string $slug): void
    {
        $urls = [
            'https://restaurantesmexicanosfamosos.com.mx/restaurante/' . $slug,
            'https://restaurantesmexicanosfamosos.com/restaurante/' . $slug,
            'https://famousmexicanrestaurants.com/restaurant/' . $slug,
        ];

        static::submitUrls($urls, 'restaurantesmexicanosfamosos.com.mx');
    }

    /**
     * Notify IndexNow for all approved restaurants in a given state.
     * Used when bulk-updating state-level data (e.g. state descriptions, schema).
     */
    public static function notifyStateChange(string $stateSlug): void
    {
        $slugs = Restaurant::query()
            ->where('status', 'approved')
            ->whereHas('state', fn ($q) => $q->where('slug', $stateSlug))
            ->whereNotNull('slug')
            ->pluck('slug')
            ->toArray();

        if (empty($slugs)) {
            Log::info("IndexNow: no approved restaurants found for state slug [{$stateSlug}]");
            return;
        }

        Log::info("IndexNow: notifying state change for [{$stateSlug}] — " . count($slugs) . ' restaurants');
        static::notifyBatch($slugs);
    }

    /**
     * Notify IndexNow for a batch of restaurant slugs across all 3 FAMER domains.
     * Automatically splits into chunks of max 10,000 URLs per API call.
     */
    public static function notifyBatch(array $slugs): void
    {
        if (empty($slugs)) {
            return;
        }

        $allUrls = [];
        foreach ($slugs as $slug) {
            $allUrls[] = 'https://restaurantesmexicanosfamosos.com.mx/restaurante/' . $slug;
            $allUrls[] = 'https://restaurantesmexicanosfamosos.com/restaurante/' . $slug;
            $allUrls[] = 'https://famousmexicanrestaurants.com/restaurant/' . $slug;
        }

        $chunks = array_chunk($allUrls, self::MAX_URLS_PER_CALL);

        foreach ($chunks as $i => $chunk) {
            Log::info('IndexNow: submitting batch chunk ' . ($i + 1) . '/' . count($chunks) . ' (' . count($chunk) . ' URLs)');
            static::submitUrls($chunk, 'restaurantesmexicanosfamosos.com.mx');
        }
    }

    /**
     * Submit a list of URLs to IndexNow (single API call, max 10,000 URLs).
     */
    public static function submitUrls(array $urls, string $host): void
    {
        try {
            $response = Http::timeout(10)->post(self::API_URL, [
                'host'        => $host,
                'key'         => self::KEY,
                'keyLocation' => 'https://' . $host . '/' . self::KEY . '.txt',
                'urlList'     => $urls,
            ]);

            if ($response->successful() || $response->status() === 202) {
                Log::info('IndexNow: submitted ' . count($urls) . ' URLs', ['status' => $response->status()]);
            } else {
                Log::warning('IndexNow: unexpected response', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            // Non-critical — log and continue
            Log::warning('IndexNow: failed to notify', ['error' => $e->getMessage()]);
        }
    }
}
