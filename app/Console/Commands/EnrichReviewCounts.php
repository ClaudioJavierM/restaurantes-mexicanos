<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Services\GooglePlacesService;
use App\Services\YelpFusionService;
use Illuminate\Support\Facades\Log;

class EnrichReviewCounts extends Command
{
    protected $signature = 'restaurants:enrich-review-counts 
                            {--limit=100 : Number of restaurants to process}
                            {--platform=all : Platform to enrich (google, yelp, all)}
                            {--missing-ratings : Include restaurants with place_id but no rating}';

    protected $description = 'Enrich restaurants with ratings and review counts from Google and Yelp';

    protected $googleService;
    protected $yelpService;

    public function __construct(GooglePlacesService $googleService, YelpFusionService $yelpService)
    {
        parent::__construct();
        $this->googleService = $googleService;
        $this->yelpService = $yelpService;
    }

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $platform = $this->option('platform');
        $missingRatings = $this->option('missing-ratings');

        $this->info("Enriching review counts (limit: {$limit}, platform: {$platform})");

        $stats = [
            'google_updated' => 0,
            'yelp_updated' => 0,
        ];

        if ($platform === 'all' || $platform === 'google') {
            $stats['google_updated'] = $this->enrichGoogle($limit, $missingRatings);
        }

        if ($platform === 'all' || $platform === 'yelp') {
            $stats['yelp_updated'] = $this->enrichYelp($limit, $missingRatings);
        }

        $this->newLine();
        $this->info('Enrichment completed:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Google Updated', $stats['google_updated']],
                ['Yelp Updated', $stats['yelp_updated']],
            ]
        );

        return Command::SUCCESS;
    }

    protected function enrichGoogle(int $limit, bool $missingRatings): int
    {
        $query = Restaurant::query()->whereNotNull('google_place_id');

        if ($missingRatings) {
            // Get restaurants with place_id but no rating
            $query->where(function($q) {
                $q->whereNull('google_rating')
                  ->orWhere('google_rating', 0);
            });
        } else {
            // Get restaurants with rating but no review count
            $query->whereNotNull('google_rating')
                  ->where('google_rating', '>', 0)
                  ->where(function($q) {
                      $q->whereNull('google_reviews_count')
                        ->orWhere('google_reviews_count', 0);
                  });
        }

        $restaurants = $query->take($limit)->get();
        $updated = 0;

        $this->info("Processing {$restaurants->count()} restaurants for Google...");
        $bar = $this->output->createProgressBar($restaurants->count());

        foreach ($restaurants as $restaurant) {
            try {
                $details = $this->googleService->getPlaceDetails($restaurant->google_place_id);
                
                if ($details) {
                    $updateData = [];
                    
                    if (isset($details['rating'])) {
                        $updateData['google_rating'] = $details['rating'];
                    }
                    if (isset($details['user_ratings_total'])) {
                        $updateData['google_reviews_count'] = $details['user_ratings_total'];
                    }
                    
                    if (!empty($updateData)) {
                        $restaurant->update($updateData);
                        $updated++;
                    }
                }

                usleep(100000); // 100ms delay
            } catch (\Exception $e) {
                Log::warning("Failed to enrich Google for restaurant {$restaurant->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $updated;
    }

    protected function enrichYelp(int $limit, bool $missingRatings): int
    {
        $query = Restaurant::query()->whereNotNull('yelp_id')
            ->where(function($q) {
                // Only re-fetch if not enriched in the last 30 days
                $q->whereNull('yelp_enriched_at')
                  ->orWhere('yelp_enriched_at', '<', now()->subDays(30));
            });

        if ($missingRatings) {
            $query->where(function($q) {
                $q->whereNull('yelp_rating')
                  ->orWhere('yelp_rating', 0);
            });
        } else {
            $query->whereNotNull('yelp_rating')
                  ->where('yelp_rating', '>', 0)
                  ->where(function($q) {
                      $q->whereNull('yelp_reviews_count')
                        ->orWhere('yelp_reviews_count', 0);
                  });
        }

        $restaurants = $query->take($limit)->get();
        $updated = 0;

        $this->info("Processing {$restaurants->count()} restaurants for Yelp...");
        $bar = $this->output->createProgressBar($restaurants->count());

        foreach ($restaurants as $restaurant) {
            try {
                $yelpData = $this->yelpService->getBusinessDetails($restaurant->yelp_id);
                
                if ($yelpData) {
                    $updateData = [];
                    
                    if (isset($yelpData['rating'])) {
                        $updateData['yelp_rating'] = $yelpData['rating'];
                    }
                    if (isset($yelpData['review_count'])) {
                        $updateData['yelp_reviews_count'] = $yelpData['review_count'];
                    }

                    if (!empty($updateData)) {
                        $updateData['yelp_enriched_at'] = now();
                        $restaurant->update($updateData);
                        $updated++;
                    }
                }

                usleep(200000); // 200ms delay
            } catch (\Exception $e) {
                Log::warning("Failed to enrich Yelp for restaurant {$restaurant->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $updated;
    }
}
