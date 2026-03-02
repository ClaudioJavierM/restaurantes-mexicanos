<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadStreetViewPhotos extends Command
{
    protected $signature = 'restaurants:download-streetview
                            {--limit=100 : Number of restaurants to process}
                            {--batch=20 : Number of photos to download in each batch}
                            {--force : Overwrite existing photos}';

    protected $description = 'Download Street View photos for restaurants using their lat/long coordinates';

    protected $apiKey;
    protected $stats = [
        'processed' => 0,
        'downloaded' => 0,
        'skipped' => 0,
        'no_location' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->apiKey = config('services.google.places_api_key');

        if (empty($this->apiKey)) {
            $this->error('❌ Google Places API key not found in config/services.php');
            return 1;
        }

        $this->info('📸 Starting Street View Photo Downloader');
        $this->info('🔑 API Key: ' . substr($this->apiKey, 0, 10) . '...');
        $this->newLine();

        // Get restaurants without images (or with --force flag)
        $query = Restaurant::query();

        if (!$this->option('force')) {
            $query->whereNull('image');
        }

        $restaurants = $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit($this->option('limit'))
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info('✅ All restaurants already have photos!');
            return 0;
        }

        $this->info("📊 Found {$restaurants->count()} restaurants to process");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($restaurants->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');

        $batchSize = $this->option('batch');
        $processed = 0;

        foreach ($restaurants as $restaurant) {
            $progressBar->setMessage("Processing: {$restaurant->name}");
            $progressBar->advance();

            $this->processRestaurant($restaurant);
            $this->stats['processed']++;
            $processed++;

            // Rate limiting
            if ($processed % $batchSize === 0) {
                sleep(1); // Wait 1 second every batch
            } else {
                usleep(200000); // Wait 0.2 seconds
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->displayStats();

        return 0;
    }

    protected function processRestaurant(Restaurant $restaurant)
    {
        try {
            if (!$restaurant->latitude || !$restaurant->longitude) {
                $this->stats['no_location']++;
                return;
            }

            if ($restaurant->image && !$this->option('force')) {
                $this->stats['skipped']++;
                return;
            }

            $photoPath = $this->downloadStreetViewPhoto(
                $restaurant->latitude,
                $restaurant->longitude,
                $restaurant->name
            );

            if ($photoPath) {
                $restaurant->update(['image' => $photoPath]);
                $this->stats['downloaded']++;
            } else {
                $this->stats['errors']++;
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
        }
    }

    protected function downloadStreetViewPhoto(float $lat, float $lng, string $restaurantName): ?string
    {
        try {
            $url = "https://maps.googleapis.com/maps/api/streetview?" . http_build_query([
                'size' => '800x600',
                'location' => "{$lat},{$lng}",
                'fov' => 90,
                'heading' => 0,
                'pitch' => 0,
                'key' => $this->apiKey,
                'source' => 'outdoor',
            ]);

            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type');
            if (!str_contains($contentType, 'image')) {
                return null;
            }

            $filename = Str::slug($restaurantName) . '-' . time() . '-' . rand(1000, 9999) . '.jpg';
            $path = 'restaurants/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function displayStats()
    {
        $this->info('✅ Street View Photo Download Complete!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $this->stats['processed']],
                ['Downloaded', $this->stats['downloaded']],
                ['Skipped (had photo)', $this->stats['skipped']],
                ['No Location Data', $this->stats['no_location']],
                ['Errors', $this->stats['errors']],
            ]
        );

        $this->newLine();

        $totalWithPhotos = Restaurant::whereNotNull('image')->count();
        $totalRestaurants = Restaurant::count();
        $coverage = $totalRestaurants > 0 ? round(($totalWithPhotos / $totalRestaurants) * 100, 2) : 0;

        $this->info("📊 Total restaurants with photos: {$totalWithPhotos} / {$totalRestaurants}");
        $this->info("📈 Coverage: {$coverage}%");
    }
}
