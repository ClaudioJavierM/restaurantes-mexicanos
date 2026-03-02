<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Restaurant;
use App\Models\Suggestion;
use App\Models\State;
use App\Services\GooglePlacesService;
use App\Services\YelpFusionService;
use App\Services\BusinessValidationService;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;

class SmartSuggestionForm extends Component
{
    // Step management
    public int $step = 1; // 1 = search, 2 = form

    // Search fields
    public string $searchName = '';
    public string $searchCity = '';
    public string $searchState = '';

    // Search results
    public array $searchResults = [];
    public bool $isSearching = false;
    public bool $hasSearched = false;
    public ?array $selectedResult = null;
    public string $resultSource = ''; // 'database', 'yelp', 'google'

    // Form fields (same as SuggestionForm)
    #[Validate('required|min:3')]
    public $submitter_name = '';

    #[Validate('required|email')]
    public $submitter_email = '';

    #[Validate('required|min:3')]
    public $restaurant_name = '';

    #[Validate('required')]
    public $restaurant_address = '';

    #[Validate('required')]
    public $restaurant_city = '';

    #[Validate('required')]
    public $restaurant_state = '';

    #[Validate('nullable')]
    public $restaurant_phone = '';

    #[Validate('nullable|url')]
    public $restaurant_website = '';

    public $restaurant_zip_code = '';

    #[Validate('required|exists:categories,id')]
    public $category_id = '';

    public $submitter_phone = '';
    public $description = '';

    #[Validate('nullable')]
    public $notes = '';

    // External data
    public $yelp_id = null;
    public $yelp_data = null;
    public $google_place_id = null;
    public $google_data = null;

    public bool $showSuccessMessage = false;

    public function mount()
    {
        if (auth()->check()) {
            $this->submitter_name = auth()->user()->name;
            $this->submitter_email = auth()->user()->email;
        }
    }

    /**
     * Search for the restaurant across platforms with automatic fallback
     */
    public function search()
    {
        $this->validate([
            'searchName' => 'required|min:2',
            'searchCity' => 'required|min:2',
        ]);

        $this->isSearching = true;
        $this->searchResults = [];
        $this->hasSearched = true;
        $yelpFailed = false;

        try {
            // 1. First, search in our database
            $dbResults = $this->searchInDatabase();

            // 2. Try Yelp first
            $yelpResults = [];
            try {
                $yelpResults = $this->searchInYelp();
            } catch (\Exception $e) {
                Log::warning('Yelp search failed, using Google fallback: ' . $e->getMessage());
                $yelpFailed = true;
            }

            // 3. Search in Google (expanded if Yelp failed or returned few results)
            $needMoreResults = $yelpFailed || count($yelpResults) < 2;
            $googleResults = $this->searchInGoogle($needMoreResults ? 5 : 1);

            // Combine results
            $this->searchResults = array_merge($dbResults, $yelpResults, $googleResults);

            // Remove duplicates by name similarity
            $this->searchResults = $this->removeDuplicateResults($this->searchResults);

            // Sort by relevance/rating
            usort($this->searchResults, function($a, $b) {
                // Prioritize database results
                if ($a['source'] === 'database' && $b['source'] !== 'database') return -1;
                if ($b['source'] === 'database' && $a['source'] !== 'database') return 1;

                // Then by rating
                $ratingA = $a['rating'] ?? 0;
                $ratingB = $b['rating'] ?? 0;
                return $ratingB <=> $ratingA;
            });

            // Show message if using fallback
            if ($yelpFailed && !empty($googleResults)) {
                session()->flash('info', 'Usando Google Maps como fuente alternativa.');
            }

        } catch (\Exception $e) {
            Log::error('Smart search error: ' . $e->getMessage());
            session()->flash('error', 'Error al buscar. Por favor intenta de nuevo.');
        }

        $this->isSearching = false;
    }

    /**
     * Remove duplicate results based on name similarity
     */
    protected function removeDuplicateResults(array $results): array
    {
        $unique = [];
        $seenNames = [];

        foreach ($results as $result) {
            $normalizedName = strtolower(trim($result['name']));
            $isDuplicate = false;

            foreach ($seenNames as $seen) {
                similar_text($normalizedName, $seen, $percent);
                if ($percent > 85) {
                    $isDuplicate = true;
                    break;
                }
            }

            if (!$isDuplicate) {
                $unique[] = $result;
                $seenNames[] = $normalizedName;
            }
        }

        return $unique;
    }

