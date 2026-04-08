<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackfillYelpData extends Command
{
    protected $signature = 'yelp:backfill
                            {--limit=100 : Number of restaurants to process}
                            {--force : Overwrite existing data}
                            {--dry-run : Show what would be updated without saving}
                            {--state= : Only process restaurants in specific state code}';

    protected $description = 'Backfill Yelp photos, attributes, transactions, and hours for existing restaurants';

    protected YelpFusionService $yelpService;

    public function __construct(YelpFusionService $yelpService)
    {
        parent::__construct();
        $this->yelpService = $yelpService;
    }

    public function handle()
    {
        $this->info('Backfilling Yelp data for restaurants...');
        $this->newLine();

        $query = Restaurant::approved()
            ->whereNotNull('yelp_id')
            ->where('yelp_id', '!=', '');

        // Only get restaurants missing data unless force is specified
        if (!$this->option('force')) {
            $query->where(function($q) {
                // Skip restaurants already enriched in the last 30 days
                $q->whereNull('yelp_enriched_at')
                  ->orWhere('yelp_enriched_at', '<', now()->subDays(30));
            })->where(function($q) {
                $q->whereNull('yelp_photos')
                  ->orWhereNull('yelp_attributes')
                  ->orWhereNull('yelp_transactions')
                  ->orWhereNull('yelp_hours');
            });
        }

        if ($stateCode = $this->option('state')) {
            $query->whereHas('state', fn($q) => $q->where('code', strtoupper($stateCode)));
        }

        $limit = (int) $this->option('limit');
        $restaurants = $query->limit($limit)->get();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need Yelp data backfill.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$restaurants->count()} restaurants...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($restaurants->count());
        $progressBar->start();

        $updated = 0;
        $errors = 0;
        $skipped = 0;

        foreach ($restaurants as $restaurant) {
            try {
                $details = $this->yelpService->getBusinessDetails($restaurant->yelp_id);

                if (!$details) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                $updateData = [];

                // Photos
                if (!empty($details['photos'])) {
                    $updateData['yelp_photos'] = $details['photos'];
                }

                // Transactions (delivery, pickup, etc.)
                if (!empty($details['transactions'])) {
                    $updateData['yelp_transactions'] = $details['transactions'];
                }

                // Attributes (menu_url, etc.)
                if (!empty($details['attributes'])) {
                    $updateData['yelp_attributes'] = $details['attributes'];
                    
                    // Extract menu_url if available
                    if (!empty($details['attributes']['menu_url'])) {
                        $updateData['menu_url'] = $details['attributes']['menu_url'];
                    }
                }

                // Hours
                if (!empty($details['hours'])) {
                    $updateData['yelp_hours'] = $details['hours'];
                    
                    // Also update main hours field if empty
                    if (empty($restaurant->hours) && !empty($details['hours'][0]['open'])) {
                        $updateData['hours'] = $this->formatYelpHours($details['hours'][0]['open']);
                    }
                }

                // Categories (update if more complete)
                if (!empty($details['categories']) && count($details['categories']) > 0) {
                    $updateData['yelp_categories'] = $details['categories'];
                }

                if ($this->option('dry-run')) {
                    $this->newLine(2);
                    $this->line("Restaurant: {$restaurant->name}");
                    $this->line("Would update: " . implode(', ', array_keys($updateData)));
                    if (!empty($updateData['yelp_photos'])) {
                        $this->line("  Photos: " . count($updateData['yelp_photos']));
                    }
                    if (!empty($updateData['yelp_transactions'])) {
                        $this->line("  Transactions: " . implode(', ', $updateData['yelp_transactions']));
                    }
                    if (!empty($updateData['menu_url'])) {
                        $this->line("  Menu URL: " . $updateData['menu_url']);
                    }
                } else {
                    if (!empty($updateData)) {
                        $updateData['yelp_enriched_at'] = now();
                        $restaurant->update($updateData);
                        $updated++;
                    }
                }

                // Rate limiting - be gentle with the API
                usleep(200000); // 200ms delay between requests

            } catch (\Exception $e) {
                $errors++;
                Log::error("Yelp backfill error for {$restaurant->name}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN - No changes were saved');
        } else {
            $this->info("Updated: {$updated} restaurants");
        }

        if ($skipped > 0) {
            $this->warn("Skipped: {$skipped} (no Yelp data returned)");
        }

        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }

        return Command::SUCCESS;
    }

    /**
     * Convert Yelp hours format to our standard format
     */
    protected function formatYelpHours(array $yelpOpen): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $hours = [];

        foreach ($days as $index => $day) {
            $dayHours = collect($yelpOpen)->filter(fn($h) => $h['day'] === $index);
            
            if ($dayHours->isEmpty()) {
                $hours[$day] = ['closed' => true];
            } else {
                $first = $dayHours->first();
                $hours[$day] = [
                    'open' => substr($first['start'], 0, 2) . ':' . substr($first['start'], 2),
                    'close' => substr($first['end'], 0, 2) . ':' . substr($first['end'], 2),
                    'closed' => false,
                ];
            }
        }

        return $hours;
    }
}
