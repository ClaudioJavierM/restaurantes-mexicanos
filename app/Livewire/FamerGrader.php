<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\FamerScore;
use App\Models\FamerScoreRequest;
use App\Services\FamerScoreService;
use App\Services\YelpFusionService;
use App\Services\GooglePlacesService;
use App\Mail\FamerScoreReport;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.owners-public')]
#[Title('FAMER Score - Califica tu Restaurante Mexicano')]
class FamerGrader extends Component
{
    // URL parameters
    #[Url]
    public string $q = '';

    // Search state
    public string $searchName = '';
    public string $searchCity = '';
    public string $searchState = '';
    public array $searchResults = [];
    public bool $isSearching = false;
    public bool $hasSearched = false;

    // Selected restaurant state
    public ?int $selectedRestaurantId = null;
    public ?array $selectedRestaurant = null;
    public ?array $scoreData = null;
    public bool $isCalculating = false;
    public bool $isExternalRestaurant = false;
    public bool $showAnalysis = false;
    public bool $analysisComplete = false;

    // Lead capture state
    public bool $showEmailCapture = false;
    public string $leadEmail = '';
    public string $leadName = '';
    public string $leadPhone = '';
    public bool $marketingConsent = false;
    public bool $isOwner = false;
    public bool $emailSubmitted = false;
    public bool $isSendingEmail = false;

    // Scan step data for frontend animation
    public array $scanStepData = [];

    // Platform stats (dynamic, rounded to nearest 1000)
    public int $totalRestaurantsRounded = 0;

    // Messages
    public string $errorMessage = '';
    public string $successMessage = '';

    protected FamerScoreService $scoreService;
    protected YelpFusionService $yelpService;
    protected GooglePlacesService $googleService;

    public function boot(
        FamerScoreService $scoreService,
        YelpFusionService $yelpService,
        GooglePlacesService $googleService
    ) {
        $this->scoreService = $scoreService;
        $this->yelpService = $yelpService;
        $this->googleService = $googleService;
    }

    public function mount(?string $slug = null)
    {
        // Dynamic restaurant count rounded to nearest 1000
        $total = Cache::remember('grader_total_restaurants', 3600, function () {
            return Restaurant::approved()->forCurrentCountry()->count();
        });
        $this->totalRestaurantsRounded = (int) (floor($total / 1000) * 1000);

        // If a restaurant slug is provided, load it directly
        if ($slug) {
            $restaurant = Restaurant::where('slug', $slug)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if ($restaurant) {
                $this->selectRestaurantById($restaurant->id);
            }
        }
    }

    /**
     * Search for restaurants
     */
    public function search()
    {
        $this->validate([
            'searchName' => 'required|min:2',
            'searchCity' => 'required|min:2',
        ], [
            'searchName.required' => 'Ingresa el nombre del restaurante',
            'searchName.min' => 'El nombre debe tener al menos 2 caracteres',
            'searchCity.required' => 'Ingresa la ciudad',
            'searchCity.min' => 'La ciudad debe tener al menos 2 caracteres',
        ]);

        $this->isSearching = true;
        $this->searchResults = [];
        $this->hasSearched = true;
        $this->errorMessage = '';
        $this->resetScore();

        try {
            // 1. Search in our database first
            $dbResults = $this->searchInDatabase();

            // 2. Search in Yelp if we have few or no results
            $yelpResults = [];
            if (count($dbResults) < 3) {
                $yelpResults = $this->searchInYelp();
            }

            // 3. Search in Google if still no results
            $googleResults = [];
            if (count($dbResults) === 0 && count($yelpResults) === 0) {
                $googleResults = $this->searchInGoogle();
            }

            // Combine and deduplicate results
            $this->searchResults = $this->combineResults($dbResults, $yelpResults, $googleResults);

            if (empty($this->searchResults)) {
                $this->errorMessage = "No encontramos \"{$this->searchName}\" en {$this->searchCity}. Intenta con otro nombre o ciudad.";
            }
        } catch (\Exception $e) {
            Log::error('FAMER Grader search error: ' . $e->getMessage());
            $this->errorMessage = 'Error al buscar. Por favor intenta de nuevo.';
        }

        $this->isSearching = false;
    }

