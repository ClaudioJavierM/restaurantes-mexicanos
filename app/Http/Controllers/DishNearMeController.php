<?php
namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\View\View;

class DishNearMeController extends Controller
{
    protected array $dishes = [
        'birria' => [
            'name' => 'Birria',
            'slug' => 'birria',
            'column' => 'has_birria',
            'title' => 'Birria Cerca de Mí — Restaurantes de Birria Mexicana',
            'title_en' => 'Birria Near Me — Best Mexican Birria Restaurants',
            'description' => 'Encuentra los mejores restaurantes de birria auténtica cerca de ti. Birria de res, chivo y mixta.',
            'description_en' => 'Find the best authentic birria restaurants near you. Beef birria, goat birria tacos and more.',
            'hero_text' => 'Birria Cerca de Mí',
        ],
        'tamales' => [
            'name' => 'Tamales',
            'slug' => 'tamales',
            'column' => 'has_tamales',
            'title' => 'Tamales Cerca de Mí — Restaurantes de Tamales Mexicanos',
            'title_en' => 'Tamales Near Me — Best Mexican Tamales Restaurants',
            'description' => 'Encuentra los mejores restaurantes y tamalerías cerca de ti. Tamales de rajas, dulce, verde y rojo.',
            'description_en' => 'Find the best tamale restaurants near you. Green, red, sweet and cheese tamales.',
            'hero_text' => 'Tamales Cerca de Mí',
        ],
        'pozole' => [
            'name' => 'Pozole',
            'slug' => 'pozole',
            'column' => 'has_pozole_menudo',
            'title' => 'Pozole Cerca de Mí — Restaurantes de Pozole Mexicano',
            'title_en' => 'Pozole Near Me — Best Mexican Pozole Restaurants',
            'description' => 'Encuentra los mejores restaurantes de pozole auténtico cerca de ti. Pozole rojo, blanco y verde.',
            'description_en' => 'Find the best pozole restaurants near you. Red, white and green Mexican pozole.',
            'hero_text' => 'Pozole Cerca de Mí',
        ],
        'carnitas' => [
            'name' => 'Carnitas',
            'slug' => 'carnitas',
            'column' => 'has_carnitas',
            'title' => 'Carnitas Cerca de Mí — Restaurantes de Carnitas Mexicanas',
            'title_en' => 'Carnitas Near Me — Best Mexican Carnitas Restaurants',
            'description' => 'Encuentra los mejores restaurantes de carnitas auténticas cerca de ti. Cerdo confitado al estilo michoacano.',
            'description_en' => 'Find the best authentic carnitas restaurants near you. Michoacan-style pork confit with tortillas and salsa.',
            'hero_text' => 'Carnitas Cerca de Mí',
        ],
        'barbacoa' => [
            'name' => 'Barbacoa',
            'slug' => 'barbacoa',
            'column' => 'has_barbacoa',
            'title' => 'Barbacoa Cerca de Mí — Restaurantes de Barbacoa Mexicana',
            'title_en' => 'Barbacoa Near Me — Best Mexican Barbacoa Restaurants',
            'description' => 'Encuentra los mejores restaurantes de barbacoa auténtica cerca de ti. Barbacoa de res y borrego al estilo tradicional.',
            'description_en' => 'Find the best authentic barbacoa restaurants near you. Traditional slow-cooked beef and lamb barbacoa.',
            'hero_text' => 'Barbacoa Cerca de Mí',
        ],
        'mole' => [
            'name' => 'Mole',
            'slug' => 'mole',
            'column' => 'has_homemade_mole',
            'title' => 'Mole Cerca de Mí — Restaurantes de Mole Mexicano',
            'title_en' => 'Mole Near Me — Best Mexican Mole Restaurants',
            'description' => 'Encuentra los mejores restaurantes de mole auténtico cerca de ti. Mole negro, rojo, verde y poblano.',
            'description_en' => 'Find the best authentic mole restaurants near you. Black, red, green and poblano mole.',
            'hero_text' => 'Mole Cerca de Mí',
        ],
        'carne-asada' => [
            'name' => 'Carne Asada',
            'slug' => 'carne-asada',
            'column' => 'has_charcoal_grill',
            'title' => 'Carne Asada Cerca de Mí — Restaurantes de Carne Asada Mexicana',
            'title_en' => 'Carne Asada Near Me — Best Mexican Carne Asada Restaurants',
            'description' => 'Encuentra los mejores restaurantes de carne asada auténtica cerca de ti. Arrachera a las brasas con guacamole y tortillas.',
            'description_en' => 'Find the best authentic carne asada restaurants near you. Charcoal-grilled beef with guacamole and tortillas.',
            'hero_text' => 'Carne Asada Cerca de Mí',
        ],
    ];

    public function show(string $dish): View
    {
        $data = $this->dishes[$dish] ?? abort(404);
        $column = $data['column'];

        $topRestaurants = Restaurant::approved()
            ->where($column, true)
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->limit(24)
            ->get();

        if ($topRestaurants->isEmpty()) {
            $topRestaurants = Restaurant::approved()
                ->with(['state'])
                ->select(['id', 'name', 'slug', 'city', 'state_id', 'average_rating', 'total_reviews', 'image'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->limit(24)
                ->get();
        }

        $topCities = Restaurant::query()
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_active', true)
            ->whereNull('restaurants.deleted_at')
            ->where("restaurants.{$column}", true)
            ->select('restaurants.city', 'states.code as state_code', 'states.name as state_name')
            ->selectRaw('COUNT(*) as restaurant_count')
            ->groupBy('restaurants.city', 'states.code', 'states.name')
            ->orderByDesc('restaurant_count')
            ->limit(16)
            ->get();

        if ($topCities->isEmpty()) {
            $topCities = Restaurant::query()
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.status', 'approved')
                ->where('restaurants.is_active', true)
                ->whereNull('restaurants.deleted_at')
                ->select('restaurants.city', 'states.code as state_code', 'states.name as state_name')
                ->selectRaw('COUNT(*) as restaurant_count')
                ->groupBy('restaurants.city', 'states.code', 'states.name')
                ->orderByDesc('restaurant_count')
                ->limit(16)
                ->get();
        }

        return view('near-me.dish', compact('dish', 'data', 'topRestaurants', 'topCities'));
    }

    public function birria(): View     { return $this->show('birria'); }
    public function tamales(): View    { return $this->show('tamales'); }
    public function pozole(): View     { return $this->show('pozole'); }
    public function carnitas(): View   { return $this->show('carnitas'); }
    public function barbacoa(): View   { return $this->show('barbacoa'); }
    public function mole(): View       { return $this->show('mole'); }
    public function carneAsada(): View { return $this->show('carne-asada'); }
}
