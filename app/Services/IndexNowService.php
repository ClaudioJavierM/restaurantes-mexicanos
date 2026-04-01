<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    private const KEY = 'famer2026mexicanrestaurants';
    private const API_URL = 'https://api.indexnow.org/IndexNow';

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
     * Submit a batch of URLs to IndexNow.
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
