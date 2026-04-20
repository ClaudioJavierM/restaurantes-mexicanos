<?php
namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\View\View;

class DishCityController extends Controller
{
    protected array $dishes = [
        'birria'          => ['name' => 'Birria',          'column' => 'has_birria',        'keyword' => 'birria'],
        'tamales'         => ['name' => 'Tamales',         'column' => 'has_tamales',       'keyword' => 'tamale'],
        'pozole'          => ['name' => 'Pozole',          'column' => 'has_pozole_menudo', 'keyword' => 'pozole'],
        'carnitas'        => ['name' => 'Carnitas',        'column' => 'has_carnitas',      'keyword' => 'carnitas'],
        'barbacoa'        => ['name' => 'Barbacoa',        'column' => 'has_barbacoa',      'keyword' => 'barbacoa'],
        'mole'            => ['name' => 'Mole',            'column' => 'has_homemade_mole', 'keyword' => 'mole'],
        'tacos'           => ['name' => 'Tacos',           'column' => null,                'keyword' => 'taco'],
        'menudo'          => ['name' => 'Menudo',          'column' => 'has_pozole_menudo', 'keyword' => 'menudo'],
        'chiles-rellenos' => ['name' => 'Chiles Rellenos', 'column' => null,               'keyword' => 'chile rellen'],
        'carne-asada'     => ['name' => 'Carne Asada',    'column' => null,                'keyword' => 'carne asada'],
        'enchiladas'      => ['name' => 'Enchiladas',     'column' => null,                'keyword' => 'enchilada'],
        'quesadillas'     => ['name' => 'Quesadillas',    'column' => null,                'keyword' => 'quesadilla'],
        'guacamole'       => ['name' => 'Guacamole',      'column' => null,                'keyword' => 'guacamole'],
        'fajitas'         => ['name' => 'Fajitas',        'column' => null,                'keyword' => 'fajita'],
        'churros'         => ['name' => 'Churros',        'column' => null,                'keyword' => 'churro'],
        'horchata'        => ['name' => 'Horchata',       'column' => null,                'keyword' => 'horchata'],
        'margaritas'      => ['name' => 'Margaritas',     'column' => null,                'keyword' => 'margarita'],
    ];

    /** Minimum restaurants required to serve a page (SEO: avoid thin content). */
    protected int $minRestaurants = 3;

    /**
     * State-level dish page: /{dish}-en-{stateCode}
     * e.g. /birria-en-tx
     */
    public function state(string $dish, string $stateCode): View
    {
        if (!isset($this->dishes[$dish])) {
            abort(404);
        }

        $stateCode = strtolower($stateCode);

        // Validate state dynamically from DB (all 50 US states)
        $state = State::where('code', strtoupper($stateCode))
            ->where('country', 'US')
            ->first();

        if (!$state) {
            abort(404);
        }

        $dishData  = $this->dishes[$dish];
        $stateName = $state->name;

        $restaurants = $this->queryByState($dishData, $state->id);

        if ($restaurants->count() < $this->minRestaurants) {
            abort(404);
        }

        $title           = "Mejores {$dishData['name']} en {$stateName} — FAMER";
        $metaDescription = "Encuentra los mejores restaurantes con {$dishData['name']} en {$stateName}. {$restaurants->count()} restaurantes verificados con calificaciones, fotos y horarios.";
        $canonical       = url("/{$dish}-en-{$stateCode}");

        return view('dishes.dish-state', compact(
            'dish', 'dishData', 'stateCode', 'stateName', 'state', 'restaurants',
            'title', 'metaDescription', 'canonical'
        ));
    }

    /**
     * City-level dish page: /{dish}-en-{citySlug}-{stateCode}
     * e.g. /birria-en-los-angeles-ca
     */
    public function show(string $dish, string $citySlug, string $stateCode): View
    {
        if (!isset($this->dishes[$dish])) {
            abort(404);
        }

        $stateCode = strtolower($stateCode);

        // Validate state dynamically from DB (all 50 US states)
        $state = State::where('code', strtoupper($stateCode))
            ->where('country', 'US')
            ->first();

        if (!$state) {
            abort(404);
        }

        $dishData  = $this->dishes[$dish];
        $stateName = $state->name;
        $cityName  = ucwords(str_replace('-', ' ', $citySlug));

        $restaurants = $this->queryByCity($dishData, $state->id, $cityName);

        if ($restaurants->count() < $this->minRestaurants) {
            abort(404);
        }

        $title           = "Mejores {$dishData['name']} en {$cityName}, {$stateCode} — FAMER";
        $metaDescription = "Encuentra los mejores restaurantes con {$dishData['name']} en {$cityName}, {$stateName}. {$restaurants->count()} restaurantes verificados con calificaciones, fotos y horarios.";
        $canonical       = url("/{$dish}-en-{$citySlug}-{$stateCode}");

        return view('dishes.dish-city', compact(
            'dish', 'dishData', 'citySlug', 'cityName', 'stateCode', 'stateName', 'state', 'restaurants',
            'title', 'metaDescription', 'canonical'
        ));
    }

    /**
     * Shared: city() method — explicit city route if registered separately.
     * Delegates to show() since the signature is identical.
     */
    public function city(string $dish, string $citySlug, string $stateCode): View
    {
        return $this->show($dish, $citySlug, $stateCode);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Build restaurant query filtered to a state.
     * Tries boolean column first; falls back to keyword match on name.
     */
    private function queryByState(array $dishData, int $stateId)
    {
        $base = fn() => Restaurant::approved()
            ->where('state_id', $stateId)
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(30);

        if ($dishData['column']) {
            $results = $base()->where($dishData['column'], true)->get();
            if ($results->count() >= $this->minRestaurants) {
                return $results;
            }
        }

        // Keyword fallback on restaurant name
        return $base()->where('name', 'LIKE', '%' . $dishData['keyword'] . '%')->get();
    }

    /**
     * Build restaurant query filtered to a city within a state.
     * Tries boolean column first; falls back to keyword match on name.
     * Does NOT fall back to "any restaurant in city" — that would produce
     * misleading dish pages and hurt SEO quality.
     */
    private function queryByCity(array $dishData, int $stateId, string $cityName)
    {
        $base = fn() => Restaurant::approved()
            ->where('state_id', $stateId)
            ->where('city', 'LIKE', '%' . $cityName . '%')
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(30);

        if ($dishData['column']) {
            $results = $base()->where($dishData['column'], true)->get();
            if ($results->count() >= $this->minRestaurants) {
                return $results;
            }
        }

        // Keyword fallback on restaurant name
        return $base()->where('name', 'LIKE', '%' . $dishData['keyword'] . '%')->get();
    }
}
