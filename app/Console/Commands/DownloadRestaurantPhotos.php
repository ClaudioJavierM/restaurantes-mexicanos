<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadRestaurantPhotos extends Command
{
    protected $signature = 'restaurants:download-photos
                            {--limit=100 : Number of restaurants to process}
                            {--batch=10 : Number of photos to download in each batch}
                            {--country= : Filter by country code (US, MX)}';

    protected $description = 'Download photos for restaurants that have Google Place ID but no image';

    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/place';
    protected $stats = [
        'processed' => 0,
        'downloaded' => 0,
        'no_photo' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->apiKey = config('services.google.places_api_key');

        if (empty($this->apiKey)) {
            $this->error('❌ Google Places API key not found in config/services.php');
            return 1;
        }

        $this->info('📸 Starting Restaurant Photo Downloader');
        $this->info('🔑 Google Places API Key: ' . substr($this->apiKey, 0, 10) . '...');
        $this->newLine();

        // Get restaurants without images but with google_place_id
        $restaurants = Restaurant::whereNotNull('google_place_id')
            ->whereNull('image')
            ->when($this->option('country'), fn($q) => $q->where('country', $this->option('country')))
            ->limit($this->option('limit'))
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info('✅ All restaurants already have photos!');
            return 0;
        }

        $this->info("📊 Found {$restaurants->count()} restaurants without photos");
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

            // Rate limiting: sleep after each batch
            if ($processed % $batchSize === 0) {
                sleep(2); // Wait 2 seconds every batch
            } else {
                usleep(500000); // Wait 0.5 seconds between each request
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
            // Get place details with photos
            $details = $this->getPlaceDetails($restaurant->google_place_id);

            if (!$details || empty($details['photo_reference'])) {
                $this->stats['no_photo']++;
                return;
            }

            // Download photo
            $photoPath = $this->downloadPhoto($details['photo_reference'], $restaurant->name);

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

    protected function getPlaceDetails(string $placeId): ?array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/details/json", [
                'place_id' => $placeId,
                'key' => $this->apiKey,
                'fields' => 'name,photos',
            ]);

            if (!$response->successful()) {
                \Log::error('HTTP request failed', ['place_id' => $placeId, 'status' => $response->status()]);
                return null;
            }

            $data = $response->json();
            if ($data['status'] !== 'OK' || !isset($data['result'])) {
                \Log::error('API status not OK', ['place_id' => $placeId, 'status' => $data['status'] ?? 'UNKNOWN']);
                return null;
            }

            $result = $data['result'];

            // Get photo reference if available
            $photoReference = null;
            if (!empty($result['photos']) && isset($result['photos'][0]['photo_reference'])) {
                $photoReference = $result['photos'][0]['photo_reference'];
            }

            if (!$photoReference) {
                \Log::info('No photo found', ['place_id' => $placeId]);
            }

            return [
                'photo_reference' => $photoReference,
            ];

        } catch (\Exception $e) {
            \Log::error('Exception in getPlaceDetails', ['place_id' => $placeId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function downloadPhoto(string $photoReference, string $restaurantName): ?string
    {
        try {
            // Get photo from Google Places Photo API
            $photoUrl = "{$this->baseUrl}/photo?" . http_build_query([
                'photoreference' => $photoReference,
                'maxwidth' => 1200,
                'key' => $this->apiKey,
            ]);

            $response = Http::timeout(30)->get($photoUrl);

            if (!$response->successful()) {
                return null;
            }

            // Generate unique filename
            $filename = Str::slug($restaurantName) . '-' . time() . '-' . rand(1000, 9999) . '.jpg';
            $path = 'restaurants/' . $filename;

            // Save to public storage
            Storage::disk('public')->put($path, $response->body());

            return $path;

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function displayStats()
    {
        $this->info('✅ Photo Download Complete!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $this->stats['processed']],
                ['Downloaded', $this->stats['downloaded']],
                ['No Photo Available', $this->stats['no_photo']],
                ['Errors', $this->stats['errors']],
            ]
        );

        $this->newLine();

        $totalWithPhotos = Restaurant::whereNotNull('image')
            ->where('image', '!=', '')
            ->count();

        $totalRestaurants = Restaurant::count();

        $this->info("📊 Total restaurants with photos: {$totalWithPhotos} / {$totalRestaurants}");
        $this->info("📈 Coverage: " . round(($totalWithPhotos / $totalRestaurants) * 100, 2) . "%");
    }
}
