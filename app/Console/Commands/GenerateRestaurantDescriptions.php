<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateRestaurantDescriptions extends Command
{
    protected $signature = 'restaurants:generate-descriptions
                            {--limit=50 : Number of restaurants to process per run}
                            {--dry-run : Print descriptions without saving}
                            {--force : Process all restaurants, including those with existing descriptions}';

    protected $description = 'Generate SEO descriptions for restaurants without one using OpenAI';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            $this->error('OPENAI_API_KEY is not set in .env');
            return Command::FAILURE;
        }

        // Build query — description is a Spatie translatable JSON field.
        // When never set it is NULL, or stored as '{}' / '[]' (empty JSON).
        $query = Restaurant::approved()
            ->with(['state', 'category'])
            ->orderBy('id');

        if (! $force) {
            $query->where(function ($q) {
                $q->whereNull('description')
                  ->orWhere('description', '')
                  ->orWhere('description', '{}')
                  ->orWhere('description', '[]');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No restaurants need descriptions' . ($force ? '' : ' — all already have one') . '.');
            return Command::SUCCESS;
        }

        $label = $dryRun ? ' (dry-run)' : '';
        $this->info("Processing {$limit} restaurants without descriptions{$label}...");
        $this->newLine();

        $restaurants = $query->limit($limit)->get();
        $generated   = 0;
        $failed      = 0;
        $index       = 0;
        $count       = $restaurants->count();

        foreach ($restaurants as $restaurant) {
            $index++;
            $cityState = trim(($restaurant->city ?? '') . ', ' . ($restaurant->state?->code ?? $restaurant->state?->name ?? ''));
            $rowLabel  = "[{$index}/{$count}] {$restaurant->name} ({$cityState})";

            $isUS   = $restaurant->state?->country === 'US';
            $lang   = $isUS ? 'en' : 'es';
            $prompt = $this->buildPrompt($restaurant, $lang);

            if ($dryRun) {
                $this->line($rowLabel . ' — DRY RUN');
                $this->line('Prompt:');
                $this->line($prompt);
                $this->newLine();
                continue;
            }

            try {
                $description = $this->callOpenAI($apiKey, $prompt);

                if (empty($description)) {
                    $this->warn("{$rowLabel} — empty response, skipped");
                    $failed++;
                    continue;
                }

                // setTranslation respects the Spatie HasTranslations trait
                $restaurant->setTranslation('description', $lang, $description);
                $restaurant->save();

                $wordCount = str_word_count($description);
                $this->info("{$rowLabel} — generated ({$wordCount} words)");
                $generated++;

            } catch (\Exception $e) {
                $this->warn("{$rowLabel} — failed: " . $e->getMessage());
                Log::error("GenerateRestaurantDescriptions: restaurant {$restaurant->id} failed — " . $e->getMessage());
                $failed++;
            }

            // 100ms between requests to respect rate limits
            usleep(100000);
        }

        $this->newLine();
        $this->info("Done: {$generated} generated, {$failed} failed.");

        return Command::SUCCESS;
    }

    private function buildPrompt(Restaurant $restaurant, string $lang): string
    {
        $name      = $restaurant->name;
        $city      = $restaurant->city ?? 'Unknown City';
        $stateName = $restaurant->state?->name ?? '';
        $stateCode = $restaurant->state?->code ?? $stateName;
        $price     = $restaurant->price_range ?? 'varies';
        $rating    = $restaurant->average_rating ?? $restaurant->google_rating ?? null;
        $ratingStr = $rating ? "{$rating}/5" : 'not yet rated';
        $category  = $restaurant->category?->name ?? 'Mexican Restaurant';

        if ($lang === 'en') {
            return <<<PROMPT
            Write a 2-sentence SEO description for a Mexican restaurant with these details:
            - Name: {$name}
            - City: {$city}, {$stateName} ({$stateCode})
            - Cuisine: Mexican
            - Price range: {$price}
            - Rating: {$ratingStr}
            - Categories: {$category}

            Requirements:
            - 40-60 words total
            - Mention the city name
            - Highlight authentic Mexican cuisine
            - Mention what makes it worth visiting
            - Write in English
            - No marketing fluff, factual and helpful tone

            Output ONLY the description text, no quotes, no titles.
            PROMPT;
        }

        return <<<PROMPT
        Escribe una descripción SEO de 2 oraciones para un restaurante mexicano con estos datos:
        - Nombre: {$name}
        - Ciudad: {$city}, {$stateName} ({$stateCode})
        - Cocina: Mexicana
        - Rango de precio: {$price}
        - Calificación: {$ratingStr}
        - Categoría: {$category}

        Requisitos:
        - Total de 40-60 palabras
        - Menciona el nombre de la ciudad
        - Destaca la cocina mexicana auténtica
        - Menciona qué lo hace especial o digno de visita
        - Escribe en español
        - Sin frases de marketing vacías, tono informativo y útil

        Devuelve SOLO el texto de la descripción, sin comillas, sin títulos.
        PROMPT;
    }

    private function callOpenAI(string $apiKey, string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model'       => 'gpt-4o-mini',
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => 'You write concise, factual restaurant descriptions for SEO.',
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens'  => 120,
            'temperature' => 0.7,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenAI API error: ' . $response->body());
        }

        return trim($response->json('choices.0.message.content') ?? '');
    }
}
