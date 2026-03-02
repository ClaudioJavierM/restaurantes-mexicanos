<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanAndReverifyYelp extends Command
{
    protected $signature = 'yelp:clean-reverify
                            {--duplicates-only : Only process restaurants with duplicate Yelp IDs}
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--limit=100 : Maximum number of restaurants to process}
                            {--test : Test mode - process only 5 restaurants}
                            {--dry-run : Show what would be changed without making changes}';

    protected $description = 'Clean duplicate Yelp IDs and re-verify restaurant data with improved matching';

    protected YelpFusionService $yelpService;
    protected int $cleaned = 0;
    protected int $reverified = 0;
    protected int $notFound = 0;
    protected int $errors = 0;

    public function handle(YelpFusionService $yelpService): int
    {
        $this->yelpService = $yelpService;

        $this->info('=== Clean and Re-verify Yelp Data ===');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Get restaurants to process
        if ($this->option('duplicates-only')) {
            $restaurants = $this->getRestaurantsWithDuplicateYelpIds();
        } else {
            $restaurants = $this->getAllRestaurantsToProcess();
        }

        $this->info("Restaurants to process: " . $restaurants->count());
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need processing.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting
            usleep(500000); // 0.5 seconds
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->showSummary();

        return Command::SUCCESS;
    }

    protected function getRestaurantsWithDuplicateYelpIds()
    {
        // Find duplicate Yelp IDs
        $duplicateIds = DB::table('restaurants')
            ->select('yelp_id')
            ->whereNotNull('yelp_id')
            ->groupBy('yelp_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('yelp_id');

        $this->info("Found " . $duplicateIds->count() . " duplicate Yelp IDs");

        $query = Restaurant::whereIn('yelp_id', $duplicateIds)
            ->where('status', 'approved');

        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
        }

        $limit = $this->option('test') ? 5 : (int) $this->option('limit');

        return $query->with('state')->limit($limit)->get();
    }

    protected function getAllRestaurantsToProcess()
    {
        $query = Restaurant::where('status', 'approved')
            ->whereNotNull('yelp_id');

        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
        }

        $limit = $this->option('test') ? 5 : (int) $this->option('limit');

        return $query->with('state')->limit($limit)->get();
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            $oldYelpId = $restaurant->yelp_id;
            $stateCode = $restaurant->state?->code ?? '';

            // Search for this restaurant with improved matching
            $result = $this->yelpService->searchBusiness(
                $restaurant->name,
                $restaurant->city,
                $stateCode,
                $restaurant->address // Pass address for better matching
            );

            if ($this->option('dry-run')) {
                if ($result && ($result['verified'] ?? false)) {
                    $newYelpId = $result['yelp_id'];
                    if ($newYelpId !== $oldYelpId) {
                        $this->newLine();
                        $this->line("Would update: {$restaurant->name} ({$restaurant->city})");
                        $this->line("  Old Yelp ID: {$oldYelpId}");
                        $this->line("  New Yelp ID: {$newYelpId}");
                        $this->line("  Match: {$result['name']} (score: {$result['combined_score']})");
                    }
                } else {
                    $this->newLine();
                    $this->line("Would clear Yelp data: {$restaurant->name} ({$restaurant->city}) - no match found");
                }
                return;
            }

            if ($result && ($result['verified'] ?? false)) {
                $newYelpId = $result['yelp_id'];

                // Check if the new Yelp ID is different
                if ($newYelpId !== $oldYelpId) {
                    $this->cleaned++;
                }

                // Update with new data
                $updateData = [
                    'yelp_id' => $newYelpId,
                    'yelp_url' => $result['url'] ?? null,
                    'yelp_rating' => $result['rating'] ?? null,
                    'yelp_reviews_count' => (int) ($result['review_count'] ?? 0),
                    'updated_at' => now(),
                ];

                // Store coordinates if available
                if (!empty($result['coordinates'])) {
                    $updateData['latitude'] = $result['coordinates']['latitude'] ?? $restaurant->latitude;
                    $updateData['longitude'] = $result['coordinates']['longitude'] ?? $restaurant->longitude;
                }

                // Update address from Yelp if ours is incomplete
                if (strlen($restaurant->address ?? '') < 10 && !empty($result['location']['address1'])) {
                    $updateData['address'] = $result['location']['address1'];
                }

                // Update phone if not set
                if (empty($restaurant->phone) && !empty($result['phone'])) {
                    $updateData['phone'] = $result['phone'];
                }

                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update($updateData);

                $this->reverified++;

                Log::info("Yelp re-verified: {$restaurant->name} ({$restaurant->city}) -> {$result['name']}");

            } else {
                // No match found - clear Yelp data
                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update([
                        'yelp_id' => null,
                        'yelp_url' => null,
                        'yelp_rating' => null,
                        'yelp_reviews_count' => 0, // Use 0 instead of null
                        'updated_at' => now(),
                    ]);

                $this->notFound++;

                Log::info("Yelp cleared (no match): {$restaurant->name} ({$restaurant->city})");
            }

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error processing {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function showSummary(): void
    {
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Re-verified (same/better match)', $this->reverified],
                ['Cleaned (different Yelp ID)', $this->cleaned],
                ['Not found (Yelp data cleared)', $this->notFound],
                ['Errors', $this->errors],
            ]
        );

        // Show sample of changes
        if ($this->reverified > 0 && !$this->option('dry-run')) {
            $this->newLine();
            $this->info('Sample of re-verified restaurants:');

            $samples = Restaurant::where('import_source', $this->option('source') ?? 'mf_imports')
                ->whereNotNull('yelp_id')
                ->whereNotNull('latitude')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get(['name', 'city', 'yelp_rating', 'latitude', 'longitude']);

            $this->table(
                ['Name', 'City', 'Yelp Rating', 'Has Coords'],
                $samples->map(fn($r) => [
                    \Illuminate\Support\Str::limit($r->name, 25),
                    $r->city,
                    $r->yelp_rating ?? 'N/A',
                    ($r->latitude && $r->longitude) ? 'Yes' : 'No',
                ])->toArray()
            );
        }
    }
}
