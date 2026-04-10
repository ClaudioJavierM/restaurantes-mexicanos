<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\CompetitorInsightsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateCompetitorReports extends Command
{
    protected $signature = 'famer:competitor-reports
                            {--limit=100 : Maximum number of restaurants to process}
                            {--force : Force recompute even if cache already exists}
                            {--restaurant= : Process a single restaurant by ID}';

    protected $description = 'Pre-compute and cache competitor insights for restaurant owners (24h TTL)';

    public function handle(CompetitorInsightsService $service): int
    {
        $limit      = (int) $this->option('limit');
        $force      = (bool) $this->option('force');
        $singleId   = $this->option('restaurant');

        if ($singleId) {
            $restaurant = Restaurant::find($singleId);
            if (! $restaurant) {
                $this->error("Restaurant ID {$singleId} not found.");
                return self::FAILURE;
            }
            $this->processRestaurant($service, $restaurant, $force);
            $this->info("Done: {$restaurant->name}");
            return self::SUCCESS;
        }

        // Process claimed restaurants first (owners who actually see the dashboard)
        $query = Restaurant::approved()
            ->where('is_claimed', true)
            ->orderBy('id')
            ->limit($limit);

        $restaurants = $query->get();
        $total       = $restaurants->count();

        if ($total === 0) {
            $this->warn('No claimed approved restaurants found.');
            return self::SUCCESS;
        }

        $this->info("Processing {$total} restaurants (limit: {$limit}, force: " . ($force ? 'yes' : 'no') . ")");

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $processed = 0;
        $skipped   = 0;
        $errors    = 0;

        // Process in chunks of 20 to avoid memory issues
        $restaurants->chunk(20)->each(function ($chunk) use (
            $service, $force, $bar, &$processed, &$skipped, &$errors
        ) {
            foreach ($chunk as $restaurant) {
                $bar->setMessage($restaurant->name);

                try {
                    $cacheKey = "competitor_insights_{$restaurant->id}";

                    if (! $force && Cache::has($cacheKey)) {
                        $skipped++;
                    } else {
                        // refreshInsights() clears cache then recomputes
                        $service->refreshInsights($restaurant);
                        $processed++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Error on restaurant {$restaurant->id} ({$restaurant->name}): {$e->getMessage()}");
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed (cache written)', $processed],
                ['Skipped (cache hit)',       $skipped],
                ['Errors',                    $errors],
                ['Total',                     $total],
            ]
        );

        if ($errors > 0) {
            $this->warn("{$errors} restaurant(s) failed. Check logs for details.");
            return self::FAILURE;
        }

        $this->info('Competitor reports generated successfully.');
        return self::SUCCESS;
    }

    protected function processRestaurant(
        CompetitorInsightsService $service,
        Restaurant $restaurant,
        bool $force
    ): void {
        if ($force) {
            $service->refreshInsights($restaurant);
        } else {
            $service->getInsights($restaurant);
        }
    }
}
