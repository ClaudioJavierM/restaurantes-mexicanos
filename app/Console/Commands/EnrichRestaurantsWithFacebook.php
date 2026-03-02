<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\FacebookPlacesService;
use Illuminate\Console\Command;

class EnrichRestaurantsWithFacebook extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'facebook:enrich-restaurants
                            {--limit=100 : Maximum number of restaurants to process}
                            {--all : Process all restaurants, including already enriched}
                            {--restaurant= : Process a specific restaurant by ID}
                            {--test : Test mode - process only 5 restaurants}';

    /**
     * The console command description.
     */
    protected $description = 'Enrich restaurant data with Facebook Places API';

    /**
     * Execute the console command.
     */
    public function handle(FacebookPlacesService $service): int
    {
        $this->info('Starting Facebook Places enrichment...');
        $this->newLine();

        // Check if Facebook credentials are configured
        if (!config('services.facebook.client_id') || !config('services.facebook.client_secret')) {
            $this->error('Facebook API credentials not configured. Please set FACEBOOK_CLIENT_ID and FACEBOOK_CLIENT_SECRET in .env');
            return Command::FAILURE;
        }

        // Process specific restaurant
        if ($restaurantId = $this->option('restaurant')) {
            return $this->processSpecificRestaurant($service, $restaurantId);
        }

        // Test mode
        if ($this->option('test')) {
            $this->warn('Running in TEST mode - processing only 5 restaurants');
            $this->newLine();
            return $this->processBatch($service, 5);
        }

        // Batch processing
        $limit = (int) $this->option('limit');
        return $this->processBatch($service, $limit);
    }

    /**
     * Process a specific restaurant
     */
    protected function processSpecificRestaurant(FacebookPlacesService $service, int $id): int
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            $this->error("Restaurant with ID {$id} not found.");
            return Command::FAILURE;
        }

        $this->info("Processing: {$restaurant->name} ({$restaurant->city}, {$restaurant->state?->code})");

        $result = $service->enrichRestaurant($restaurant);

        if ($result) {
            $this->info("Successfully enriched with Facebook data:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Facebook Page ID', $restaurant->facebook_page_id],
                    ['Facebook URL', $restaurant->facebook_url],
                    ['Facebook Rating', $restaurant->facebook_rating ?? 'N/A'],
                    ['Review Count', $restaurant->facebook_review_count ?? 'N/A'],
                ]
            );
            return Command::SUCCESS;
        }

        $this->warn("Could not find matching Facebook page for this restaurant.");
        return Command::SUCCESS;
    }

    /**
     * Process batch of restaurants
     */
    protected function processBatch(FacebookPlacesService $service, int $limit): int
    {
        $onlyWithoutFacebook = !$this->option('all');

        // Count how many need processing
        $query = Restaurant::where('status', 'approved');
        if ($onlyWithoutFacebook) {
            $query->whereNull('facebook_page_id')
                  ->where(function ($q) {
                      $q->whereNull('facebook_enriched_at')
                        ->orWhere('facebook_enriched_at', '<', now()->subDays(30));
                  });
        }
        $totalToProcess = $query->count();

        $this->info("Restaurants to process: {$totalToProcess}");
        $this->info("Processing limit: {$limit}");
        $this->newLine();

        if ($totalToProcess === 0) {
            $this->info('No restaurants need Facebook enrichment.');
            return Command::SUCCESS;
        }

        // Get restaurants
        $restaurants = $query->limit($limit)->get();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $enriched = 0;
        $failed = 0;

        foreach ($restaurants as $restaurant) {
            try {
                // Rate limiting
                usleep(500000); // 0.5 seconds

                if ($service->enrichRestaurant($restaurant)) {
                    $enriched++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Error processing {$restaurant->name}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $restaurants->count()],
                ['Successfully Enriched', $enriched],
                ['Not Found / Failed', $failed],
                ['Remaining', max(0, $totalToProcess - $limit)],
            ]
        );

        // Show sample of enriched restaurants
        if ($enriched > 0) {
            $this->newLine();
            $this->info('Sample of enriched restaurants:');

            $sampleRestaurants = Restaurant::whereNotNull('facebook_page_id')
                ->orderByDesc('facebook_enriched_at')
                ->limit(5)
                ->get(['name', 'city', 'facebook_rating', 'facebook_url']);

            $this->table(
                ['Name', 'City', 'FB Rating', 'Facebook URL'],
                $sampleRestaurants->map(fn($r) => [
                    $r->name,
                    $r->city,
                    $r->facebook_rating ?? 'N/A',
                    substr($r->facebook_url ?? '', 0, 40) . '...',
                ])->toArray()
            );
        }

        return Command::SUCCESS;
    }
}