    /**
     * Search in our database
     */
    protected function searchInDatabase(): array
    {
        $results = [];

        $restaurants = Restaurant::where('status', 'approved')
            ->where('is_active', true)
            ->forCurrentCountry()
            ->where('name', 'LIKE', '%' . $this->searchName . '%')
            ->where('city', 'LIKE', '%' . $this->searchCity . '%')
            ->when($this->searchState, function ($query) {
                $query->whereHas('state', function ($q) {
                    $q->where('code', $this->searchState);
                });
            })
            ->with(['state', 'category', 'famerScore'])
            ->limit(5)
            ->get();

        foreach ($restaurants as $restaurant) {
            $results[] = [
                'source' => 'famer',
                'source_label' => 'FAMER',
                'source_color' => 'emerald',
                'id' => $restaurant->id,
                'restaurant_id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'city' => $restaurant->city,
                'state' => $restaurant->state?->code,
                'phone' => $restaurant->phone,
                'website' => $restaurant->website,
                'rating' => $restaurant->average_rating ?? $restaurant->yelp_rating ?? $restaurant->google_rating,
                'review_count' => $restaurant->total_reviews ?? 0,
                'image_url' => $restaurant->getFirstMediaUrl('images') ?: $restaurant->getFirstMediaUrl('logo'),
                'is_claimed' => $restaurant->is_claimed,
                'has_score' => $restaurant->famerScore !== null,
                'existing_score' => $restaurant->famerScore?->overall_score,
                'existing_grade' => $restaurant->famerScore?->letter_grade,
            ];
        }

        return $results;
    }