    /**
     * Search in our database
     */
    protected function searchInDatabase(): array
    {
        $results = [];

        $restaurants = Restaurant::where('status', 'approved')
            ->where(function($query) {
                $query->where('name', 'LIKE', '%' . $this->searchName . '%')
                    ->orWhere('name', 'SOUNDS LIKE', $this->searchName);
            })
            ->where('city', 'LIKE', '%' . $this->searchCity . '%')
            ->when($this->searchState, function($query) {
                $query->whereHas('state', function($q) {
                    $q->where('code', $this->searchState)
                      ->orWhere('name', 'LIKE', '%' . $this->searchState . '%');
                });
            })
            ->limit(3)
            ->get();

        foreach ($restaurants as $restaurant) {
            $results[] = [
                'source' => 'database',
                'source_label' => 'Ya en FAMER',
                'source_color' => 'green',
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'city' => $restaurant->city,
                'state' => $restaurant->state?->code,
                'phone' => $restaurant->phone,
                'website' => $restaurant->website,
                'rating' => $restaurant->yelp_rating ?? $restaurant->google_rating,
                'review_count' => $restaurant->yelp_review_count ?? $restaurant->google_reviews_count,
                'image_url' => $restaurant->photos?->first()?->photo_url,
                'url' => route('restaurants.show', $restaurant->slug),
                'is_existing' => true,
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

        $yelpService = new YelpFusionService();
        $response = $yelpService->searchBusinesses(
            $this->searchName,
            $this->searchCity,
            $this->searchState ?: 'US',
            5
        );

        if ($response && !empty($response['businesses'])) {
            foreach ($response['businesses'] as $business) {
                // Check if already in our database by yelp_id
                $exists = Restaurant::where('yelp_id', $business['id'])->exists();
                if ($exists) continue;

                $results[] = [
                    'source' => 'yelp',
                    'source_label' => 'Yelp',
                    'source_color' => 'red',
                    'id' => $business['id'],
                    'yelp_id' => $business['id'],
                    'name' => $business['name'],
                    'address' => $business['location']['address1'] ?? '',
                    'city' => $business['location']['city'] ?? '',
                    'state' => $business['location']['state'] ?? '',
                    'zip_code' => $business['location']['zip_code'] ?? '',
                    'phone' => $business['display_phone'] ?? $business['phone'] ?? '',
                    'website' => null, // Yelp doesn't provide website in search
                    'rating' => $business['rating'] ?? null,
                    'review_count' => $business['review_count'] ?? 0,
                    'image_url' => $business['image_url'] ?? null,
                    'url' => $business['url'] ?? null,
                    'coordinates' => $business['coordinates'] ?? null,
                    'categories' => $business['categories'] ?? [],
                    'is_existing' => false,
                ];
            }
        }

        return $results;
    }

    /**
     * Search in Google Places with fallback support
     * @param int $limit Number of results to return (increased when Yelp fails)
     */
    protected function searchInGoogle(int $limit = 1): array
    {
        $results = [];

        try {
            $googleService = new GooglePlacesService();

            // If we need multiple results, use text search
            if ($limit > 1) {
                $places = $googleService->searchPlaces(
                    $this->searchName,
                    $this->searchCity,
                    $this->searchState,
                    $limit
                );

                foreach ($places as $place) {
                    // Check if already in our database
                    $placeId = $place['place_id'] ?? null;
                    if (!$placeId) continue;

                    $exists = Restaurant::where('google_place_id', $placeId)->exists();
                    if ($exists) continue;

                    $results[] = [
                        'source' => 'google',
                        'source_label' => 'Google',
                        'source_color' => 'blue',
                        'id' => $placeId,
                        'google_place_id' => $placeId,
                        'name' => $place['name'] ?? '',
                        'address' => $place['formatted_address'] ?? '',
                        'city' => $this->searchCity,
                        'state' => $this->searchState,
                        'phone' => '',
                        'website' => null,
                        'rating' => $place['rating'] ?? null,
                        'review_count' => $place['user_ratings_total'] ?? 0,
                        'image_url' => null,
                        'url' => null,
                        'coordinates' => [
                            'latitude' => $place['geometry']['location']['lat'] ?? null,
                            'longitude' => $place['geometry']['location']['lng'] ?? null,
                        ],
                        'is_existing' => false,
                    ];
                }
            } else {
                // Single result - use findPlace for more accuracy
                $place = $googleService->findPlace(
                    $this->searchName,
                    '',
                    $this->searchCity,
                    $this->searchState
                );

                if ($place && !empty($place['place_id'])) {
                    // Check if already in our database
                    $exists = Restaurant::where('google_place_id', $place['place_id'])->exists();
                    if (!$exists) {
                        // Get full details
                        $details = $googleService->getPlaceDetails($place['place_id']);

                        $results[] = [
                            'source' => 'google',
                            'source_label' => 'Google',
                            'source_color' => 'blue',
                            'id' => $place['place_id'],
                            'google_place_id' => $place['place_id'],
                            'name' => $place['name'] ?? '',
                            'address' => $details['formatted_address'] ?? $place['formatted_address'] ?? '',
                            'city' => $this->searchCity,
                            'state' => $this->searchState,
                            'phone' => $details['formatted_phone_number'] ?? '',
                            'website' => $details['website'] ?? null,
                            'rating' => $place['rating'] ?? $details['rating'] ?? null,
                            'review_count' => $place['user_ratings_total'] ?? $details['user_ratings_total'] ?? 0,
                            'image_url' => null,
                            'url' => $details['url'] ?? null,
                            'coordinates' => [
                                'latitude' => $place['geometry']['location']['lat'] ?? null,
                                'longitude' => $place['geometry']['location']['lng'] ?? null,
                            ],
                            'is_existing' => false,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Google search error: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Select a result and populate the form
     */
    public function selectResult(int $index)
    {
        if (!isset($this->searchResults[$index])) {
            return;
        }

        $result = $this->searchResults[$index];

        // If it's already in our database, redirect to that page
        if ($result['source'] === 'database') {
            session()->flash('info', '¡Este restaurante ya está en FAMER! Puedes visitarlo o votar por él.');
            return redirect($result['url']);
        }

        $this->selectedResult = $result;
        $this->resultSource = $result['source'];

        // Populate form fields
        $this->restaurant_name = $result['name'];
        $this->restaurant_address = $result['address'];
        $this->restaurant_city = $result['city'];
        $this->restaurant_state = $result['state'];
        $this->restaurant_zip_code = $result['zip_code'] ?? '';
        $this->restaurant_phone = $result['phone'];
        $this->restaurant_website = $result['website'] ?? '';

        // Store external IDs
        if ($result['source'] === 'yelp') {
            $this->yelp_id = $result['yelp_id'];
            $this->yelp_data = $result;
        } elseif ($result['source'] === 'google') {
            $this->google_place_id = $result['google_place_id'];
            $this->google_data = $result;
        }

        // Move to step 2
        $this->step = 2;
    }

    /**
     * Skip search and enter manually
     */
    public function enterManually()
    {
        $this->restaurant_name = $this->searchName;
        $this->restaurant_city = $this->searchCity;
        $this->restaurant_state = $this->searchState;
        $this->step = 2;
    }

    /**
     * Go back to search
     */
    public function backToSearch()
    {
        $this->step = 1;
        $this->selectedResult = null;
        $this->resultSource = '';
    }

    /**
     * Submit the suggestion
     */
    public function submit()
    {
        $this->validate();

        // Prepare validation data
        $validationData = [
            'user_id' => auth()->id(),
            'submitter_email' => $this->submitter_email,
            'restaurant_name' => $this->restaurant_name,
            'restaurant_address' => $this->restaurant_address,
            'restaurant_city' => $this->restaurant_city,
            'restaurant_state' => $this->restaurant_state,
            'restaurant_phone' => $this->restaurant_phone,
            'restaurant_website' => $this->restaurant_website,
        ];

        // Run validation
        $validationService = new BusinessValidationService();
        $validationResult = $validationService->validateBusiness($validationData);

        // Use pre-fetched data if available
        $googleData = $this->google_data ?? $validationResult['validation_data']['google'] ?? [];
        $yelpData = $this->yelp_data ?? $validationResult['validation_data']['yelp'] ?? [];

        // Boost trust score if we have Yelp/Google data from search
        $trustScore = $validationResult['trust_score'];
        if ($this->yelp_id || $this->google_place_id) {
            $trustScore = min(100, $trustScore + 20);
        }

        // Extract spam data
        $spamData = $validationResult['validation_data']['spam'] ?? [];

        // Create the suggestion
        $suggestion = Suggestion::create([
            'user_id' => auth()->id(),
            'submitter_name' => $this->submitter_name,
            'submitter_email' => $this->submitter_email,
            'submitter_phone' => $this->submitter_phone,
            'restaurant_name' => $this->restaurant_name,
            'restaurant_address' => $this->restaurant_address,
            'restaurant_city' => $this->restaurant_city,
            'restaurant_state' => $this->restaurant_state,
            'restaurant_zip_code' => $this->restaurant_zip_code,
            'restaurant_phone' => $this->restaurant_phone,
            'restaurant_website' => $this->restaurant_website,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'notes' => $this->notes,
            'status' => 'pending',

            // Validation fields
            'trust_score' => $trustScore,
            'validation_status' => $validationResult['recommendation'],
            'validation_data' => array_merge($validationResult['validation_data'], [
                'smart_search_source' => $this->resultSource,
            ]),

            // External IDs
            'google_place_id' => $this->google_place_id ?? ($googleData['place_id'] ?? null),
            'google_verified' => !empty($this->google_place_id) || ($googleData['verified'] ?? false),
            'google_rating' => $googleData['rating'] ?? null,
            'google_reviews_count' => $googleData['user_ratings_total'] ?? ($googleData['review_count'] ?? null),

            'yelp_id' => $this->yelp_id ?? ($yelpData['yelp_id'] ?? null),
            'yelp_verified' => !empty($this->yelp_id) || ($yelpData['verified'] ?? false),
            'yelp_rating' => $yelpData['rating'] ?? null,
            'yelp_reviews_count' => $yelpData['review_count'] ?? null,

            // Spam detection
            'spam_score' => $spamData['spam_score'] ?? 0,
            'spam_risk_level' => $spamData['risk_level'] ?? 'low',
            'spam_flags' => $spamData['flags'] ?? [],
            'is_spam' => $validationResult['spam_detected'] ?? false,
            'is_potential_duplicate' => $validationResult['is_potential_duplicate'],
            'duplicate_check_data' => $validationResult['duplicate_check_data'],
            'website_verified' => $validationResult['validation_data']['website']['verified'] ?? false,
            'phone_verified' => $validationResult['validation_data']['phone']['format_valid'] ?? false,
        ]);

        // Auto-approve if high trust score and verified from external source
        $shouldAutoApprove = $trustScore >= 80
            && ($this->yelp_id || $this->google_place_id)
            && !$validationResult['is_potential_duplicate'];

        if ($shouldAutoApprove) {
            $suggestion->update([
                'status' => 'approved',
                'validation_status' => 'auto_approved',
                'verified_at' => now(),
            ]);

            session()->flash('success', '¡Excelente! Tu sugerencia fue verificada automáticamente y aprobada. ¡Gracias por tu contribución!');
        } elseif ($validationResult['is_potential_duplicate']) {
            session()->flash('warning', 'Tu sugerencia ha sido recibida. Detectamos que podría ser un duplicado, nuestro equipo lo revisará pronto.');
        } else {
            session()->flash('success', '¡Gracias! Tu sugerencia ha sido enviada y será revisada pronto.');
        }

        $this->showSuccessMessage = true;
        $this->reset([
            'step', 'searchName', 'searchCity', 'searchState', 'searchResults',
            'selectedResult', 'resultSource', 'restaurant_name', 'restaurant_address',
            'restaurant_city', 'restaurant_state', 'restaurant_zip_code',
            'restaurant_phone', 'restaurant_website', 'category_id', 'description',
            'notes', 'yelp_id', 'yelp_data', 'google_place_id', 'google_data',
            'hasSearched'
        ]);
        $this->step = 1;
    }

    public function render()
    {
        // Only show US states for restaurant suggestions
        $states = State::where('is_active', true)
            ->where('country', 'US')
            ->orderBy('name')
            ->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('livewire.smart-suggestion-form', [
            'states' => $states,
            'categories' => $categories,
        ])->layout('layouts.owners-public', ['title' => 'Agregar Restaurante']);
    }
}
