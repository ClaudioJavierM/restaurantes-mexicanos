<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FetchYelpPhotos extends Command
{
    protected $signature = 'yelp:fetch-photos
                            {--limit=100 : Maximum number of restaurants to process}
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--force : Re-fetch photos even if restaurant already has photos}
                            {--test : Test mode - process only 5 restaurants}';

    protected $description = 'Fetch photos from Yelp for restaurants that have a Yelp ID';

    protected YelpFusionService $yelpService;
    protected int $fetched = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function handle(YelpFusionService $yelpService): int
    {
        $this->yelpService = $yelpService;

        $this->info('=== Fetching Yelp Photos ===');
        $this->newLine();

        // Build query
        $query = Restaurant::whereNotNull('yelp_id')
            ->where('status', 'approved');

        // Filter by source if specified
        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
        }

        // Skip restaurants with photos unless --force
        if (!$this->option('force')) {
            $query->doesntHave('media');
        }

        $total = $query->count();
        $this->info("Restaurants to process: {$total}");

        // Apply limit
        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = $query->limit($limit)->get();

        $this->info("Processing: {$limit}");
        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting
            usleep(500000); // 0.5 seconds
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Photos Fetched', $this->fetched],
                ['Skipped (no photo available)', $this->skipped],
                ['Errors', $this->errors],
            ]
        );

        return Command::SUCCESS;
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            // Get business details from Yelp
            $details = $this->yelpService->getBusinessDetails($restaurant->yelp_id);

            if (!$details) {
                $this->skipped++;
                return;
            }

            // Get photos (Yelp provides an array of photo URLs)
            $photos = $details['photos'] ?? [];
            $mainImage = $details['image_url'] ?? null;

            // Use main image if no photos array
            if (empty($photos) && $mainImage) {
                $photos = [$mainImage];
            }

            if (empty($photos)) {
                $this->skipped++;
                return;
            }

            // Download and attach up to 3 photos
            $photoCount = 0;
            foreach (array_slice($photos, 0, 3) as $photoUrl) {
                if ($this->downloadAndAttachPhoto($restaurant, $photoUrl)) {
                    $photoCount++;
                }
            }

            if ($photoCount > 0) {
                $this->fetched++;
                Log::info("Fetched {$photoCount} photos for {$restaurant->name}");
            } else {
                $this->skipped++;
            }

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error fetching photos for {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function downloadAndAttachPhoto(Restaurant $restaurant, string $url): bool
    {
        try {
            // Download image
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return false;
            }

            // Get content type
            $contentType = $response->header('Content-Type');
            $extension = match (true) {
                str_contains($contentType, 'jpeg') => 'jpg',
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                default => 'jpg',
            };

            // Save to temp file
            $tempPath = storage_path('app/temp/' . uniqid('yelp_') . '.' . $extension);

            // Ensure temp directory exists
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            file_put_contents($tempPath, $response->body());

            // Attach to restaurant using Spatie Media Library
            $restaurant->addMedia($tempPath)
                ->usingFileName("{$restaurant->slug}-yelp.{$extension}")
                ->toMediaCollection('images');

            return true;

        } catch (\Exception $e) {
            Log::warning("Failed to download photo for {$restaurant->name}: {$e->getMessage()}");
            return false;
        }
    }
}
