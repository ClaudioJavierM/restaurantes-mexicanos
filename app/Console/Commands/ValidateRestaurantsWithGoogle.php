<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\GooglePlacesService;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateRestaurantsWithGoogle extends Command
{
    protected $signature = 'restaurants:validate-google
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--limit=100 : Maximum number of restaurants to process}
                            {--test : Test mode - process only 5 restaurants}
                            {--with-yelp : Also enrich with Yelp if Google validates}
                            {--clear-invalid : Clear Yelp data for restaurants that fail validation}';

    protected $description = 'Validate restaurants using Google Places - verify address and name match';

    protected GooglePlacesService $googleService;
    protected YelpFusionService $yelpService;

    protected int $validated = 0;
    protected int $notFound = 0;
    protected int $nameMismatch = 0;
    protected int $closed = 0;
    protected int $errors = 0;

    public function handle(GooglePlacesService $googleService, YelpFusionService $yelpService): int
    {
        $this->googleService = $googleService;
        $this->yelpService = $yelpService;

        $this->info('=== Validate Restaurants with Google Places ===');
        $this->newLine();

        // Build query - restaurants without google_place_id
        $query = Restaurant::where('status', 'approved')
            ->whereNull('google_place_id');

        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
            $this->info("Filtering by source: {$source}");
        }

        $total = $query->count();
        $this->info("Restaurants to validate: {$total}");

        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = $query->with('state')->limit($limit)->get();

        $this->info("Processing: {$limit}");
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need validation.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting - Google has strict limits
            usleep(200000); // 0.2 seconds
        }

        $bar->finish();
        $this->newLine(2);

        $this->showSummary($total, $limit);

        return Command::SUCCESS;
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            $stateCode = $restaurant->state?->code ?? '';
            $stateName = $restaurant->state?->name ?? $stateCode;

            // Step 1: Search on Google Places by address + name
            $place = $this->googleService->findPlace(
                $restaurant->name,
                $restaurant->address ?? '',
                $restaurant->city,
                $stateName
            );

            if (!$place || !isset($place['place_id'])) {
                $this->handleNotFound($restaurant);
                return;
            }

            // Step 2: Verify the name matches
            $googleName = $place['name'] ?? '';
            $nameSimilarity = $this->calculateSimilarity($restaurant->name, $googleName);

            if ($nameSimilarity < 60) {
                $this->handleNameMismatch($restaurant, $googleName, $nameSimilarity);
                return;
            }

            // Step 3: Check business status
            $businessStatus = $place['business_status'] ?? 'OPERATIONAL';
            if ($businessStatus === 'CLOSED_PERMANENTLY') {
                $this->handleClosed($restaurant);
                return;
            }

            // Step 4: Get full details from Google
            $details = $this->googleService->getPlaceDetails($place['place_id']);

            if (!$details) {
                $this->notFound++;
                return;
            }

            // Step 5: Update restaurant with validated data
            $updateData = [
                'google_place_id' => $place['place_id'],
                'google_maps_url' => $details['url'] ?? null,
                'google_rating' => $details['rating'] ?? null,
                'google_reviews_count' => $details['user_ratings_total'] ?? 0,
                'latitude' => $place['geometry']['location']['lat'] ?? null,
                'longitude' => $place['geometry']['location']['lng'] ?? null,
                'updated_at' => now(),
            ];

            // Update address from Google if ours is incomplete
            if (strlen($restaurant->address ?? '') < 5 && !empty($details['formatted_address'])) {
                // Extract just the street address
                $addressParts = explode(',', $details['formatted_address']);
                $updateData['address'] = trim($addressParts[0] ?? '');
            }

            // Update phone if not set
            if (empty($restaurant->phone) && !empty($details['formatted_phone_number'])) {
                $updateData['phone'] = $details['formatted_phone_number'];
            }

            // Update website if not set
            if (empty($restaurant->website) && !empty($details['website'])) {
                $updateData['website'] = $details['website'];
            }

            // Update average rating from Google
            if (!empty($details['rating'])) {
                $updateData['average_rating'] = $details['rating'];
                $updateData['total_reviews'] = $details['user_ratings_total'] ?? 0;
            }

            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update($updateData);

            $this->validated++;

            Log::info("Google validated: {$restaurant->name} ({$restaurant->city}) -> {$googleName} (similarity: {$nameSimilarity}%)");

            // Step 6: Optionally enrich with Yelp using the validated coordinates
            if ($this->option('with-yelp') && empty($restaurant->yelp_id)) {
                $this->enrichWithYelp($restaurant, $updateData['latitude'], $updateData['longitude']);
            }

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Validation error for {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function enrichWithYelp(Restaurant $restaurant, $lat, $lng): void
    {
        try {
            $stateCode = $restaurant->state?->code ?? '';

            $result = $this->yelpService->searchBusiness(
                $restaurant->name,
                $restaurant->city,
                $stateCode,
                $restaurant->address
            );

            if ($result && ($result['verified'] ?? false)) {
                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update([
                        'yelp_id' => $result['yelp_id'],
                        'yelp_url' => $result['url'] ?? null,
                        'yelp_rating' => $result['rating'] ?? null,
                        'yelp_reviews_count' => $result['review_count'] ?? 0,
                    ]);

                Log::info("Yelp enriched after Google validation: {$restaurant->name}");
            }
        } catch (\Exception $e) {
            Log::warning("Yelp enrichment failed for {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function handleNotFound(Restaurant $restaurant): void
    {
        $this->notFound++;

        if ($this->option('clear-invalid')) {
            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update([
                    'yelp_id' => null,
                    'yelp_url' => null,
                    'yelp_rating' => null,
                    'status' => 'pending', // Mark as pending for manual review
                    'updated_at' => now(),
                ]);
        }

        Log::info("Google not found: {$restaurant->name} ({$restaurant->city}, {$restaurant->address})");
    }

    protected function handleNameMismatch(Restaurant $restaurant, string $googleName, float $similarity): void
    {
        $this->nameMismatch++;

        if ($this->option('clear-invalid')) {
            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update([
                    'yelp_id' => null,
                    'yelp_url' => null,
                    'yelp_rating' => null,
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
        }

        Log::info("Google name mismatch: {$restaurant->name} ({$restaurant->city}) -> Found: {$googleName} (similarity: {$similarity}%)");
    }

    protected function handleClosed(Restaurant $restaurant): void
    {
        $this->closed++;

        DB::table('restaurants')
            ->where('id', $restaurant->id)
            ->update([
                'status' => 'closed',
                'yelp_id' => null,
                'yelp_url' => null,
                'updated_at' => now(),
            ]);

        Log::info("Google: CLOSED PERMANENTLY - {$restaurant->name} ({$restaurant->city})");
    }

    protected function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        // Remove common suffixes
        $suffixes = ['restaurant', 'restaurante', 'mexican', 'grill', 'bar', 'cafe', 'cantina', 'taqueria', 'inc', 'llc'];
        foreach ($suffixes as $suffix) {
            $str1 = trim(str_ireplace($suffix, '', $str1));
            $str2 = trim(str_ireplace($suffix, '', $str2));
        }

        similar_text($str1, $str2, $percent);
        return round($percent, 2);
    }

    protected function showSummary(int $total, int $limit): void
    {
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Validated (Google confirmed)', $this->validated],
                ['Not found on Google', $this->notFound],
                ['Name mismatch (different business)', $this->nameMismatch],
                ['Permanently closed', $this->closed],
                ['Errors', $this->errors],
                ['Remaining to process', max(0, $total - $limit)],
            ]
        );

        if ($this->validated > 0) {
            $this->newLine();
            $this->info('Sample of validated restaurants:');

            $samples = Restaurant::where('import_source', $this->option('source') ?? 'mf_imports')
                ->whereNotNull('google_place_id')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get(['name', 'city', 'address', 'google_rating']);

            $this->table(
                ['Name', 'City', 'Address', 'Google Rating'],
                $samples->map(fn($r) => [
                    \Illuminate\Support\Str::limit($r->name, 20),
                    $r->city,
                    \Illuminate\Support\Str::limit($r->address ?? 'N/A', 25),
                    $r->google_rating ?? 'N/A',
                ])->toArray()
            );
        }

        if ($this->nameMismatch > 0 || $this->notFound > 0) {
            $this->newLine();
            $this->warn('Note: Restaurants not found or with name mismatch may need manual review.');
            if ($this->option('clear-invalid')) {
                $this->warn('Their Yelp data was cleared and status set to "pending".');
            }
        }
    }
}
