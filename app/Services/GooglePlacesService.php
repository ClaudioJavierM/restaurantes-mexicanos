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
     * Obtener detalles completos de un lugar por Place ID
     */
    public function getPlaceDetails($placeId)
    {
        try {
            // Verificar límites ANTES de hacer el request
            $this->checkAndTrackUsage('google_places_details', 1);

            $response = Http::get("{$this->baseUrl}/place/details/json", [
                'place_id' => $placeId,
                'fields' => 'name,formatted_address,geometry,formatted_phone_number,website,opening_hours,business_status,rating,user_ratings_total,photos,url,reviews',
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
}
