<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use App\Services\GooglePlacesService;
use App\Services\FacebookPlacesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportYelpMexico extends Command
{
    protected $signature = 'yelp:import-mexico
                            {--states=* : Mexican states to import (by code). Leave empty for all}
                            {--cities-per-state=2 : Number of major cities per state}
                            {--limit=20 : Max restaurants per city}
                            {--delay=3 : Delay between API calls}
                            {--dry-run : Show what would be imported}
                            {--enrich : Enrich with Google Places and Facebook}
                            {--yelp-categories= : Yelp categories to search (e.g., mexican,tacos,foodtrucks)}';

    protected $description = 'Import Mexican restaurants from Yelp for Mexico (uses Mexico API key with 5000/month limit)';

    protected $apiKey;
    protected $baseUrl = 'https://api.yelp.com/v3';
    protected $apiCalls = 0;
    protected $maxDailyCalls = 160;
    protected $googleService;
    protected $facebookService;

    protected array $majorCitiesByState = [
        'CMX' => ['Ciudad de Mexico', 'Coyoacan', 'Tlalpan'],
        'JAL' => ['Guadalajara', 'Zapopan', 'Tlaquepaque', 'Puerto Vallarta'],
        'NLE' => ['Monterrey', 'San Pedro Garza Garcia', 'Guadalupe'],
        'BCN' => ['Tijuana', 'Mexicali', 'Ensenada'],
        'QUE' => ['Queretaro', 'San Juan del Rio'],
        'GUA' => ['Leon', 'Guanajuato', 'Irapuato', 'Celaya'],
        'PUE' => ['Puebla', 'Cholula', 'Atlixco'],
        'YUC' => ['Merida', 'Valladolid', 'Progreso'],
        'ROO' => ['Cancun', 'Playa del Carmen', 'Tulum', 'Cozumel'],
        'SON' => ['Hermosillo', 'Ciudad Obregon', 'Nogales'],
        'CHH' => ['Chihuahua', 'Ciudad Juarez', 'Delicias'],
        'SIN' => ['Culiacan', 'Mazatlan', 'Los Mochis'],
        'VER' => ['Veracruz', 'Xalapa', 'Coatzacoalcos'],
        'OAX' => ['Oaxaca de Juarez', 'Huatulco', 'Puerto Escondido'],
        'MIC' => ['Morelia', 'Uruapan', 'Patzcuaro'],
        'TAM' => ['Tampico', 'Reynosa', 'Nuevo Laredo'],
        'COA' => ['Saltillo', 'Torreon', 'Monclova'],
        'AGU' => ['Aguascalientes'],
        'SLP' => ['San Luis Potosi'],
        'GRO' => ['Acapulco', 'Zihuatanejo', 'Taxco'],
        'NAY' => ['Tepic', 'Nuevo Vallarta'],
        'COL' => ['Colima', 'Manzanillo'],
        'HID' => ['Pachuca'],
        'MOR' => ['Cuernavaca', 'Tepoztlan'],
        'MEX' => ['Toluca', 'Metepec'],
        'DUR' => ['Durango'],
        'ZAC' => ['Zacatecas'],
        'TLA' => ['Tlaxcala'],
        'CAM' => ['Campeche', 'Ciudad del Carmen'],
        'TAB' => ['Villahermosa'],
        'CHP' => ['Tuxtla Gutierrez', 'San Cristobal de las Casas'],
        'BCS' => ['La Paz', 'Cabo San Lucas', 'San Jose del Cabo'],
    ];

    public function handle()
    {
        $this->apiKey = config('services.yelp.api_key_mexico');
        
        if (empty($this->apiKey)) {
            $this->error('YELP_API_KEY_MEXICO not configured in .env');
            return 1;
        }

        $this->googleService = app(GooglePlacesService::class);
        $this->facebookService = app(FacebookPlacesService::class);

        $this->info('🇲🇽 Starting Yelp import for MEXICO');
        $this->info("API Limit: {$this->maxDailyCalls} calls/day (5000/month)");
        $this->info("Enrichment: Google Places + Facebook");
        $this->newLine();

        $stateCodes = $this->option('states');
        $citiesPerState = (int) $this->option('cities-per-state');
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');
        $dryRun = $this->option('dry-run');
        $enrich = $this->option('enrich');
        $categories = $this->option('yelp-categories') ?: 'mexican,restaurants,tacos,seafood';

        $states = State::where('country', 'MX')
            ->where('is_active', true)
            ->when(!empty($stateCodes), fn($q) => $q->whereIn('code', $stateCodes))
            ->get();

        if ($states->isEmpty()) {
            $this->error('No Mexican states found in database');
            return 1;
        }

        $this->info("Processing {$states->count()} Mexican states");

        $totalImported = 0;
        $totalSkipped = 0;
        $totalEnriched = 0;

        foreach ($states as $state) {
            if ($this->apiCalls >= $this->maxDailyCalls) {
                $this->warn("⚠️ Daily API limit reached ({$this->maxDailyCalls} calls). Stopping.");
                break;
            }

            $cities = $this->majorCitiesByState[$state->code] ?? [$state->name];
            $cities = array_slice($cities, 0, $citiesPerState);

            $this->info("📍 {$state->name} ({$state->code}): " . implode(', ', $cities));

            foreach ($cities as $city) {
                if ($this->apiCalls >= $this->maxDailyCalls) break;

                $result = $this->importFromCity($city, $state, $limit, $dryRun, $enrich, $categories);
                $totalImported += $result['imported'];
                $totalSkipped += $result['skipped'];
                $totalEnriched += $result['enriched'];

                sleep($delay);
            }
        }

        $this->newLine();
        $this->info("✅ Import complete!");
        $this->info("   API calls used: {$this->apiCalls}");
        $this->info("   Restaurants imported: {$totalImported}");
        $this->info("   Enriched (Google/FB): {$totalEnriched}");
        $this->info("   Duplicates skipped: {$totalSkipped}");

        return 0;
    }

    protected function importFromCity(string $city, State $state, int $limit, bool $dryRun, bool $enrich, string $categories = 'mexican,restaurants,tacos,seafood'): array
    {
        $stats = ['imported' => 0, 'skipped' => 0, 'enriched' => 0];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get("{$this->baseUrl}/businesses/search", [
                'term' => 'restaurant',
                'location' => "{$city}, {$state->name}, Mexico",
                'categories' => $categories,
                'limit' => min($limit, 50),
                'sort_by' => 'rating',
            ]);

            $this->apiCalls++;

            if (!$response->successful()) {
                $error = $response->json()['error'] ?? [];
                $this->error("   ❌ API Error for {$city}: " . ($error['code'] ?? 'Unknown'));
                Log::error('Yelp Mexico API error', ['city' => $city, 'response' => $response->json()]);
                return $stats;
            }

            $businesses = $response->json()['businesses'] ?? [];
            $this->line("   Found " . count($businesses) . " restaurants in {$city}");

            foreach ($businesses as $business) {
                if ($dryRun) {
                    $this->line("   [DRY-RUN] Would import: {$business['name']}");
                    continue;
                }

                // Check for duplicates by Yelp ID
                $exists = Restaurant::where('yelp_id', $business['id'])->exists();
                if ($exists) {
                    $stats['skipped']++;
                    continue;
                }

                // Check by name and coordinates
                $lat = $business['coordinates']['latitude'] ?? 0;
                $lng = $business['coordinates']['longitude'] ?? 0;
                
                if ($lat && $lng) {
                    $exists = Restaurant::where('name', $business['name'])
                        ->where('state_id', $state->id)
                        ->whereRaw('ABS(latitude - ?) < 0.001 AND ABS(longitude - ?) < 0.001', [$lat, $lng])
                        ->exists();
                    
                    if ($exists) {
                        $stats['skipped']++;
                        continue;
                    }
                }

                $restaurant = $this->createRestaurant($business, $state, $city);
                
                if ($restaurant) {
                    $stats['imported']++;
                    
                    if ($enrich) {
                        $enriched = $this->enrichRestaurant($restaurant);
                        if ($enriched) $stats['enriched']++;
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error("   Exception: {$e->getMessage()}");
            Log::error('Yelp Mexico import exception', ['city' => $city, 'error' => $e->getMessage()]);
        }

        return $stats;
    }

    protected function createRestaurant(array $business, State $state, string $city): ?Restaurant
    {
        try {
            $category = Category::where('slug', 'mexican-restaurant')->first();
            
            // Extract Yelp categories
            $yelpCategories = collect($business['categories'] ?? [])
                ->pluck('alias')
                ->implode(',');
            
            // Extract transactions (delivery, pickup, etc.)
            $transactions = implode(',', $business['transactions'] ?? []);
            
            return Restaurant::create([
                'name' => $business['name'],
                'slug' => Str::slug($business['name'] . '-' . $city . '-' . Str::random(4)),
                'address' => $business['location']['address1'] ?? '',
                'city' => $business['location']['city'] ?? $city,
                'state_id' => $state->id,
                'zip_code' => $business['location']['zip_code'] ?? '',
                'country' => 'MX',
                'phone' => $business['phone'] ?? null,
                'latitude' => $business['coordinates']['latitude'] ?? null,
                'longitude' => $business['coordinates']['longitude'] ?? null,
                // Yelp data
                'yelp_id' => $business['id'],
                'yelp_url' => $business['url'] ?? null,
                'yelp_rating' => $business['rating'] ?? null,
                'yelp_reviews_count' => $business['review_count'] ?? 0,
                'yelp_categories' => $yelpCategories,
                'yelp_transactions' => $transactions,
                'yelp_last_sync' => now(),
                // Image
                'image' => $business['image_url'] ?? null,
                // Category
                'category_id' => $category?->id ?? 17,
                // Status
                'is_verified' => false,
                'status' => 'approved',
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create restaurant', ['business' => $business['name'], 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function enrichRestaurant(Restaurant $restaurant): bool
    {
        $enriched = false;
        
        // Enrich with Google Places
        try {
            $googleData = $this->googleService->findPlace(
                $restaurant->name,
                $restaurant->address ?? '',
                $restaurant->city,
                $restaurant->state->name ?? ''
            );

            if ($googleData && isset($googleData['place_id'])) {
                $placeDetails = $this->googleService->getPlaceDetails($googleData['place_id']);
                
                $restaurant->update([
                    'google_place_id' => $googleData['place_id'],
                    'google_rating' => $placeDetails['rating'] ?? $googleData['rating'] ?? null,
                    'google_reviews_count' => $placeDetails['user_ratings_total'] ?? null,
                    'website' => $placeDetails['website'] ?? $restaurant->website,
                    'google_maps_url' => $placeDetails['url'] ?? null,
                    'google_price_level' => $placeDetails['price_level'] ?? null,
                    'google_photos_count' => isset($placeDetails['photos']) ? count($placeDetails['photos']) : null,
                ]);
                $enriched = true;
                $this->line("     ✓ Google: {$restaurant->name}");
            }
        } catch (\Exception $e) {
            // Silently fail Google enrichment
        }

        // Enrich with Facebook
        try {
            $fbData = $this->facebookService->searchPlace(
                $restaurant->name,
                $restaurant->latitude,
                $restaurant->longitude
            );

            if ($fbData && isset($fbData['id'])) {
                $restaurant->update([
                    'facebook_page_id' => $fbData['id'],
                    'facebook_url' => $fbData['link'] ?? "https://facebook.com/{$fbData['id']}",
                    'facebook_rating' => $fbData['overall_star_rating'] ?? null,
                    'facebook_review_count' => $fbData['rating_count'] ?? null,
                    'facebook_enriched_at' => now(),
                ]);
                $enriched = true;
                $this->line("     ✓ Facebook: {$restaurant->name}");
            }
        } catch (\Exception $e) {
            // Silently fail Facebook enrichment
        }

        return $enriched;
    }
}
