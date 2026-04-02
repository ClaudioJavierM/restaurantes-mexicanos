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
    ];

    protected array $allowedStates = [
        'tx' => 'Texas', 'ca' => 'California', 'il' => 'Illinois', 'az' => 'Arizona',
        'fl' => 'Florida', 'co' => 'Colorado', 'nv' => 'Nevada', 'nm' => 'New Mexico',
        'ny' => 'New York', 'ga' => 'Georgia', 'wa' => 'Washington', 'nc' => 'North Carolina',
        'or' => 'Oregon', 'ut' => 'Utah', 'tn' => 'Tennessee',
    ];

    public function show(string $dish, string $citySlug, string $stateCode): View
    {
        if (!isset($this->dishes[$dish])) abort(404);

        $stateCode = strtolower($stateCode);
        if (!isset($this->allowedStates[$stateCode])) abort(404);

        $dishData  = $this->dishes[$dish];
        $stateName = $this->allowedStates[$stateCode];
        $cityName  = ucwords(str_replace('-', ' ', $citySlug));

        $state = State::where('code', strtoupper($stateCode))->first();

        $baseQuery = fn() => Restaurant::approved()
            ->when($state, fn($q) => $q->where('state_id', $state->id))
            ->where('city', 'LIKE', '%' . $cityName . '%')
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(30);

        // Primary: boolean column if available
        if ($dishData['column']) {
            $restaurants = $baseQuery()->where($dishData['column'], true)->get();
        } else {
            // Keyword fallback on name
            $restaurants = $baseQuery()->where('name', 'LIKE', '%' . $dishData['keyword'] . '%')->get();
        }

        // Fallback: any approved restaurant in the city (dish filter dropped)
        if ($restaurants->isEmpty()) {
            $restaurants = Restaurant::approved()
                ->when($state, fn($q) => $q->where('state_id', $state->id))
                ->where('city', 'LIKE', '%' . $cityName . '%')
                ->with(['state'])
                ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->limit(30)
                ->get();
        }

        return view('dishes.dish-city', compact(
            'dish', 'dishData', 'citySlug', 'cityName', 'stateCode', 'stateName', 'restaurants'
        ));
    }
}
