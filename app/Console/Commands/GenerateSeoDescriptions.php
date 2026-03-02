<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSeoDescriptions extends Command
{
    protected $signature = 'restaurants:generate-seo
                            {--limit=100 : Number of restaurants to process}
                            {--force : Overwrite existing descriptions}
                            {--dry-run : Show what would be generated without saving}
                            {--state= : Only process restaurants in specific state code}';

    protected $description = 'Generate SEO-optimized descriptions for restaurants without descriptions';

    /**
     * Templates for different restaurant types
     */
    protected array $templates = [
        'tacos' => [
            "Descubre los auténticos tacos mexicanos en {name}, ubicado en {city}, {state}. {rating_text} Disfruta de tacos preparados con recetas tradicionales, tortillas frescas y los mejores ingredientes. Visítanos y experimenta el verdadero sabor de México.",
            "{name} es tu destino para los mejores tacos en {city}, {state}. {rating_text} Ofrecemos una variedad de tacos auténticos mexicanos, desde tacos al pastor hasta carnitas, preparados con pasión y tradición.",
            "En {name} servimos tacos auténticos mexicanos en {city}, {state}. {rating_text} Nuestras recetas familiares y ingredientes frescos hacen de cada taco una experiencia inolvidable.",
        ],
        'mariscos' => [
            "Saborea los mejores mariscos mexicanos en {name}, {city}, {state}. {rating_text} Especializados en ceviches, cockteles de camarón, pescado fresco y platillos del mar preparados al estilo tradicional mexicano.",
            "{name} ofrece auténticos mariscos estilo mexicano en {city}, {state}. {rating_text} Desde aguachiles hasta camarones a la diabla, nuestro menú celebra los sabores del océano con un toque mexicano.",
            "Descubre el sabor del mar en {name}, ubicado en {city}, {state}. {rating_text} Mariscos frescos preparados con recetas tradicionales mexicanas que te transportarán a las costas de México.",
        ],
        'birria' => [
            "{name} es el hogar de la auténtica birria mexicana en {city}, {state}. {rating_text} Nuestra birria se prepara con recetas tradicionales de Jalisco, perfecta para tacos, quesabirria o en consomé.",
            "Prueba la mejor birria de {city} en {name}, {state}. {rating_text} Carne jugosa, consomé rico y tortillas recién hechas hacen de nuestra birria una experiencia auténtica mexicana.",
            "En {name} servimos birria tradicional mexicana en {city}, {state}. {rating_text} Nuestras quesabirrias y tacos de birria son preparados diariamente con los mejores cortes de carne.",
        ],
        'burritos' => [
            "{name} sirve los burritos más grandes y deliciosos de {city}, {state}. {rating_text} Rellenos generosos, tortillas de harina suaves y sabores auténticos mexicanos en cada bocado.",
            "Descubre los auténticos burritos mexicanos en {name}, {city}, {state}. {rating_text} Desde burritos de carne asada hasta opciones vegetarianas, tenemos algo para todos.",
            "En {name} preparamos burritos al estilo tradicional mexicano en {city}, {state}. {rating_text} Ingredientes frescos, porciones generosas y sabor inigualable.",
        ],
        'carnitas' => [
            "Las mejores carnitas de {city} las encuentras en {name}, {state}. {rating_text} Cerdo cocido lentamente al estilo Michoacán, dorado a la perfección y servido con tortillas recién hechas.",
            "{name} es famoso por sus auténticas carnitas estilo Michoacán en {city}, {state}. {rating_text} Carne de cerdo jugosa y crujiente, perfecta para tacos o por kilo.",
            "Prueba las carnitas tradicionales mexicanas en {name}, ubicado en {city}, {state}. {rating_text} Preparadas con técnicas ancestrales para un sabor incomparable.",
        ],
        'tamales' => [
            "En {name} preparamos tamales auténticos mexicanos en {city}, {state}. {rating_text} Variedad de sabores tradicionales como rajas con queso, mole, y puerco en salsa verde.",
            "{name} ofrece los mejores tamales caseros de {city}, {state}. {rating_text} Hechos a mano con masa de maíz y rellenos generosos, siguiendo recetas familiares.",
            "Descubre los tamales tradicionales de {name} en {city}, {state}. {rating_text} Cada tamal es preparado con amor y las mejores recetas mexicanas.",
        ],
        'tortas' => [
            "Las tortas más deliciosas de {city} las encuentras en {name}, {state}. {rating_text} Pan crujiente, carnes jugosas y todos los complementos que hacen una torta mexicana perfecta.",
            "{name} sirve auténticas tortas mexicanas en {city}, {state}. {rating_text} Desde la clásica torta de milanesa hasta la cubana, tenemos tu favorita.",
            "En {name} preparamos tortas al estilo tradicional mexicano en {city}, {state}. {rating_text} Ingredientes frescos y porciones generosas en cada orden.",
        ],
        'panaderia' => [
            "{name} es tu panadería mexicana de confianza en {city}, {state}. {rating_text} Pan dulce fresco diariamente: conchas, cuernos, orejas, polvorones y más favoritos tradicionales.",
            "Descubre el auténtico pan dulce mexicano en {name}, {city}, {state}. {rating_text} Horneamos diariamente una variedad de panes tradicionales con recetas de generaciones.",
            "En {name} encontrarás la mejor panadería mexicana de {city}, {state}. {rating_text} Pan fresco, café de olla y el sabor de México en cada visita.",
        ],
        'default' => [
            "{name} es un restaurante mexicano auténtico ubicado en {city}, {state}. {rating_text} Ofrecemos una variedad de platillos tradicionales preparados con ingredientes frescos y recetas familiares que celebran la rica cultura culinaria de México.",
            "Descubre la auténtica cocina mexicana en {name}, {city}, {state}. {rating_text} Nuestro menú incluye favoritos tradicionales preparados con pasión y los mejores ingredientes, brindándote una experiencia gastronómica única.",
            "Bienvenido a {name}, tu destino para comida mexicana auténtica en {city}, {state}. {rating_text} Desde antojitos hasta platillos principales, cada dish está preparado con amor y tradición mexicana.",
            "{name} sirve deliciosa comida mexicana tradicional en {city}, {state}. {rating_text} Visítanos para disfrutar de sabores auténticos, ambiente acogedor y hospitalidad mexicana.",
            "En {name} celebramos la cocina mexicana en {city}, {state}. {rating_text} Nuestros platillos tradicionales son preparados diariamente con ingredientes frescos y recetas que han pasado de generación en generación.",
        ],
    ];

    /**
     * SEO title templates
     */
    protected array $titleTemplates = [
        "{name} - Restaurante Mexicano en {city}, {state_code}",
        "{name} | Comida Mexicana Auténtica en {city}",
        "{name} - Auténtica Cocina Mexicana | {city}, {state_code}",
    ];

    /**
     * Meta description templates
     */
    protected array $metaTemplates = [
        "{name} en {city}, {state} ofrece auténtica comida mexicana. {rating_text} Menú variado, ingredientes frescos. ¡Visítanos hoy!",
        "Restaurante mexicano {name} en {city}, {state}. {rating_text} Tacos, burritos y platillos tradicionales. Ordena ahora.",
        "Descubre {name}, restaurante mexicano en {city}. {rating_text} Comida auténtica, precios accesibles. {city}, {state_code}.",
    ];

    public function handle()
    {
        $this->info('🔄 Generando descripciones SEO para restaurantes...');
        $this->newLine();

        $query = Restaurant::approved()
            ->with(['state', 'category']);

        if (!$this->option('force')) {
            $query->where(function($q) {
                $q->whereNull('description')
                  ->orWhere('description', '');
            });
        }

        if ($stateCode = $this->option('state')) {
            $query->whereHas('state', fn($q) => $q->where('code', strtoupper($stateCode)));
        }

        $limit = (int) $this->option('limit');
        $restaurants = $query->limit($limit)->get();

        if ($restaurants->isEmpty()) {
            $this->info('✅ No hay restaurantes que necesiten descripciones.');
            return Command::SUCCESS;
        }

        $this->info("📝 Procesando {$restaurants->count()} restaurantes...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($restaurants->count());
        $progressBar->start();

        $updated = 0;
        $errors = 0;

        foreach ($restaurants as $restaurant) {
            try {
                $description = $this->generateDescription($restaurant);
                $seoTitle = $this->generateSeoTitle($restaurant);
                $metaDescription = $this->generateMetaDescription($restaurant);

                if ($this->option('dry-run')) {
                    $this->newLine(2);
                    $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
                    $this->info("🏪 {$restaurant->name}");
                    $this->line("📍 {$restaurant->city}, {$restaurant->state?->name}");
                    $this->newLine();
                    $this->line("<fg=yellow>SEO Title:</> {$seoTitle}");
                    $this->newLine();
                    $this->line("<fg=yellow>Meta Description:</> {$metaDescription}");
                    $this->newLine();
                    $this->line("<fg=yellow>Description:</>");
                    $this->line($description);
                } else {
                    $restaurant->update([
                        'description' => $description,
                        // If you have these columns, uncomment:
                        // 'seo_title' => $seoTitle,
                        // 'meta_description' => $metaDescription,
                    ]);
                    $updated++;
                }

            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error con {$restaurant->name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN - No se guardaron cambios');
        } else {
            $this->info("✅ {$updated} restaurantes actualizados");
        }

        if ($errors > 0) {
            $this->warn("⚠️  {$errors} errores encontrados");
        }

        return Command::SUCCESS;
    }

    protected function generateDescription(Restaurant $restaurant): string
    {
        // Determine category/type for template selection
        $categorySlug = $restaurant->category?->slug ?? 'default';
        $yelpCategories = $restaurant->yelp_categories ?? [];

        // Try to match a specific template
        $templateKey = 'default';
        foreach (['tacos', 'mariscos', 'birria', 'burritos', 'carnitas', 'tamales', 'tortas', 'panaderia'] as $type) {
            if ($categorySlug === $type ||
                Str::contains(strtolower($restaurant->name), $type) ||
                $this->hasYelpCategory($yelpCategories, $type)) {
                $templateKey = $type;
                break;
            }
        }

        $templates = $this->templates[$templateKey];
        $template = $templates[array_rand($templates)];

        return $this->fillTemplate($template, $restaurant);
    }

    protected function generateSeoTitle(Restaurant $restaurant): string
    {
        $template = $this->titleTemplates[array_rand($this->titleTemplates)];
        return $this->fillTemplate($template, $restaurant);
    }

    protected function generateMetaDescription(Restaurant $restaurant): string
    {
        $template = $this->metaTemplates[array_rand($this->metaTemplates)];
        $meta = $this->fillTemplate($template, $restaurant);

        // Ensure it's under 160 characters for SEO
        if (strlen($meta) > 160) {
            $meta = Str::limit($meta, 157, '...');
        }

        return $meta;
    }

    protected function fillTemplate(string $template, Restaurant $restaurant): string
    {
        $ratingText = '';
        if ($restaurant->google_rating && $restaurant->google_rating >= 4.0) {
            $reviewCount = $restaurant->google_reviews_count ?? 0;
            if ($reviewCount > 100) {
                $ratingText = "Con {$restaurant->google_rating} estrellas y más de " . number_format($reviewCount) . " reseñas, es uno de los favoritos locales.";
            } elseif ($reviewCount > 0) {
                $ratingText = "Calificado con {$restaurant->google_rating} estrellas por nuestros clientes.";
            }
        } elseif ($restaurant->yelp_rating && $restaurant->yelp_rating >= 4.0) {
            $ratingText = "Altamente recomendado con {$restaurant->yelp_rating} estrellas en Yelp.";
        }

        $replacements = [
            '{name}' => $restaurant->name,
            '{city}' => $restaurant->city,
            '{state}' => $restaurant->state?->name ?? '',
            '{state_code}' => $restaurant->state?->code ?? '',
            '{rating_text}' => $ratingText,
            '{category}' => $restaurant->category?->name ?? 'Comida Mexicana',
        ];

        $result = str_replace(array_keys($replacements), array_values($replacements), $template);

        // Clean up double spaces
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return $result;
    }

    protected function hasYelpCategory(array|string|null $categories, string $type): bool
    {
        if (empty($categories)) return false;

        if (is_string($categories)) {
            $categories = json_decode($categories, true) ?? [];
        }

        $searchTerms = [
            'tacos' => ['tacos', 'taqueria'],
            'mariscos' => ['seafood', 'mariscos', 'fish'],
            'birria' => ['birria'],
            'burritos' => ['burritos', 'burrito'],
            'carnitas' => ['carnitas'],
            'tamales' => ['tamales'],
            'tortas' => ['tortas', 'sandwiches'],
            'panaderia' => ['bakery', 'panaderia', 'bakeries'],
        ];

        $terms = $searchTerms[$type] ?? [];

        if (!is_array($categories)) return false; foreach ($categories as $cat) {
            $catLower = strtolower(is_array($cat) ? ($cat['alias'] ?? $cat['title'] ?? '') : $cat);
            foreach ($terms as $term) {
                if (Str::contains($catLower, $term)) {
                    return true;
                }
            }
        }

        return false;
    }
}
