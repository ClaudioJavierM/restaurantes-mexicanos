<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'       => 'La Historia de la Birria: Del Jalisco al Mundo',
                'title_en'    => 'The History of Birria: From Jalisco to the World',
                'slug'        => 'historia-de-la-birria',
                'category'    => 'historia',
                'author'      => 'Equipo FAMER',
                'featured'    => true,
                'seo_title'   => 'Historia de la Birria Mexicana: Origen y Evolución | FAMER',
                'seo_description' => 'Descubre el fascinante origen de la birria en Jalisco, cómo pasó de ser comida de chivo a estrella mundial y por qué conquistó a Estados Unidos.',
                'excerpt'     => 'La birria nació en Jalisco hace más de 400 años como un platillo humilde de cabra. Hoy conquista ciudades enteras en Estados Unidos. Esta es su historia.',
                'excerpt_en'  => 'Birria was born in Jalisco over 400 years ago as a humble goat dish. Today it conquers entire cities across the United States. This is its story.',
                'image_prompt' => 'Professional food photography of authentic Mexican birria de res, steaming deep red consomé broth in a traditional clay bowl, tender shredded beef visible, garnished with fresh cilantro, diced white onion and lime wedges, warm dramatic lighting, rustic Mexican restaurant setting, high detail, appetizing',
                'content' => '<h2>Los Humildes Orígenes del Platillo Más Viral de México</h2>
<p>La birria es hoy uno de los platillos mexicanos más reconocidos en el mundo. Sus fotos inundan Instagram, sus birrieros tienen filas de horas en Los Ángeles, Nueva York y Chicago, y la palabra "birria" aparece en los menús de restaurantes de alta cocina en tres continentes. Sin embargo, su historia comienza de manera muy distinta: en la pobreza rural de Jalisco, hace más de cuatro siglos.</p>

<h2>Jalisco: La Cuna de un Clásico</h2>
<p>La birria tiene su origen en el estado de Jalisco, México, aunque su historia exacta se mezcla con la del mestizaje culinario que siguió a la conquista española. Cuando los colonizadores trajeron cabras al continente americano, los animales se reprodujeron rápidamente y pronto abundaban por todo el occidente de México. El problema era que su carne tenía un olor fuerte y peculiar que la hacía poco apetecible para muchos.</p>
<p>Fue la necesidad y el ingenio de las comunidades indígenas y mestizas de Jalisco lo que transformó este desafío en una oportunidad culinaria extraordinaria. Comenzaron a marinar la carne de chivo durante horas en una mezcla de chiles secos —guajillo, ancho, pasilla, árbol—, vinagre, hierbas de olor y especias. Luego la cocinaban lentamente en hornos de tierra, envuelta en pencas de maguey, durante toda la noche.</p>
<p>El resultado era una carne tan tierna que se deshacía al contacto, con un sabor profundo y complejo que no tenía nada de ese olor original. El consomé que quedaba del cocimiento —rojo intenso, aromático y lleno de sabor— se convirtió en parte esencial del plato.</p>

<h2>El Significado de la Palabra</h2>
<p>Existe debate sobre el origen exacto del término "birria". La explicación más aceptada es que la palabra viene del español antiguo y significa algo de poco valor, una "birria" era algo despreciable o sin importancia. Los españoles habrían usado este término despectivo para referirse al platillo de cabra que comían los indígenas pobres. Con el tiempo, como ocurre con tantas cosas en la cultura popular, el insulto se convirtió en identidad y orgullo.</p>

<h2>De Chivo a Res: La Gran Transformación</h2>
<p>Durante siglos, la birria fue exclusivamente de chivo o borrego. Era el platillo de las bodas, los bautizos y las fiestas patronales en los pueblos jaliscienses. Cada familia tenía su receta secreta, transmitida de generación en generación.</p>
<p>La transición hacia la carne de res llegó con la urbanización. A medida que las familias jaliscienses migraron a Guadalajara y luego a Estados Unidos, la cabra fue difícil de conseguir y cara. La res era más accesible, y aunque el sabor es diferente, el resultado con las técnicas tradicionales de marinado y cocción lenta es igualmente espectacular.</p>
<p>Hoy, la birria de res —especialmente en forma de tacos con el consomé para remojar— es la versión más popular en ciudades como Los Ángeles, donde la comunidad jalisciense es especialmente numerosa.</p>

