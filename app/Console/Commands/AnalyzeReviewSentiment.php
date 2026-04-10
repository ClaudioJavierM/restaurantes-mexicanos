<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\ReviewSentimentService;
use Illuminate\Console\Command;

class AnalyzeReviewSentiment extends Command
{
    protected $signature = 'famer:analyze-sentiment
                            {--restaurant= : Process a single restaurant by ID}
                            {--limit=50    : Maximum number of reviews to process per restaurant}
                            {--all         : Process all restaurants}';

    protected $description = 'Analyze review sentiment using GPT-4o-mini';

    public function handle(ReviewSentimentService $service): int
    {
        $restaurantId = $this->option('restaurant');
        $limit        = (int) $this->option('limit');
        $all          = $this->option('all');

        if (!$restaurantId && !$all) {
            $this->error('Specify --restaurant=ID or --all');
            return self::FAILURE;
        }

        if ($restaurantId) {
            return $this->processRestaurant((int) $restaurantId, $limit, $service);
        }

        // Process all restaurants that have unanalyzed reviews
        $restaurants = Restaurant::whereHas('reviews', function ($q) {
            $q->where('status', 'approved')->whereNull('sentiment_analyzed_at');
        })->get();

        if ($restaurants->isEmpty()) {
            $this->info('No pending reviews to analyze.');
            return self::SUCCESS;
        }

        $this->info("Processing {$restaurants->count()} restaurant(s)…");

        $totalProcessed = 0;
        $totalFailed    = 0;

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            try {
                $summary         = $service->analyzeForRestaurant($restaurant->id, $limit);
                $totalProcessed += $summary['processed'];
                $totalFailed    += $summary['failed'];
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Restaurant {$restaurant->id} error: " . $e->getMessage());
                $totalFailed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Restaurants processed', $restaurants->count()],
                ['Reviews analyzed',      $totalProcessed],
                ['Reviews failed',        $totalFailed],
            ]
        );

        return self::SUCCESS;
    }

    private function processRestaurant(int $restaurantId, int $limit, ReviewSentimentService $service): int
    {
        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            $this->error("Restaurant #{$restaurantId} not found.");
            return self::FAILURE;
        }

        $this->info("Analyzing reviews for: {$restaurant->name}");

        $pendingCount = $restaurant->reviews()
            ->where('status', 'approved')
            ->whereNull('sentiment_analyzed_at')
            ->count();

        if ($pendingCount === 0) {
            $this->info('No pending reviews to analyze.');
            return self::SUCCESS;
        }

        $toProcess = min($pendingCount, $limit);
        $this->info("Pending: {$pendingCount} — will process up to {$toProcess}");

        $bar = $this->output->createProgressBar($toProcess);
        $bar->start();

        $processed = 0;
        $failed    = 0;

        $reviews = $restaurant->reviews()
            ->where('status', 'approved')
            ->whereNull('sentiment_analyzed_at')
            ->limit($toProcess)
            ->get();

        foreach ($reviews as $review) {
            try {
                $service->analyzeReview($review);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->warn("Review #{$review->id} failed: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Analyzed successfully', $processed],
                ['Failed',               $failed],
            ]
        );

        // Show updated summary
        $summary = $service->getSentimentSummary($restaurantId);

        if ($summary['total_analyzed'] > 0) {
            $scorePercent = round($summary['avg_score'] * 100);
            $this->info("Overall sentiment: {$scorePercent}% positive ({$summary['total_analyzed']} reviews analyzed)");
        }

        return self::SUCCESS;
    }
}
