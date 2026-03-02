<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\FoursquareService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnrichWithFoursquare extends Command
{
    protected $signature = 'foursquare:enrich-restaurants 
                            {--limit=250 : Number of restaurants to process}
                            {--delay=1 : Delay between API calls in seconds}
                            {--force : Re-enrich restaurants that already have Foursquare data}';

    protected $description = 'Enrich restaurants with Foursquare ratings and check-ins';

    protected FoursquareService $foursquare;

    public function __construct(FoursquareService $foursquare)
    {
        parent::__construct();
        $this->foursquare = $foursquare;
    }

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');
        $force = $this->option('force');

        $remaining = $this->foursquare->getRemainingCalls();
        $this->info("Foursquare API calls remaining today: {$remaining}");

        if ($remaining < 2) {
            $this->error('Daily API limit reached. Try again tomorrow.');
            return 1;
        }

        // Each restaurant needs 2 API calls (search + details)
        $maxRestaurants = min($limit, floor($remaining / 2));

        if ($maxRestaurants < 1) {
            $this->error('Not enough API calls remaining.');
            return 1;
        }

        $this->info("Processing up to {$maxRestaurants} restaurants...");

        $query = Restaurant::where('status', 'approved')
            ->whereNotNull('google_place_id');

        if (!$force) {
            $query->whereNull('foursquare_id');
        }

        $restaurants = $query->orderBy('google_rating', 'desc')
            ->limit($maxRestaurants)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants to enrich.');
            return 0;
        }

        $enriched = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            if (!$this->foursquare->canMakeCall()) {
                $this->warn("\nDaily limit reached. Stopping.");
                break;
            }

            try {
                $data = $this->foursquare->enrichRestaurant($restaurant);

                if ($data) {
                    $data['foursquare_last_sync'] = now();
                    $restaurant->update($data);
                    $enriched++;

                    Log::info("Foursquare enriched: {$restaurant->name}", [
                        'id' => $restaurant->id,
                        'foursquare_id' => $data['foursquare_id'] ?? null,
                        'rating' => $data['foursquare_rating'] ?? null,
                    ]);
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Foursquare error for {$restaurant->name}: " . $e->getMessage());
            }

            $bar->advance();
            
            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Completed: {$enriched} enriched, {$failed} not found");
        $this->info("API calls remaining: " . $this->foursquare->getRemainingCalls());

        return 0;
    }
}
