<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use App\Services\GooglePlacesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportGoogleRestaurants extends Command
{
    protected $signature = 'google:import
                            {city : City name}
                            {state : State code (e.g., CMX, JAL)}
                            {--type=restaurant : Place type to search}
                            {--query= : Custom search query}
                            {--limit=20 : Maximum number of restaurants to import}
                            {--delay=2 : Delay between API calls in seconds}';

    protected $description = 'Import restaurants from Google Places for a specific city in Mexico';

    protected GooglePlacesService $googleService;
    protected int $imported = 0;
    protected int $skipped = 0;
    protected int $errors = 0;

    public function handle(GooglePlacesService $googleService): int
    {
        $this->googleService = $googleService;

        $city = $this->argument('city');
        $stateCode = $this->argument('state');
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');

        $this->info("🔍 Importing restaurants from Google Places...");
        $this->info("📍 Location: {$city}, {$stateCode}");
        $this->newLine();

        // Find state
        $state = State::where('code', strtoupper($stateCode))->first();
        if (!$state) {
            $this->error("State '{$stateCode}' not found in database");
            return Command::FAILURE;
        }

        // Find Mexican category
        $category = Category::where('slug', 'mexican')
            ->orWhere('name', 'LIKE', '%mexican%')
            ->first();

        if (!$category) {
            $this->error("Mexican category not found");
            return Command::FAILURE;
        }

        // Search for restaurants
        $query = $this->option('query') ?: "restaurantes mexicanos {$city} {$stateCode} Mexico";
        $places = $this->searchPlaces($query, $limit);

        if (empty($places)) {
            $this->warn("No places found for the search");
            return Command::SUCCESS;
        }

        $this->info("Found " . count($places) . " places");
        $bar = $this->output->createProgressBar(count($places));
        $bar->start();

        foreach ($places as $place) {
            $this->processPlace($place, $state, $category);
            $bar->advance();

            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Imported', $this->imported],
                ['⏭️  Skipped (duplicates)', $this->skipped],
                ['⚠️  Errors', $this->errors],
            ]
        );

        return Command::SUCCESS;
    }

    protected function searchPlaces(string $query, int $limit): array
    {
        $apiKey = config('services.google.places_api_key');
        $places = [];

        try {
            // Text search for restaurants
            $response = Http::get("https://maps.googleapis.com/maps/api/place/textsearch/json", [
                'query' => $query,
                'type' => 'restaurant',
                'key' => $apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                $results = $response->json('results') ?? [];
                $places = array_slice($results, 0, $limit);
            }
        } catch (\Exception $e) {
            Log::error('Google Places search error: ' . $e->getMessage());
        }

        return $places;
    }

    protected function processPlace(array $place, State $state, Category $category): void
    {
        try {
            $placeId = $place['place_id'] ?? null;
            if (!$placeId) {
                $this->errors++;
                return;
            }

            // Check for duplicate
            $existing = Restaurant::where('google_place_id', $placeId)->first();
            if ($existing) {
                $this->skipped++;
                return;
            }

            // Get full details
            $details = $this->googleService->getPlaceDetails($placeId);
            if (!$details) {
                $this->errors++;
                return;
            }

            // Parse data
            $services = $this->googleService->parseServices($details);
            $openingHours = $this->googleService->parseOpeningHours($details);

            // Extract address components
            $address = $place['formatted_address'] ?? '';
            $addressParts = $this->parseAddress($address);
            $city = $addressParts['city'] ?: $this->argument('city');
            $zipCode = $addressParts['zip_code'] ?: '00000';

            // Create restaurant
            $restaurant = Restaurant::create([
                'name' => $place['name'],
                'address' => $addressParts['street'] ?: $address,
                'city' => $city,
                'zip_code' => $zipCode,
                'state_id' => $state->id,
                'category_id' => $category->id,
                'country' => $state->country ?? 'MX',
                'latitude' => $place['geometry']['location']['lat'] ?? null,
                'longitude' => $place['geometry']['location']['lng'] ?? null,
                'google_place_id' => $placeId,
                'google_maps_url' => $details['url'] ?? null,
                'google_rating' => $place['rating'] ?? null,
                'google_reviews_count' => $place['user_ratings_total'] ?? 0,
                'google_verified' => true,
                'last_google_verification' => now(),
                'phone' => $details['formatted_phone_number'] ?? null,
                'website' => $details['website'] ?? null,
                'opening_hours' => $openingHours ? json_encode($openingHours) : null,
                'services' => !empty($services) ? json_encode($services) : null,
                'average_rating' => $place['rating'] ?? null,
                'total_reviews' => $place['user_ratings_total'] ?? 0,
                'price_range' => $this->mapPriceLevel($details['price_level'] ?? null),
                'status' => 'approved',
                'is_active' => true,
                'import_source' => 'google_places',
                'imported_at' => now(),
            ]);

            $this->imported++;
            Log::info("Imported: {$restaurant->name} in {$city}");

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Import error: " . $e->getMessage());
        }
    }

    protected function parseAddress(string $address): array
    {
        $result = [
            'street' => null,
            'city' => null,
            'zip_code' => null,
        ];

        // Mexican address format: "Street, Colonia, CP Ciudad, State, Mexico"
        // Example: "Calle Palma 23, Centro, 06000 Ciudad de México, CDMX, Mexico"
        
        $parts = array_map('trim', explode(',', $address));
        
        if (count($parts) >= 1) {
            $result['street'] = $parts[0];
        }

        // Look for zip code (5 digits)
        foreach ($parts as $part) {
            if (preg_match('/\b(\d{5})\b/', $part, $matches)) {
                $result['zip_code'] = $matches[1];
                // City is usually after the zip code
                $cityPart = preg_replace('/\b\d{5}\b/', '', $part);
                $result['city'] = trim($cityPart);
                break;
            }
        }

        // If no city found, try to get it from the parts
        if (empty($result['city']) && count($parts) >= 3) {
            $result['city'] = $parts[count($parts) - 3]; // Usually 3rd from end
        }

        return $result;
    }

    protected function mapPriceLevel(?int $level): ?string
    {
        return match($level) {
            0, 1 => '$',
            2 => '$$',
            3 => '$$$',
            4 => '$$$$',
            default => null,
        };
    }
}
