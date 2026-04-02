<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\View\View;

class DishController extends Controller
{
    /**
     * Dish metadata: name, optional boolean restaurant column (legacy), SEO copy.
     */
    protected array $dishes = [
        'birria' => [
            'name'        => 'Birria',
            'column'      => 'has_birria',
            'title'       => 'Mejores Restaurantes de Birria Mexicana',
            'title_en'    => 'Best Mexican Birria Restaurants',
            'description' => 'Encuentra los mejores restaurantes de birria auténtica mexicana cerca de ti. Birria de res, chivo y mixta.',
            'description_en' => 'Find the best authentic Mexican birria restaurants near you. Beef birria, goat birria, and more.',
            'hero_text'   => 'Birria Auténtica Mexicana',
            'body'        => 'La birria es uno de los platillos más icónicos de la cocina mexicana, originaria del estado de Jalisco. Este estofado tradicional de carne de res o chivo cocinado a fuego lento con chiles secos y especias se ha convertido en un fenómeno mundial.',
        ],
        'tacos' => [
            'name'        => 'Tacos Mexicanos',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Tacos Mexicanos',
            'title_en'    => 'Best Mexican Taco Restaurants',
            'description' => 'Los mejores tacos mexicanos auténticos: al pastor, carnitas, barbacoa, suadero y más.',
            'description_en' => 'Best authentic Mexican tacos: al pastor, carnitas, barbacoa, suadero and more.',
            'hero_text'   => 'Tacos Mexicanos Auténticos',
            'body'        => 'Los tacos son el platillo más versátil y representativo de México. Con decenas de variedades regionales, desde los tacos de canasta del centro de México hasta los tacos de carne asada del norte, hay un taco para cada gusto.',
        ],
        'tamales' => [
            'name'        => 'Tamales',
            'column'      => 'has_tamales',
            'title'       => 'Mejores Restaurantes de Tamales Mexicanos',
            'title_en'    => 'Best Mexican Tamales Restaurants',
            'description' => 'Los mejores tamales mexicanos auténticos. Tamales de rajas, dulce, verde, rojo y más estilos regionales.',
            'description_en' => 'Best authentic Mexican tamales. Rajas, sweet, green, red and more regional styles.',
            'hero_text'   => 'Tamales Mexicanos Auténticos',
            'body'        => 'Los tamales son una de las tradiciones culinarias más antiguas de México, con historia de más de 5,000 años. Masa de maíz rellena con carnes, chiles, queso o dulces, envuelta en hojas de maíz o plátano y cocida al vapor.',
        ],
        'pozole' => [
            'name'        => 'Pozole',
            'column'      => 'has_pozole_menudo',
            'title'       => 'Mejores Restaurantes de Pozole Mexicano',
            'title_en'    => 'Best Mexican Pozole Restaurants',
            'description' => 'Encuentra los mejores restaurantes de pozole mexicano auténtico. Pozole rojo, blanco y verde.',
            'description_en' => 'Find the best authentic Mexican pozole restaurants. Red, white and green pozole.',
            'hero_text'   => 'Pozole Mexicano Auténtico',
            'body'        => 'El pozole es un caldo ceremonial de origen prehispánico, hecho con maíz cacahuazintle (hominy), carne y chile. Se sirve con una variedad de toppings frescos como lechuga, rábano, cebolla, orégano y tostadas.',
        ],
        'enchiladas' => [
            'name'        => 'Enchiladas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Enchiladas Mexicanas',
            'title_en'    => 'Best Mexican Enchiladas Restaurants',
            'description' => 'Las mejores enchiladas mexicanas auténticas. Enchiladas verdes, rojas, suizas, mole y más estilos regionales.',
            'description_en' => 'Best authentic Mexican enchiladas. Green, red, Swiss, mole and more regional styles.',
            'hero_text'   => 'Enchiladas Mexicanas Auténticas',
            'body'        => 'Las enchiladas son tortillas de maíz bañadas en salsa de chile y rellenas de pollo, queso, frijoles o carne. Son uno de los platillos más representativos de la cocina mexicana, con decenas de variaciones regionales.',
        ],
        'mole' => [
            'name'        => 'Mole',
            'column'      => 'has_homemade_mole',
            'title'       => 'Mejores Restaurantes de Mole Mexicano',
            'title_en'    => 'Best Mexican Mole Restaurants',
            'description' => 'Los mejores restaurantes de mole auténtico mexicano. Mole negro, rojo, verde, poblano y más variedades.',
            'description_en' => 'Best authentic Mexican mole restaurants. Black, red, green, poblano mole and more varieties.',
            'hero_text'   => 'Mole Mexicano Auténtico',
            'body'        => 'El mole es la salsa más compleja de la gastronomía mexicana, con recetas que pueden incluir más de 30 ingredientes: chiles secos, chocolate, especias, nueces y semillas. El mole negro de Oaxaca y el mole poblano son los más reconocidos mundialmente.',
        ],
        'chiles-rellenos' => [
            'name'        => 'Chiles Rellenos',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Chiles Rellenos',
            'title_en'    => 'Best Chiles Rellenos Restaurants',
            'description' => 'Los mejores chiles rellenos mexicanos auténticos. Poblano relleno de queso, picadillo o rajas con crema.',
            'description_en' => 'Best authentic Mexican chiles rellenos. Poblano peppers stuffed with cheese, picadillo or rajas.',
            'hero_text'   => 'Chiles Rellenos Auténticos',
            'body'        => 'Los chiles rellenos son chiles poblanos asados y pelados, rellenos de queso Oaxaca, picadillo o rajas con crema, capeados en huevo y fritos. Se sirven bañados en salsa roja o verde y acompañados de arroz y frijoles.',
        ],
        'tortas' => [
            'name'        => 'Tortas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Tortas Mexicanas',
            'title_en'    => 'Best Mexican Torta Restaurants',
            'description' => 'Las mejores tortas mexicanas auténticas. Tortas de milanesa, ahogadas, cubanas y más.',
            'description_en' => 'Best authentic Mexican tortas. Milanesa, ahogada, cubana and more.',
            'hero_text'   => 'Tortas Mexicanas Auténticas',
            'body'        => 'La torta es el sándwich mexicano por excelencia, servido en bolillo o telera. Desde la torta ahogada de Guadalajara hasta la torta de milanesa capitalina, cada región tiene su versión favorita.',
        ],
        'tostadas' => [
            'name'        => 'Tostadas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Tostadas Mexicanas',
            'title_en'    => 'Best Mexican Tostada Restaurants',
            'description' => 'Las mejores tostadas mexicanas auténticas. Tostadas de tinga, ceviche, frijoles y más.',
            'description_en' => 'Best authentic Mexican tostadas. Tinga, ceviche, beans and more.',
            'hero_text'   => 'Tostadas Mexicanas Auténticas',
            'body'        => 'Las tostadas son tortillas de maíz fritas o tostadas, crujientes y cubiertas con frijoles, carne, ceviche o tinga. Son ligeras, versátiles y perfectas como entrada o platillo completo.',
        ],
        'quesadillas' => [
            'name'        => 'Quesadillas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Quesadillas Mexicanas',
            'title_en'    => 'Best Mexican Quesadilla Restaurants',
            'description' => 'Las mejores quesadillas mexicanas auténticas. Con queso Oaxaca, huitlacoche, flor de calabaza y más.',
            'description_en' => 'Best authentic Mexican quesadillas. With Oaxacan cheese, huitlacoche, squash blossom and more.',
            'hero_text'   => 'Quesadillas Mexicanas Auténticas',
            'body'        => 'Las quesadillas son tortillas de maíz dobladas o cerradas rellenas de queso y otros ingredientes. La versión chilanga de comal con huitlacoche, flor de calabaza o rajas es diferente a las versiones del norte con tortilla de harina.',
        ],
        'flautas' => [
            'name'        => 'Flautas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Flautas Mexicanas',
            'title_en'    => 'Best Mexican Flautas Restaurants',
            'description' => 'Las mejores flautas mexicanas auténticas. Flautas de pollo, res y papa fritas y crujientes.',
            'description_en' => 'Best authentic Mexican flautas. Crispy chicken, beef and potato flautas.',
            'hero_text'   => 'Flautas Mexicanas Auténticas',
            'body'        => 'Las flautas son tortillas enrolladas y fritas hasta quedar crujientes, rellenas de pollo, res o papa. Se sirven con guacamole, crema, queso y salsa verde o roja. Son uno de los antojitos mexicanos más populares.',
        ],
        'sopes' => [
            'name'        => 'Sopes',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Sopes Mexicanos',
            'title_en'    => 'Best Mexican Sopes Restaurants',
            'description' => 'Los mejores sopes mexicanos auténticos. Masa gruesa con frijoles, carne, crema y queso.',
            'description_en' => 'Best authentic Mexican sopes. Thick masa topped with beans, meat, cream and cheese.',
            'hero_text'   => 'Sopes Mexicanos Auténticos',
            'body'        => 'Los sopes son discos gruesos de masa de maíz con bordes levantados, fritos y cubiertos con frijoles, carne (res, pollo o chorizo), cebolla, crema, queso y salsa. Son un antojito sustancioso y reconfortante.',
        ],
        'menudo' => [
            'name'        => 'Menudo',
            'column'      => 'has_pozole_menudo',
            'title'       => 'Mejores Restaurantes de Menudo Mexicano',
            'title_en'    => 'Best Mexican Menudo Restaurants',
            'description' => 'Los mejores restaurantes de menudo auténtico mexicano. Caldo de res con maíz cacahuazintle, chile y orégano.',
            'description_en' => 'Best authentic Mexican menudo restaurants. Beef tripe soup with hominy, chile and oregano.',
            'hero_text'   => 'Menudo Mexicano Auténtico',
            'body'        => 'El menudo es un caldo tradicional mexicano preparado con callos de res (tripa) y maíz cacahuazintle (hominy), sazonado con chile rojo, orégano y especias. Se sirve típicamente los fines de semana como remedio natural.',
        ],
        'carnitas' => [
            'name'        => 'Carnitas',
            'column'      => 'has_carnitas',
            'title'       => 'Mejores Restaurantes de Carnitas Mexicanas',
            'title_en'    => 'Best Mexican Carnitas Restaurants',
            'description' => 'Los mejores restaurantes de carnitas auténticas. Cerdo confitado en manteca al estilo michoacano.',
            'description_en' => 'Best authentic Mexican carnitas restaurants. Michoacan-style pork confit with tortillas and salsa.',
            'hero_text'   => 'Carnitas Mexicanas Auténticas',
            'body'        => 'Las carnitas son carne de cerdo cocida en su propia manteca a fuego lento, originarias del estado de Michoacán. El resultado es carne tierna y jugosa con una capa exterior crujiente. Se sirven en tortillas con cebolla, cilantro y salsa verde o roja.',
        ],
        'barbacoa' => [
            'name'        => 'Barbacoa',
            'column'      => 'has_barbacoa',
            'title'       => 'Mejores Restaurantes de Barbacoa Mexicana',
            'title_en'    => 'Best Mexican Barbacoa Restaurants',
            'description' => 'Los mejores restaurantes de barbacoa auténtica. Carne cocida lentamente en hoyo de tierra o vapor al estilo tradicional.',
            'description_en' => 'Best authentic Mexican barbacoa restaurants. Slow-cooked beef or lamb in the traditional pit style.',
            'hero_text'   => 'Barbacoa Mexicana Auténtica',
            'body'        => 'La barbacoa es una técnica milenaria de cocción lenta donde la carne de res, borrego o cabeza se envuelve en pencas de maguey y se cuece bajo tierra o al vapor. Es un platillo dominical por excelencia en México central.',
        ],
        'ceviche' => [
            'name'        => 'Ceviche',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Ceviche Mexicano',
            'title_en'    => 'Best Mexican Ceviche Restaurants',
            'description' => 'Los mejores restaurantes de ceviche mexicano auténtico. Mariscos frescos marinados en limón con chile y cilantro.',
            'description_en' => 'Best authentic Mexican ceviche restaurants. Fresh seafood marinated in lime with chile and cilantro.',
            'hero_text'   => 'Ceviche Mexicano Auténtico',
            'body'        => 'El ceviche mexicano es mariscos frescos (camarón, pescado o pulpo) marinados en jugo de limón con tomate, cebolla, chile y cilantro. Las versiones de Sinaloa, Jalisco y Guerrero son las más famosas del país.',
        ],
        'elotes' => [
            'name'        => 'Elotes',
            'column'      => null,
            'title'       => 'Mejores Lugares de Elotes Mexicanos',
            'title_en'    => 'Best Mexican Elotes Spots',
            'description' => 'Los mejores elotes y esquites mexicanos auténticos. Elotes en vaso, en mazorca, con crema, queso y chile.',
            'description_en' => 'Best authentic Mexican elotes and esquites. Cup corn, on the cob, with cream, cheese and chile.',
            'hero_text'   => 'Elotes Mexicanos Auténticos',
            'body'        => 'Los elotes son maíz asado o cocido servido con mayonesa, crema, queso cotija, chile en polvo y limón. Los esquites son la versión en vaso con los granos sueltos. Son el antojito callejero más popular de México.',
        ],
        'churros' => [
            'name'        => 'Churros',
            'column'      => null,
            'title'       => 'Mejores Lugares de Churros Mexicanos',
            'title_en'    => 'Best Mexican Churros Spots',
            'description' => 'Los mejores churros mexicanos auténticos. Frescos, crujientes, con chocolate, cajeta o rellenos.',
            'description_en' => 'Best authentic Mexican churros. Fresh, crispy, with chocolate, cajeta or filled.',
            'hero_text'   => 'Churros Mexicanos Auténticos',
            'body'        => 'Los churros son masa de harina frita espolvoreada con azúcar y canela. En México se sirven con chocolate caliente, cajeta o helado. Las churrerías tradicionales los preparan frescos al momento.',
        ],
        'horchata' => [
            'name'        => 'Horchata',
            'column'      => null,
            'title'       => 'Mejores Lugares de Horchata Mexicana',
            'title_en'    => 'Best Mexican Horchata Spots',
            'description' => 'La mejor horchata mexicana auténtica. Agua fresca de arroz con canela, vainilla y almendra.',
            'description_en' => 'Best authentic Mexican horchata. Rice water with cinnamon, vanilla and almond.',
            'hero_text'   => 'Horchata Mexicana Auténtica',
            'body'        => 'La horchata mexicana es una bebida refrescante hecha de arroz remojado y molido con canela, vainilla y azúcar. Es una de las aguas frescas más populares de México y el complemento perfecto para tacos y antojitos.',
        ],
        'margaritas' => [
            'name'        => 'Margaritas',
            'column'      => null,
            'title'       => 'Mejores Restaurantes con Margaritas Mexicanas',
            'title_en'    => 'Best Mexican Margarita Bars & Restaurants',
            'description' => 'Los mejores restaurantes y bares con margaritas mexicanas auténticas. Margaritas clásicas, de fresa, de mango y más.',
            'description_en' => 'Best Mexican bars and restaurants with authentic margaritas. Classic, strawberry, mango and more.',
            'hero_text'   => 'Margaritas Mexicanas Auténticas',
            'body'        => 'La margarita es el cóctel mexicano más famoso del mundo, hecho con tequila, licor de naranja y jugo de limón. Servida con o sin sal en el borde, en vaso o congelada, es el acompañamiento perfecto para la comida mexicana.',
        ],
        // Legacy slugs (keep for backward compat)
        'tacos-al-pastor' => [
            'name'        => 'Tacos al Pastor',
            'column'      => null,
            'title'       => 'Mejores Restaurantes de Tacos al Pastor',
            'title_en'    => 'Best Tacos al Pastor Restaurants',
            'description' => 'Los mejores tacos al pastor auténticos. Carne marinada en achiote con piña, cilantro y cebolla.',
            'description_en' => 'Best authentic tacos al pastor. Achiote-marinated pork with pineapple, cilantro and onion.',
            'hero_text'   => 'Tacos al Pastor Auténticos',
            'body'        => 'Los tacos al pastor tienen influencia del shawarma árabe traído a México en el siglo XX. La carne de cerdo se marina en achiote y especias, se asa en un trompo vertical y se sirve con piña, cilantro y cebolla en tortilla de maíz.',
        ],
        'carne-asada' => [
            'name'        => 'Carne Asada',
            'column'      => 'has_charcoal_grill',
            'title'       => 'Mejores Restaurantes de Carne Asada Mexicana',
            'title_en'    => 'Best Mexican Carne Asada Restaurants',
            'description' => 'Los mejores restaurantes de carne asada mexicana auténtica. Arrachera marinada a las brasas.',
            'description_en' => 'Best authentic Mexican carne asada restaurants. Marinated beef grilled over charcoal.',
            'hero_text'   => 'Carne Asada Mexicana Auténtica',
            'body'        => 'La carne asada es un platillo central de la cultura norteña mexicana, especialmente en Sonora, Chihuahua y Sinaloa. Arrachera o corte de res marinado en cítricos y especias, asado a las brasas y servido con tortillas, guacamole, frijoles y salsa.',
        ],
    ];

