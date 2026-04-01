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
                            {--country= : Filter by country code (US, MX)}
                            {--rephoto : Force re-download even if photos column is already populated}';

    protected $description = 'Download up to 5 photos per restaurant from Google Places API';

    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/place';
    protected $stats = [
        'processed' => 0,
        'downloaded' => 0,
        'skipped' => 0,
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

        $this->info('📸 Starting Restaurant Photo Downloader (multi-photo mode)');
        $this->info('🔑 Google Places API Key: ' . substr($this->apiKey, 0, 10) . '...');
        $this->newLine();

        $rephoto = $this->option('rephoto');

        // Build query
        $query = Restaurant::whereNotNull('google_place_id')
            ->when($this->option('country'), fn($q) => $q->where('country', $this->option('country')));

        if (!$rephoto) {
            // Skip restaurants that already have photos column populated
            $query->where(function ($q) {
                $q->whereNull('photos')
                  ->orWhere('photos', '[]')
                  ->orWhere('photos', '');
            });
            // Also skip those without a main image (no photos available anyway)
            $query->whereNull('image');
        }

        $restaurants = $query->limit($this->option('limit'))->get();

        if ($restaurants->isEmpty()) {
            $this->info('✅ All restaurants already have photos! Use --rephoto to force re-download.');
            return 0;
        }

        $this->info("📊 Found {$restaurants->count()} restaurants to process");
        if ($rephoto) {
            $this->warn('⚠️  --rephoto flag active: will overwrite existing photos');
        }
        $this->newLine();

        $progressBar = $this->output->createProgressBar($restaurants->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');

        $batchSize = $this->option('batch');
        $processed = 0;

        foreach ($restaurants as $restaurant) {
            $progressBar->setMessage("Processing: {$restaurant->name}");
            $progressBar->advance();

            $this->processRestaurant($restaurant, $rephoto);
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

    protected function processRestaurant(Restaurant $restaurant, bool $rephoto = false)
    {
        try {
            // Skip if already has photos and not forcing rephoto
            if (!$rephoto && !empty($restaurant->photos) && count($restaurant->photos) > 0) {
                $this->stats['skipped']++;
                return;
            }

            // Get place details with up to 5 photo references
            $photoReferences = $this->getPhotoReferences($restaurant->google_place_id);

            if (empty($photoReferences)) {
                $this->stats['no_photo']++;
                return;
            }

            $downloadedPaths = [];
            $photoIndex = 1;

            foreach ($photoReferences as $photoRef) {
                $filename = $restaurant->slug . '-' . $photoIndex . '.jpg';
                $path = 'restaurants/' . $filename;

                $downloaded = $this->downloadPhotoToPath($photoRef, $path);

                if ($downloaded) {
                    $downloadedPaths[] = $path;

                    // First photo = main image (keep existing logic)
                    if ($photoIndex === 1 && empty($restaurant->image)) {
                        $restaurant->image = $path;
                    }

                    $photoIndex++;
                }

                if ($photoIndex > 5) {
                    break;
                }
            }

            if (!empty($downloadedPaths)) {
                $restaurant->photos = $downloadedPaths;
                $restaurant->save();
                $this->stats['downloaded']++;
            } else {
                $this->stats['errors']++;
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
            \Log::error('DownloadRestaurantPhotos error', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function getPhotoReferences(string $placeId): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/details/json", [
                'place_id' => $placeId,
                'key' => $this->apiKey,
                'fields' => 'name,photos',
            ]);

            if (!$response->successful()) {
                \Log::error('HTTP request failed', ['place_id' => $placeId, 'status' => $response->status()]);
                return [];
            }

            $data = $response->json();
            if ($data['status'] !== 'OK' || !isset($data['result'])) {
                \Log::error('API status not OK', ['place_id' => $placeId, 'status' => $data['status'] ?? 'UNKNOWN']);
                return [];
            }

            $result = $data['result'];

            if (empty($result['photos'])) {
                return [];
            }

            // Extract up to 5 photo references
            return array_column(array_slice($result['photos'], 0, 5), 'photo_reference');

        } catch (\Exception $e) {
            \Log::error('Exception in getPhotoReferences', ['place_id' => $placeId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    protected function downloadPhotoToPath(string $photoReference, string $storagePath): bool
    {
        try {
            $photoUrl = "{$this->baseUrl}/photo?" . http_build_query([
                'photoreference' => $photoReference,
                'maxwidth' => 1200,
                'key' => $this->apiKey,
            ]);

            $response = Http::timeout(30)->get($photoUrl);

            if (!$response->successful()) {
                return false;
            }

            Storage::disk('public')->put($storagePath, $response->body());

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    // Legacy method kept for backward compatibility
    protected function getPlaceDetails(string $placeId): ?array
    {
        $refs = $this->getPhotoReferences($placeId);
        return ['photo_reference' => $refs[0] ?? null];
    }

    // Legacy method kept for backward compatibility
    protected function downloadPhoto(string $photoReference, string $restaurantName): ?string
    {
        $filename = Str::slug($restaurantName) . '-' . time() . '-' . rand(1000, 9999) . '.jpg';
        $path = 'restaurants/' . $filename;
        return $this->downloadPhotoToPath($photoReference, $path) ? $path : null;
    }

    protected function displayStats()
    {
        $this->info('✅ Photo Download Complete!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $this->stats['processed']],
                ['Downloaded (multi-photo)', $this->stats['downloaded']],
                ['Skipped (already had photos)', $this->stats['skipped']],
                ['No Photo Available', $this->stats['no_photo']],
                ['Errors', $this->stats['errors']],
            ]
        );

        $this->newLine();

        $totalWithPhotos = Restaurant::whereNotNull('image')
            ->where('image', '!=', '')
            ->count();

        $totalWithGallery = Restaurant::whereNotNull('photos')
            ->where('photos', '!=', '[]')
            ->where('photos', '!=', '')
            ->count();

        $totalRestaurants = Restaurant::count();

        $this->info("📊 Total restaurants with main image: {$totalWithPhotos} / {$totalRestaurants}");
        $this->info("🖼️  Total restaurants with photo gallery: {$totalWithGallery} / {$totalRestaurants}");
        $this->info("📈 Coverage: " . round(($totalWithPhotos / $totalRestaurants) * 100, 2) . "%");
    }
}