<h2>La Conquista de Estados Unidos</h2>
<p>El fenómeno de la birria en Estados Unidos es relativamente reciente pero extraordinariamente rápido. En los años 2010, los birrieros de Los Ángeles empezaron a experimentar con los "quesabirria" —tacos de birria con queso Oaxaca fundido, la tortilla dorada en el aceite rojo del consomé. La combinación visual era irresistible para las redes sociales.</p>
<p>En 2019 y 2020, los videos de quesabirria se volvieron virales en TikTok e Instagram. El "cheese pull" —el queso que se estira al separar el taco— se convirtió en uno de los momentos gastronómicos más compartidos de internet. De repente, todo el mundo quería birria.</p>

<h2>Encuentra la Mejor Birria Cerca de Ti</h2>
<p>En FAMER, tenemos listados miles de restaurantes que sirven birria auténtica en todo Estados Unidos y México. Desde los clásicos establecimientos jaliscienses hasta las innovadoras birrerías de nueva generación que experimentan con ramen de birria, pizzas y más.</p>
<p>La birria ya no es solo comida: es cultura, es identidad y es el mejor argumento gastronómico que México ha exportado al mundo en décadas.</p>',
                'content_en' => '<h2>The Humble Origins of Mexico\'s Most Viral Dish</h2>
