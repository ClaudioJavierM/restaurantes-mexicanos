<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Suggestion;
use App\Models\State;
use App\Services\GooglePlacesService;
use App\Services\BusinessValidationService;
use Livewire\Component;
use Livewire\Attributes\Validate;

class SuggestionForm extends Component
{
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

    public $google_verified = false;
    public $google_place_data = null;
    public $showSuccessMessage = false;

    public function mount()
    {
        // Pre-fill with auth user if logged in
        if (auth()->check()) {
            $this->submitter_name = auth()->user()->name;
            $this->submitter_email = auth()->user()->email;
        }
    }

    public function verifyWithGoogle()
    {
        $this->validate([
            'restaurant_name' => 'required',
            'restaurant_address' => 'required',
            'restaurant_city' => 'required',
            'restaurant_state' => 'required',
        ]);

        $googleService = new GooglePlacesService();

        $place = $googleService->findPlace(
            $this->restaurant_name,
            $this->restaurant_address,
            $this->restaurant_city,
            $this->restaurant_state
        );

        if ($place) {
            $this->google_verified = true;
            $this->google_place_data = $place;

            // Auto-fill data from Google
            if (isset($place['formatted_address'])) {
                $this->restaurant_address = $place['formatted_address'];
            }

            session()->flash('google_success', '✅ Restaurante verificado con Google Places!');
        } else {
            $this->google_verified = false;
            session()->flash('google_error', '⚠️ No pudimos verificar este restaurante en Google. Puedes continuar de todas formas.');
        }
    }

    public function submit()
    {
        $this->validate();

        // Preparar datos para validación
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

        // Ejecutar validación completa
        $validationService = new BusinessValidationService();
        $validationResult = $validationService->validateBusiness($validationData);

        // Extraer datos de Google Places si están disponibles
        $googleData = $validationResult['validation_data']['google'] ?? [];
        $googlePlaceId = $googleData['place_id'] ?? null;
        $googleRating = $googleData['rating'] ?? null;
        $googleReviewsCount = $googleData['user_ratings_total'] ?? null;
        $googleVerified = $googleData['verified'] ?? false;

        // Extraer datos de Yelp si están disponibles
        $yelpData = $validationResult['validation_data']['yelp'] ?? [];
        $yelpId = $yelpData['yelp_id'] ?? null;
        $yelpRating = $yelpData['rating'] ?? null;
        $yelpReviewsCount = $yelpData['review_count'] ?? null;
        $yelpVerified = $yelpData['verified'] ?? false;

        // Extraer datos de detección de spam
        $spamData = $validationResult['validation_data']['spam'] ?? [];
        $spamScore = $spamData['spam_score'] ?? 0;
        $spamRiskLevel = $spamData['risk_level'] ?? 'low';
        $spamFlags = $spamData['flags'] ?? [];
        $isSpam = $validationResult['spam_detected'] ?? false;

        // Crear la sugerencia con todos los datos de validación
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
            'trust_score' => $validationResult['trust_score'],
            'validation_status' => $validationResult['recommendation'],
            'validation_data' => $validationResult['validation_data'],
            'google_place_id' => $googlePlaceId,
            'google_verified' => $googleVerified,
            'google_rating' => $googleRating,
            'google_reviews_count' => $googleReviewsCount,
            'yelp_id' => $yelpId,
            'yelp_verified' => $yelpVerified,
            'yelp_rating' => $yelpRating,
            'yelp_reviews_count' => $yelpReviewsCount,
            'spam_score' => $spamScore,
            'spam_risk_level' => $spamRiskLevel,
            'spam_flags' => $spamFlags,
            'is_spam' => $isSpam,
            'is_potential_duplicate' => $validationResult['is_potential_duplicate'],
            'duplicate_check_data' => $validationResult['duplicate_check_data'],
            'website_verified' => $validationResult['validation_data']['website']['verified'] ?? false,
            'phone_verified' => $validationResult['validation_data']['phone']['format_valid'] ?? false,
        ]);

        // Si el trust score es muy alto y no hay duplicados, auto-aprobar
        if ($validationResult['trust_score'] >= 85 && !$validationResult['is_potential_duplicate']) {
            $suggestion->update([
                'status' => 'approved',
                'validation_status' => 'auto_approved',
                'verified_at' => now(),
            ]);

            session()->flash('success', '🎉 ¡Excelente! Tu sugerencia fue verificada automáticamente y aprobada. ¡Gracias por tu contribución!');
        } elseif ($validationResult['is_potential_duplicate']) {
            session()->flash('warning', '⚠️ Tu sugerencia ha sido recibida. Detectamos que podría ser un duplicado, nuestro equipo lo revisará pronto.');
        } else {
            session()->flash('success', '✅ ¡Gracias! Tu sugerencia ha sido enviada y será revisada pronto. Trust Score: ' . $validationResult['trust_score'] . '/100');
        }

        $this->showSuccessMessage = true;
        $this->reset([
            'restaurant_name',
            'restaurant_address',
            'restaurant_city',
            'restaurant_state',
            'restaurant_zip_code',
            'restaurant_phone',
            'restaurant_website',
            'category_id',
            'description',
            'notes',
            'google_verified',
            'google_place_data'
        ]);
    }

    public function render()
    {
        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.suggestion-form', [
            'states' => $states,
            'categories' => $categories,
        ])->layout('layouts.app', ['title' => 'Sugerir Restaurante']);
    }
}
