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
        'enchiladas' => [
            'name' => 'Enchiladas',
            'column' => null,
            'title' => 'Mejores Restaurantes de Enchiladas Mexicanas',
            'title_en' => 'Best Mexican Enchiladas Restaurants',
            'description' => 'Las mejores enchiladas mexicanas auténticas. Enchiladas verdes, rojas, suizas, mole y más estilos regionales.',
            'description_en' => 'Best authentic Mexican enchiladas. Green, red, Swiss, mole and more regional styles.',
            'hero_text' => 'Enchiladas Mexicanas Auténticas',
            'body' => 'Las enchiladas son tortillas de maíz bañadas en salsa de chile y rellenas de pollo, queso, frijoles o carne. Son uno de los platillos más representativos de la cocina mexicana, con decenas de variaciones regionales.',
        ],
        'tacos-al-pastor' => [
            'name' => 'Tacos al Pastor',
            'column' => null,
            'title' => 'Mejores Restaurantes de Tacos al Pastor',
            'title_en' => 'Best Tacos al Pastor Restaurants',
            'description' => 'Los mejores tacos al pastor auténticos. Carne marinada en achiote con piña, cilantro y cebolla.',
            'description_en' => 'Best authentic tacos al pastor. Achiote-marinated pork with pineapple, cilantro and onion.',
            'hero_text' => 'Tacos al Pastor Auténticos',
            'body' => 'Los tacos al pastor tienen influencia del shawarma árabe traído a México en el siglo XX. La carne de cerdo se marina en achiote y especias, se asa en un trompo vertical y se sirve con piña, cilantro y cebolla en tortilla de maíz.',
        ],
        'mole' => [
            'name' => 'Mole',
            'column' => 'has_homemade_mole',
            'title' => 'Mejores Restaurantes de Mole Mexicano',
            'title_en' => 'Best Mexican Mole Restaurants',
            'description' => 'Los mejores restaurantes de mole auténtico mexicano. Mole negro, rojo, verde, poblano y más variedades.',
            'description_en' => 'Best authentic Mexican mole restaurants. Black, red, green, poblano mole and more varieties.',
            'hero_text' => 'Mole Mexicano Auténtico',
            'body' => 'El mole es la salsa más compleja de la gastronomía mexicana, con recetas que pueden incluir más de 30 ingredientes: chiles secos, chocolate, especias, nueces y semillas. El mole negro de Oaxaca y el mole poblano son los más reconocidos mundialmente.',
        ],
        'menudo' => [
            'name' => 'Menudo',
            'column' => 'has_pozole_menudo',
            'title' => 'Mejores Restaurantes de Menudo Mexicano',
            'title_en' => 'Best Mexican Menudo Restaurants',
            'description' => 'Los mejores restaurantes de menudo auténtico mexicano. Caldo de res con maíz cacahuazintle, chile y orégano.',
            'description_en' => 'Best authentic Mexican menudo restaurants. Beef tripe soup with hominy, chile and oregano.',
            'hero_text' => 'Menudo Mexicano Auténtico',
            'body' => 'El menudo es un caldo tradicional mexicano preparado con callos de res (tripa) y maíz cacahuazintle (hominy), sazonado con chile rojo, orégano y especias. Se sirve típicamente los fines de semana como remedio natural.',
        ],
        'chiles-rellenos' => [
            'name' => 'Chiles Rellenos',
            'column' => null,
            'title' => 'Mejores Restaurantes de Chiles Rellenos',
            'title_en' => 'Best Chiles Rellenos Restaurants',
            'description' => 'Los mejores chiles rellenos mexicanos auténticos. Poblano relleno de queso, picadillo o rajas con crema.',
            'description_en' => 'Best authentic Mexican chiles rellenos. Poblano peppers stuffed with cheese, picadillo or rajas.',
            'hero_text' => 'Chiles Rellenos Auténticos',
            'body' => 'Los chiles rellenos son chiles poblanos asados y pelados, rellenos de queso Oaxaca, picadillo o rajas con crema, capeados en huevo y fritos. Se sirven bañados en salsa roja o verde y acompañados de arroz y frijoles.',
        ],
        'carne-asada' => [
            'name' => 'Carne Asada',
            'column' => 'has_charcoal_grill',
            'title' => 'Mejores Restaurantes de Carne Asada Mexicana',
            'title_en' => 'Best Mexican Carne Asada Restaurants',
            'description' => 'Los mejores restaurantes de carne asada mexicana auténtica. Arrachera marinada a las brasas con guacamole y tortillas.',
            'description_en' => 'Best authentic Mexican carne asada restaurants. Marinated beef grilled over charcoal with guacamole and tortillas.',
            'hero_text' => 'Carne Asada Mexicana Auténtica',
            'body' => 'La carne asada es un platillo central de la cultura norteña mexicana, especialmente en Sonora, Chihuahua y Sinaloa. Arrachera o corte de res marinado en cítricos y especias, asado a las brasas y servido con tortillas, guacamole, frijoles y salsa.',
        ],
        'carnitas' => [
            'name' => 'Carnitas',
            'column' => 'has_carnitas',
            'title' => 'Mejores Restaurantes de Carnitas Mexicanas',
            'title_en' => 'Best Mexican Carnitas Restaurants',
            'description' => 'Los mejores restaurantes de carnitas auténticas. Cerdo confitado en manteca al estilo michoacano.',
            'description_en' => 'Best authentic Mexican carnitas restaurants. Michoacan-style pork confit with tortillas and salsa.',
            'hero_text' => 'Carnitas Mexicanas Auténticas',
            'body' => 'Las carnitas son carne de cerdo cocida en su propia manteca a fuego lento, originarias del estado de Michoacán. El resultado es carne tierna y jugosa con una capa exterior crujiente. Se sirven en tortillas con cebolla, cilantro y salsa verde o roja.',
        ],
        'barbacoa' => [
            'name' => 'Barbacoa',
            'column' => 'has_barbacoa',
            'title' => 'Mejores Restaurantes de Barbacoa Mexicana',
            'title_en' => 'Best Mexican Barbacoa Restaurants',
            'description' => 'Los mejores restaurantes de barbacoa auténtica. Carne cocida lentamente en hoyo de tierra o vapor al estilo tradicional.',
            'description_en' => 'Best authentic Mexican barbacoa restaurants. Slow-cooked beef or lamb in the traditional pit style.',
            'hero_text' => 'Barbacoa Mexicana Auténtica',
            'body' => 'La barbacoa es una técnica milenaria de cocción lenta donde la carne de res, borrego o cabeza se envuelve en pencas de maguey y se cuece bajo tierra o al vapor. Es un platillo dominical por excelencia en México central, especialmente en Hidalgo y el Estado de México.',
        ],
    ];

    public function show(string $dish): View
    {
        if (!isset($this->dishes[$dish])) {
            abort(404);
        }

        $data = $this->dishes[$dish];
        $column = $data['column'];

        $query = Restaurant::approved()
            ->with(['state'])
            ->select(['id', 'name', 'slug', 'city', 'state_id', 'address', 'average_rating', 'total_reviews', 'description', 'image'])
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews');

        if ($column) {
            $query->where($column, true);
        }

        $restaurants = $query->limit(50)->get();

        // Fallback: if no results, get top Mexican restaurants
        if ($restaurants->isEmpty()) {
            $restaurants = Restaurant::approved()
                ->with(['state'])
                ->select(['id', 'name', 'slug', 'city', 'state_id', 'address', 'average_rating', 'total_reviews', 'description', 'image'])
                ->orderByDesc('average_rating')
                ->orderByDesc('total_reviews')
                ->limit(50)
                ->get();
        }

        return view('dishes.dish', compact('dish', 'data', 'restaurants'));
    }

    public function birria(): View  { return $this->show('birria'); }
    public function tamales(): View { return $this->show('tamales'); }
    public function pozole(): View  { return $this->show('pozole'); }
    public function enchiladas(): View    { return $this->show('enchiladas'); }
    public function tacosAlPastor(): View { return $this->show('tacos-al-pastor'); }
    public function mole(): View          { return $this->show('mole'); }
    public function menudo(): View        { return $this->show('menudo'); }
    public function chilesRellenos(): View{ return $this->show('chiles-rellenos'); }
    public function carneAsada(): View    { return $this->show('carne-asada'); }
    public function carnitas(): View  { return $this->show('carnitas'); }
    public function barbacoa(): View  { return $this->show('barbacoa'); }
}