    /**
     * Display the dish search page.
     */
    public function show(string $dish): View
    {
        if (!isset($this->dishes[$dish])) {
            abort(404);
        }

        $data   = $this->dishes[$dish];
        $column = $data['column'];

        // 1. Primary: restaurants with tagged menu items (new dish_type system)
        $taggedIds = MenuItem::byDishType($dish)
            ->join('menu_categories', 'menu_items.menu_category_id', '=', 'menu_categories.id')
            ->pluck('menu_categories.restaurant_id')
            ->unique();

        // 2. Legacy: restaurants with boolean column set (existing data)
        $columnIds = collect();
        if ($column) {
            $columnIds = Restaurant::where('status', 'approved')
                ->where($column, true)
                ->pluck('id');
        }

        // 3. Fallback: name/description keyword search
        $searchTerm = str_replace('-', ' ', $dish);
        $fallbackIds = Restaurant::where('status', 'approved')
            ->where(function ($q) use ($searchTerm, $data) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$data['name']}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('ai_description', 'like', "%{$searchTerm}%");
            })
            ->pluck('id');

        $allIds = $taggedIds->merge($columnIds)->merge($fallbackIds)->unique()->values();

        // Build paginated query
        $restaurants = Restaurant::whereIn('id', $allIds)
            ->where('status', 'approved')
            ->orderByDesc('average_rating')
            ->orderByDesc('total_reviews')
            ->paginate(20)
            ->withQueryString();

        $totalCount = $restaurants->total();
        $dishName   = $data['name'];

        return view('dishes.show', compact('dish', 'data', 'dishName', 'restaurants', 'totalCount'));
    }

    // Named shortcut methods (legacy routes)
    public function birria(): View         { return $this->show('birria'); }
    public function tamales(): View        { return $this->show('tamales'); }
    public function pozole(): View         { return $this->show('pozole'); }
    public function enchiladas(): View     { return $this->show('enchiladas'); }
    public function tacosAlPastor(): View  { return $this->show('tacos-al-pastor'); }
    public function mole(): View           { return $this->show('mole'); }
    public function menudo(): View         { return $this->show('menudo'); }
    public function chilesRellenos(): View { return $this->show('chiles-rellenos'); }
    public function carneAsada(): View     { return $this->show('carne-asada'); }
    public function carnitas(): View       { return $this->show('carnitas'); }
    public function barbacoa(): View       { return $this->show('barbacoa'); }
}
