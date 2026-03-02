<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RematchYelpRestaurants extends Command
{
    protected $signature = 'yelp:rematch
                            {--restaurant-id= : Process a specific restaurant by ID}
                            {--slug= : Process a specific restaurant by slug}
                            {--source=yelp : Filter by import_source}
                            {--limit=100 : Maximum number of restaurants to process}
                            {--flexible : Use flexible search without category filter}
                            {--dry-run : Show what would be changed without making changes}';

    protected $description = 'Re-match restaurants that lost their Yelp ID with Yelp API';

    protected YelpFusionService $yelpService;
    protected int $matched = 0;
    protected int $notFound = 0;
    protected int $errors = 0;

    public function handle(YelpFusionService $yelpService): int
    {
        $this->yelpService = $yelpService;

        $this->info('=== Re-match Restaurants with Yelp ===');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get restaurants to process
        $restaurants = $this->getRestaurantsToProcess();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants found to process.');
            return Command::SUCCESS;
        }

        $this->info("Restaurants to process: " . $restaurants->count());
        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting - 0.5 seconds between requests
            usleep(500000);
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->showSummary();

        return Command::SUCCESS;
    }

    protected function getRestaurantsToProcess()
    {
        // If specific restaurant ID provided
        if ($restaurantId = $this->option('restaurant-id')) {
            return Restaurant::where('id', $restaurantId)
                ->with('state')
                ->get();
        }

        // If specific slug provided
        if ($slug = $this->option('slug')) {
            return Restaurant::where('slug', $slug)
                ->with('state')
                ->get();
        }

        // Get restaurants that have import_source but no yelp_id
        $source = $this->option('source');
        $limit = (int) $this->option('limit');

        return Restaurant::where('status', 'approved')
            ->where('import_source', $source)
            ->whereNull('yelp_id')
            ->with('state')
            ->limit($limit)
            ->get();
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            $stateCode = $restaurant->state?->code ?? '';

            $this->newLine();
            $this->line("Processing: {$restaurant->name} ({$restaurant->city}, {$stateCode})");

            // Search for this restaurant on Yelp
            // Use flexible search (no category filter) if --flexible option is set
            $strictCategory = !$this->option('flexible');
            $result = $this->yelpService->searchBusiness(
                $restaurant->name,
                $restaurant->city,
                $stateCode,
                $restaurant->address,
                $strictCategory
            );

            if ($this->option('dry-run')) {
                if ($result && ($result['verified'] ?? false)) {
                    $this->info("  ✓ Would match to: {$result['name']}");
                    $this->line("    Yelp ID: {$result['yelp_id']}");
                    $this->line("    Rating: {$result['rating']} ({$result['review_count']} reviews)");
                    $this->line("    Match score: {$result['combined_score']}");
                } else {
                    $this->warn("  ✗ No match found");
                }
                return;
            }

            if ($result && ($result['verified'] ?? false)) {
                // Update with Yelp data
                $updateData = [
                    'yelp_id' => $result['yelp_id'],
                    'yelp_url' => $result['url'] ?? null,
                    'yelp_rating' => $result['rating'] ?? null,
                    'yelp_reviews_count' => (int) ($result['review_count'] ?? 0),
                    'updated_at' => now(),
                ];

                // Update coordinates if available and ours are missing
                if (!empty($result['coordinates'])) {
                    if (!$restaurant->latitude) {
                        $updateData['latitude'] = $result['coordinates']['latitude'];
                    }
                    if (!$restaurant->longitude) {
                        $updateData['longitude'] = $result['coordinates']['longitude'];
                    }
                }

                // Update phone if not set
                if (empty($restaurant->phone) && !empty($result['phone'])) {
                    $updateData['phone'] = $result['phone'];
                }

                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update($updateData);

                $this->matched++;

                $this->info("  ✓ Matched to: {$result['name']}");
                $this->line("    Yelp ID: {$result['yelp_id']}");
                $this->line("    Rating: {$result['rating']} ({$result['review_count']} reviews)");

                Log::info("Yelp re-matched: {$restaurant->name} ({$restaurant->city}) -> {$result['name']}", [
                    'restaurant_id' => $restaurant->id,
                    'yelp_id' => $result['yelp_id'],
                ]);

            } else {
                $this->notFound++;
                $this->warn("  ✗ No match found on Yelp");

                Log::info("Yelp no match: {$restaurant->name} ({$restaurant->city})");
            }

        } catch (\Exception $e) {
            $this->errors++;
            $this->error("  ✗ Error: {$e->getMessage()}");
            Log::error("Error re-matching {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function showSummary(): void
    {
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['✓ Matched', $this->matched],
                ['✗ Not found', $this->notFound],
                ['⚠ Errors', $this->errors],
            ]
        );
    }
}
