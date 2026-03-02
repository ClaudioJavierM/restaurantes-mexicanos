<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\GooglePlacesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillGoogleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:backfill-google
                            {--limit= : Limit number of restaurants to process}
                            {--delay=2 : Delay in seconds between API calls (default 2)}
                            {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill missing Google Places data (website) for restaurants that have google_place_id';

    protected $googlePlacesService;

    public function __construct(GooglePlacesService $googlePlacesService)
    {
        parent::__construct();
        $this->googlePlacesService = $googlePlacesService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting Google Places data backfill...');
        $this->newLine();

        // Find restaurants with google_place_id but missing website
        $query = Restaurant::whereNotNull('google_place_id')
            ->whereNull('website');

        $total = $query->count();

        if ($total === 0) {
            $this->info('✅ No restaurants need backfill - all have websites!');
            return Command::SUCCESS;
        }

        $this->info("Found {$total} restaurants that need Google data backfill");
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
            'updated' => 0,
            'failed' => 0,
            'no_website' => 0,
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
                // Fetch full Google Place details
                $placeDetails = $this->googlePlacesService->getPlaceDetails($restaurant->google_place_id);

                if (!$placeDetails) {
                    $stats['failed']++;
                    $progressBar->advance();
                    continue;
                }

                // Extract data to update
                $updateData = [];

                // Website (primary goal of this backfill)
                if (isset($placeDetails['website']) && !empty($placeDetails['website'])) {
                    $updateData['website'] = $placeDetails['website'];
                }

                // Also backfill other missing data while we're here
                if (!$restaurant->phone && isset($placeDetails['formatted_phone_number'])) {
                    $updateData['phone'] = $placeDetails['formatted_phone_number'];
                }

                if (!$restaurant->google_maps_url && isset($placeDetails['url'])) {
                    $updateData['google_maps_url'] = $placeDetails['url'];
                }

                if (!$restaurant->google_rating && isset($placeDetails['rating'])) {
                    $updateData['google_rating'] = $placeDetails['rating'];
                }

                if (!$restaurant->google_reviews_count && isset($placeDetails['user_ratings_total'])) {
                    $updateData['google_reviews_count'] = $placeDetails['user_ratings_total'];
                }

                // Update last verification timestamp
                $updateData['last_google_verification'] = now();

                if (empty($updateData) || (count($updateData) === 1 && isset($updateData['last_google_verification']))) {
                    // No new data found (only timestamp would be updated)
                    $stats['no_website']++;
                    $progressBar->advance();
                    continue;
                }

                // Update restaurant if not dry run
                if (!$isDryRun) {
                    // Use direct DB update to avoid slug regeneration
                    DB::table('restaurants')
                        ->where('id', $restaurant->id)
                        ->update(array_merge($updateData, ['updated_at' => now()]));

                    $stats['updated']++;
                } else {
                    // In dry run, just count as updated
                    $stats['updated']++;
                }

                $progressBar->advance();

                // Respect API rate limits
                if ($delay > 0) {
                    sleep($delay);
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                $this->newLine();
                $this->error("Error processing restaurant ID {$restaurant->id}: " . $e->getMessage());
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
        $this->line("  ✅ Successfully updated: {$stats['updated']}");
        $this->line("  ⚠️  No website found: {$stats['no_website']}");
        $this->line("  ❌ Failed: {$stats['failed']}");
        $this->newLine();

        if ($isDryRun) {
            $this->warn('🔍 This was a DRY RUN - no changes were saved to the database');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Backfill completed successfully!');
        }

        return Command::SUCCESS;
    }
}
