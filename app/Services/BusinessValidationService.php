<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BusinessValidationService
{
    protected $googlePlacesService;
    protected $yelpService;
    protected $spamDetectionService;

    public function __construct()
    {
        $this->googlePlacesService = app(GooglePlacesService::class);
        $this->yelpService = app(YelpFusionService::class);
        $this->spamDetectionService = app(SpamDetectionService::class);
    }

    /**
     * Valida un negocio completo y retorna un trust score
     *
     * @param array $data Datos del restaurante a validar
     * @return array Resultado de la validación con score y detalles
     */
    public function validateBusiness(array $data): array
    {
        $score = 0;
        $validations = [];

        // 0. SPAM DETECTION (puede reducir hasta -50 puntos)
        $spamAnalysis = $this->spamDetectionService->analyzeSuggestion($data);
        $validations['spam'] = $spamAnalysis;

        // Si es spam confirmado, retornar inmediatamente con score bajo
        if ($spamAnalysis['is_spam']) {
            return [
                'trust_score' => 0,
                'validation_data' => $validations,
                'recommendation' => 'reject',
                'is_potential_duplicate' => false,
                'duplicate_check_data' => ['is_duplicate' => false],
                'spam_detected' => true,
            ];
        }

        // Reducir score si es sospechoso
        if ($spamAnalysis['is_suspicious']) {
            $score -= $spamAnalysis['spam_score'] / 2; // Penalización
        }

        // 1. Verificar con Google Places (40 puntos)
        $googleData = $this->verifyGooglePlaces($data);
        if ($googleData && $googleData['verified']) {
            $googleScore = 40;

            // Bonus si tiene buen rating en Google
            if (isset($googleData['rating']) && $googleData['rating'] >= 4.0) {
                $googleScore += 5;
            }

            // Bonus si tiene muchas reviews
            if (isset($googleData['user_ratings_total']) && $googleData['user_ratings_total'] >= 50) {
                $googleScore += 5;
            }

            $score += $googleScore;
            $validations['google'] = $googleData;
        } else {
            $validations['google'] = ['verified' => false];
        }

        // 2. Verificar con Yelp Fusion API (20 puntos)
        $yelpData = $this->verifyYelp($data);
        if ($yelpData && $yelpData['verified']) {
            $yelpScore = 20;

            // Bonus si tiene buen rating en Yelp
            if (isset($yelpData['rating']) && $yelpData['rating'] >= 4.0) {
                $yelpScore += 3;
            }

            // Bonus si tiene muchas reviews
            if (isset($yelpData['review_count']) && $yelpData['review_count'] >= 50) {
                $yelpScore += 2;
            }

            $score += $yelpScore;
            $validations['yelp'] = $yelpData;
        } else {
            $validations['yelp'] = ['verified' => false];
        }

        // 3. Verificar website (10 puntos)
        if (!empty($data['restaurant_website'])) {
            $websiteVerified = $this->verifyWebsite($data['restaurant_website']);
            if ($websiteVerified) {
                $score += 10;
                $validations['website'] = ['verified' => true, 'accessible' => true];
            } else {
                $validations['website'] = ['verified' => false, 'accessible' => false];
            }
        }

        // 4. Verificar formato de teléfono (5 puntos)
        if (!empty($data['restaurant_phone'])) {
            $phoneValid = $this->validatePhoneFormat($data['restaurant_phone']);
            if ($phoneValid) {
                $score += 5;
                $validations['phone'] = ['format_valid' => true];
            } else {
                $validations['phone'] = ['format_valid' => false];
            }
        }

        // 5. Usuario registrado vs anónimo (10 puntos)
        if (!empty($data['user_id'])) {
            $score += 10;
            $validations['user'] = ['registered' => true];
        } else {
            $validations['user'] = ['registered' => false, 'anonymous' => true];
        }

        // 6. Email verificado (5 puntos)
        if (!empty($data['submitter_email'])) {
            $emailValid = filter_var($data['submitter_email'], FILTER_VALIDATE_EMAIL);
            if ($emailValid) {
                $score += 5;
                $validations['email'] = ['valid' => true];
            }
        }

        // 7. Cross-validation: Google + Yelp ambos verificados (bonus 10 puntos)
        if (($googleData['verified'] ?? false) && ($yelpData['verified'] ?? false)) {
            $score += 10;
            $validations['cross_validated'] = true;
        }

        // 8. Detección de duplicados
        $duplicateCheck = $this->checkDuplicates(
            $data['restaurant_name'],
            $data['restaurant_city'],
            $data['restaurant_state']
        );

        $validations['duplicates'] = $duplicateCheck;

        // Asegurar que el score esté entre 0 y 100
        $finalScore = max(0, min($score, 100));

        return [
            'trust_score' => $finalScore,
            'validation_data' => $validations,
            'recommendation' => $this->getRecommendation($finalScore, $duplicateCheck['is_duplicate'], $spamAnalysis['is_suspicious']),
            'is_potential_duplicate' => $duplicateCheck['is_duplicate'],
            'duplicate_check_data' => $duplicateCheck,
            'spam_detected' => $spamAnalysis['is_spam'],
        ];
    }

    /**
     * Verifica el negocio con Google Places API
     */
    protected function verifyGooglePlaces(array $data): ?array
    {
        try {
            $place = $this->googlePlacesService->findPlace(
                $data['restaurant_name'],
                $data['restaurant_address'] ?? '',
                $data['restaurant_city'],
                $data['restaurant_state']
            );

            if ($place) {
                return [
                    'verified' => true,
                    'place_id' => $place['place_id'] ?? null,
                    'rating' => $place['rating'] ?? null,
                    'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                    'formatted_address' => $place['formatted_address'] ?? null,
                    'formatted_phone_number' => $place['formatted_phone_number'] ?? null,
                    'website' => $place['website'] ?? null,
                    'opening_hours' => $place['opening_hours'] ?? null,
                    'photos' => isset($place['photos']) ? array_slice($place['photos'], 0, 3) : [],
                ];
            }

            return ['verified' => false];
        } catch (\Exception $e) {
            Log::error('Google Places verification failed: ' . $e->getMessage());
            return ['verified' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verifica el negocio con Yelp Fusion API
     */
    protected function verifyYelp(array $data): ?array
    {
        try {
            $business = $this->yelpService->searchBusiness(
                $data['restaurant_name'],
                $data['restaurant_city'],
                $data['restaurant_state']
            );

            return $business ?? ['verified' => false];
        } catch (\Exception $e) {
            Log::error('Yelp verification failed: ' . $e->getMessage());
            return ['verified' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verifica que el website sea accesible
     */
    protected function verifyWebsite(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Asegurar que tiene protocolo
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        try {
            $response = Http::timeout(5)->head($url);
            return $response->successful() || $response->status() === 403;
        } catch (\Exception $e) {
            Log::info("Website verification failed for {$url}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida el formato del teléfono (US format)
     */
    protected function validatePhoneFormat(?string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        $cleaned = preg_replace('/\D/', '', $phone);
        return preg_match('/^1?\d{10}$/', $cleaned);
    }

    /**
     * Detecta duplicados en la base de datos
     */
    public function checkDuplicates(string $name, string $city, string $state): array
    {
        $duplicates = [];

        // Buscar en restaurantes existentes
        $existingRestaurants = Restaurant::whereHas('state', function ($query) use ($state) {
            $query->where('name', 'LIKE', "%{$state}%");
        })
        ->where('city', 'LIKE', "%{$city}%")
        ->get();

        foreach ($existingRestaurants as $restaurant) {
            $similarity = $this->calculateSimilarity($name, $restaurant->name);

            if ($similarity > 85) {
                $duplicates[] = [
                    'type' => 'restaurant',
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'address' => $restaurant->address,
                    'similarity' => $similarity,
                ];
            }
        }

        // Buscar en sugerencias pendientes
        $existingSuggestions = Suggestion::where('status', 'pending')
            ->where('restaurant_city', 'LIKE', "%{$city}%")
            ->where('restaurant_state', 'LIKE', "%{$state}%")
            ->get();

        foreach ($existingSuggestions as $suggestion) {
            $similarity = $this->calculateSimilarity($name, $suggestion->restaurant_name);

            if ($similarity > 85) {
                $duplicates[] = [
                    'type' => 'suggestion',
                    'id' => $suggestion->id,
                    'name' => $suggestion->restaurant_name,
                    'address' => $suggestion->restaurant_address,
                    'similarity' => $similarity,
                ];
            }
        }

        return [
            'is_duplicate' => count($duplicates) > 0,
            'count' => count($duplicates),
            'matches' => $duplicates,
        ];
    }

    /**
     * Calcula la similitud entre dos strings usando similar_text
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        similar_text($str1, $str2, $percent);

        return round($percent, 2);
    }

    /**
     * Obtiene recomendación basada en el score
     */
    protected function getRecommendation(int $score, bool $isDuplicate, bool $isSuspicious): string
    {
        // Si es duplicado o sospechoso, siempre requiere revisión
        if ($isDuplicate) {
            return 'duplicate_review';
        }

        if ($isSuspicious) {
            return 'spam_review';
        }

        if ($score >= 75) {
            return 'auto_approve';
        } elseif ($score >= 50) {
            return 'quick_review';
        } else {
            return 'full_review';
        }
    }

    /**
     * Obtiene el color del badge según el score
     */
    public static function getScoreColor(int $score): string
    {
        if ($score >= 75) return 'success';
        if ($score >= 50) return 'warning';
        return 'danger';
    }

    /**
     * Obtiene el texto del badge según el score
     */
    public static function getScoreLabel(int $score): string
    {
        if ($score >= 75) return 'Alto';
        if ($score >= 50) return 'Medio';
        return 'Bajo';
    }
}
