<?php
namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\View\View;

class DishStateController extends Controller
{
    protected array $dishes = [
        'birria'    => ['name' => 'Birria',    'column' => 'has_birria',        'title_es' => 'Birria en',    'title_en' => 'Birria in'],
        'tamales'   => ['name' => 'Tamales',   'column' => 'has_tamales',       'title_es' => 'Tamales en',   'title_en' => 'Tamales in'],
        'pozole'    => ['name' => 'Pozole',    'column' => 'has_pozole_menudo', 'title_es' => 'Pozole en',    'title_en' => 'Pozole in'],
        'carnitas'  => ['name' => 'Carnitas',  'column' => 'has_carnitas',      'title_es' => 'Carnitas en',  'title_en' => 'Carnitas in'],
        'barbacoa'  => ['name' => 'Barbacoa',  'column' => 'has_barbacoa',      'title_es' => 'Barbacoa en',  'title_en' => 'Barbacoa in'],
        'mole'      => ['name' => 'Mole',      'column' => 'has_homemade_mole', 'title_es' => 'Mole en',      'title_en' => 'Mole in'],
    ];

    // Whitelisted state slugs (US states only — most search traffic is US)
    protected array $allowedStates = [
        'tx' => 'Texas', 'ca' => 'California', 'il' => 'Illinois', 'az' => 'Arizona',
        'fl' => 'Florida', 'co' => 'Colorado', 'nv' => 'Nevada', 'nm' => 'New Mexico',
        'ny' => 'New York', 'ga' => 'Georgia', 'wa' => 'Washington', 'nc' => 'North Carolina',
        'or' => 'Oregon', 'ut' => 'Utah', 'tn' => 'Tennessee',
    ];

    public function show(string $dish, string $stateCode): View
    {
        if (!isset($this->dishes[$dish])) abort(404);

        $stateCode = strtolower($stateCode);
        if (!isset($this->allowedStates[$stateCode])) abort(404);

        $dishData = $this->dishes[$dish];
        $stateName = $this->allowedStates[$stateCode];
        $column = $dishData['column'];

        $state = State::where('code', strtoupper($stateCode))->first();

        $restaurants = Restaurant::approved()
            ->where($column, true)
            ->when($state, fn($q) => $q->where('state_id', $state->id))
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(30)
            ->get();

        // Fallback: restaurants in state without dish filter
        if ($restaurants->isEmpty() && $state) {
            $restaurants = Restaurant::approved()
                ->where('state_id', $state->id)
                ->with(['state'])
                ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->limit(30)
                ->get();
        }

        return view('dishes.dish-state', compact('dish', 'dishData', 'stateCode', 'stateName', 'restaurants'));
    }
}