    /**
     * Search in Yelp
     */
    protected function searchInYelp(): array
    {
        $results = [];

        try {
            $response = $this->yelpService->searchBusinesses(
                $this->searchName,
                $this->searchCity,
                $this->searchState ?: 'US',
                5
            );

            if ($response && !empty($response['businesses'])) {
                foreach ($response['businesses'] as $business) {
                    // Check if already in our database
                    $exists = Restaurant::where('yelp_id', $business['id'])->exists();
                    if ($exists) continue;

                    $results[] = [
                        'source' => 'yelp',
                        'source_label' => 'Yelp',
                        'source_color' => 'red',
                        'id' => 'yelp_' . $business['id'],
                        'yelp_id' => $business['id'],
                        'name' => $business['name'],
                        'address' => $business['location']['address1'] ?? '',
                        'city' => $business['location']['city'] ?? '',
                        'state' => $business['location']['state'] ?? '',
                        'zip_code' => $business['location']['zip_code'] ?? '',
                        'phone' => $business['display_phone'] ?? '',
                        'website' => null,
                        'rating' => $business['rating'] ?? null,
                        'review_count' => $business['review_count'] ?? 0,
                        'image_url' => $business['image_url'] ?? null,
                        'yelp_url' => $business['url'] ?? null,
                        'is_claimed' => false,
                        'has_score' => false,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Yelp search in grader failed: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Search in Google
     */
    protected function searchInGoogle(): array
    {
        $results = [];

        try {
            $place = $this->googleService->findPlace(
                $this->searchName,
                '',
                $this->searchCity,
                $this->searchState
            );

            if ($place && !empty($place['place_id'])) {
                // Check if already in our database
                $exists = Restaurant::where('google_place_id', $place['place_id'])->exists();

                if (!$exists) {
                    $details = $this->googleService->getPlaceDetails($place['place_id']);

                    $results[] = [
                        'source' => 'google',
                        'source_label' => 'Google',
                        'source_color' => 'blue',
                        'id' => 'google_' . $place['place_id'],
                        'google_place_id' => $place['place_id'],
                        'name' => $place['name'] ?? '',
                        'address' => $details['formatted_address'] ?? $place['formatted_address'] ?? '',
                        'city' => $this->searchCity,
                        'state' => $this->searchState,
                        'phone' => $details['formatted_phone_number'] ?? '',
                        'website' => $details['website'] ?? null,
                        'rating' => $place['rating'] ?? null,
                        'review_count' => $place['user_ratings_total'] ?? 0,
                        'image_url' => null,
                        'google_url' => $details['url'] ?? null,
                        'is_claimed' => false,
                        'has_score' => false,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Google search in grader failed: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Combine and deduplicate results from different sources
     */
    protected function combineResults(array $db, array $yelp, array $google): array
    {
        $combined = array_merge($db, $yelp, $google);

        // Remove duplicates by name similarity
        $unique = [];
        foreach ($combined as $result) {
            $isDuplicate = false;
            foreach ($unique as $existing) {
                $similarity = 0;
                similar_text(
                    strtolower($result['name']),
                    strtolower($existing['name']),
                    $similarity
                );
                if ($similarity > 80) {
                    $isDuplicate = true;
                    break;
                }
            }
            if (!$isDuplicate) {
                $unique[] = $result;
            }
        }

        // Sort: FAMER first, then by rating
        usort($unique, function ($a, $b) {
            if ($a['source'] === 'famer' && $b['source'] !== 'famer') return -1;
            if ($b['source'] === 'famer' && $a['source'] !== 'famer') return 1;
            return ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
        });

        return array_slice($unique, 0, 8);
    }

    /**
     * Select a restaurant and start analysis animation
     */
    public function selectResult(string $resultId)
    {
        $result = collect($this->searchResults)->firstWhere('id', $resultId);

        if (!$result) {
            $this->errorMessage = 'Restaurante no encontrado.';
            return;
        }

        $this->selectedRestaurant = $result;
        $this->isExternalRestaurant = $result['source'] !== 'famer';
        $this->showAnalysis = true;
        $this->analysisComplete = false;
        $this->isCalculating = true;

        // Calculate the score in background - frontend will animate
        try {
            if ($result['source'] === 'famer') {
                $this->selectRestaurantById($result['restaurant_id']);
            } else {
                $this->calculateExternalScore($result);
            }
        } catch (\Exception $e) {
            Log::error('Error calculating score: ' . $e->getMessage());
            $this->errorMessage = 'Error al calcular el score.';
            $this->showAnalysis = false;
        }

        $this->isCalculating = false;
    }

    /**
     * Called when analysis animation completes
     */
    public function completeAnalysis()
    {
        $this->analysisComplete = true;
        $this->showAnalysis = false;
    }

    /**
     * Select and score a restaurant by ID
     */
    protected function selectRestaurantById(int $restaurantId)
    {
        $restaurant = Restaurant::with(['state', 'category'])->find($restaurantId);

        if (!$restaurant) {
            $this->errorMessage = 'Restaurante no encontrado.';
            return;
        }

        $this->selectedRestaurantId = $restaurantId;
        $this->selectedRestaurant = [
            'id' => $restaurant->id,
            'source' => 'famer',
            'name' => $restaurant->name,
            'address' => $restaurant->address,
            'city' => $restaurant->city,
            'state' => $restaurant->state?->code,
            'slug' => $restaurant->slug,
            'is_claimed' => $restaurant->is_claimed,
            'logo_url' => $restaurant->getFirstMediaUrl('logo'),
        ];
        $this->isExternalRestaurant = false;

        // Build primary image URL
        $imgUrl = $restaurant->getFirstMediaUrl('images') ?: $restaurant->getFirstMediaUrl('logo');

        // Populate scan step data for frontend animation
        $this->scanStepData = [
            'restaurant' => [
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'city' => $restaurant->city,
                'state' => $restaurant->state->code ?? '',
                'lat' => $restaurant->latitude,
                'lng' => $restaurant->longitude,
                'phone' => $restaurant->phone,
                'website' => $restaurant->website,
                'image' => $imgUrl,
            ],
            'competitors' => [],
            'reviews' => [],
            'photos' => [],
            'google_rating' => $restaurant->google_rating,
            'google_reviews_count' => $restaurant->google_reviews_count,
            'yelp_rating' => $restaurant->yelp_rating,
            'yelp_reviews_count' => $restaurant->yelp_reviews_count,
        ];

        // Competitors: up to 5 other restaurants in the same city
        $competitors = Restaurant::where('status', 'approved')
            ->where('is_active', true)
            ->where('city', $restaurant->city)
            ->where('state_id', $restaurant->state_id)
            ->where('id', '!=', $restaurant->id)
            ->orderByDesc('google_rating')
            ->limit(5)
            ->get(['name', 'google_rating', 'google_reviews_count', 'address']);

        $this->scanStepData['competitors'] = $competitors->map(fn($c) => [
            'name' => $c->name,
            'rating' => $c->google_rating,
            'reviews' => $c->google_reviews_count,
        ])->toArray();

        // Reviews: recent approved reviews
        $reviews = $restaurant->reviews()
            ->where('status', 'approved')
            ->latest()
            ->limit(5)
            ->get();

        $this->scanStepData['reviews'] = $reviews->map(fn($r) => [
            'name' => $r->reviewer_name ?? 'Anonymous',
            'rating' => $r->rating,
            'comment' => $r->comment,
            'date' => $r->created_at->diffForHumans(),
        ])->toArray();

        // Photos: collect from multiple sources
        $photos = [];
        if ($restaurant->image) {
            $photos[] = str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image);
        }
        foreach ($restaurant->getMedia('images') as $media) {
            $photos[] = $media->getUrl();
            if (count($photos) >= 8) break;
        }
        if (is_array($restaurant->yelp_photos)) {
            foreach ($restaurant->yelp_photos as $photo) {
                $photos[] = $photo;
                if (count($photos) >= 8) break;
            }
        }
        $this->scanStepData['photos'] = array_slice($photos, 0, 8);

        // Calculate score
        $famerScore = $this->scoreService->getScore($restaurant);

        $this->scoreData = [
            'overall_score' => $famerScore->overall_score,
            'letter_grade' => $famerScore->letter_grade,
            'grade_color' => $famerScore->grade_color,
            'categories' => $famerScore->category_scores,
            'top_recommendations' => $famerScore->top_recommendations,
            'all_recommendations' => $famerScore->recommendations ?? [],
            'percentile' => $famerScore->percentile,
            'area_rank' => $famerScore->area_rank,
            'area_total' => $famerScore->area_total,
            'score_description' => $famerScore->score_description,
            'is_partial' => false,
        ];
    }

    /**
     * Calculate partial score for external restaurant
     */
    protected function calculateExternalScore(array $result)
    {
        $externalData = [
            'name' => $result['name'],
            'address' => $result['address'],
            'phone' => $result['phone'] ?? null,
            'website' => $result['website'] ?? null,
            'yelp_id' => $result['yelp_id'] ?? null,
            'yelp_rating' => $result['rating'] ?? null,
            'yelp_reviews' => $result['review_count'] ?? 0,
            'google_place_id' => $result['google_place_id'] ?? null,
            'google_rating' => $result['rating'] ?? null,
            'google_reviews' => $result['review_count'] ?? 0,
        ];

        // Populate scan step data for external restaurant
        $this->scanStepData = [
            'restaurant' => [
                'name' => $result['name'] ?? '',
                'address' => $result['address'] ?? '',
                'city' => $result['city'] ?? '',
                'state' => $result['state_code'] ?? $result['state'] ?? '',
                'lat' => $result['lat'] ?? null,
                'lng' => $result['lng'] ?? null,
                'phone' => $result['phone'] ?? null,
                'website' => $result['website'] ?? null,
                'image' => $result['image_url'] ?? $result['image'] ?? null,
            ],
            'competitors' => [],
            'reviews' => [],
            'photos' => $result['photos'] ?? [],
            'google_rating' => $result['rating'] ?? null,
            'google_reviews_count' => $result['review_count'] ?? null,
        ];

        $partialScore = $this->scoreService->calculateExternalScore($externalData);

        $this->scoreData = [
            'overall_score' => $partialScore['overall_score'],
            'letter_grade' => $partialScore['letter_grade'],
            'grade_color' => $this->getGradeColor($partialScore['letter_grade']),
            'categories' => null, // No category breakdown for external
            'top_recommendations' => [
                [
                    'category' => 'digital',
                    'priority' => 'critical',
                    'title' => 'Agrega tu Restaurante a FAMER',
                    'description' => 'Reclama tu perfil para obtener un score completo y acceder a herramientas de marketing.',
                    'impact' => '+20-40 puntos',
                    'action_url' => '/claim',
                    'action_label' => 'Agregar a FAMER',
                ],
                [
                    'category' => 'profile',
                    'priority' => 'high',
                    'title' => 'Completa tu Perfil',
                    'description' => 'Agrega fotos, menú, horarios y más para mejorar tu visibilidad.',
                    'impact' => '+15-25 puntos',
                ],
                [
                    'category' => 'presence',
                    'priority' => 'high',
                    'title' => 'Conecta tus Redes',
                    'description' => 'Vincula tu perfil de Google, Yelp y Facebook.',
                    'impact' => '+15-20 puntos',
                ],
            ],
            'all_recommendations' => [],
            'percentile' => null,
            'area_rank' => null,
            'area_total' => null,
            'score_description' => 'Score estimado basado en datos públicos. Reclama tu restaurante para un análisis completo.',
            'is_partial' => true,
            'message' => $partialScore['message'] ?? null,
        ];
    }

    /**
     * Show email capture modal
     */
    public function requestFullReport()
    {
        $this->showEmailCapture = true;
    }

    /**
     * Submit email and send full report
     */
    public function submitEmailForReport()
    {
        $this->validate([
            'leadEmail' => 'required|email',
            'leadName' => 'nullable|string|max:255',
            'leadPhone' => 'nullable|string|max:20',
        ], [
            'leadEmail.required' => 'El email es requerido',
            'leadEmail.email' => 'Ingresa un email válido',
        ]);

        $this->isSendingEmail = true;

        try {
            // Create lead request
            $request = FamerScoreRequest::create([
                'restaurant_id' => $this->selectedRestaurantId,
                'famer_score_id' => $this->selectedRestaurantId
                    ? FamerScore::where('restaurant_id', $this->selectedRestaurantId)->first()?->id
                    : null,
                'email' => $this->leadEmail,
                'name' => $this->leadName,
                'phone' => $this->leadPhone,
                'restaurant_name' => $this->selectedRestaurant['name'] ?? null,
                'restaurant_city' => $this->selectedRestaurant['city'] ?? null,
                'restaurant_state' => $this->selectedRestaurant['state'] ?? null,
                'yelp_id' => $this->selectedRestaurant['yelp_id'] ?? null,
                'google_place_id' => $this->selectedRestaurant['google_place_id'] ?? null,
                'utm_source' => request()->input('utm_source'),
                'utm_medium' => request()->input('utm_medium'),
                'utm_campaign' => request()->input('utm_campaign'),
                'referrer' => request()->header('referer'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'marketing_consent' => $this->marketingConsent,
                'is_owner' => $this->isOwner,
                'status' => 'pending',
            ]);

            // Send email
            Mail::to($this->leadEmail)->queue(new FamerScoreReport(
                $request,
                $this->selectedRestaurant,
                $this->scoreData
            ));

            $request->markAsSent();

            $this->emailSubmitted = true;
            $this->showEmailCapture = false;
            $this->successMessage = "¡Listo! Tu reporte completo ha sido enviado a {$this->leadEmail}";

            Log::info("FAMER Score report sent to {$this->leadEmail} for restaurant: " . ($this->selectedRestaurant['name'] ?? 'External'));

        } catch (\Exception $e) {
            Log::error('Error sending FAMER score report: ' . $e->getMessage());
            $this->errorMessage = 'Error al enviar el reporte. Por favor intenta de nuevo.';
        }

        $this->isSendingEmail = false;
    }

    /**
     * Reset and start new search
     */
    public function resetSearch()
    {
        $this->reset([
            'searchName',
            'searchCity',
            'searchState',
            'searchResults',
            'hasSearched',
            'errorMessage',
            'successMessage',
            'scanStepData',
        ]);
        $this->resetScore();
    }

    /**
     * Reset score state
     */
    protected function resetScore()
    {
        $this->selectedRestaurantId = null;
        $this->selectedRestaurant = null;
        $this->scoreData = null;
        $this->scanStepData = [];
        $this->isExternalRestaurant = false;
        $this->showAnalysis = false;
        $this->analysisComplete = false;
        $this->showEmailCapture = false;
        $this->emailSubmitted = false;
        $this->leadEmail = '';
        $this->leadName = '';
        $this->leadPhone = '';
        $this->marketingConsent = false;
        $this->isOwner = false;
    }

    /**
     * Get grade color
     */
    protected function getGradeColor(string $grade): string
    {
        return match (substr($grade, 0, 1)) {
            'A' => 'emerald',
            'B' => 'blue',
            'C' => 'yellow',
            'D' => 'orange',
            default => 'red',
        };
    }

    public function render()
    {
        $states = State::where('is_active', true)
            ->forCurrentCountry()
            ->orderBy('name')
            ->get();

        return view('livewire.famer-grader', [
            'states' => $states,
        ]);
    }
}
