<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FamerScoreService;
use App\Models\Restaurant;

class RecalculateFamerScores extends Command
{
    protected $signature = 'famer:recalculate-scores 
                            {--limit=0 : Limit number of restaurants to process (0 = all)}
                            {--restaurant= : Process specific restaurant by ID or slug}';

    protected $description = 'Recalculate FAMER Scores for all restaurants using weighted average from all platforms';

    public function handle(FamerScoreService $scoreService)
    {
        $restaurantOption = $this->option('restaurant');
        $limit = (int) $this->option('limit');

        if ($restaurantOption) {
            // Process single restaurant
            $restaurant = is_numeric($restaurantOption) 
                ? Restaurant::find($restaurantOption)
                : Restaurant::where('slug', $restaurantOption)->first();

            if (!$restaurant) {
                $this->error("Restaurant not found: {$restaurantOption}");
                return Command::FAILURE;
            }

            $result = $scoreService->calculateScore($restaurant);
            $scoreService->updateRestaurantScore($restaurant);

            $this->info("Restaurant: {$restaurant->name}");
            $this->newLine();
            $this->table(
                ['Platform', 'Rating', 'Reviews', 'Weight', 'Contribution'],
                collect($result['breakdown'])->except('votes')->map(function ($data, $platform) {
                    return [
                        strtoupper($platform),
                        $data['rating'] ?? '-',
                        $data['count'] ?? '-',
                        $data['weight'] . 'x',
                        number_format($data['contribution'] ?? 0, 2),
                    ];
                })->values()->toArray()
            );

            $this->newLine();
            $this->info("Base Score: " . $result['base_score']);
            $this->info("Vote Bonus: +" . $result['vote_bonus'] . " (" . $result['breakdown']['votes']['count'] . " votes)");
            $this->info("FAMER Score: " . $result['final_score'] . " ⭐");

            return Command::SUCCESS;
        }

        // Process multiple restaurants in chunks to avoid memory exhaustion
        $query = Restaurant::approved();
        $total = $limit > 0 ? min($limit, $query->count()) : $query->count();
        $this->info("Recalculating FAMER Scores for {$total} restaurants...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $processed = 0;

        $query->chunkById(200, function ($restaurants) use ($scoreService, $bar, $limit, &$updated, &$processed) {
            foreach ($restaurants as $restaurant) {
                if ($limit > 0 && $processed >= $limit) return false;
                try {
                    $scoreService->calculateScore($restaurant);
                    $updated++;
                } catch (\Exception $e) {
                    \Log::warning("Score calc failed for {$restaurant->id}: " . $e->getMessage());
                }
                $processed++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Successfully recalculated {$updated} FAMER Scores!");

        // Show sample results
        $this->newLine();
        $this->info('Sample results (top 10 by score):');
        
        $topRestaurants = Restaurant::orderBy('average_rating', 'desc')
            ->whereNotNull('famer_score_breakdown')
            ->take(10)
            ->get(['name', 'average_rating', 'google_rating', 'yelp_rating']);

        $this->table(
            ['Restaurant', 'FAMER Score', 'Google', 'Yelp'],
            $topRestaurants->map(function ($r) {
                return [
                    $r->name,
                    $r->average_rating . ' ⭐',
                    $r->google_rating ?? '-',
                    $r->yelp_rating ?? '-',
                ];
            })->toArray()
        );

        return Command::SUCCESS;
    }
}
