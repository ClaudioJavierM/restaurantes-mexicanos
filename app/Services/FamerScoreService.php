<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\FamerScore;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FamerScoreService
{
    // Category weights (must sum to 100)
    protected const WEIGHTS = [
        'profile_completeness' => 20,
        'online_presence' => 25,
        'customer_engagement' => 20,
        'menu_offerings' => 15,
        'mexican_authenticity' => 10,
        'digital_readiness' => 10,
    ];

    // Grade thresholds
    protected const GRADES = [
        97 => 'A+',
        93 => 'A',
        90 => 'A-',
        87 => 'B+',
        83 => 'B',
        80 => 'B-',
        77 => 'C+',
        73 => 'C',
        70 => 'C-',
        67 => 'D+',
        63 => 'D',
        60 => 'D-',
        0 => 'F',
    ];

    // Expiration days for cached scores
    protected const SCORE_EXPIRATION_DAYS = 7;

    // Current algorithm version
    protected const ALGORITHM_VERSION = 1;

    protected YelpFusionService $yelpService;
    protected GooglePlacesService $googleService;

    public function __construct(
        YelpFusionService $yelpService,
        GooglePlacesService $googleService
    ) {
        $this->yelpService = $yelpService;
        $this->googleService = $googleService;
    }

    /**
     * Get or calculate FAMER score for a restaurant
     */
    public function getScore(Restaurant $restaurant, bool $forceRefresh = false): FamerScore
    {
        // Check for existing valid score
        if (!$forceRefresh) {
            $existingScore = $restaurant->famerScore;

            if ($existingScore && $existingScore->isValid()) {
                return $existingScore;
            }
        }

        return $this->calculateScore($restaurant);
    }

    /**
     * Calculate fresh FAMER score
     */
    public function calculateScore(Restaurant $restaurant): FamerScore
    {
        Log::info("Calculating FAMER score for restaurant: {$restaurant->id} - {$restaurant->name}");

        // Calculate each category
        $profileScore = $this->calculateProfileCompleteness($restaurant);
        $presenceScore = $this->calculateOnlinePresence($restaurant);
        $engagementScore = $this->calculateCustomerEngagement($restaurant);
        $menuScore = $this->calculateMenuOfferings($restaurant);
        $authenticityScore = $this->calculateMexicanAuthenticity($restaurant);
        $digitalScore = $this->calculateDigitalReadiness($restaurant);

        // Calculate weighted overall score
        $overallScore = (int) round(
            ($profileScore['score'] * self::WEIGHTS['profile_completeness'] / 100) +
            ($presenceScore['score'] * self::WEIGHTS['online_presence'] / 100) +
            ($engagementScore['score'] * self::WEIGHTS['customer_engagement'] / 100) +
            ($menuScore['score'] * self::WEIGHTS['menu_offerings'] / 100) +
            ($authenticityScore['score'] * self::WEIGHTS['mexican_authenticity'] / 100) +
            ($digitalScore['score'] * self::WEIGHTS['digital_readiness'] / 100)
        );

        // Calculate comparison data
        $comparison = $this->calculateComparison($restaurant, $overallScore);

        // Generate recommendations
        $recommendations = $this->generateRecommendations(
            $restaurant,
            $profileScore,
            $presenceScore,
            $engagementScore,
            $menuScore,
            $authenticityScore,
            $digitalScore
        );

        // Create or update score record
        $famerScore = FamerScore::updateOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'overall_score' => $overallScore,
                'letter_grade' => $this->getLetterGrade($overallScore),
                'profile_completeness_score' => $profileScore['score'],
                'online_presence_score' => $presenceScore['score'],
                'customer_engagement_score' => $engagementScore['score'],
                'menu_offerings_score' => $menuScore['score'],
                'mexican_authenticity_score' => $authenticityScore['score'],
                'digital_readiness_score' => $digitalScore['score'],
                'profile_breakdown' => $profileScore['breakdown'],
                'presence_breakdown' => $presenceScore['breakdown'],
                'engagement_breakdown' => $engagementScore['breakdown'],
                'menu_breakdown' => $menuScore['breakdown'],
                'authenticity_breakdown' => $authenticityScore['breakdown'],
                'digital_breakdown' => $digitalScore['breakdown'],
                'recommendations' => $recommendations,
                'area_rank' => $comparison['area_rank'],
                'area_total' => $comparison['area_total'],
                'category_rank' => $comparison['category_rank'],
                'category_total' => $comparison['category_total'],
                'area_average' => $comparison['area_average'],
                'category_average' => $comparison['category_average'],
                'calculated_at' => now(),
                'expires_at' => now()->addDays(self::SCORE_EXPIRATION_DAYS),
                'version' => self::ALGORITHM_VERSION,
            ]
        );

        Log::info("FAMER score calculated: {$overallScore} ({$famerScore->letter_grade}) for {$restaurant->name}");

        return $famerScore;
    }

    /**
     * Calculate Profile Completeness score (0-100)
     */
    protected function calculateProfileCompleteness(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Basic Info (40 points max)
        $breakdown['has_name'] = !empty($restaurant->name);
        $descriptionLength = strlen($restaurant->description ?? '');
        $breakdown['has_description'] = $descriptionLength >= 50;
        $breakdown['description_length'] = $descriptionLength;
        $breakdown['description_quality'] = $descriptionLength >= 200 ? 'excellent' :
            ($descriptionLength >= 100 ? 'good' : ($descriptionLength >= 50 ? 'fair' : 'needs_work'));
        $breakdown['has_address'] = !empty($restaurant->address);
        $breakdown['has_city'] = !empty($restaurant->city);
        $breakdown['has_phone'] = !empty($restaurant->phone);
        $breakdown['has_email'] = !empty($restaurant->email);

        if ($breakdown['has_name']) $score += 5;
        if ($breakdown['has_description']) $score += 10;
        if ($breakdown['description_quality'] === 'excellent') $score += 5;
        if ($breakdown['has_address']) $score += 5;
        if ($breakdown['has_city']) $score += 5;
        if ($breakdown['has_phone']) $score += 5;
        if ($breakdown['has_email']) $score += 5;

        // Hours (15 points)
        $hours = $restaurant->hours;
        $hasHours = !empty($hours) && is_array($hours);
        $breakdown['has_hours'] = $hasHours;
        $breakdown['hours_days_count'] = $hasHours ? count($hours) : 0;
        $breakdown['hours_complete'] = $hasHours && count($hours) >= 7;

        if ($breakdown['has_hours']) $score += 10;
        if ($breakdown['hours_complete']) $score += 5;

        // Photos (30 points max)
        $photoCount = $restaurant->getMedia('images')->count();
        $hasLogo = $restaurant->getMedia('logo')->count() > 0;
        $menuPhotoCount = $restaurant->getMedia('menu')->count();

        $breakdown['photo_count'] = $photoCount;
        $breakdown['has_logo'] = $hasLogo;
        $breakdown['menu_photo_count'] = $menuPhotoCount;

        if ($hasLogo) $score += 5;
        $score += min($photoCount * 3, 15); // Up to 5 photos = 15 points
        $score += min($menuPhotoCount * 2, 10); // Up to 5 menu photos = 10 points

        // Location (15 points)
        $breakdown['has_coordinates'] = !empty($restaurant->latitude) && !empty($restaurant->longitude);
        $breakdown['has_zip'] = !empty($restaurant->zip_code);

        if ($breakdown['has_coordinates']) $score += 10;
        if ($breakdown['has_zip']) $score += 5;

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate Online Presence score (0-100)
     */
    protected function calculateOnlinePresence(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Google Presence (35 points max)
        $breakdown['google_connected'] = !empty($restaurant->google_place_id);
        $breakdown['google_verified'] = (bool) $restaurant->google_verified;
        $breakdown['google_rating'] = $restaurant->google_rating;
        $breakdown['google_reviews'] = $restaurant->google_reviews_count ?? 0;

        if ($breakdown['google_connected']) $score += 15;
        if ($breakdown['google_verified']) $score += 5;
        if ($breakdown['google_rating'] >= 4.0) $score += 10;
        elseif ($breakdown['google_rating'] >= 3.5) $score += 5;
        if ($breakdown['google_reviews'] >= 50) $score += 5;

        // Yelp Presence (35 points max)
        $breakdown['yelp_connected'] = !empty($restaurant->yelp_id);
        $breakdown['yelp_rating'] = $restaurant->yelp_rating;
        $breakdown['yelp_reviews'] = $restaurant->yelp_reviews_count ?? 0;

        if ($breakdown['yelp_connected']) $score += 15;
        if ($breakdown['yelp_rating'] >= 4.0) $score += 15;
        elseif ($breakdown['yelp_rating'] >= 3.5) $score += 10;
        elseif ($breakdown['yelp_rating'] >= 3.0) $score += 5;
        if ($breakdown['yelp_reviews'] >= 50) $score += 5;

        // Facebook Presence (30 points max)
        $breakdown['facebook_connected'] = !empty($restaurant->facebook_page_id);
        $breakdown['facebook_rating'] = $restaurant->facebook_rating;
        $breakdown['facebook_reviews'] = $restaurant->facebook_review_count ?? 0;

        if ($breakdown['facebook_connected']) $score += 15;
        if ($breakdown['facebook_rating'] >= 4.0) $score += 10;
        elseif ($breakdown['facebook_rating'] >= 3.5) $score += 5;
        if ($breakdown['facebook_reviews'] >= 20) $score += 5;

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate Customer Engagement score (0-100)
     */
    protected function calculateCustomerEngagement(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Total Reviews quantity across platforms (40 points)
        $totalReviews = ($restaurant->total_reviews ?? 0) +
            ($restaurant->yelp_reviews_count ?? 0) +
            ($restaurant->google_reviews_count ?? 0) +
            ($restaurant->facebook_review_count ?? 0);

        $breakdown['total_reviews'] = $totalReviews;
        $breakdown['internal_reviews'] = $restaurant->total_reviews ?? 0;

        if ($totalReviews >= 200) $score += 40;
        elseif ($totalReviews >= 100) $score += 35;
        elseif ($totalReviews >= 50) $score += 25;
        elseif ($totalReviews >= 25) $score += 15;
        elseif ($totalReviews >= 10) $score += 10;
        elseif ($totalReviews > 0) $score += 5;

        // Average Rating (35 points)
        // Use best available rating
        $ratings = array_filter([
            $restaurant->average_rating,
            $restaurant->yelp_rating,
            $restaurant->google_rating,
            $restaurant->facebook_rating,
        ]);
        $avgRating = !empty($ratings) ? array_sum($ratings) / count($ratings) : 0;

        $breakdown['average_rating'] = round($avgRating, 2);
        $breakdown['rating_sources'] = count($ratings);

        if ($avgRating >= 4.5) $score += 35;
        elseif ($avgRating >= 4.0) $score += 28;
        elseif ($avgRating >= 3.5) $score += 20;
        elseif ($avgRating >= 3.0) $score += 12;
        elseif ($avgRating > 0) $score += 5;

        // Internal Platform Reviews (15 points)
        $internalReviews = $restaurant->approvedReviews()->count();
        $breakdown['famer_reviews'] = $internalReviews;

        if ($internalReviews >= 20) $score += 15;
        elseif ($internalReviews >= 10) $score += 10;
        elseif ($internalReviews >= 5) $score += 7;
        elseif ($internalReviews > 0) $score += 3;

        // User Photos (10 points)
        $userPhotos = $restaurant->approvedPhotos()->count();
        $breakdown['user_photos'] = $userPhotos;

        if ($userPhotos >= 10) $score += 10;
        elseif ($userPhotos >= 5) $score += 7;
        elseif ($userPhotos > 0) $score += 3;

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate Menu & Offerings score (0-100)
     */
    protected function calculateMenuOfferings(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Menu Items Count (40 points)
        $menuItems = $restaurant->availableMenuItems()->count();
        $breakdown['menu_item_count'] = $menuItems;

        if ($menuItems >= 30) $score += 40;
        elseif ($menuItems >= 20) $score += 30;
        elseif ($menuItems >= 10) $score += 20;
        elseif ($menuItems >= 5) $score += 10;
        elseif ($menuItems > 0) $score += 5;

        // Menu Categories Coverage (25 points)
        $categories = $restaurant->menuCategories()
            ->whereHas('items', fn($q) => $q->where('is_available', true))
            ->count();

        $breakdown['category_count'] = $categories;

        if ($categories >= 6) $score += 25;
        elseif ($categories >= 4) $score += 18;
        elseif ($categories >= 2) $score += 10;
        elseif ($categories > 0) $score += 5;

        // Dietary Options (20 points)
        $dietaryOptions = $restaurant->dietary_options ?? [];
        $dietaryCount = count($dietaryOptions);
        $breakdown['dietary_options'] = $dietaryOptions;
        $breakdown['dietary_count'] = $dietaryCount;

        if ($dietaryCount >= 4) $score += 20;
        elseif ($dietaryCount >= 2) $score += 12;
        elseif ($dietaryCount >= 1) $score += 6;

        // Price Range & Pricing (15 points)
        $hasPriceRange = !empty($restaurant->price_range);
        $hasItemPrices = $restaurant->menuItems()
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->exists();

        $breakdown['has_price_range'] = $hasPriceRange;
        $breakdown['price_range'] = $restaurant->price_range;
        $breakdown['has_item_prices'] = $hasItemPrices;

        if ($hasPriceRange) $score += 5;
        if ($hasItemPrices) $score += 10;

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate Mexican Authenticity score (0-100)
     */
    protected function calculateMexicanAuthenticity(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Authenticity Badges (45 points max)
        $breakdown['chef_certified'] = (bool) $restaurant->chef_certified;
        $breakdown['traditional_recipes'] = (bool) $restaurant->traditional_recipes;
        $breakdown['imported_ingredients'] = (bool) $restaurant->imported_ingredients;

        $badgeCount = ($breakdown['chef_certified'] ? 1 : 0) +
            ($breakdown['traditional_recipes'] ? 1 : 0) +
            ($breakdown['imported_ingredients'] ? 1 : 0);

        $breakdown['badge_count'] = $badgeCount;

        if ($breakdown['chef_certified']) $score += 15;
        if ($breakdown['traditional_recipes']) $score += 15;
        if ($breakdown['imported_ingredients']) $score += 15;

        // Mexican Region (25 points)
        $hasRegion = !empty($restaurant->mexican_region);
        $breakdown['mexican_region'] = $restaurant->mexican_region;
        $breakdown['has_region'] = $hasRegion;

        if ($hasRegion && $restaurant->mexican_region !== 'General') {
            $score += 25;
        } elseif ($hasRegion) {
            $score += 10;
        }

        // Spice Level (10 points)
        $hasSpice = !empty($restaurant->spice_level);
        $breakdown['spice_level'] = $restaurant->spice_level;
        $breakdown['has_spice_level'] = $hasSpice;

        if ($hasSpice) $score += 10;

        // Mexican Features (20 points max)
        $features = $restaurant->special_features ?? [];
        $mexicanFeatures = array_intersect($features, ['mariachi', 'live_music', 'outdoor_patio']);
        $breakdown['special_features'] = $features;
        $breakdown['mexican_features'] = array_values($mexicanFeatures);
        $breakdown['mexican_features_count'] = count($mexicanFeatures);

        $score += min(count($mexicanFeatures) * 7, 20);

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate Digital Readiness score (0-100)
     */
    protected function calculateDigitalReadiness(Restaurant $restaurant): array
    {
        $breakdown = [];
        $score = 0;

        // Website (30 points)
        $hasWebsite = !empty($restaurant->website);
        $breakdown['has_website'] = $hasWebsite;
        $breakdown['website'] = $restaurant->website;

        if ($hasWebsite) $score += 30;

        // Online Ordering (25 points)
        $hasOnlineOrdering = (bool) $restaurant->online_ordering;
        $hasOrderUrl = !empty($restaurant->order_url);
        $breakdown['online_ordering'] = $hasOnlineOrdering;
        $breakdown['has_order_url'] = $hasOrderUrl;

        if ($hasOnlineOrdering || $hasOrderUrl) $score += 25;

        // Reservations (25 points)
        $acceptsReservations = (bool) $restaurant->accepts_reservations;
        $reservationType = $restaurant->reservation_type;
        $hasReservationSystem = $reservationType && $reservationType !== 'none';

        $breakdown['accepts_reservations'] = $acceptsReservations;
        $breakdown['reservation_type'] = $reservationType;
        $breakdown['has_reservation_system'] = $hasReservationSystem;

        if ($acceptsReservations && $hasReservationSystem) $score += 25;
        elseif ($acceptsReservations) $score += 15;

        // Claimed Status & Subscription (20 points)
        $isClaimed = (bool) $restaurant->is_claimed;
        $subscriptionTier = $restaurant->subscription_tier;

        $breakdown['is_claimed'] = $isClaimed;
        $breakdown['subscription_tier'] = $subscriptionTier;

        if ($isClaimed) {
            $score += 10;
            if (in_array($subscriptionTier, ['premium', 'elite'])) {
                $score += 10;
            }
        }

        return [
            'score' => min($score, 100),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Calculate comparison data with other restaurants
     */
    protected function calculateComparison(Restaurant $restaurant, int $overallScore): array
    {
        // Area comparison (same city or state)
        $areaQuery = FamerScore::whereHas('restaurant', function ($q) use ($restaurant) {
            $q->where('status', 'approved')
                ->where('is_active', true)
                ->where('city', $restaurant->city)
                ->where('state_id', $restaurant->state_id);
        });

        $areaTotal = $areaQuery->count();
        $areaRank = $areaQuery->where('overall_score', '>', $overallScore)->count() + 1;
        $areaAverage = $areaQuery->avg('overall_score');

        // Category comparison
        $categoryQuery = FamerScore::whereHas('restaurant', function ($q) use ($restaurant) {
            $q->where('status', 'approved')
                ->where('is_active', true)
                ->where('category_id', $restaurant->category_id);
        });

        $categoryTotal = $categoryQuery->count();
        $categoryRank = $categoryQuery->where('overall_score', '>', $overallScore)->count() + 1;
        $categoryAverage = $categoryQuery->avg('overall_score');

        return [
            'area_rank' => $areaRank,
            'area_total' => max($areaTotal, 1),
            'area_average' => round($areaAverage ?? 0, 2),
            'category_rank' => $categoryRank,
            'category_total' => max($categoryTotal, 1),
            'category_average' => round($categoryAverage ?? 0, 2),
        ];
    }

    /**
     * Generate recommendations based on scores
     */
    protected function generateRecommendations(
        Restaurant $restaurant,
        array $profile,
        array $presence,
        array $engagement,
        array $menu,
        array $authenticity,
        array $digital
    ): array {
        $recommendations = [];

        // CRITICAL: Claim recommendation (if not claimed)
        if (!$digital['breakdown']['is_claimed']) {
            $recommendations[] = [
                'category' => 'digital',
                'priority' => 'critical',
                'title' => 'Reclama tu Restaurante en FAMER',
                'description' => 'Toma control de tu perfil, responde reseñas y desbloquea funciones premium.',
                'impact' => '+10-20 puntos',
                'effort' => 'easy',
                'action_url' => '/claim',
                'action_label' => 'Reclamar Ahora',
            ];
        }

        // CRITICAL: Google Business Profile
        if (!$presence['breakdown']['google_connected']) {
            $recommendations[] = [
                'category' => 'presence',
                'priority' => 'critical',
                'title' => 'Conecta Google Business Profile',
                'description' => 'Aparece en Google Maps y mejora tu visibilidad en búsquedas locales.',
                'impact' => '+15-20 puntos',
                'effort' => 'medium',
            ];
        }

        // HIGH: Yelp presence
        if (!$presence['breakdown']['yelp_connected']) {
            $recommendations[] = [
                'category' => 'presence',
                'priority' => 'high',
                'title' => 'Reclama tu Página de Yelp',
                'description' => 'Yelp es crucial para que los clientes descubran restaurantes.',
                'impact' => '+15 puntos',
                'effort' => 'medium',
            ];
        }

        // HIGH: Photos
        if ($profile['breakdown']['photo_count'] < 5) {
            $recommendations[] = [
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Agrega Más Fotos',
                'description' => 'Sube al menos 5 fotos de alta calidad de tu restaurante, platillos y ambiente.',
                'impact' => '+10-15 puntos',
                'effort' => 'easy',
            ];
        }

        // HIGH: Description
        if (!$profile['breakdown']['has_description'] || $profile['breakdown']['description_quality'] !== 'excellent') {
            $recommendations[] = [
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Mejora tu Descripción',
                'description' => 'Agrega una descripción detallada (200+ caracteres) destacando tu cocina, historia y ofertas únicas.',
                'impact' => '+5-10 puntos',
                'effort' => 'easy',
            ];
        }

        // HIGH: Business hours
        if (!$profile['breakdown']['has_hours']) {
            $recommendations[] = [
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Agrega Horarios de Operación',
                'description' => 'Los clientes necesitan saber cuándo estás abierto.',
                'impact' => '+10-15 puntos',
                'effort' => 'easy',
            ];
        }

        // HIGH: Menu
        if ($menu['breakdown']['menu_item_count'] < 10) {
            $recommendations[] = [
                'category' => 'menu',
                'priority' => 'high',
                'title' => 'Agrega tu Menú Completo',
                'description' => 'Muestra tus platillos agregando al menos 10 items con precios.',
                'impact' => '+20-30 puntos',
                'effort' => 'medium',
            ];
        }

        // HIGH: Website
        if (!$digital['breakdown']['has_website']) {
            $recommendations[] = [
                'category' => 'digital',
                'priority' => 'high',
                'title' => 'Crea un Sitio Web',
                'description' => 'Un sitio web ayuda a los clientes a encontrarte y conocer tu menú.',
                'impact' => '+30 puntos',
                'effort' => 'medium',
            ];
        }

        // HIGH: Online ordering
        if (!$digital['breakdown']['online_ordering'] && !$digital['breakdown']['has_order_url']) {
            $recommendations[] = [
                'category' => 'digital',
                'priority' => 'high',
                'title' => 'Activa Pedidos Online',
                'description' => 'Ofrece pedidos online para capturar más ventas.',
                'impact' => '+25 puntos',
                'effort' => 'medium',
            ];
        }

        // MEDIUM: Reservations
        if (!$digital['breakdown']['accepts_reservations']) {
            $recommendations[] = [
                'category' => 'digital',
                'priority' => 'medium',
                'title' => 'Acepta Reservaciones',
                'description' => 'Permite que tus clientes reserven mesa online.',
                'impact' => '+15-25 puntos',
                'effort' => 'medium',
            ];
        }

        // MEDIUM: Facebook
        if (!$presence['breakdown']['facebook_connected']) {
            $recommendations[] = [
                'category' => 'presence',
                'priority' => 'medium',
                'title' => 'Conecta Facebook',
                'description' => 'Agrega tu página de Facebook para aumentar tu presencia social.',
                'impact' => '+15 puntos',
                'effort' => 'easy',
            ];
        }

        // MEDIUM: Authenticity badges
        $badgeCount = $authenticity['breakdown']['badge_count'];
        if ($badgeCount < 2) {
            $recommendations[] = [
                'category' => 'authenticity',
                'priority' => 'medium',
                'title' => 'Obtén Badges de Autenticidad',
                'description' => 'Destaca tu herencia mexicana con certificaciones de chef, recetas tradicionales o ingredientes importados.',
                'impact' => '+15-30 puntos',
                'effort' => 'medium',
            ];
        }

        // MEDIUM: Mexican region
        if (!$authenticity['breakdown']['has_region'] || $authenticity['breakdown']['mexican_region'] === 'General') {
            $recommendations[] = [
                'category' => 'authenticity',
                'priority' => 'medium',
                'title' => 'Especifica tu Región Mexicana',
                'description' => 'Indica la región de México que representa tu cocina (Oaxaca, Jalisco, Yucatán, etc.).',
                'impact' => '+15-25 puntos',
                'effort' => 'easy',
            ];
        }

        // MEDIUM: Dietary options
        if ($menu['breakdown']['dietary_count'] < 2) {
            $recommendations[] = [
                'category' => 'menu',
                'priority' => 'medium',
                'title' => 'Agrega Opciones Dietéticas',
                'description' => 'Indica opciones vegetarianas, veganas, sin gluten, etc.',
                'impact' => '+12-20 puntos',
                'effort' => 'easy',
            ];
        }

        // LOW: Logo
        if (!$profile['breakdown']['has_logo']) {
            $recommendations[] = [
                'category' => 'profile',
                'priority' => 'low',
                'title' => 'Agrega tu Logo',
                'description' => 'Un logo profesional mejora el reconocimiento de marca.',
                'impact' => '+5 puntos',
                'effort' => 'easy',
            ];
        }

        // Sort by priority
        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        usort($recommendations, function ($a, $b) use ($priorityOrder) {
            return ($priorityOrder[$a['priority']] ?? 4) <=> ($priorityOrder[$b['priority']] ?? 4);
        });

        return array_slice($recommendations, 0, 15); // Return top 15 recommendations
    }

    /**
     * Get letter grade for a score
     */
    protected function getLetterGrade(int $score): string
    {
        foreach (self::GRADES as $threshold => $grade) {
            if ($score >= $threshold) {
                return $grade;
            }
        }
        return 'F';
    }

    /**
     * Calculate score for an external restaurant (from Yelp/Google search)
     */
    public function calculateExternalScore(array $externalData): array
    {
        // Build a partial score based on available external data
        $score = 0;
        $breakdown = [];

        // Online Presence (from external source)
        if (!empty($externalData['yelp_id'])) {
            $score += 15; // Yelp connected
            $breakdown['yelp_connected'] = true;

            if (($externalData['yelp_rating'] ?? 0) >= 4.0) $score += 15;
            elseif (($externalData['yelp_rating'] ?? 0) >= 3.5) $score += 10;

            if (($externalData['yelp_reviews'] ?? 0) >= 50) $score += 5;
        }

        if (!empty($externalData['google_place_id'])) {
            $score += 15; // Google connected
            $breakdown['google_connected'] = true;

            if (($externalData['google_rating'] ?? 0) >= 4.0) $score += 10;

            if (($externalData['google_reviews'] ?? 0) >= 50) $score += 5;
        }

        // Basic profile info
        if (!empty($externalData['name'])) $score += 5;
        if (!empty($externalData['address'])) $score += 5;
        if (!empty($externalData['phone'])) $score += 5;
        if (!empty($externalData['website'])) $score += 15;

        // Normalize to 0-100 scale (max possible from external is ~90)
        $normalizedScore = min((int) round($score * 100 / 90), 100);

        return [
            'overall_score' => $normalizedScore,
            'letter_grade' => $this->getLetterGrade($normalizedScore),
            'breakdown' => $breakdown,
            'is_partial' => true,
            'message' => 'Score parcial basado en datos externos. Reclama tu restaurante para un score completo.',
        ];
    }
}
