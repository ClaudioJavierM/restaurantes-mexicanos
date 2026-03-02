<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MergeCrossStateDuplicates extends Command
{
    protected $signature = 'restaurants:merge-cross-state
                            {--dry-run : Show what would be merged without actually merging}
                            {--auto : Automatically merge without confirmation}';

    protected $description = 'Find and merge restaurants that are duplicated across different states (same GPS location)';

    public function handle()
    {
        $this->info('🔍 Searching for cross-state duplicates...');
        $this->newLine();

        $duplicatePairs = $this->findCrossStateDuplicates();

        if (empty($duplicatePairs)) {
            $this->info('✅ No cross-state duplicates found!');
            return Command::SUCCESS;
        }

        $this->warn("Found " . count($duplicatePairs) . " cross-state duplicate pairs:");
        $this->newLine();

        $merged = 0;
        foreach ($duplicatePairs as $pair) {
            $this->displayPair($pair);

            if ($this->option('dry-run')) {
                $this->line('  🔍 DRY RUN - Would merge these records');
                $this->newLine();
                continue;
            }

            if (!$this->option('auto')) {
                if (!$this->confirm('Merge these records?', true)) {
                    $this->warn('  ⏭️  Skipped');
                    $this->newLine();
                    continue;
                }
            }

            $this->mergePair($pair);
            $merged++;
            $this->info('  ✅ Merged successfully!');
            $this->newLine();
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN - No changes were made');
        } else {
            $this->info("✅ Successfully merged {$merged} duplicate pairs!");
        }

        return Command::SUCCESS;
    }

    protected function findCrossStateDuplicates(): array
    {
        $pairs = [];

        // Get all restaurants with GPS coordinates
        $restaurants = Restaurant::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('state')
            ->orderBy('id')
            ->get();

        $processed = [];

        foreach ($restaurants as $r1) {
            if (in_array($r1->id, $processed)) {
                continue;
            }

            foreach ($restaurants as $r2) {
                if ($r1->id >= $r2->id || in_array($r2->id, $processed)) {
                    continue;
                }

                // Skip if same state
                if ($r1->state_id === $r2->state_id) {
                    continue;
                }

                // Calculate distance
                $distance = $this->calculateDistance(
                    $r1->latitude,
                    $r1->longitude,
                    $r2->latitude,
                    $r2->longitude
                );

                // If within 50 meters, it's a duplicate
                if ($distance < 0.05) {
                    // Determine which is the correct one based on GPS coordinates
                    $correct = $this->selectCorrectRestaurant($r1, $r2);
                    $incorrect = $correct->id === $r1->id ? $r2 : $r1;

                    $pairs[] = [
                        'correct' => $correct,
                        'incorrect' => $incorrect,
                        'distance' => round($distance * 1000, 2), // meters
                    ];

                    $processed[] = $r1->id;
                    $processed[] = $r2->id;
                    break;
                }
            }
        }

        return $pairs;
    }

    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function selectCorrectRestaurant($r1, $r2)
    {
        $score1 = $this->scoreRestaurant($r1);
        $score2 = $this->scoreRestaurant($r2);

        return $score1 >= $score2 ? $r1 : $r2;
    }

    protected function scoreRestaurant($restaurant): int
    {
        $score = 0;

        // Prefer manual imports over Yelp (they usually have better data)
        if ($restaurant->import_source === 'manual') {
            $score += 20;
        }

        // Data completeness
        if ($restaurant->website && !str_contains($restaurant->website, 'yelp.com')) {
            $score += 15; // Real website is very valuable
        }
        if ($restaurant->image) {
            $score += 10;
        }
        if ($restaurant->description && strlen($restaurant->description) > 100) {
            $score += 10;
        }
        if ($restaurant->phone) {
            $score += 5;
        }

        // Reviews and ratings
        if ($restaurant->total_reviews > 0) {
            $score += min($restaurant->total_reviews / 10, 10);
        }
        if ($restaurant->average_rating) {
            $score += $restaurant->average_rating;
        }

        // Older records are usually more trusted
        $score += 1;

        return $score;
    }

    protected function displayPair(array $pair)
    {
        $correct = $pair['correct'];
        $incorrect = $pair['incorrect'];

        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("Distance: {$pair['distance']}m apart");
        $this->newLine();

        $this->info("  ✅ KEEP [ID {$correct->id}] {$correct->name}");
        $this->line("      State: {$correct->state->name} ({$correct->state->code})");
        $this->line("      Address: {$correct->address}");
        $this->line("      GPS: {$correct->latitude}, {$correct->longitude}");
        $this->line("      Source: " . ($correct->import_source ?? 'manual'));
        $this->line("      Image: " . ($correct->image ? '✅ YES' : '❌ NO'));
        $this->line("      Description: " . (strlen($correct->description ?? '') > 0 ? '✅ YES' : '❌ NO'));
        $this->line("      Website: " . ($correct->website ? '✅ ' . $correct->website : '❌ NO'));
        $this->newLine();

        $this->error("  ❌ REMOVE [ID {$incorrect->id}] {$incorrect->name}");
        $this->line("      State: {$incorrect->state->name} ({$incorrect->state->code})");
        $this->line("      Address: {$incorrect->address}");
        $this->line("      Source: " . ($incorrect->import_source ?? 'manual'));
        $this->line("      Image: " . ($incorrect->image ? '✅ YES' : '❌ NO'));
        $this->line("      Description: " . (strlen($incorrect->description ?? '') > 0 ? '✅ YES' : '❌ NO'));
        $this->line("      Website: " . ($incorrect->website ? '✅ ' . $incorrect->website : '❌ NO'));
        $this->newLine();
    }

    protected function mergePair(array $pair)
    {
        $correct = $pair['correct'];
        $incorrect = $pair['incorrect'];

        DB::transaction(function () use ($correct, $incorrect) {
            // Merge data from incorrect to correct if correct is missing it
            $updates = [];

            // Copy image if correct doesn't have one but incorrect does
            if (!$correct->image && $incorrect->image) {
                $updates['image'] = $incorrect->image;
                $this->line("    📸 Copied image from incorrect record");
            }

            // Copy description if correct doesn't have one but incorrect does
            if ((!$correct->description || strlen($correct->description) < 50) &&
                $incorrect->description && strlen($incorrect->description) > 50) {
                $updates['description'] = $incorrect->description;
                $this->line("    📝 Copied description from incorrect record");
            }

            // Copy website if correct doesn't have one but incorrect does (and it's not Yelp)
            if (!$correct->website && $incorrect->website &&
                !str_contains($incorrect->website, 'yelp.com')) {
                $updates['website'] = $incorrect->website;
                $this->line("    🌐 Copied website from incorrect record");
            }

            // Copy Yelp data if available
            if (!$correct->yelp_id && $incorrect->yelp_id) {
                $updates['yelp_id'] = $incorrect->yelp_id;
                $updates['yelp_rating'] = $incorrect->yelp_rating;
                $updates['yelp_reviews_count'] = $incorrect->yelp_reviews_count;
                $updates['yelp_url'] = $incorrect->yelp_url;
                $this->line("    ⭐ Copied Yelp data from incorrect record");
            }

            // Apply updates if any
            if (!empty($updates)) {
                $correct->update($updates);
            }

            // Delete the incorrect record
            $incorrect->delete();
            $this->line("    🗑️  Deleted incorrect record [ID {$incorrect->id}]");
        });
    }
}