<p>Birria is today one of the most recognized Mexican dishes in the world. Its photos flood Instagram, birrieros have hour-long lines in Los Angeles, New York and Chicago, and the word "birria" appears on fine dining menus on three continents. However, its history begins very differently: in the rural poverty of Jalisco, over four centuries ago.</p>
<p>Born from necessity and indigenous ingenuity, birria transformed from a despised "throwaway" dish into a global culinary phenomenon. This is the remarkable story of how goat stew became the most talked-about food on social media.</p>',
            ],
            [
                'title'       => 'Los Mejores Tamales por Región de México: Guía Completa',
                'title_en'    => 'The Best Mexican Tamales by Region: A Complete Guide',
                'slug'        => 'tamales-por-region-de-mexico',
                'category'    => 'guias',
                'author'      => 'Equipo FAMER',
                'featured'    => false,
                'seo_title'   => 'Tipos de Tamales Mexicanos por Región: Guía Completa | FAMER',
                'seo_description' => 'Conoce todos los tipos de tamales mexicanos: oaxaqueños, veracruzanos, norteños, dulces y más. Guía completa por región con ingredientes y tradiciones.',
                'excerpt'     => 'México tiene tantos tipos de tamales como regiones. Desde los enormes tamales de rajas con queso norteños hasta los delicados oaxaqueños envueltos en hoja de plátano. Una guía esencial.',
                'excerpt_en'  => 'Mexico has as many types of tamales as it has regions. From the enormous northern tamales with rajas and cheese to the delicate Oaxacan ones wrapped in banana leaf. An essential guide.',
                'image_prompt' => 'Professional overhead flat lay food photography of assorted authentic Mexican tamales, some unwrapped showing colorful fillings like mole negro chicken, rajas con queso, and sweet pink tamale, mix of corn husks and banana leaf wrappings, steam rising, rustic wooden table, warm natural lighting, high resolution',
                'content' => '<h2>El Tamal: Patrimonio Culinario de México</h2>
<p>El tamal es quizás el alimento más antiguo y universal de México. Con más de 3,000 años de historia —los aztecas los preparaban como ofrenda a sus dioses y como alimento para los guerreros— los tamales han evolucionado de manera fascinante en cada rincón del país. Hoy existen más de 500 variedades documentadas, y cada región tiene su propia versión que refleja sus ingredientes, su historia y su identidad cultural.</p>

<h2>Tamales Oaxaqueños: El Rey de la Elegancia</h2>
<p>Los tamales oaxaqueños son considerados por muchos la versión más sofisticada. Se distinguen por tres características principales: la masa preparada con asiento (grasa de cerdo con sedimento), el relleno siempre lleva mole negro con pollo o puerco, y están envueltos en hoja de plátano en lugar de hoja de maíz.</p>
<p>La hoja de plátano le da un sabor y textura únicos: la masa queda más húmeda y aromática, con un ligero sabor verde que complementa perfectamente la complejidad del mole. Son más grandes que la mayoría de los tamales del país, y su color oscuro del mole negro contrasta hermosamente con la masa blanca.</p>

<h2>Tamales Veracruzanos: La Frescura del Trópico</h2>
<p>En Veracruz, los tamales incorporan los sabores del Golfo de México. Los más famosos son los de rajas con queso, rellenos de chiles jalapeños o poblanos con queso fresco, pero también hay versiones con camarones o jaiba (cangrejo). Se envuelven tanto en hoja de maíz como de plátano, dependiendo de la subregión.</p>
<p>Los "zacahuiles" son los tamales gigantes veracruzanos: pueden medir hasta un metro de largo, se preparan para bodas y fiestas grandes, y se cuecen en hornos de barro durante toda la noche con carne de puerco y chiles anchos.</p>

<h2>Tamales Norteños: Grandes y Generosos</h2>
<p>En estados como Chihuahua, Sonora y Nuevo León, los tamales son notoriamente más grandes que en el resto del país. La masa es más gruesa y se rellena generosamente con carne de puerco en salsa roja, rajas con queso, o picadillo con pasas y almendras (influencia árabe de la migración libanesa al norte de México).</p>
<p>En las comunidades mexicanas del sur de Texas y Nuevo México, los tamales norteños son la base de las celebraciones navideñas. La tradición de hacer tamales en familia —las "tamaladas"— es un ritual social tan importante como la comida misma.</p>

<h2>Tamales del Centro: Los Más Conocidos en el Mundo</h2>
<p>Los tamales del Estado de México, Ciudad de México e Hidalgo son probablemente los más conocidos fuera de México. Son de tamaño mediano, envueltos en hoja de maíz, con rellenos que van desde el clásico de rajas con queso hasta el mole con pollo, frijoles con queso, o dulce de fresa.</p>
<p>El "tamal de elote" o tamal de maíz tierno es una especialidad de la temporada de lluvias: masa preparada con elote fresco molido, a veces con rajas y crema, con una textura más suave y un sabor dulce natural extraordinario.</p>

<h2>Tamales Chiapanecos: Complejidad en Cada Bocado</h2>
<p>En Chiapas, los tamales alcanzan su máxima complejidad. Los "chipilín" incorporan esta hierba aromática local en la masa, dándole un color verde y un sabor herbáceo único. Los de "bola" son esféricos y se sirven flotando en caldo de pollo. Los "untados" o "de cambray" llevan recado rojo (pasta de especias) y se sirven deshojados directamente en el plato.</p>

<h2>Tamales Dulces: El Lado Festivo</h2>
<p>En prácticamente todo México existen tamales dulces para el desayuno o como postre. Los más comunes son los de color rosa (con colorante y azúcar), los de piña, los de fresa, y los de chocolate. En la Ciudad de México, los tamales dulces de "atole" se venden en los puestos de la madrugada junto con su bebida homónima.</p>

<h2>La Temporada de los Tamales</h2>
<p>Aunque se comen todo el año, los tamales tienen su gran momento de gloria en dos épocas: el Día de Muertos (donde son ofrenda obligatoria) y la Navidad-Reyes Magos. La tradición de hacer tamales en familia para las posadas es una de las más entrañables de la cultura mexicana.</p>

<h2>Encuentra Tamales Auténticos Cerca de Ti</h2>
<p>En FAMER, nuestro directorio de restaurantes mexicanos te permite encontrar los mejores tamales de tu ciudad. Muchos de nuestros restaurantes afiliados especifican su región de origen y los tipos de tamales que ofrecen. Encuentra tu próxima tamalada en nuestro buscador.</p>',
                'content_en' => '<h2>Tamales: Mexico\'s 3,000-Year-Old Culinary Heritage</h2>
<p>The tamal is perhaps Mexico\'s most ancient and universal food. With over 3,000 years of history — the Aztecs prepared them as offerings to their gods and food for warriors — tamales have evolved fascinatingly in every corner of the country. Today there are over 500 documented varieties, and each region has its own version reflecting its ingredients, history, and cultural identity.</p>
<p>From the sophisticated Oaxacan tamales wrapped in banana leaf with black mole, to the giant northern "zacahuiles" a meter long, to the sweet pink tamales of Christmas celebrations — this guide will help you discover Mexico\'s incredible tamal diversity.</p>',
            ],
            [
                'title'       => 'Guía del Mole Mexicano: Los 7 Moles y Sus Orígenes',
                'title_en'    => 'Guide to Mexican Mole: The 7 Moles and Their Origins',
                'slug'        => 'guia-del-mole-mexicano-7-tipos',
                'category'    => 'guias',
                'author'      => 'Equipo FAMER',
                'featured'    => false,
                'seo_title'   => 'Los 7 Tipos de Mole Mexicano: Guía Completa de Orígenes | FAMER',
                'seo_description' => 'Conoce los 7 moles mexicanos auténticos: negro, coloradito, amarillo, verde, rojo, chichilo y manchamanteles. Historia, ingredientes y dónde encontrarlos.',
                'excerpt'     => 'El mole no es una salsa: es una filosofía culinaria. Con hasta 36 ingredientes y días de preparación, los 7 moles de Oaxaca representan la cima de la cocina mexicana.',
                'excerpt_en'  => 'Mole is not just a sauce: it is a culinary philosophy. With up to 36 ingredients and days of preparation, the 7 moles of Oaxaca represent the pinnacle of Mexican cuisine.',
                'image_prompt' => 'Professional food photography of Mexican mole negro, rich deep dark brown sauce with complex texture poured over tender chicken breast, garnished with toasted sesame seeds and a single dried chile, served on traditional hand-painted Talavera plate, soft dramatic side lighting, depth of field, Oaxacan restaurant atmosphere',
                'content' => '<h2>El Mole: La Madre de Todas las Salsas Mexicanas</h2>
<p>Si hay un platillo que encapsula la complejidad, la historia y el alma de la cocina mexicana, ese es el mole. La palabra viene del náhuatl "mulli" o "molli", que simplemente significa salsa o mezcla. Pero reducir el mole a una simple salsa es como llamar a la Ópera de Wagner "una canción".</p>
<p>Un mole negro oaxaqueño auténtico puede contener hasta 36 ingredientes diferentes: múltiples tipos de chiles secos tostados, chocolate amargo, especias como clavo, canela y pimienta negra, semillas de ajonjolí y calabaza tostadas, jitomates, tomatillos, cebollas quemadas, ajo asado, pan o tortilla quemados (que dan color y espesor), y hierbas aromáticas. Su preparación puede llevar dos o tres días completos.</p>

<h2>Los 7 Moles de Oaxaca</h2>
<p>Oaxaca es conocida mundialmente como "la tierra de los siete moles", aunque en la práctica existen decenas de variaciones. Los siete reconocidos oficialmente son:</p>

<h3>1. Mole Negro: El Rey</h3>
<p>El más complejo y profundo de todos. Su color casi negro viene de los chiles mulato y chilhuacle negro tostados hasta casi quemarse, combinados con chocolate amargo. Es el mole de las grandes celebraciones: bodas, quince años, funerales. Su sabor es una sinfonía de dulce, amargo, picante y ahumado que evoluciona en el paladar por minutos.</p>

<h3>2. Mole Colorado o Coloradito</h3>
<p>De color rojo intenso, más dulce que el negro y menos complejo. Lleva chile ancho, jitomate, pasas y chocolate en menor proporción. Es el mole más accesible para quienes se inician en este universo, y se sirve frecuentemente con enchiladas coloradas en la Ciudad de México.</p>

<h3>3. Mole Amarillo</h3>
<p>El más cotidiano de los siete. De color amarillo-naranja, más ligero en cuerpo y sabor. Lleva chile guajillo, jitomate amarillo, hojas de aguacate (que le dan un anís sutil) y masa de maíz como espesante. Es el mole familiar, el de los tlayudos y las enfrijoladas oaxaqueñas del día a día.</p>

<h3>4. Mole Verde</h3>
<p>El más fresco y herbáceo. A diferencia de los demás, usa ingredientes en su mayoría crudos o cocidos brevemente: chile verde, tomate verde, pepitas de calabaza, hierbas como epazote, hoja santa y hierba santa. Su color verde intenso y su sabor limpio y vegetal lo hacen ideal para pescados y mariscos.</p>

<h3>5. Mole Rojo</h3>
<p>Similar al coloradito pero más sencillo en su composición. Usa principalmente chile guajillo y ancho, con jitomate, ajo y cebolla. Es el más fácil de preparar en casa y el más extendido en el centro de México como salsa para enchiladas y tamales.</p>

<h3>6. Chichilo</h3>
<p>El menos conocido fuera de Oaxaca. De color marrón oscuro, lleva chile chilhuacle negro quemado, tomate verde y una hierba local llamada hierba santa. Su sabor es ahumado y ligeramente amargo, con notas herbáceas únicas. Se sirve casi exclusivamente con carne de res, especialmente con costilla.</p>

<h3>7. Manchamanteles</h3>
<p>El más dulce y frutal de todos. Literalmente "mancha manteles", advirtiendo sobre su naturaleza colorida. Lleva chile ancho, jitomate, pero también frutas como piña, plátano macho, durazno y manzana. Es el mole más parecido a un mole de olla o un guiso, y se sirve con cerdo o pollo. Su sabor es una fusión de lo salado, lo picante y lo frutal que sorprende en cada bocado.</p>

<h2>El Mole Poblano: Un Caso Aparte</h2>
<p>Técnicamente Puebla tiene su propia tradición de moles, y el mole poblano es el más famoso de México a nivel internacional. La leyenda dice que fue inventado por las monjas del Convento de Santa Rosa en Puebla en el siglo XVII para homenajear a un virrey. Lleva chile mulato, chile pasilla, chile ancho y chile chipotle, junto con chocolate, almendras, pasas, ajonjolí y más de 20 ingredientes adicionales.</p>

<h2>Dónde Encontrar Mole Auténtico</h2>
<p>El mole auténtico requiere días de preparación y conocimiento profundo. No todos los restaurantes que dicen servirlo lo hacen desde cero. En FAMER, puedes buscar restaurantes que específicamente indican "mole negro auténtico" o que son de origen oaxaqueño o poblano, lo que aumenta la probabilidad de encontrar una versión verdaderamente tradicional.</p>
<p>Si visitas Oaxaca, el mercado 20 de Noviembre tiene puestos especializados donde puedes probar los siete moles en un solo lugar. En Estados Unidos, busca restaurantes oaxaqueños en ciudades con comunidad importante como Los Ángeles, Chicago y Nueva York.</p>',
                'content_en' => '<h2>Mole: The Mother of All Mexican Sauces</h2>
<p>If there is one dish that encapsulates the complexity, history, and soul of Mexican cuisine, it is mole. The word comes from the Nahuatl "mulli" or "molli," which simply means sauce or mixture. But reducing mole to a simple sauce is like calling Wagner\'s Opera "a song."</p>
<p>An authentic Oaxacan black mole can contain up to 36 different ingredients: multiple types of toasted dried chiles, bitter chocolate, spices like clove, cinnamon and black pepper, toasted sesame and pumpkin seeds, tomatoes, tomatillos, charred onions, roasted garlic, charred bread or tortilla (which give color and thickness), and aromatic herbs. Its preparation can take two or three full days.</p>',
            ],
        ];

        $openaiKey = env('OPENAI_API_KEY');

        foreach ($posts as $post) {
            if (BlogPost::where('slug', $post['slug'])->exists()) {
                $this->command->info("Skipping existing post: {$post['slug']}");
                continue;
            }

            $this->command->info("Creating post: {$post['title']}");

            // Generate image with DALL-E 3
            $coverImage = null;
            if ($openaiKey) {
                try {
                    $this->command->info("  → Generating image with DALL-E 3...");
                    $response = Http::withHeaders([
                        'Authorization' => "Bearer {$openaiKey}",
                        'Content-Type'  => 'application/json',
                    ])->timeout(60)->post('https://api.openai.com/v1/images/generations', [
                        'model'   => 'dall-e-3',
                        'prompt'  => $post['image_prompt'],
                        'n'       => 1,
                        'size'    => '1792x1024',
                        'quality' => 'standard',
                    ]);

                    if ($response->successful()) {
                        $imageUrl = $response->json('data.0.url');
                        if ($imageUrl) {
                            $imageContent = Http::timeout(30)->get($imageUrl)->body();
                            $filename     = 'blog/covers/' . $post['slug'] . '.jpg';
                            Storage::disk('public')->makeDirectory('blog/covers');
                            Storage::disk('public')->put($filename, $imageContent);
                            $coverImage = $filename;
                            $this->command->info("  ✓ Image saved: {$filename}");
                        }
                    } else {
                        $this->command->warn("  ⚠ DALL-E failed: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->command->warn("  ⚠ Image error: " . $e->getMessage());
                }
            }

            BlogPost::create([
                'title'           => $post['title'],
                'title_en'        => $post['title_en'],
                'slug'            => $post['slug'],
                'category'        => $post['category'],
                'author'          => $post['author'],
                'featured'        => $post['featured'],
                'excerpt'         => $post['excerpt'],
                'excerpt_en'      => $post['excerpt_en'],
                'content'         => $post['content'],
                'content_en'      => $post['content_en'],
                'cover_image'     => $coverImage,
                'seo_title'       => $post['seo_title'],
                'seo_description' => $post['seo_description'],
                'tags'            => ['cocina-mexicana', $post['category']],
                'is_published'    => true,
                'published_at'    => now(),
                'view_count'      => 0,
            ]);

            $this->command->info("  ✓ Post created: {$post['slug']}");
        }

        $this->command->info("\n✅ Blog posts seeded successfully!");
    }
}
