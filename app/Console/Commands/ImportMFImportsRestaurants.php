<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use App\Services\GooglePlacesService;
use App\Services\YelpFusionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportMFImportsRestaurants extends Command
{
    protected $signature = 'import:mf-restaurants
                            {--file=archivo_maestro_actualizado_final.csv : CSV file to import}
                            {--limit=100 : Maximum number of restaurants to process}
                            {--skip-existing : Skip restaurants that already exist}
                            {--enrich-google : Enrich with Google Places data}
                            {--enrich-yelp : Enrich with Yelp data}
                            {--dry-run : Show what would be imported without actually importing}
                            {--state= : Filter by state code (e.g., TX, CA)}
                            {--with-email : Only process restaurants with valid email}
                            {--test : Test mode - process only 5 restaurants}';

    protected $description = 'Import restaurants from MF Imports CSV file and enrich with Google/Yelp data';

    protected GooglePlacesService $googleService;
    protected YelpFusionService $yelpService;
    protected array $stateCache = [];
    protected int $imported = 0;
    protected int $skipped = 0;
    protected int $enrichedGoogle = 0;
    protected int $enrichedYelp = 0;
    protected int $errors = 0;

    public function handle(GooglePlacesService $googleService, YelpFusionService $yelpService): int
    {
        $this->googleService = $googleService;
        $this->yelpService = $yelpService;

        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }

        $this->info("=== MF Imports Restaurant Importer ===");
        $this->newLine();

        // Load state cache
        $this->loadStateCache();

        // Parse CSV
        $restaurants = $this->parseCsv($file);
        $this->info("Total records in CSV: " . count($restaurants));

        // Filter valid records
        $restaurants = $this->filterValidRecords($restaurants);
        $this->info("Valid records after filtering: " . count($restaurants));

        // Apply state filter if specified
        if ($stateFilter = $this->option('state')) {
            $restaurants = array_filter($restaurants, fn($r) =>
                strtoupper($r['state']) === strtoupper($stateFilter)
            );
            $this->info("Records matching state {$stateFilter}: " . count($restaurants));
        }

        // Filter to only restaurants with valid email
        if ($this->option('with-email')) {
            $restaurants = array_filter($restaurants, fn($r) =>
                !empty($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL)
            );
            $this->info("Records with valid email: " . count($restaurants));
        }

        // Apply limit
        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = array_slice($restaurants, 0, $limit);
        $this->info("Processing: {$limit} restaurants");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Process restaurants
        $bar = $this->output->createProgressBar(count($restaurants));
        $bar->start();

        foreach ($restaurants as $data) {
            $this->processRestaurant($data);
            $bar->advance();

            // Rate limiting for API calls
            if ($this->option('enrich-google') || $this->option('enrich-yelp')) {
                usleep(300000); // 0.3 seconds between API calls
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->showSummary();

        return Command::SUCCESS;
    }

    protected function loadStateCache(): void
    {
        $states = State::all();
        foreach ($states as $state) {
            $this->stateCache[strtoupper($state->code)] = $state->id;
            $this->stateCache[strtoupper($state->name)] = $state->id;
        }
        $this->info("Loaded " . count($this->stateCache) . " state mappings");
    }

    protected function parseCsv(string $file): array
    {
        $restaurants = [];
        $handle = fopen($file, 'r');

        if (!$handle) {
            return [];
        }

        // Skip BOM if present and read header
        $header = fgetcsv($handle);
        if ($header && isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        }

        // Map columns
        $columnMap = [
            'strNombre' => 'first_name',
            'strApellido' => 'last_name',
            'strRestaurant' => 'restaurant_name',
            'strDireccion' => 'address',
            'strCiudad' => 'city',
            'strEstado' => 'state',
            'strCP' => 'zip',
            'strEmail' => 'email',
            'strtelefono' => 'phone',
            'Formato' => 'format',
        ];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($header)) {
                continue;
            }

            $data = [];
            foreach ($header as $i => $col) {
                $key = $columnMap[$col] ?? $col;
                $data[$key] = trim($row[$i] ?? '');
            }

            $restaurants[] = $data;
        }

        fclose($handle);
        return $restaurants;
    }

    protected function filterValidRecords(array $restaurants): array
    {
        return array_filter($restaurants, function ($r) {
            // Must have a restaurant name
            if (empty($r['restaurant_name']) || strlen($r['restaurant_name']) < 3) {
                return false;
            }

            // Name should not be just numbers
            if (preg_match('/^\d+$/', $r['restaurant_name'])) {
                return false;
            }

            // Must have a city
            if (empty($r['city']) || strlen($r['city']) < 2) {
                return false;
            }

            // Skip test/placeholder data
            $testPatterns = ['test', 'prueba', 'ejemplo', 'sample', 'demo', 'tu restaurante', 'your restaurant', 'my restaurant'];
            foreach ($testPatterns as $pattern) {
                if (stripos($r['restaurant_name'], $pattern) !== false) {
                    return false;
                }
            }

            // Skip fake emails
            $fakeEmailPatterns = ['@email.tst', '@fsdf.com', '@test.com', '@ejemplo.com'];
            foreach ($fakeEmailPatterns as $pattern) {
                if (stripos($r['email'] ?? '', $pattern) !== false) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function processRestaurant(array $data): void
    {
        try {
            $name = $this->cleanRestaurantName($data['restaurant_name']);
            $city = $this->cleanCity($data['city']);
            $stateCode = $this->normalizeState($data['state']);
            $stateId = $this->stateCache[strtoupper($stateCode)] ?? null;

            // Skip if state not found
            if (!$stateId) {
                $this->skipped++;
                return;
            }

            // Check if restaurant already exists
            if ($this->option('skip-existing')) {
                $exists = Restaurant::where('name', 'like', $name)
                    ->where('city', 'like', $city)
                    ->where('state_id', $stateId)
                    ->exists();

                if ($exists) {
                    $this->skipped++;
                    return;
                }
            }

            if ($this->option('dry-run')) {
                $this->imported++;
                return;
            }

            // Create restaurant
            $restaurant = new Restaurant([
                'name' => $name,
                'slug' => Str::slug($name . '-' . $city . '-' . Str::random(4)),
                'address' => $data['address'] ?? null,
                'city' => $city,
                'state_id' => $stateId,
                'zip_code' => $this->cleanZip($data['zip']),
                'phone' => $this->cleanPhone($data['phone']),
                'email' => $this->validateEmail($data['email']) ? $data['email'] : null,
                'category_id' => 17, // Default: Mexican Restaurant
                'status' => 'pending', // Pending until enriched/verified
                'import_source' => 'mf_imports',
                'imported_at' => now(),
            ]);

            // Enrich with Google Places
            if ($this->option('enrich-google')) {
                $this->enrichWithGoogle($restaurant, $data);
            }

            // Enrich with Yelp
            if ($this->option('enrich-yelp')) {
                $this->enrichWithYelp($restaurant, $data);
            }

            // If enriched with either source, mark as approved
            if ($restaurant->google_place_id || $restaurant->yelp_id) {
                $restaurant->status = 'approved';
            }

            $restaurant->save();
            $this->imported++;

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error importing restaurant: {$data['restaurant_name']}", [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    protected function enrichWithGoogle(Restaurant $restaurant, array $data): void
    {
        try {
            $place = $this->googleService->findPlace(
                $restaurant->name,
                $data['address'] ?? '',
                $restaurant->city,
                $this->getStateCode($restaurant->state_id)
            );

            if ($place && isset($place['place_id'])) {
                $details = $this->googleService->getPlaceDetails($place['place_id']);

                if ($details) {
                    $restaurant->google_place_id = $place['place_id'];
                    $restaurant->google_maps_url = $details['url'] ?? null;
                    $restaurant->latitude = $place['geometry']['location']['lat'] ?? null;
                    $restaurant->longitude = $place['geometry']['location']['lng'] ?? null;
                    $restaurant->google_rating = $details['rating'] ?? null;
                    $restaurant->google_reviews_count = $details['user_ratings_total'] ?? 0;
                    $restaurant->average_rating = $details['rating'] ?? null;
                    $restaurant->total_reviews = $details['user_ratings_total'] ?? 0;

                    // Update phone/website if not set
                    if (empty($restaurant->phone) && !empty($details['formatted_phone_number'])) {
                        $restaurant->phone = $details['formatted_phone_number'];
                    }
                    if (empty($restaurant->website) && !empty($details['website'])) {
                        $restaurant->website = $details['website'];
                    }

                    $this->enrichedGoogle++;
                }
            }
        } catch (\Exception $e) {
            Log::warning("Google enrichment failed for {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function enrichWithYelp(Restaurant $restaurant, array $data): void
    {
        try {
            $result = $this->yelpService->searchBusiness(
                $restaurant->name,
                $restaurant->city,
                $this->getStateCode($restaurant->state_id)
            );

            if ($result && ($result['verified'] ?? false)) {
                $restaurant->yelp_id = $result['yelp_id'];
                $restaurant->yelp_url = $result['url'] ?? null;
                $restaurant->yelp_rating = $result['rating'] ?? null;
                $restaurant->yelp_reviews_count = $result['review_count'] ?? 0;

                // Use Yelp rating if no Google rating
                if (!$restaurant->average_rating && $result['rating']) {
                    $restaurant->average_rating = $result['rating'];
                    $restaurant->total_reviews = $result['review_count'] ?? 0;
                }

                // Update phone if not set
                if (empty($restaurant->phone) && !empty($result['phone'])) {
                    $restaurant->phone = $result['phone'];
                }

                $this->enrichedYelp++;
            }
        } catch (\Exception $e) {
            Log::warning("Yelp enrichment failed for {$restaurant->name}: {$e->getMessage()}");
        }
    }

    protected function cleanRestaurantName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);
        return Str::title($name);
    }

    protected function cleanCity(string $city): string
    {
        $city = trim($city);
        $city = preg_replace('/\s+/', ' ', $city);
        return Str::title($city);
    }

    protected function normalizeState(string $state): string
    {
        $state = strtoupper(trim($state));

        // Common state name mappings
        $stateMap = [
            'TEXAS' => 'TX',
            'CALIFORNIA' => 'CA',
            'FLORIDA' => 'FL',
            'NEW YORK' => 'NY',
            'ILLINOIS' => 'IL',
            'ARIZONA' => 'AZ',
            'NEVADA' => 'NV',
            'COLORADO' => 'CO',
            'GEORGIA' => 'GA',
            'NEW MEXICO' => 'NM',
            'NORTH CAROLINA' => 'NC',
            'VIRGINIA' => 'VA',
            'WASHINGTON' => 'WA',
            'OREGON' => 'OR',
            'OHIO' => 'OH',
            'MICHIGAN' => 'MI',
            'PENNSYLVANIA' => 'PA',
            'NEW JERSEY' => 'NJ',
            'MARYLAND' => 'MD',
            'MASSACHUSETTS' => 'MA',
        ];

        return $stateMap[$state] ?? $state;
    }

    protected function getStateCode(int $stateId): string
    {
        foreach ($this->stateCache as $code => $id) {
            if ($id === $stateId && strlen($code) === 2) {
                return $code;
            }
        }
        return '';
    }

    protected function cleanZip(string $zip): ?string
    {
        $zip = preg_replace('/[^0-9]/', '', $zip);
        if (strlen($zip) >= 5) {
            return substr($zip, 0, 5);
        }
        return null;
    }

    protected function cleanPhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) >= 10) {
            return $phone;
        }
        return null;
    }

    protected function validateEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        // Extract domain
        $parts = explode('@', strtolower($email));
        if (count($parts) !== 2) {
            return false;
        }

        $domain = $parts[1];

        // Fake/placeholder domain patterns
        $fakeDomains = [
            'restaurant.com',
            'rest.com',
            'restaurantes.com',
            'restaurante.com',
            'restaurants.com',
            'restaur.com',
            'casa.com',
            'imports.com',
        ];

        // Check exact matches
        if (in_array($domain, $fakeDomains)) {
            return false;
        }

        // Check if domain contains "restaur" (catches typos like restaurnat.com, restuaurant.com, etc.)
        if (strpos($domain, 'restaur') !== false) {
            return false;
        }

        // Check if domain starts with "rest." or "rest" and ends with .com
        if (preg_match('/^rest[a-z]*\.com$/', $domain)) {
            return false;
        }

        return true;
    }

    protected function showSummary(): void
    {
        $this->info('=== Import Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Imported', $this->imported],
                ['Skipped (existing/invalid)', $this->skipped],
                ['Enriched with Google', $this->enrichedGoogle],
                ['Enriched with Yelp', $this->enrichedYelp],
                ['Errors', $this->errors],
            ]
        );

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->warn('This was a DRY RUN. Run again without --dry-run to actually import.');
        }

        // Show sample of recently imported
        if ($this->imported > 0 && !$this->option('dry-run')) {
            $this->newLine();
            $this->info('Sample of imported restaurants:');

            $samples = Restaurant::where('import_source', 'mf_imports')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(['name', 'city', 'google_place_id', 'yelp_id', 'status']);

            $this->table(
                ['Name', 'City', 'Google', 'Yelp', 'Status'],
                $samples->map(fn($r) => [
                    Str::limit($r->name, 30),
                    $r->city,
                    $r->google_place_id ? 'Yes' : 'No',
                    $r->yelp_id ? 'Yes' : 'No',
                    $r->status,
                ])->toArray()
            );
        }
    }
}
