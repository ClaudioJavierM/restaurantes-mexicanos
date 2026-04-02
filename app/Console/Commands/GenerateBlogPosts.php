<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateBlogPosts extends Command
{
    protected $signature = 'blog:generate-posts
                            {--count=3 : Number of posts to generate}
                            {--lang=es : Language for generation (es)}
                            {--dry-run : Preview topics without generating content}';

    protected $description = 'Generate AI blog posts about Mexican cuisine using GPT-4o-mini';

    protected array $topics = [
        // History & Culture
        ['title_es' => 'Historia de los Tacos al Pastor', 'title_en' => 'History of Tacos al Pastor', 'category' => 'historia', 'slug' => 'historia-tacos-al-pastor'],
        ['title_es' => 'El Pozole: Raíces Prehispánicas', 'title_en' => 'Pozole: Prehispanic Roots', 'category' => 'historia', 'slug' => 'pozole-raices-prehispanicas'],
        ['title_es' => 'La Tortilla de Maíz: Corazón de México', 'title_en' => 'The Corn Tortilla: Heart of Mexico', 'category' => 'cultura', 'slug' => 'tortilla-maiz-corazon-mexico'],
        ['title_es' => 'Chiles de México: Guía Completa', 'title_en' => 'Mexican Chiles: Complete Guide', 'category' => 'cultura', 'slug' => 'chiles-mexico-guia-completa'],
        ['title_es' => 'El Chile en Nogada: Tradición Poblana', 'title_en' => 'Chile en Nogada: Puebla Tradition', 'category' => 'historia', 'slug' => 'chile-en-nogada-tradicion-poblana'],
        ['title_es' => 'Mezcal vs Tequila: Diferencias y Sabores', 'title_en' => 'Mezcal vs Tequila: Differences', 'category' => 'cultura', 'slug' => 'mezcal-vs-tequila-diferencias'],
        ['title_es' => 'Cocina Oaxaqueña: Los 7 Moles', 'title_en' => 'Oaxacan Cuisine: The 7 Moles', 'category' => 'guias', 'slug' => 'cocina-oaxaquena-7-moles'],
        ['title_es' => 'Elotes y Esquites: Antojo Callejero', 'title_en' => 'Elotes and Esquites: Street Snacks', 'category' => 'cultura', 'slug' => 'elotes-esquites-antojo-callejero'],
        ['title_es' => 'Las Enchiladas: Variantes por Estado', 'title_en' => 'Enchiladas: Regional Variations', 'category' => 'guias', 'slug' => 'enchiladas-variantes-por-estado'],
        ['title_es' => 'Agua de Jamaica y Horchata: Bebidas Clásicas', 'title_en' => 'Agua de Jamaica and Horchata', 'category' => 'cultura', 'slug' => 'agua-jamaica-horchata-bebidas-clasicas'],
        ['title_es' => 'El Tlayuda: Pizza Oaxaqueña', 'title_en' => 'Tlayuda: Oaxacan Pizza', 'category' => 'guias', 'slug' => 'tlayuda-pizza-oaxaquena'],
        ['title_es' => 'Cochinita Pibil: Secreto Yucateco', 'title_en' => 'Cochinita Pibil: Yucatan Secret', 'category' => 'historia', 'slug' => 'cochinita-pibil-secreto-yucateco'],
        ['title_es' => 'Tamales: Tradición en Hoja de Maíz', 'title_en' => 'Tamales: Tradition in Corn Husk', 'category' => 'historia', 'slug' => 'tamales-tradicion-hoja-maiz'],
        ['title_es' => 'Sopas Mexicanas: Más Allá del Menudo', 'title_en' => 'Mexican Soups: Beyond Menudo', 'category' => 'guias', 'slug' => 'sopas-mexicanas-mas-alla-menudo'],
        ['title_es' => 'Pan Dulce: El Desayuno Mexicano', 'title_en' => 'Pan Dulce: The Mexican Breakfast', 'category' => 'cultura', 'slug' => 'pan-dulce-desayuno-mexicano'],
    ];

    public function handle(): int
    {
        $count   = (int) $this->option('count');
        $dryRun  = (bool) $this->option('dry-run');

        // Filter out topics whose slugs already exist in the DB
        $existingSlugs = BlogPost::pluck('slug')->toArray();

        $pending = array_filter(
            $this->topics,
            fn($topic) => ! in_array($topic['slug'], $existingSlugs)
        );

        $pending = array_values($pending);

        if (empty($pending)) {
            $this->info('All topics are already published. No new posts to generate.');
            return self::SUCCESS;
        }

        $toGenerate = array_slice($pending, 0, $count);

        if ($dryRun) {
            $this->info("Dry-run mode — would generate {$count} post(s):");
            foreach ($toGenerate as $topic) {
                $this->line("  • [{$topic['category']}] {$topic['title_es']} ({$topic['slug']})");
            }
            $this->info(count($pending) . ' topic(s) remaining in pool (including these).');
            return self::SUCCESS;
        }

        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            $this->error('OpenAI API key not configured (services.openai.api_key).');
            return self::FAILURE;
        }

        $generated = 0;
        $failed    = 0;

        foreach ($toGenerate as $topic) {
            $this->info("Generating: {$topic['title_es']}...");

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                    'model'      => 'gpt-4o-mini',
                    'max_tokens' => 1500,
                    'messages'   => [
                        [
                            'role'    => 'system',
                            'content' => 'Eres un experto en gastronomía mexicana y escritor de contenido SEO. Escribe artículos detallados, informativos y atractivos sobre la cocina mexicana. Usa HTML semántico con etiquetas h2, h3, p, ul, li. NO incluyas h1. Escribe en español neutro. Longitud: 800-1000 palabras.',
                        ],
                        [
                            'role'    => 'user',
                            'content' => "Escribe un artículo de blog completo sobre: {$topic['title_es']}. Incluye historia, preparación tradicional, variantes regionales y consejos prácticos. Formato HTML con h2, h3, p, ul/li.",
                        ],
                    ],
                ]);

                if (! $response->successful()) {
                    $this->error("  OpenAI error ({$response->status()}): " . $response->body());
                    Log::error('blog:generate-posts OpenAI error', [
                        'slug'   => $topic['slug'],
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    $failed++;
                    continue;
                }

                $content = $response->json('choices.0.message.content');

                if (empty($content)) {
                    $this->error('  Empty content returned from OpenAI.');
                    $failed++;
                    continue;
                }

                $plainText = strip_tags($content);
                $excerpt   = Str::limit($plainText, 200, '');
                $excerpt   = trim($excerpt);

                BlogPost::create([
                    'title'           => $topic['title_es'],
                    'title_en'        => $topic['title_en'],
                    'slug'            => $topic['slug'],
                    'content'         => $content,
                    'excerpt'         => $excerpt,
                    'category'        => $topic['category'],
                    'author'          => 'Equipo FAMER',
                    'seo_title'       => $topic['title_es'] . ' | Blog FAMER',
                    'seo_description' => Str::limit($plainText, 160),
                    'tags'            => [],
                    'is_published'    => true,
                    'featured'        => false,
                    'published_at'    => now()->subMinutes(rand(0, 60)),
                ]);

                $this->info("  Generado: {$topic['title_es']}");
                $generated++;

            } catch (\Exception $e) {
                $this->error("  Error generating '{$topic['title_es']}': " . $e->getMessage());
                Log::error('blog:generate-posts exception', [
                    'slug'  => $topic['slug'],
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done. Generated: {$generated} | Failed: {$failed}");

        if ($failed > 0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
