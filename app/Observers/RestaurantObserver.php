<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RestaurantObserver
{
    /**
     * Handle the Restaurant "saving" event.
     * Auto-geocode address to coordinates if address changed.
     */
    public function saving(Restaurant $restaurant): void
    {
        // Check if address-related fields have changed
        $addressFields = ['address', 'city', 'state_id', 'zip_code'];
        $addressChanged = false;

        foreach ($addressFields as $field) {
            if ($restaurant->isDirty($field)) {
                $addressChanged = true;
                break;
            }
        }

        // Only geocode if address changed and we have the minimum required fields
        // Also geocode if lat/lng are empty
        $needsGeocode = ($addressChanged || (!$restaurant->latitude && !$restaurant->longitude)) 
                        && $restaurant->address 
                        && $restaurant->city;

        if ($needsGeocode) {
            $this->geocodeAddress($restaurant);
        }
    }

    /**
     * Geocode the restaurant address and set lat/lng
     */
    protected function geocodeAddress(Restaurant $restaurant): void
    {
        try {
            $state = $restaurant->state_id ? State::find($restaurant->state_id)?->name : '';
            $fullAddress = "{$restaurant->address}, {$restaurant->city}, {$state} {$restaurant->zip_code}, USA";

            $apiKey = config('services.google.maps_api_key') ?: env('GOOGLE_MAPS_API_KEY');
            
            if (!$apiKey) {
                Log::warning('Google Maps API key not configured for geocoding');
                return;
            }

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query([
                'address' => $fullAddress,
                'key' => $apiKey,
            ]);

            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                $restaurant->latitude = round($location['lat'], 8);
                $restaurant->longitude = round($location['lng'], 8);
                
                Log::info("Geocoded restaurant {$restaurant->id}: {$fullAddress} -> {$location['lat']}, {$location['lng']}");
            } else {
                Log::warning("Geocoding failed for restaurant {$restaurant->id}: {$fullAddress} - Status: {$data['status']}");
            }
        } catch (\Exception $e) {
            Log::error("Geocoding error for restaurant {$restaurant->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Restaurant "created" event.
     */
    public function created(Restaurant $restaurant): void
    {
        $this->clearRestaurantCaches();
        Log::info("Restaurant created, cache cleared", ['restaurant_id' => $restaurant->id]);
    }

    /**
     * Handle the Restaurant "updated" event.
     */
    public function updated(Restaurant $restaurant): void
    {
        $this->clearRestaurantCaches();
        Log::info("Restaurant updated, cache cleared", ['restaurant_id' => $restaurant->id]);
    }

    /**
     * Handle the Restaurant "deleted" event.
     */
    public function deleted(Restaurant $restaurant): void
    {
        $this->clearRestaurantCaches();
        Log::info("Restaurant deleted, cache cleared", ['restaurant_id' => $restaurant->id]);
    }

    /**
     * Handle the Restaurant "restored" event.
     */
    public function restored(Restaurant $restaurant): void
    {
        $this->clearRestaurantCaches();
        Log::info("Restaurant restored, cache cleared", ['restaurant_id' => $restaurant->id]);
    }

    /**
     * Handle the Restaurant "force deleted" event.
     */
    public function forceDeleted(Restaurant $restaurant): void
    {
        $this->clearRestaurantCaches();
        Log::info("Restaurant force deleted, cache cleared", ['restaurant_id' => $restaurant->id]);
    }

    /**
     * Clear all restaurant-related caches
     */
    protected function clearRestaurantCaches(): void
    {
        // Homepage caches
        Cache::forget('home_featured_restaurants');
        Cache::forget('home_stats');
        Cache::forget('home_categories');
        Cache::forget('home_states');

        // Restaurant list caches
        Cache::forget('restaurant_states');
        Cache::forget('restaurant_categories');

        // Clear all restaurant list filter combinations
        $cacheKeys = Cache::get('restaurant_cache_keys', []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear the tracking array
        Cache::forget('restaurant_cache_keys');

        Log::info("All restaurant caches cleared");
    }
}
