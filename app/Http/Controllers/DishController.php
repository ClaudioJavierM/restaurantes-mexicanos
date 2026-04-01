<?php
namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\View\View;

class DishController extends Controller
{
    protected array $dishes = [
        'birria' => [
            'name' => 'Birria',
            'column' => 'has_birria',
            'title' => 'Mejores Restaurantes de Birria Mexicana',
            'title_en' => 'Best Mexican Birria Restaurants',
            'description' => 'Encuentra los mejores restaurantes de birria auténtica mexicana cerca de ti. Birria de res, chivo y mixta.',
            'description_en' => 'Find the best authentic Mexican birria restaurants near you. Beef birria, goat birria, and more.',
            'hero_text' => 'Birria Auténtica Mexicana',
            'body' => 'La birria es uno de los platillos más icónicos de la cocina mexicana, originaria del estado de Jalisco. Este estofado tradicional de carne de res o chivo cocinado a fuego lento con chiles secos y especias se ha convertido en un fenómeno mundial.',
        ],
        'tamales' => [
            'name' => 'Tamales',
            'column' => 'has_tamales',
            'title' => 'Mejores Restaurantes de Tamales Mexicanos',
            'title_en' => 'Best Mexican Tamales Restaurants',
            'description' => 'Los mejores tamales mexicanos auténticos. Tamales de rajas, dulce, verde, rojo y más estilos regionales.',
            'description_en' => 'Best authentic Mexican tamales. Rajas, sweet, green, red and more regional styles.',
            'hero_text' => 'Tamales Mexicanos Auténticos',
            'body' => 'Los tamales son una de las tradiciones culinarias más antiguas de México, con historia de más de 5,000 años. Masa de maíz rellena con carnes, chiles, queso o dulces, envuelta en hojas de maíz o plátano y cocida al vapor.',
        ],
        'pozole' => [
            'name' => 'Pozole',
            'column' => 'has_pozole_menudo',
            'title' => 'Mejores Restaurantes de Pozole Mexicano',
            'title_en' => 'Best Mexican Pozole Restaurants',
            'description' => 'Encuentra los mejores restaurantes de pozole mexicano auténtico. Pozole rojo, blanco y verde.',
            'description_en' => 'Find the best authentic Mexican pozole restaurants. Red, white and green pozole.',
            'hero_text' => 'Pozole Mexicano Auténtico',
            'body' => 'El pozole es un caldo ceremonial de origen prehispánico, hecho con maíz cacahuazintle (hominy), carne y chile. Se sirve con una variedad de toppings frescos como lechuga, rábano, cebolla, orégano y tostadas.',
        ],
    ];

    public function show(string $dish): View
    {
        if (!isset($this->dishes[$dish])) {
            abort(404);
        }

        $data = $this->dishes[$dish];
        $column = $data['column'];

        $restaurants = Restaurant::approved()
            ->where($column, true)
            ->with(['state', 'category'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'address', 'rating', 'review_count', 'description', 'cover_image', 'logo_image', $column])
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->limit(50)
            ->get();

        // Fallback: if no specific column data, get top Mexican restaurants
        if ($restaurants->isEmpty()) {
            $restaurants = Restaurant::approved()
                ->with(['state', 'category'])
                ->select(['id', 'name', 'slug', 'city', 'state_id', 'address', 'rating', 'review_count', 'description', 'cover_image', 'logo_image'])
                ->orderByDesc('rating')
                ->orderByDesc('review_count')
                ->limit(50)
                ->get();
        }

        return view('dishes.dish', compact('dish', 'data', 'restaurants'));
    }
}
