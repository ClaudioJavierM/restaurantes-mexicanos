<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncYelpRatings extends Command
{
    protected $signature = 'yelp:sync-ratings
                            {--limit=100 : Maximum number of restaurants to process}
                            {--days=30 : Only update if last sync was more than X days ago}
                            {--state= : Filter by state code}
                            {--force : Force update even if recently synced}
                            {--delay=2 : Delay in seconds between API calls}';

    protected $description = 'Sync/update Yelp ratings for restaurants that have a Yelp ID';

    protected YelpFusionService $yelpService;
    protected int $updated = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function handle(YelpFusionService $yelpService): int
    {
        $this->yelpService = $yelpService;
        
        $limit = (int) $this->option('limit');
        $days = (int) $this->option('days');
        $force = $this->option('force');
        $delay = (int) $this->option('delay');

        $this->info('🔄 Syncing Yelp Ratings...');
        $this->newLine();

        // Get restaurants with Yelp ID that need updating
        $query = Restaurant::whereNotNull('yelp_id')
            ->where('status', 'approved');

        if (!$force) {
            $query->where(function ($q) use ($days) {
                $q->whereNull('yelp_last_sync')
                    ->orWhere('yelp_last_sync', '<', now()->subDays($days));
            });
        }

        if ($stateCode = $this->option('state')) {
            $query->whereHas('state', function ($q) use ($stateCode) {
                $q->where('code', strtoupper($stateCode));
            });
        }

        $total = $query->count();
        $this->info("Found {$total} restaurants needing Yelp sync");

        $restaurants = $query->limit($limit)->get();
        $this->info("Processing {$restaurants->count()} restaurants...");
        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            try {
                $this->syncRestaurant($restaurant);
            } catch (\Exception $e) {
                $this->errors++;
                Log::error("Yelp sync error for {$restaurant->name}: " . $e->getMessage());
            }

            $bar->advance();
            
            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Updated', $this->updated],
                ['⏭️ Skipped', $this->skipped],
                ['❌ Errors', $this->errors],
            ]
        );

        return Command::SUCCESS;
    }

    protected function syncRestaurant(Restaurant $restaurant): void
    {
        if (empty($restaurant->yelp_id)) {
            $this->skipped++;
            return;
        }

        try {
            // Fetch business details from Yelp
            $business = $this->yelpService->getBusinessDetails($restaurant->yelp_id);

            if (!$business) {
                $this->skipped++;
                return;
            }

            // Update restaurant with fresh Yelp data
            $restaurant->update([
                'yelp_rating' => $business['rating'] ?? $restaurant->yelp_rating,
                'yelp_reviews_count' => $business['review_count'] ?? $restaurant->yelp_reviews_count,
                'yelp_url' => $business['url'] ?? $restaurant->yelp_url,
                'yelp_last_sync' => now(),
            ]);

            $this->updated++;

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
