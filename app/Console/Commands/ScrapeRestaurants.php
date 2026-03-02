<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeRestaurants extends Command
{
    protected $signature = 'scrape:restaurants
                            {--state= : Specific state code (e.g., CA, TX)}
                            {--city= : Specific city name (e.g., Los Angeles, Houston)}
                            {--limit=50 : Number of results per search}
                            {--dry-run : Preview without saving}';

    protected $description = 'Scrape Mexican restaurants from Google Places API';

    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/place';
    protected $defaultCategory;
    protected $stats = [
        'processed' => 0,
        'created' => 0,
        'duplicates' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->apiKey = config('services.google.places_api_key');

        if (empty($this->apiKey)) {
            $this->error('❌ Google Places API key not found in config/services.php');
            $this->info('Add: GOOGLE_PLACES_API_KEY=your_key to .env');
            return 1;
        }

        $this->defaultCategory = Category::firstOrCreate(
            ['slug' => 'mexican-restaurant'],
            ['name' => 'Mexican Restaurant', 'description' => 'Traditional Mexican Restaurant', 'is_active' => true]
        );

        $this->info('🚀 Starting Mexican Restaurant Scraper');
        $this->info('📍 Google Places API Key: ' . substr($this->apiKey, 0, 10) . '...');
        $this->newLine();

        // Check if city search is requested
        if ($city = $this->option('city')) {
            return $this->scrapeByCity($city);
        }

        // Get states to scrape
        $states = $this->option('state')
            ? State::where('code', strtoupper($this->option('state')))->get()
            : State::orderBy('name')->get();

        if ($states->isEmpty()) {
            $this->error('❌ No states found');
            return 1;
        }

        $this->info("📊 Will scrape {$states->count()} state(s)");
        $this->newLine();

        // Progress bar
        $progressBar = $this->output->createProgressBar($states->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');

        foreach ($states as $state) {
            $progressBar->setMessage("Processing: {$state->name}");
            $progressBar->advance();

            $this->scrapeState($state);

            // Rate limiting: wait 2 seconds between states
            sleep(2);
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->displayStats();

        return 0;
    }

    protected function scrapeState(State $state)
    {
        $queries = [
            "mexican restaurant in {$state->name}",
            "taqueria in {$state->name}",
            "mexican food {$state->name}",
        ];

        foreach ($queries as $query) {
            $this->searchPlaces($query, $state);
            sleep(1); // Rate limiting between queries
        }
    }

    protected function searchPlaces(string $query, State $state)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/textsearch/json", [
                'query' => $query,
                'key' => $this->apiKey,
                'type' => 'restaurant',
                'language' => 'en',
            ]);

            if (!$response->successful()) {
                $this->stats['errors']++;
                return;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                if ($data['status'] === 'ZERO_RESULTS') {
                    return; // No results, continue
                }
                $this->warn("⚠️  API Status: {$data['status']} for query: {$query}");
                $this->stats['errors']++;
                return;
            }

            $results = $data['results'] ?? [];
            $limit = min(count($results), $this->option('limit'));

            for ($i = 0; $i < $limit; $i++) {
                $place = $results[$i];
                $this->processPlace($place, $state);
            }

        } catch (\Exception $e) {
            $this->error("❌ Error searching places: {$e->getMessage()}");
            $this->stats['errors']++;
        }
    }

    protected function processPlace(array $place, State $state)
    {
        $this->stats['processed']++;

        $placeId = $place['place_id'] ?? null;
        if (!$placeId) {
            $this->stats['errors']++;
            return;
        }

        // Get detailed information
        try {
            $details = $this->getPlaceDetails($placeId);

            if (!$details) {
                $this->stats['errors']++;
                return;
            }

            // Check for duplicates
            $exists = Restaurant::where('google_place_id', $placeId)
                ->orWhere(function($query) use ($details) {
                    $query->where('name', $details['name'])
                          ->where('address', $details['address']);
                })
                ->exists();

            if ($exists) {
                $this->stats['duplicates']++;
                return;
            }

            // Dry run check
            if ($this->option('dry-run')) {
                $this->line("🔍 Would create: {$details['name']} - {$details['city']}, {$state->code}");
                $this->stats['created']++;
                return;
            }

            // Create restaurant
            $this->createRestaurant($details, $state);
            $this->stats['created']++;

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
                'fields' => 'name,formatted_address,formatted_phone_number,website,geometry,rating,price_level,address_components,place_id,types,photos',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            if ($data['status'] !== 'OK' || !isset($data['result'])) {
                return null;
            }

            $result = $data['result'];
            $addressComponents = $result['address_components'] ?? [];

            // Get photo reference if available
            $photoReference = null;
            if (!empty($result['photos']) && isset($result['photos'][0]['photo_reference'])) {
                $photoReference = $result['photos'][0]['photo_reference'];
            }

            return [
                'name' => $result['name'] ?? 'Unknown',
                'address' => $this->extractStreetAddress($result['formatted_address'] ?? ''),
                'city' => $this->extractComponent($addressComponents, 'locality'),
                'zip_code' => $this->extractComponent($addressComponents, 'postal_code'),
                'phone' => $result['formatted_phone_number'] ?? null,
                'website' => $result['website'] ?? null,
                'latitude' => $result['geometry']['location']['lat'] ?? null,
                'longitude' => $result['geometry']['location']['lng'] ?? null,
                'google_place_id' => $placeId,
                'average_rating' => $result['rating'] ?? 0,
                'price_range' => $this->convertPriceLevel($result['price_level'] ?? null),
                'photo_reference' => $photoReference,
            ];

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function createRestaurant(array $details, State $state)
    {
        // Download and save photo if available
        $photoPath = null;
        if (!empty($details['photo_reference'])) {
            $photoPath = $this->downloadPhoto($details['photo_reference'], $details['name']);
        }

        Restaurant::create([
            'name' => $details['name'],
            'slug' => Str::slug($details['name'] . '-' . $details['city'] . '-' . $state->code),
            'address' => $details['address'],
            'city' => $details['city'] ?: 'Unknown',
            'state_id' => $state->id,
            'zip_code' => $details['zip_code'],
            'phone' => $details['phone'],
            'email' => null,
            'website' => $details['website'],
            'description' => $this->generateDescription($details, $state),
            'category_id' => $this->defaultCategory->id,
            'latitude' => $details['latitude'],
            'longitude' => $details['longitude'],
            'google_place_id' => $details['google_place_id'],
            'google_verified' => true,
            'average_rating' => $details['average_rating'],
            'price_range' => $details['price_range'],
            'status' => 'approved',
            'is_featured' => false,
            'image' => $photoPath,
        ]);
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
            $filename = Str::slug($restaurantName) . '-' . time() . '.jpg';
            $path = 'restaurants/' . $filename;

            // Save to public storage
            Storage::disk('public')->put($path, $response->body());

            return $path;

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function extractStreetAddress(string $fullAddress): string
    {
        // Extract just street address (before city)
        $parts = explode(',', $fullAddress);
        return trim($parts[0] ?? $fullAddress);
    }

    protected function convertPriceLevel(?int $level): ?string
    {
        return match($level) {
            1 => '$',
            2 => '$$',
            3 => '$$$',
            4 => '$$$$',
            default => '$$',
        };
    }

    protected function generateDescription(array $details, State $state): string
    {
        $templates = [
            "Discover authentic Mexican flavors at {name} in {city}, {state}. A beloved local spot serving delicious traditional cuisine.",
            "{name} brings the taste of Mexico to {city}, {state}. Experience genuine Mexican hospitality and flavorful dishes.",
            "Located in {city}, {state}, {name} is your destination for authentic Mexican food. Fresh ingredients and traditional recipes.",
            "Experience the vibrant flavors of Mexico at {name} in {city}. A favorite among locals for its authentic Mexican cuisine.",
            "{name} in {city}, {state} serves up traditional Mexican dishes with a passion for authentic flavors and quality ingredients.",
            "Craving authentic Mexican food? Visit {name} in {city}, {state} for a true taste of Mexico.",
            "Family-friendly {name} offers delicious Mexican cuisine in the heart of {city}, {state}. Fresh, flavorful, and authentic.",
            "At {name}, located in {city}, {state}, every dish celebrates the rich culinary traditions of Mexico.",
            "{name} is {city}'s go-to spot for authentic Mexican food. Serving traditional favorites with fresh, quality ingredients.",
            "Welcome to {name} in {city}, {state} - where traditional Mexican recipes meet warm, friendly service.",
        ];

        $template = $templates[array_rand($templates)];

        return str_replace(
            ['{name}', '{city}', '{state}'],
            [$details['name'], $details['city'] ?: 'Unknown', $state->name],
            $template
        );
    }

    protected function scrapeByCity(string $city)
    {
        // Get state if provided
        $state = null;
        if ($stateCode = $this->option('state')) {
            $state = State::where('code', strtoupper($stateCode))->first();
            if (!$state) {
                $this->error("❌ State not found: {$stateCode}");
                return 1;
            }
            $this->info("📍 Scraping: {$city}, {$state->name}");
        } else {
            $this->info("📍 Scraping: {$city}");
        }

        $this->newLine();

        // Search queries for the city
        $queries = [
            "mexican restaurant in {$city}",
            "taqueria in {$city}",
            "mexican food {$city}",
            "authentic mexican restaurant {$city}",
        ];

        foreach ($queries as $query) {
            if ($state) {
                $this->searchPlaces($query, $state);
            } else {
                // Try to find state from results
                $this->searchPlacesWithoutState($query);
            }
            sleep(1);
        }

        $this->newLine();
        $this->displayStats();

        return 0;
    }

    protected function searchPlacesWithoutState(string $query)
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/textsearch/json", [
                'query' => $query,
                'key' => $this->apiKey,
                'type' => 'restaurant',
                'language' => 'en',
            ]);

            if (!$response->successful()) {
                $this->stats['errors']++;
                return;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK') {
                if ($data['status'] !== 'ZERO_RESULTS') {
                    $this->warn("⚠️  API Status: {$data['status']} for query: {$query}");
                    $this->stats['errors']++;
                }
                return;
            }

            $results = $data['results'] ?? [];
            $limit = min(count($results), $this->option('limit'));

            for ($i = 0; $i < $limit; $i++) {
                $place = $results[$i];

                // Get details to find state
                $details = $this->getPlaceDetails($place['place_id'] ?? null);
                if (!$details) {
                    $this->stats['errors']++;
                    continue;
                }

                // Try to match state from address components
                $stateCode = $this->extractComponent($details['address_components'] ?? [], 'administrative_area_level_1', true);
                if ($stateCode) {
                    $state = State::where('code', $stateCode)->first();
                    if ($state) {
                        $this->processPlace($place, $state);
                        continue;
                    }
                }

                $this->stats['errors']++;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error searching places: {$e->getMessage()}");
            $this->stats['errors']++;
        }
    }

    protected function extractComponent(array $components, string $type, bool $shortName = false): ?string
    {
        foreach ($components as $component) {
            if (in_array($type, $component['types'])) {
                return $shortName ? $component['short_name'] : $component['long_name'];
            }
        }
        return null;
    }

    protected function displayStats()
    {
        $this->info('✅ Scraping Complete!');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $this->stats['processed']],
                ['Created', $this->stats['created']],
                ['Duplicates', $this->stats['duplicates']],
                ['Errors', $this->stats['errors']],
            ]
        );

        $this->newLine();
        $this->info("📊 Total restaurants in database: " . Restaurant::count());
    }
}
