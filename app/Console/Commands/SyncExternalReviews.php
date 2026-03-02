<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\ExternalReview;
use App\Services\TripAdvisorService;
use App\Services\YelpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncExternalReviews extends Command
{
    protected $signature = 'reviews:sync-external 
                            {--platform=all : Platform to sync (google, yelp, tripadvisor, all)}
                            {--limit=50 : Number of restaurants to process}
                            {--restaurant= : Specific restaurant ID}';

    protected $description = 'Sync reviews from external platforms';

    public function handle()
    {
        $platform = $this->option('platform');
        $limit = (int) $this->option('limit');
        $restaurantId = $this->option('restaurant');

        $this->info("Syncing reviews from: {$platform}");

        if ($restaurantId) {
            $restaurants = Restaurant::where('id', $restaurantId)->get();
        } else {
            $restaurants = Restaurant::query()
                ->where('is_claimed', true)
                ->orderBy('claimed_at', 'desc')
                ->limit($limit)
                ->get();
        }

        $bar = $this->output->createProgressBar($restaurants->count());

        foreach ($restaurants as $restaurant) {
            try {
                if ($platform === 'all' || $platform === 'yelp') {
                    $this->syncYelpReviews($restaurant);
                }
                if ($platform === 'all' || $platform === 'tripadvisor') {
                    $this->syncTripAdvisorReviews($restaurant);
                }
            } catch (\Exception $e) {
                $this->error("Error syncing {$restaurant->name}: " . $e->getMessage());
            }

            $bar->advance();
            usleep(500000); // 0.5 second delay
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sync completed!');
    }

    private function syncYelpReviews(Restaurant $restaurant): int
    {
        if (!$restaurant->yelp_id) {
            return 0;
        }

        try {
            $response = Http::withToken(config('services.yelp.api_key'))
                ->get("https://api.yelp.com/v3/businesses/{$restaurant->yelp_id}/reviews");

            if (!$response->successful()) {
                return 0;
            }

            $reviews = $response->json('reviews', []);
            $synced = 0;

            foreach ($reviews as $review) {
                ExternalReview::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'platform' => 'yelp',
                        'platform_review_id' => $review['id'],
                    ],
                    [
                        'platform_url' => $review['url'] ?? null,
                        'reviewer_name' => $review['user']['name'] ?? 'Yelp User',
                        'reviewer_avatar' => $review['user']['image_url'] ?? null,
                        'reviewer_profile_url' => $review['user']['profile_url'] ?? null,
                        'rating' => $review['rating'],
                        'comment' => $review['text'],
                        'reviewed_at' => $review['time_created'] ?? now(),
                        'last_synced_at' => now(),
                    ]
                );
                $synced++;
            }

            return $synced;

        } catch (\Exception $e) {
            \Log::error('Yelp review sync failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    private function syncTripAdvisorReviews(Restaurant $restaurant): int
    {
        if (!$restaurant->tripadvisor_id) {
            return 0;
        }

        try {
            $apiKey = config('services.tripadvisor.api_key');
            $response = Http::get("https://api.content.tripadvisor.com/api/v1/location/{$restaurant->tripadvisor_id}/reviews", [
                'key' => $apiKey,
                'language' => 'es',
            ]);

            if (!$response->successful()) {
                return 0;
            }

            $reviews = $response->json('data', []);
            $synced = 0;

            foreach ($reviews as $review) {
                ExternalReview::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'platform' => 'tripadvisor',
                        'platform_review_id' => $review['id'],
                    ],
                    [
                        'platform_url' => $review['url'] ?? null,
                        'reviewer_name' => $review['user']['username'] ?? 'TripAdvisor User',
                        'reviewer_avatar' => $review['user']['avatar']['small'] ?? null,
                        'reviewer_review_count' => $review['user']['num_reviews'] ?? null,
                        'rating' => $review['rating'],
                        'comment' => $review['text'],
                        'reviewed_at' => $review['published_date'] ?? now(),
                        'last_synced_at' => now(),
                    ]
                );
                $synced++;
            }

            return $synced;

        } catch (\Exception $e) {
            \Log::error('TripAdvisor review sync failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
