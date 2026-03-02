<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectDuplicateRestaurants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restaurants:detect-duplicates
                            {--remove : Automatically remove duplicates (keeps the one with more data)}
                            {--merge : Merge data from duplicates into the best record before removing}
                            {--dry-run : Show what would be removed without actually removing}
                            {--similarity=85 : Minimum similarity percentage to consider duplicate (default 85%)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect and optionally remove duplicate restaurants based on name and location similarity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning for duplicate restaurants...');
        $this->newLine();

        $minSimilarity = (float) $this->option('similarity');
        $duplicateGroups = [];
        $processed = [];

        // Get all restaurants
        $restaurants = Restaurant::with('state')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->info("Analyzing {$restaurants->count()} restaurants...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($restaurants->count());

        foreach ($restaurants as $restaurant) {
            $progressBar->advance();

            if (in_array($restaurant->id, $processed)) {
                continue;
            }

            $duplicates = [];

            foreach ($restaurants as $compareRestaurant) {
                if ($restaurant->id === $compareRestaurant->id) {
                    continue;
                }

                if (in_array($compareRestaurant->id, $processed)) {
                    continue;
                }

                // Check if they're in the same city and state
                if ($restaurant->city !== $compareRestaurant->city ||
                    $restaurant->state_id !== $compareRestaurant->state_id) {
                    continue;
                }

                $isDuplicate = false;
                $similarity = 0;

                // Method 1: Exact address match (highest priority)
                if ($restaurant->address && $compareRestaurant->address) {
                    $addressSimilarity = $this->calculateSimilarity(
                        $restaurant->address,
                        $compareRestaurant->address
                    );

                    if ($addressSimilarity >= 95) {
                        $isDuplicate = true;
                        $similarity = 100; // Address match is considered 100% duplicate
                    }
                }

                // Method 2: GPS coordinates match (within 50 meters)
                if (!$isDuplicate && $restaurant->latitude && $restaurant->longitude &&
                    $compareRestaurant->latitude && $compareRestaurant->longitude) {

                    $distance = $this->calculateDistance(
                        $restaurant->latitude,
                        $restaurant->longitude,
                        $compareRestaurant->latitude,
                        $compareRestaurant->longitude
                    );

                    // If within 50 meters and similar name, it's a duplicate
                    if ($distance < 0.05) {
                        $nameSimilarity = $this->calculateSimilarity(
                            $restaurant->name,
                            $compareRestaurant->name
                        );

                        if ($nameSimilarity >= 70) {
                            $isDuplicate = true;
                            $similarity = $nameSimilarity;
                        }
                    }
                }

                // Method 3: Name similarity (original method)
                if (!$isDuplicate) {
                    $similarity = $this->calculateSimilarity(
                        $restaurant->name,
                        $compareRestaurant->name
                    );

                    if ($similarity >= $minSimilarity) {
                        $isDuplicate = true;
                    }
                }

                if ($isDuplicate) {
                    $duplicates[] = [
                        'restaurant' => $compareRestaurant,
                        'similarity' => $similarity,
                    ];
                    $processed[] = $compareRestaurant->id;
                }
            }

            if (!empty($duplicates)) {
                // Add the original restaurant to the group
                array_unshift($duplicates, [
                    'restaurant' => $restaurant,
                    'similarity' => 100,
                ]);
                $processed[] = $restaurant->id;
                $duplicateGroups[] = $duplicates;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        if (empty($duplicateGroups)) {
            $this->info('✅ No duplicate restaurants found!');
            return Command::SUCCESS;
        }

        $this->warn("Found " . count($duplicateGroups) . " groups of duplicate restaurants:");
        $this->newLine();

        $totalDuplicates = 0;
        $toRemove = [];

        foreach ($duplicateGroups as $index => $group) {
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Group #" . ($index + 1) . " - " . count($group) . " duplicates:");
            $this->newLine();

            $bestRestaurant = $this->selectBestRestaurant($group);

            foreach ($group as $item) {
                $restaurant = $item['restaurant'];
                $isBest = $restaurant->id === $bestRestaurant->id;
                $symbol = $isBest ? '✅ KEEP' : '❌ REMOVE';

                $this->line("  {$symbol} [{$restaurant->id}] {$restaurant->name}");
                $this->line("      City: {$restaurant->city}, {$restaurant->state->name}");
                $this->line("      Similarity: {$item['similarity']}%");
                $this->line("      Source: " . ($restaurant->import_source ?? 'manual'));
                $this->line("      Rating: " . ($restaurant->average_rating ?? 'N/A') . " ({$restaurant->total_reviews} reviews)");
                $this->line("      Has GPS: " . ($restaurant->latitude ? 'Yes' : 'No'));
                $this->line("      Has Phone: " . ($restaurant->phone ? 'Yes' : 'No'));
                $this->line("      Created: " . $restaurant->created_at->format('Y-m-d H:i:s'));
                $this->newLine();

                if (!$isBest) {
                    $toRemove[] = $restaurant;
                    $totalDuplicates++;
                }
            }
        }

        $this->newLine();
        $this->warn("Total duplicates to remove: {$totalDuplicates}");
        $this->newLine();

        // Handle removal
        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN - No changes were made to the database');
            return Command::SUCCESS;
        }

        if ($this->option('remove')) {
            $shouldMerge = $this->option('merge');

            if ($this->confirm('Are you sure you want to ' . ($shouldMerge ? 'merge and ' : '') . 'remove these ' . $totalDuplicates . ' duplicate restaurants?')) {
                $removed = 0;
                $merged = 0;

                // If merge flag is set, first merge data from duplicates into best records
                if ($shouldMerge) {
                    $this->newLine();
                    $this->info('🔄 Merging data from duplicates into best records...');
                    $this->newLine();

                    foreach ($duplicateGroups as $group) {
                        $bestRestaurant = $this->selectBestRestaurant($group);
                        $this->mergeRestaurantData($bestRestaurant, $group);
                        $merged++;
                    }

                    $this->info("✅ Merged data in {$merged} restaurant groups");
                    $this->newLine();
                }

                // Now remove the duplicates
                $this->info('🗑️  Removing duplicate restaurants...');
                foreach ($toRemove as $restaurant) {
                    $restaurant->forceDelete(); // Force delete to avoid soft delete issues
                    $removed++;
                }

                $this->newLine();
                if ($shouldMerge) {
                    $this->info("✅ Successfully merged data and removed {$removed} duplicate restaurants!");
                } else {
                    $this->info("✅ Successfully removed {$removed} duplicate restaurants!");
                }
                return Command::SUCCESS;
            } else {
                $this->warn('Removal cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->info('💡 Tip: Run with --remove --merge to merge data and remove duplicates');
        $this->info('💡 Tip: Run with --dry-run first to preview changes safely');
        $this->info('💡 Tip: Use --merge flag to preserve data from all duplicates');

        return Command::SUCCESS;
    }

    /**
     * Calculate similarity between two strings
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        // Remove common words that might cause false matches
        $commonWords = ['restaurant', 'mexican', 'grill', 'cantina', 'taqueria', 'cocina'];
        foreach ($commonWords as $word) {
            $str1 = str_replace($word, '', $str1);
            $str2 = str_replace($word, '', $str2);
        }

        $str1 = trim($str1);
        $str2 = trim($str2);

        similar_text($str1, $str2, $percent);

        return round($percent, 2);
    }

    /**
     * Calculate distance between two GPS coordinates in kilometers
     * Using Haversine formula
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Select the best restaurant to keep from a group of duplicates
     * Criteria: most complete data, highest rating, most reviews, newest
     */
    protected function selectBestRestaurant(array $group): Restaurant
    {
        $best = null;
        $bestScore = -1;

        foreach ($group as $item) {
            $restaurant = $item['restaurant'];
            $score = 0;

            // Score based on data completeness
            if ($restaurant->latitude && $restaurant->longitude) $score += 10;
            if ($restaurant->phone) $score += 5;
            if ($restaurant->website) $score += 3;
            if ($restaurant->address) $score += 5;
            if ($restaurant->zip_code) $score += 2;

            // Score based on ratings and reviews
            if ($restaurant->average_rating) {
                $score += ($restaurant->average_rating * 2);
            }
            if ($restaurant->total_reviews) {
                $score += min($restaurant->total_reviews / 10, 10); // Max 10 points
            }

            // Score based on import source (Yelp/Google are more trusted)
            if (in_array($restaurant->import_source, ['yelp', 'google'])) {
                $score += 15;
            }

            // Score based on verification
            if ($restaurant->yelp_id) $score += 5;
            if ($restaurant->google_place_id) $score += 5;

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $restaurant;
            }
        }

        return $best;
    }

    /**
     * Merge data from duplicate restaurants into the best one
     * Strategy: Fill in any missing data from duplicates into the best record
     */
    protected function mergeRestaurantData(Restaurant $best, array $duplicates): void
    {
        $mergedData = [];
        $updated = false;

        foreach ($duplicates as $item) {
            $duplicate = $item['restaurant'];

            // Skip the best restaurant itself
            if ($duplicate->id === $best->id) {
                continue;
            }

            // Merge strategy: Use best's data, fill gaps from duplicates

            // Basic fields - use if best doesn't have it
            if (!$best->phone && $duplicate->phone) {
                $mergedData['phone'] = $duplicate->phone;
                $updated = true;
            }

            if (!$best->website && $duplicate->website) {
                $mergedData['website'] = $duplicate->website;
                $updated = true;
            }

            if (!$best->address && $duplicate->address) {
                $mergedData['address'] = $duplicate->address;
                $updated = true;
            }

            if (!$best->zip_code && $duplicate->zip_code) {
                $mergedData['zip_code'] = $duplicate->zip_code;
                $updated = true;
            }

            // GPS coordinates - use if best doesn't have them
            if (!$best->latitude && $duplicate->latitude) {
                $mergedData['latitude'] = $duplicate->latitude;
                $updated = true;
            }

            if (!$best->longitude && $duplicate->longitude) {
                $mergedData['longitude'] = $duplicate->longitude;
                $updated = true;
            }

            // Description - use the longer one
            $bestDescLength = strlen($best->description ?? '');
            $dupDescLength = strlen($duplicate->description ?? '');
            if ($dupDescLength > $bestDescLength) {
                $mergedData['description'] = $duplicate->description;
                $updated = true;
            }

            // Image - use if best doesn't have one
            if (!$best->image && $duplicate->image) {
                $mergedData['image'] = $duplicate->image;
                $updated = true;
            }

            // Yelp data - use if best doesn't have it
            if (!$best->yelp_id && $duplicate->yelp_id) {
                $mergedData['yelp_id'] = $duplicate->yelp_id;
                $mergedData['yelp_rating'] = $duplicate->yelp_rating;
                $mergedData['yelp_reviews_count'] = $duplicate->yelp_reviews_count;
                $mergedData['yelp_url'] = $duplicate->yelp_url;
                $updated = true;
            }

            // Google data - use if best doesn't have it
            if (!$best->google_place_id && $duplicate->google_place_id) {
                $mergedData['google_place_id'] = $duplicate->google_place_id;
                $mergedData['google_rating'] = $duplicate->google_rating;
                $mergedData['google_reviews_count'] = $duplicate->google_reviews_count;
                $mergedData['google_maps_url'] = $duplicate->google_maps_url;
                $mergedData['google_verified'] = $duplicate->google_verified;
                $updated = true;
            }

            // Ratings - use the higher rating and sum reviews
            if ($duplicate->average_rating && (!$best->average_rating || $duplicate->average_rating > $best->average_rating)) {
                $mergedData['average_rating'] = $duplicate->average_rating;
                $updated = true;
            }

            // Sum total reviews if both have reviews
            if ($duplicate->total_reviews > 0) {
                $totalReviews = ($best->total_reviews ?? 0) + $duplicate->total_reviews;
                $mergedData['total_reviews'] = $totalReviews;
                $updated = true;
            }
        }

        // Update the best restaurant with merged data if anything changed
        if ($updated && !empty($mergedData)) {
            // Use direct DB update to bypass slug regeneration and soft delete checks
            DB::table('restaurants')
                ->where('id', $best->id)
                ->update(array_merge($mergedData, ['updated_at' => now()]));

            $this->info("    🔄 Merged data into restaurant ID {$best->id}");
        }
    }
}
