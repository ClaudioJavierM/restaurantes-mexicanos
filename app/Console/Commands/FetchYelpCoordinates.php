<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchYelpCoordinates extends Command
{
    protected $signature = 'yelp:fetch-coordinates
                            {--limit=100 : Maximum number of restaurants to process}
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--test : Test mode - process only 5 restaurants}';

    protected $description = 'Fetch coordinates from Yelp for restaurants that have Yelp ID but no coordinates';

    protected YelpFusionService $yelpService;
    protected int $updated = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function handle(YelpFusionService $yelpService): int
    {
        $this->yelpService = $yelpService;

        $this->info('=== Fetching Yelp Coordinates ===');
        $this->newLine();

        // Build query - restaurants with Yelp ID but no coordinates
        $query = Restaurant::whereNotNull('yelp_id')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude');
            })
            ->where(function($q) {
                $q->whereNull('yelp_enriched_at')
                  ->orWhere('yelp_enriched_at', '<', now()->subDays(30));
            });

        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
            $this->info("Filtering by source: {$source}");
        }

        $total = $query->count();
        $this->info("Restaurants needing coordinates: {$total}");

        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = $query->limit($limit)->get();

        $this->info("Processing: " . $restaurants->count());
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need coordinates.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting
            usleep(300000); // 0.3 seconds
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Coordinates Updated', $this->updated],
                ['Skipped (no coords in Yelp)', $this->skipped],
                ['Errors', $this->errors],
                ['Remaining', max(0, $total - $limit)],
            ]
        );

        return Command::SUCCESS;
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            // Get business details from Yelp using the existing Yelp ID
            $details = $this->yelpService->getBusinessDetails($restaurant->yelp_id);

            if (!$details) {
                $this->skipped++;
                return;
            }

            // Extract coordinates
            $coords = $details['coordinates'] ?? null;

            if (!$coords || empty($coords['latitude']) || empty($coords['longitude'])) {
                $this->skipped++;
                return;
            }

            // Prepare update data
            $updateData = [
                'latitude' => $coords['latitude'],
                'longitude' => $coords['longitude'],
                'updated_at' => now(),
            ];

            // Also update address if ours is incomplete and Yelp has one
            $location = $details['location'] ?? [];
            if (strlen($restaurant->address ?? '') < 5 && !empty($location['address1'])) {
                $updateData['address'] = $location['address1'];
            }

            // Update phone if not set
            if (empty($restaurant->phone) && !empty($details['display_phone'])) {
                $updateData['phone'] = $details['display_phone'];
            }

            $updateData['yelp_enriched_at'] = now();
            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update($updateData);

            $this->updated++;

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error fetching coords for {$restaurant->name}: {$e->getMessage()}");
        }
    }
}
