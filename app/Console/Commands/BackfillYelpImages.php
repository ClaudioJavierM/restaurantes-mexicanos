<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillYelpImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:backfill-yelp-images
                            {--limit= : Limit number of restaurants to process}
                            {--delay=3 : Delay in seconds between API calls (default 3)}
                            {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing restaurant images from Yelp for restaurants that have yelp_id';

    protected $yelpService;

    public function __construct(YelpFusionService $yelpService)
    {
        parent::__construct();
        $this->yelpService = $yelpService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Starting Yelp images backfill...');
        $this->newLine();

        // Find restaurants with yelp_id but missing images
        $query = Restaurant::whereNotNull('yelp_id')
            ->where(function($q) {
                $q->whereNull('image')
                  ->orWhere('image', '');
            })
            ->doesntHave('media')
            ->where(function($q) {
                $q->whereNull('yelp_enriched_at')
                  ->orWhere('yelp_enriched_at', '<', now()->subDays(30));
            });

        $total = $query->count();

        if ($total === 0) {
            $this->info('✅ No restaurants need image backfill - all have images!');
            return Command::SUCCESS;
        }

        $this->info("Found {$total} restaurants that need Yelp image backfill");
        $this->newLine();

        // Apply limit if specified
        if ($limit = $this->option('limit')) {
            $restaurants = $query->limit($limit)->get();
            $this->warn("Processing only {$limit} of {$total} restaurants (--limit flag)");
        } else {
            $restaurants = $query->get();
        }

        $this->newLine();

        $stats = [
            'processed' => 0,
            'downloaded' => 0,
            'failed' => 0,
            'no_image_url' => 0,
        ];

        $delay = (int) $this->option('delay');
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        $progressBar = $this->output->createProgressBar($restaurants->count());
        $progressBar->start();

        foreach ($restaurants as $restaurant) {
            $stats['processed']++;

            try {
                // Fetch full business details from Yelp to get image URL
                $businessDetails = $this->yelpService->getBusinessDetails($restaurant->yelp_id);

                if (!$businessDetails) {
                    $stats['failed']++;
                    $this->newLine();
                    $this->error("  Failed to fetch Yelp data for: {$restaurant->name} (ID: {$restaurant->id})");
                    $progressBar->advance();
                    continue;
                }

                // Get image URL
                $imageUrl = $businessDetails['image_url'] ?? null;

                if (!$imageUrl) {
                    $stats['no_image_url']++;
                    $progressBar->advance();
                    continue;
                }

                // Download and attach image if not dry run
                if (!$isDryRun) {
                    try {
                        $this->downloadAndAttachImage($restaurant, $imageUrl);
                        $restaurant->update(['yelp_enriched_at' => now()]);
                        $stats['downloaded']++;
                    } catch (\Exception $e) {
                        $stats['failed']++;
                        $this->newLine();
                        $this->error("  Failed to download image for: {$restaurant->name} (ID: {$restaurant->id})");
                        $this->error("  Error: " . $e->getMessage());
                        Log::error("Backfill image failed for restaurant ID {$restaurant->id}", [
                            'error' => $e->getMessage(),
                            'image_url' => $imageUrl,
                        ]);
                    }
                } else {
                    // In dry run, just count as successful
                    $stats['downloaded']++;
                }

                $progressBar->advance();

                // Respect API rate limits
                if ($delay > 0) {
                    sleep($delay);
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                $this->newLine();
                $this->error("  Error processing restaurant ID {$restaurant->id}: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 BACKFILL RESULTS:');
        $this->newLine();
        $this->line("  Total processed: {$stats['processed']}");
        $this->line("  ✅ Images downloaded: {$stats['downloaded']}");
        $this->line("  ⚠️  No image URL in Yelp: {$stats['no_image_url']}");
        $this->line("  ❌ Failed: {$stats['failed']}");
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 This was a DRY RUN - no changes were saved to the database');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Image backfill completed successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Download and attach image from URL to restaurant
     */
    protected function downloadAndAttachImage(Restaurant $restaurant, string $imageUrl): void
    {
        try {
            $restaurant->addMediaFromUrl($imageUrl)
                ->toMediaCollection('images');

            Log::info("Backfill: Successfully downloaded image for restaurant: {$restaurant->name}", [
                'restaurant_id' => $restaurant->id,
                'image_url' => $imageUrl,
            ]);
        } catch (\Exception $e) {
            // Log the error with full details
            Log::error("Backfill: Failed to download image from URL for restaurant: {$restaurant->name}", [
                'restaurant_id' => $restaurant->id,
                'image_url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);

            // If Spatie Media Library fails, save URL to image field as fallback
            $restaurant->update(['image' => $imageUrl]);
            Log::warning("Backfill: Saved image URL to image field as fallback for restaurant: {$restaurant->name}", [
                'restaurant_id' => $restaurant->id,
            ]);

            // Re-throw the exception so the command can track it as a failure
            throw $e;
        }
    }
}
