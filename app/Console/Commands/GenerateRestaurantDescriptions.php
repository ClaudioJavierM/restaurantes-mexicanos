<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateRestaurantDescriptions extends Command
{
    protected $signature = 'restaurants:generate-descriptions
                            {--limit=100 : Maximum number of restaurants to process}
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--state= : Filter by state code (e.g., TX)}
                            {--force : Overwrite existing descriptions}
                            {--test : Test mode - process only 5 restaurants}';

    protected $description = 'Generate personalized descriptions for restaurants without descriptions';

    protected int $generated = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    // Description templates - varied for SEO and uniqueness
    protected array $templates = [
        'high_rating' => [
            "Experience the best of Mexican cuisine at {name} in {city}, {state}. With a stellar {rating}-star rating, this restaurant has earned its reputation for exceptional flavors and warm hospitality.",
            "Craving authentic Mexican food? {name} in {city}, {state} is a top-rated destination with {rating} stars, known for its delicious dishes and welcoming atmosphere.",
            "{name} stands out as one of {city}'s finest Mexican restaurants. Located in {state}, this {rating}-star gem offers an unforgettable dining experience.",
            "Discover why locals love {name} in {city}, {state}. Rated {rating} stars, this Mexican restaurant delivers authentic flavors that keep guests coming back.",
        ],
        'mid_rating' => [
            "Savor traditional Mexican flavors at {name} in {city}, {state}. A local favorite serving delicious, authentic cuisine in a welcoming setting.",
            "{name} brings the taste of Mexico to {city}, {state}. Enjoy classic dishes prepared with care and authentic recipes passed down through generations.",
            "Looking for great Mexican food in {city}? {name} offers a delightful dining experience with traditional recipes and friendly service in {state}.",
            "Visit {name} in {city}, {state} for a taste of authentic Mexican cooking. From sizzling fajitas to homemade salsas, every dish tells a story.",
        ],
        'no_rating' => [
            "Welcome to {name}, your destination for authentic Mexican cuisine in {city}, {state}. Experience traditional flavors in a warm and inviting atmosphere.",
            "{name} in {city}, {state} serves up classic Mexican dishes made with fresh ingredients and time-honored recipes.",
            "Discover {name} in {city}, {state} - a Mexican restaurant dedicated to bringing you the authentic taste of Mexico.",
            "At {name} in {city}, {state}, every meal is a celebration of Mexican culinary traditions. Join us for an authentic dining experience.",
        ],
        'with_yelp' => [
            "{name} in {city}, {state} has earned recognition on Yelp for its authentic Mexican cuisine. Come taste why diners keep returning for more.",
            "Featured on Yelp, {name} brings exceptional Mexican flavors to {city}, {state}. Discover a menu crafted with passion and authentic ingredients.",
            "Join the many satisfied diners who have discovered {name} in {city}, {state}. This Yelp-featured restaurant offers genuine Mexican hospitality and cuisine.",
        ],
    ];

    // Additional phrases to add variety
    protected array $specialties = [
        "Known for its flavorful tacos and enchiladas.",
        "Featuring homemade tortillas and fresh salsas.",
        "Specializing in traditional family recipes.",
        "Offering a menu full of Mexican favorites.",
        "Serving authentic dishes with a modern twist.",
        "A perfect spot for family gatherings and celebrations.",
        "Where every dish is prepared with love and tradition.",
        "Bringing the flavors of Mexico to your table.",
    ];

    public function handle(): int
    {
        $this->info('=== Generating Restaurant Descriptions ===');
        $this->newLine();

        // Build query
        $query = Restaurant::where('status', 'approved');

        // Only restaurants without descriptions (unless --force)
        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('description')
                  ->orWhere('description', '');
            });
        }

        // Filter by source if specified
        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
            $this->info("Filtering by source: {$source}");
        }

        // Filter by state if specified
        if ($stateCode = $this->option('state')) {
            $query->whereHas('state', function ($q) use ($stateCode) {
                $q->where('code', strtoupper($stateCode));
            });
            $this->info("Filtering by state: {$stateCode}");
        }

        $total = $query->count();
        $this->info("Restaurants needing descriptions: {$total}");

        // Apply limit
        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = $query->with('state')->limit($limit)->get();

        $this->info("Processing: {$limit}");
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need descriptions.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->generateDescription($restaurant);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Descriptions Generated', $this->generated],
                ['Skipped', $this->skipped],
                ['Errors', $this->errors],
                ['Remaining', max(0, $total - $limit)],
            ]
        );

        // Show samples
        if ($this->generated > 0) {
            $this->newLine();
            $this->info('Sample descriptions:');

            $samples = Restaurant::where('import_source', $this->option('source') ?? 'mf_imports')
                ->whereNotNull('description')
                ->where('description', '!=', '')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get(['name', 'city', 'description']);

            foreach ($samples as $sample) {
                $this->newLine();
                $this->line("<fg=cyan>{$sample->name}</> ({$sample->city}):");
                $this->line($sample->description);
            }
        }

        return Command::SUCCESS;
    }

    protected function generateDescription(Restaurant $restaurant): void
    {
        try {
            // Skip if already has description (and not forcing)
            if (!$this->option('force') && !empty($restaurant->description)) {
                $this->skipped++;
                return;
            }

            $stateCode = $restaurant->state?->code ?? '';
            $stateName = $restaurant->state?->name ?? $stateCode;

            // Determine which template set to use
            $rating = $restaurant->yelp_rating ?? $restaurant->google_rating ?? $restaurant->average_rating;
            $hasYelp = !empty($restaurant->yelp_id);

            if ($hasYelp && rand(0, 1) === 1) {
                $templateSet = 'with_yelp';
            } elseif ($rating && $rating >= 4.0) {
                $templateSet = 'high_rating';
            } elseif ($rating && $rating >= 3.0) {
                $templateSet = 'mid_rating';
            } else {
                $templateSet = 'no_rating';
            }

            // Pick a random template
            $templates = $this->templates[$templateSet];
            $template = $templates[array_rand($templates)];

            // Replace placeholders
            $description = str_replace(
                ['{name}', '{city}', '{state}', '{rating}'],
                [$restaurant->name, $restaurant->city, $stateName, number_format((float)$rating, 1)],
                $template
            );

            // Add a specialty phrase sometimes (50% chance)
            if (rand(0, 1) === 1) {
                $specialty = $this->specialties[array_rand($this->specialties)];
                $description .= ' ' . $specialty;
            }

            // Update using direct DB to avoid model events
            // Description is translatable (JSON format with 'en' key)
            $translatedDescription = json_encode(['en' => $description]);

            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update([
                    'description' => $translatedDescription,
                    'updated_at' => now(),
                ]);

            $this->generated++;

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error generating description for {$restaurant->name}: {$e->getMessage()}");
        }
    }
}
