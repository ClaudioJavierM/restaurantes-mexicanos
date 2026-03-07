<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantBranding;
use App\Models\AnalyticsEvent;
use Livewire\Component;

class RestaurantWebsite extends Component
{
    public Restaurant $restaurant;
    public ?RestaurantBranding $branding = null;
    public $reviews = [];
    public $menuCategories = [];
    public $photos = [];

    public function mount(string $slug)
    {
        $this->restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->with(['state', 'category', 'media'])
            ->firstOrFail();

        // Only premium/elite can have a website
        if (!in_array($this->restaurant->subscription_tier, ['premium', 'elite'])) {
            return redirect()->route('restaurants.show', $slug);
        }

        $this->branding = RestaurantBranding::getForRestaurant($this->restaurant->id);

        // Load menu categories with items
        $this->menuCategories = $this->restaurant->menuCategories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['items' => function ($q) {
                $q->where('is_available', true)->orderBy('sort_order')->orderBy('name');
            }])
            ->get();

        // Load approved reviews (top 6)
        $this->reviews = $this->restaurant->approvedReviews()
            ->with('user')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        // Collect all photos
        $this->photos = $this->collectPhotos();

        // Track page view
        AnalyticsEvent::track(
            $this->restaurant->id,
            AnalyticsEvent::EVENT_PAGE_VIEW,
            ['page_path' => '/sitio/' . $slug, 'source' => 'website']
        );
    }

    protected function collectPhotos(): array
    {
        $photos = [];

        // Media library photos
        foreach ($this->restaurant->getMedia('images') as $media) {
            $photos[] = $media->getUrl();
        }

        // Yelp photos
        if ($this->restaurant->yelp_photos) {
            foreach ($this->restaurant->yelp_photos as $photo) {
                $photos[] = $photo;
            }
        }

        // User-uploaded approved photos
        foreach ($this->restaurant->approvedPhotos()->get() as $userPhoto) {
            if ($userPhoto->photo_url) {
                $photos[] = $userPhoto->photo_url;
            }
        }

        return array_slice($photos, 0, 20); // Limit to 20
    }

    public function getOpeningHoursProperty(): array
    {
        $dayNames = [
            'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
            'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado',
            'sunday' => 'Domingo', 'domingo' => 'Domingo', 'lunes' => 'Lunes',
            'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves',
            'viernes' => 'Viernes', 'sabado' => 'Sábado',
        ];

        // Try owner-managed hours first
        $hours = $this->restaurant->hours;
        if ($hours && is_array($hours) && !empty($hours)) {
            $parsed = [];
            foreach ($hours as $day => $value) {
                $label = $dayNames[strtolower($day)] ?? ucfirst($day);
                $time = is_array($value) ? implode(', ', $value) : (string) $value;
                $parsed[$label] = $time;
            }
            return $parsed;
        }

        // Try Google format
        $openingHours = $this->restaurant->opening_hours;
        if ($openingHours && isset($openingHours['weekday_text'])) {
            $parsed = [];
            foreach ($openingHours['weekday_text'] as $line) {
                $parts = explode(': ', $line, 2);
                if (count($parts) === 2) {
                    $parsed[$parts[0]] = $parts[1];
                }
            }
            return $parsed;
        }

        return [];
    }

    public function getSpecialtiesProperty(): array
    {
        $specialties = [];
        $fields = [
            'has_cafe_de_olla' => 'Café de Olla',
            'has_fresh_tortillas' => 'Tortillas Frescas',
            'has_handmade_tortillas' => 'Tortillas Hechas a Mano',
            'has_aguas_frescas' => 'Aguas Frescas',
            'has_homemade_salsa' => 'Salsa Casera',
            'has_homemade_mole' => 'Mole Casero',
            'has_charcoal_grill' => 'Parrilla al Carbón',
            'has_comal' => 'Comal Tradicional',
            'has_birria' => 'Birria',
            'has_carnitas' => 'Carnitas',
            'has_pozole_menudo' => 'Pozole / Menudo',
            'has_barbacoa' => 'Barbacoa',
            'has_tamales' => 'Tamales',
            'has_pan_dulce' => 'Pan Dulce',
            'has_churros' => 'Churros',
            'has_mezcal_tequila' => 'Mezcal & Tequila',
            'has_micheladas' => 'Micheladas',
        ];

        foreach ($fields as $field => $label) {
            if ($this->restaurant->$field) {
                $specialties[] = $label;
            }
        }

        return $specialties;
    }

    public function render()
    {
        return view('livewire.restaurant-website')
            ->layout('layouts.restaurant-website', [
                'restaurant' => $this->restaurant,
                'branding' => $this->branding,
            ]);
    }
}
