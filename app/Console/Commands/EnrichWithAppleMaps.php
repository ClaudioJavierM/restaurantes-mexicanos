<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\AppleMapsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnrichWithAppleMaps extends Command
{
    protected $signature = "applemaps:enrich-restaurants 
                            {--limit=250 : Number of restaurants to process}
                            {--delay=1 : Delay between API calls}
                            {--force : Re-enrich all}";

    protected $description = "Enrich restaurants with Apple Maps verification";

    public function handle(): int
    {
        $appleMaps = app(AppleMapsService::class);
        $limit = (int) $this->option("limit");
        $delay = (int) $this->option("delay");
        $force = $this->option("force");

        $remaining = $appleMaps->getRemainingCalls();
        $this->info("Apple Maps API calls remaining: " . $remaining);

        if ($remaining < 1) {
            $this->error("Daily limit reached.");
            return 1;
        }

        $query = Restaurant::where("status", "approved")->whereNotNull("google_place_id");
        if (!$force) {
            $query->whereNull("apple_maps_id");
        }

        $restaurants = $query->orderBy("google_rating", "desc")->limit(min($limit, $remaining))->get();

        if ($restaurants->isEmpty()) {
            $this->info("No restaurants to enrich.");
            return 0;
        }

        $enriched = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            if (!$appleMaps->canMakeCall()) break;

            try {
                $data = $appleMaps->enrichRestaurant($restaurant);
                if ($data) {
                    $data["apple_maps_last_sync"] = now();
                    $restaurant->update($data);
                    $enriched++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error("Apple Maps error: " . $e->getMessage());
            }

            $bar->advance();
            if ($delay > 0) sleep($delay);
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completed: $enriched enriched, $failed not found");

        return 0;
    }
}
