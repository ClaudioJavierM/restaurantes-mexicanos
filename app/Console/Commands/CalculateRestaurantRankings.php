<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\ExternalRatings\ExternalRatingsService;
use Illuminate\Console\Command;

class CalculateRestaurantRankings extends Command
{
    protected $signature = 'rankings:calculate 
                            {--year= : Year for rankings (default: current year)}
                            {--sync-external : Also sync external ratings from Google/Yelp}
                            {--restaurant= : Calculate for specific restaurant ID only}
                            {--state= : Calculate for specific state only}';

    protected $description = 'Calculate FAMER Awards rankings: Top 10 per City, Top 10 per State, Top 100 National';

    protected ExternalRatingsService $ratingsService;

    public function __construct(ExternalRatingsService $ratingsService)
    {
        parent::__construct();
        $this->ratingsService = $ratingsService;
    }

    public function handle(): int
    {
        $year = $this->option('year') ?? now()->year;
        $syncExternal = $this->option('sync-external');
        $restaurantId = $this->option('restaurant');
        $stateId = $this->option('state');

        $this->info("🏆 Calculating FAMER Awards Rankings for {$year}");
        $this->info("📊 New Structure: Top 10 City | Top 10 State | Top 100 National");
        $this->newLine();

        // Get restaurants to process
        $query = Restaurant::with(['state', 'externalRatings', 'reviews'])
            ->where('is_active', true)
            ->where('status', 'approved');

        if ($restaurantId) {
            $query->where('id', $restaurantId);
        }

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        $restaurants = $query->get();
        $total = $restaurants->count();

        $this->info("Found {$total} restaurants to process");
        $this->newLine();

        // Step 1: Sync external ratings if requested
        if ($syncExternal) {
            $this->info('📡 Syncing external ratings...');
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $syncResults = ['google' => 0, 'yelp' => 0, 'failed' => 0];

            foreach ($restaurants as $restaurant) {
                try {
                    $results = $this->ratingsService->syncAllRatings($restaurant);
                    if ($results['google']) $syncResults['google']++;
                    if ($results['yelp']) $syncResults['yelp']++;
                } catch (\Exception $e) {
                    $syncResults['failed']++;
                    $this->error("Error syncing {$restaurant->name}: " . $e->getMessage());
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("External ratings synced - Google: {$syncResults['google']}, Yelp: {$syncResults['yelp']}, Failed: {$syncResults['failed']}");
            $this->newLine();
        }

        // Step 2: Calculate scores for all restaurants
        $this->info('📊 Calculating comprehensive scores...');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($restaurants as $restaurant) {
            try {
                $this->ratingsService->calculateScore($restaurant);
            } catch (\Exception $e) {
                $this->error("Error calculating score for {$restaurant->name}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Step 3: Calculate rankings
        $this->info('🏅 Generating rankings (Top 10 City, Top 10 State, Top 100 National)...');
        $rankingResults = $this->ratingsService->calculateRankings($year);

        $this->newLine();
        $this->info("Rankings calculated successfully!");
        $this->table(
            ['Scope', 'Rankings Generated', 'Limit'],
            [
                ['City', $rankingResults['city'], 'Top 10 per city'],
                ['State', $rankingResults['state'], 'Top 10 per state'],
                ['National', $rankingResults['national'], 'Top 100'],
            ]
        );

        // Show top 10 national
        $this->newLine();
        $this->info("🌟 Top 10 National Rankings {$year} - Los 100 Esenciales:");
        
        $top10 = \App\Models\RestaurantRanking::with('restaurant.state')
            ->where('year', $year)
            ->where('ranking_scope', 'usa')
            ->orderBy('position')
            ->limit(10)
            ->get();

        $rows = [];
        foreach ($top10 as $ranking) {
            $rows[] = [
                '#' . $ranking->position,
                $ranking->restaurant->name,
                $ranking->restaurant->city,
                $ranking->restaurant->state?->name ?? 'N/A',
                number_format($ranking->final_score, 2),
            ];
        }

        $this->table(['Rank', 'Restaurant', 'City', 'State', 'Score'], $rows);

        $this->newLine();
        $this->info('✅ FAMER Awards ranking calculation complete!');
        $this->info('📋 Rankings: Top 10 per City | Top 10 per State | Top 100 National');

        return Command::SUCCESS;
    }
}
