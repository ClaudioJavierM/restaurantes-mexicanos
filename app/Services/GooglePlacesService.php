<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiUsageTracker;

class GooglePlacesService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google.places_api_key');
    }

    /**
     * Verificar si se puede hacer un request a Google API
     * Lanza excepción si se excede el límite
     */
    protected function checkAndTrackUsage(string $service, int $requestCount = 1): void
    {
        $check = ApiUsageTracker::canMakeRequest($service, $requestCount);

        if (!$check['allowed']) {
            Log::warning('Google API request blocked: ' . $check['message'], [
                'service' => $service,
                'reason' => $check['reason'],
            ]);
            throw new \Exception('Google API limit exceeded: ' . $check['message']);
        }
    }

    /**
     * Registrar uso de API después de un request exitoso
     */
    protected function trackUsage(string $service, string $endpoint, int $requestCount = 1, array $metadata = []): void
    {
        ApiUsageTracker::track($service, $endpoint, $requestCount, $metadata);
    }

    /**
     * Buscar un lugar por nombre y dirección
     */
    public function findPlace($name, $address, $city, $state)
    {
        $query = "{$name}, {$address}, {$city}, {$state}, USA";

        try {
            // Verificar límites ANTES de hacer el request
            $this->checkAndTrackUsage('google_places_text_search', 1);

            $response = Http::get("{$this->baseUrl}/place/findplacefromtext/json", [
                'input' => $query,
                'inputtype' => 'textquery',
                'fields' => 'place_id,name,formatted_address,geometry,business_status,rating,user_ratings_total',
                'key' => $this->apiKey,
            ]);

            // Registrar uso después del request
            $this->trackUsage('google_places_text_search', 'findplacefromtext', 1, [
                'query' => $query,
                'status' => $response->json('status'),
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $candidates = $response->json('candidates');
                return $candidates[0] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Google Places API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener detalles completos de un lugar por Place ID.
     *
     * Billing breakdown (Places API Legacy):
     *   Basic    ($17/1000): name, formatted_address, geometry, business_status, photos, url
     *   Contact  (+$3/1000): formatted_phone_number, website, opening_hours
     *   Atmosphere (+$5/1000): rating, user_ratings_total
     *
     * "reviews" (Atmosphere) is intentionally excluded — FAMER has its own review
     * system and Google reviews are never displayed. Removing it saves $5/1000 calls.
     * Total: $20/1000 instead of $25/1000 = 20% cheaper per import.
     */
    public function getPlaceDetails($placeId)
    {
        try {
            // Verificar límites ANTES de hacer el request
            $this->checkAndTrackUsage('google_places_details', 1);

            $response = Http::get("{$this->baseUrl}/place/details/json", [
                'place_id' => $placeId,
                'fields' => 'name,formatted_address,geometry,formatted_phone_number,website,opening_hours,business_status,rating,user_ratings_total,photos,url',
                'key' => $this->apiKey,
            ]);

            // Registrar uso después del request
            $this->trackUsage('google_places_details', 'place/details', 1, [
                'place_id' => $placeId,
                'status' => $response->json('status'),
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return $response->json('result');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Google Places Details API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si un restaurante está abierto actualmente
     */
    public function isOpenNow($placeId)
    {
        $details = $this->getPlaceDetails($placeId);

        if (!$details) {
            return null;
        }

        return $details['opening_hours']['open_now'] ?? null;
    }

    /**
     * Obtener el estado del negocio desde datos ya obtenidos
     * NOTA: Este método ya NO hace llamadas a la API
     * Usar getPlaceDetails() primero y pasar los datos
     */
    public function getBusinessStatus($placeIdOrDetails)
    {
        // Si es un array, asumimos que ya son los detalles
        if (is_array($placeIdOrDetails)) {
            $status = $placeIdOrDetails['business_status'] ?? 'OPERATIONAL';
        } else {
            // Si es un string (place_id), usamos OPERATIONAL por defecto
            // para evitar llamadas adicionales a la API
            Log::warning('getBusinessStatus called with place_id instead of details - returning default to avoid API cost');
            return 'operational';
        }

        return match($status) {
            'OPERATIONAL' => 'operational',
            'CLOSED_TEMPORARILY' => 'temporarily_closed',
            'CLOSED_PERMANENTLY' => 'permanently_closed',
            default => 'operational',
        };
    }

    /**
     * Geocodificar una dirección (obtener coordenadas)
     */
    public function geocodeAddress($address, $city, $state, $zipCode)
    {
        $fullAddress = "{$address}, {$city}, {$state} {$zipCode}, USA";

        try {
            // Verificar límites ANTES de hacer el request
            $this->checkAndTrackUsage('google_places_text_search', 1);

            $response = Http::get("{$this->baseUrl}/geocode/json", [
                'address' => $fullAddress,
                'key' => $this->apiKey,
            ]);

            // Registrar uso después del request
            $this->trackUsage('google_places_text_search', 'geocode', 1, [
                'address' => $fullAddress,
                'status' => $response->json('status'),
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $location = $response->json('results')[0]['geometry']['location'];
                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Google Geocoding API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener fotos del lugar desde datos ya obtenidos
     * NOTA: Este método ya NO hace llamadas a la API
     * Pasar los detalles ya obtenidos con getPlaceDetails()
     */
    public function getPlacePhotos($placeIdOrDetails, $maxPhotos = 5)
    {
        // Si es un array, asumimos que ya son los detalles
        if (is_array($placeIdOrDetails)) {
            $details = $placeIdOrDetails;
        } else {
            // Si es un string (place_id), retornamos vacío para evitar API cost
            Log::warning('getPlacePhotos called with place_id instead of details - returning empty to avoid API cost');
            return [];
        }

        if (!isset($details['photos'])) {
            return [];
        }

        $photos = [];
        $photoReferences = array_slice($details['photos'], 0, $maxPhotos);

        foreach ($photoReferences as $photo) {
            $photos[] = $this->getPhotoUrl($photo['photo_reference'], 800);
        }

        return $photos;
    }

    /**
     * Construir URL de foto
     */
    public function getPhotoUrl($photoReference, $maxWidth = 400)
    {
        return "{$this->baseUrl}/place/photo?maxwidth={$maxWidth}&photo_reference={$photoReference}&key={$this->apiKey}";
    }

    /**
     * Buscar múltiples restaurantes por nombre y ciudad (Text Search)
     * Usado por SmartSuggestionForm cuando Yelp falla o se necesitan más resultados
     */
    public function searchPlaces(string $name, string $city, string $state, int $limit = 5): array
    {
        $query = "{$name} restaurant {$city}, {$state}";

        try {
            $this->checkAndTrackUsage('google_places_text_search', 1);

            $response = Http::get("{$this->baseUrl}/place/textsearch/json", [
                'query' => $query,
                'type' => 'restaurant',
                'key' => $this->apiKey,
            ]);

            $this->trackUsage('google_places_text_search', 'textsearch', 1, [
                'query' => $query,
                'status' => $response->json('status'),
            ]);

            if ($response->successful() && in_array($response->json('status'), ['OK', 'ZERO_RESULTS'])) {
                $results = $response->json('results') ?? [];
                return array_slice($results, 0, $limit);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Google Places Text Search Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualizar un restaurante con información de Google Places
     * OPTIMIZADO: Solo hace 2 llamadas máximo (findPlace + getPlaceDetails)
     */
    public function syncRestaurantWithGoogle($restaurant)
    {
        // Buscar el lugar (1 llamada API)
        $place = $this->findPlace(
            $restaurant->name,
            $restaurant->address,
            $restaurant->city,
            $restaurant->state->name
        );

        if (!$place) {
            return false;
        }

        $placeId = $place['place_id'];

        // Obtener detalles (1 llamada API)
        $details = $this->getPlaceDetails($placeId);

        if (!$details) {
            return false;
        }

        // Usar los detalles ya obtenidos para el business_status (NO hace llamada extra)
        $businessStatus = $this->getBusinessStatus($details);

        // Actualizar información del restaurante
        $restaurant->update([
            'google_place_id' => $placeId,
            'google_maps_url' => $details['url'] ?? null,
            'latitude' => $place['geometry']['location']['lat'] ?? null,
            'longitude' => $place['geometry']['location']['lng'] ?? null,
            'phone' => $details['formatted_phone_number'] ?? $restaurant->phone,
            'website' => $details['website'] ?? $restaurant->website,
            'business_status' => $businessStatus,
            'google_rating' => $details['rating'] ?? null,
            'google_reviews_count' => $details['user_ratings_total'] ?? 0,
            'google_verified' => true,
            'last_google_verification' => now(),
        ]);

        return true;
    }

    /**
     * Convert Google Places weekday_text to schema.org openingHours format.
     * Input: ["Monday: 11:00 AM – 10:00 PM", "Tuesday: Closed", ...]
     * Output: ["Mo 11:00-22:00", "Tu 11:00-22:00", ...] or [] if unparseable
     */
    public function parseOpeningHoursSchema(array $weekdayText): array
    {
        $dayMap = [
            'Monday' => 'Mo', 'Tuesday' => 'Tu', 'Wednesday' => 'We',
            'Thursday' => 'Th', 'Friday' => 'Fr', 'Saturday' => 'Sa', 'Sunday' => 'Su',
        ];

        $result = [];

        foreach ($weekdayText as $line) {
            // Format: "Monday: 11:00 AM – 10:00 PM" or "Monday: Closed" or "Monday: Open 24 hours"
            if (!preg_match('/^(\w+):\s+(.+)$/', $line, $m)) continue;

            $dayName = $m[1];
            $hours = trim($m[2]);
            $dayCode = $dayMap[$dayName] ?? null;
            if (!$dayCode) continue;

            if (strtolower($hours) === 'closed') continue; // Skip closed days
            if (strtolower($hours) === 'open 24 hours') {
                $result[] = "{$dayCode} 00:00-23:59";
                continue;
            }

            // Handle "11:00 AM – 10:00 PM" or "11:00 AM – 2:00 AM" (next-day close)
            // The dash is an en-dash (–), not a hyphen
            $hours = str_replace('–', '-', $hours); // normalize dash
            if (!preg_match('/^(\d{1,2}:\d{2}\s*[AP]M)\s*-\s*(\d{1,2}:\d{2}\s*[AP]M)$/i', $hours, $hm)) continue;

            $open = $this->convertTo24h($hm[1]);
            $close = $this->convertTo24h($hm[2]);
            if (!$open || !$close) continue;

            $result[] = "{$dayCode} {$open}-{$close}";
        }

        // Merge consecutive days with same hours into ranges (Mo-Fr 11:00-22:00)
        return $this->mergeConsecutiveDays($result);
    }

    private function convertTo24h(string $time12): ?string
    {
        $time12 = trim($time12);
        if (!preg_match('/^(\d{1,2}):(\d{2})\s*([AP]M)$/i', $time12, $m)) return null;
        $h = (int)$m[1];
        $min = $m[2];
        $ampm = strtoupper($m[3]);
        if ($ampm === 'AM') {
            if ($h === 12) $h = 0;
        } else {
            if ($h !== 12) $h += 12;
            if ($h >= 24) $h = 0; // midnight expressed as 12:00 AM next day
        }
        return sprintf('%02d:%s', $h, $min);
    }

    private function mergeConsecutiveDays(array $entries): array
    {
        $dayOrder = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
        // Build map: dayCode => hours string
        $map = [];
        foreach ($entries as $e) {
            if (preg_match('/^(\w{2}) (.+)$/', $e, $m)) {
                $map[$m[1]] = $m[2];
            }
        }

        $merged = [];
        $i = 0;
        while ($i < count($dayOrder)) {
            $day = $dayOrder[$i];
            if (!isset($map[$day])) { $i++; continue; }
            $hours = $map[$day];
            $rangeStart = $day;
            $rangeEnd = $day;
            // Extend range while next days have same hours
            while (($i + 1) < count($dayOrder)) {
                $nextDay = $dayOrder[$i + 1];
                if (($map[$nextDay] ?? null) === $hours) {
                    $rangeEnd = $nextDay;
                    $i++;
                } else break;
            }
            $merged[] = ($rangeStart === $rangeEnd)
                ? "{$rangeStart} {$hours}"
                : "{$rangeStart}-{$rangeEnd} {$hours}";
            $i++;
        }
        return $merged;
    }
}
