<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAiDescriptions extends Command
{
    protected $signature = 'famer:generate-descriptions
                            {--batch=50 : Restaurants per run}
                            {--state= : Filter by state name (e.g. Texas)}
                            {--force : Regenerate even if ai_description already exists}
                            {--lang=es : Language: es or en}
                            {--dry-run : Preview prompt without calling API}';

    protected $description = 'Generate SEO-optimized AI descriptions for restaurants using OpenAI GPT-4o-mini';

    public function handle(): int
    {
        $batch   = (int) $this->option('batch');
        $state   = $this->option('state');
        $force   = $this->option('force');
        $lang    = $this->option('lang');
        $dryRun  = $this->option('dry-run');
        $apiKey  = config('services.openai.api_key');

        if (! $apiKey) {
            $this->error('OPENAI_API_KEY not set in .env');
            return 1;
        }

        $query = Restaurant::approved()
            ->with(['state', 'category'])
            ->orderBy('google_reviews_count', 'desc'); // prioritize popular restaurants

        if (! $force) {
            if ($lang === 'en') {
                $query->whereNull('ai_description_en');
            } else {
                $query->whereNull('ai_description');
            }
        }
        if ($state) {
            $query->whereHas('state', fn($q) => $q->where('name', 'like', "%{$state}%"));
        }

        $total = $query->count();
        $this->info("Found {$total} restaurants to process (batch: {$batch}, lang: {$lang})");

        if ($total === 0) {
            $this->info('Nothing to do.');
            return 0;
        }

        $restaurants = $query->limit($batch)->get();
        $processed = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $prompt = $this->buildPrompt($restaurant, $lang);

            if ($dryRun) {
                $bar->finish();
                $this->newLine();
                $this->line("--- PROMPT PREVIEW for: {$restaurant->name} ---");
                $this->line($prompt);
                return 0;
            }

            try {
                $description = $this->callOpenAI($apiKey, $prompt);

                if ($description) {
                    if ($lang === 'en') {
                        $restaurant->update([
                            'ai_description_en'              => $description,
                            'ai_description_en_generated_at' => now(),
                        ]);
                    } else {
                        $restaurant->update([
                            'ai_description'              => $description,
                            'ai_description_generated_at' => now(),
                        ]);
                    }
                    $processed++;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                Log::error("AI description failed for restaurant {$restaurant->id}: " . $e->getMessage());
                $errors++;
                // Brief pause on error to avoid hammering the API
                sleep(1);
            }

            $bar->advance();

            // Rate limit: 50ms between requests (~20 req/s, well within OpenAI limits)
            usleep(50000);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Processed: {$processed} | ❌ Errors: {$errors} | Remaining: " . ($total - $processed));

        return 0;
    }

    private function buildPrompt(Restaurant $restaurant, string $lang): string
    {
        $name        = $restaurant->name;
        $city        = $restaurant->city ?? 'Unknown City';
        $stateName   = $restaurant->state?->name ?? '';
        $stateCode   = $restaurant->state?->code ?? '';
        $category    = $restaurant->category?->name ?? 'Mexican Restaurant';
        $price       = $restaurant->price_range ?? '$$';
        $gRating     = $restaurant->google_rating;
        $gReviews    = $restaurant->google_reviews_count ?? 0;
        $yRating     = $restaurant->yelp_rating;
        $yReviews    = $restaurant->yelp_reviews_count ?? 0;

        // Build specialties list from boolean fields
        $specialties = collect([
            'has_birria'           => 'birria',
            'has_carnitas'         => 'carnitas',
            'has_barbacoa'        => 'barbacoa',
            'has_tamales'          => 'tamales',
            'has_fresh_tortillas'  => 'tortillas hechas a mano',
            'has_handmade_tortillas' => 'tortillas artesanales',
            'has_pozole_menudo'    => 'pozole y menudo',
            'has_cafe_de_olla'     => 'café de olla',
            'has_aguas_frescas'    => 'aguas frescas',
            'has_mezcal_tequila'   => 'mezcal y tequila',
            'has_micheladas'       => 'micheladas',
            'has_pan_dulce'        => 'pan dulce',
            'has_churros'          => 'churros',
            'authentic_mexican'    => 'cocina mexicana auténtica',
        ])->filter(fn($v, $k) => $restaurant->$k ?? false)->values()->implode(', ');

        // Build ratings context
        $ratingContext = '';
        if ($gRating && $gReviews > 0) {
            $ratingContext .= "Google: {$gRating}/5 ({$gReviews} reseñas). ";
        }
        if ($yRating && $yReviews > 0) {
            $ratingContext .= "Yelp: {$yRating}/5 ({$yReviews} reseñas). ";
        }

        if ($lang === 'en') {
            return <<<PROMPT
            Write a unique, SEO-optimized description for a Mexican restaurant listing page.
            The description must be 150-180 words, written in natural English, and optimized
            for local search ("best Mexican restaurant in {$city}, {$stateName}").

            Restaurant data:
            - Name: {$name}
            - Location: {$city}, {$stateName} ({$stateCode})
            - Type: {$category}
            - Price range: {$price}
            - Ratings: {$ratingContext}
            - Specialties: {$specialties}

            Requirements:
            1. First sentence must mention the restaurant name and city naturally
            2. Include 2-3 specific dishes or specialties (invent plausible Mexican dishes if specialties are empty)
            3. Mention the atmosphere/experience (casual, family-friendly, authentic, etc.)
            4. Include a call-to-action in the last sentence
            5. DO NOT use the phrase "is a Mexican restaurant" — be more creative
            6. DO NOT use generic filler phrases like "popular choice" or "beloved by locals"
            7. Write in an engaging, editorial voice — like a food critic, not a directory listing

            Output ONLY the description text, no quotes, no titles.
            PROMPT;
        }

        return <<<PROMPT
        Escribe una descripción única y optimizada para SEO para la página de un restaurante mexicano.
        La descripción debe tener 150-180 palabras, en español natural, y optimizada para búsqueda local
        ("mejor restaurante mexicano en {$city}, {$stateName}").

        Datos del restaurante:
        - Nombre: {$name}
        - Ubicación: {$city}, {$stateName} ({$stateCode})
        - Tipo: {$category}
        - Rango de precio: {$price}
        - Calificaciones: {$ratingContext}
        - Especialidades: {$specialties}

        Requisitos:
        1. La primera oración debe mencionar el nombre del restaurante y la ciudad de forma natural
        2. Incluir 2-3 platillos o especialidades específicos (inventa platillos mexicanos plausibles si no hay especialidades)
        3. Mencionar el ambiente/experiencia (casual, familiar, auténtico, romántico, etc.)
        4. Incluir un llamado a la acción en la última oración
        5. NO usar la frase genérica "es un restaurante mexicano" — sé más creativo
        6. NO usar frases genéricas como "favorito local" o "muy popular"
        7. Escribe con voz editorial, como un crítico gastronómico, no como un directorio

        Devuelve SOLO el texto de la descripción, sin comillas, sin títulos.
        PROMPT;
    }

    private function callOpenAI(string $apiKey, string $prompt): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => 'gpt-4o-mini',
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => 'You are an expert food writer specializing in Mexican cuisine and local SEO for restaurant directories. Write compelling, unique, geo-targeted descriptions.',
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens'  => 300,
            'temperature' => 0.8, // some creativity per restaurant
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        return trim($response->json('choices.0.message.content') ?? '');
    }
}
