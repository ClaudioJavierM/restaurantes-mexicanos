<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use Illuminate\Support\Str;

class ImportFromZipCodes extends Command
{
    protected $signature = 'yelp:import-zipcodes 
        {--limit=100 : Number of cities to process}
        {--per-city=20 : Restaurants per city}
        {--state= : Filter by state code (e.g., TX)}
        {--min-rating=3.0 : Minimum Yelp rating}
        {--delay=2 : Delay between API calls}
        {--dry-run : Show cities without importing}
        {--api-key=us1 : Which API key to use: us1, us2, us3}';

    protected $description = 'Import restaurants from unprocessed cities using MF Imports zip codes database';

    protected $apiKey;
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $errorCount = 0;

    public function handle()
    {
        $apiKeyOption = $this->option('api-key');
        $this->apiKey = match($apiKeyOption) {
            'us1' => config('services.yelp.api_key_us_1'),
            'us2' => config('services.yelp.api_key_us_2'),
            'us3' => config('services.yelp.api_key_us_3'),
            default => config('services.yelp.api_key'),
        };
        
        $limit = (int) $this->option('limit');
        $perCity = (int) $this->option('per-city');
        $stateFilter = $this->option('state');
        $minRating = (float) $this->option('min-rating');
        $delay = (int) $this->option('delay');
        $dryRun = $this->option('dry-run');

        $this->info('🚀 Import from Zip Codes Database');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get cities already imported
        $importedCities = Restaurant::where('country', 'US')
            ->where('status', 'approved')
            ->select('city', 'state_id')
            ->distinct()
            ->get()
            ->map(fn($r) => strtolower($r->city) . '|' . $r->state_id)
            ->toArray();

        $this->info('📊 Cities already imported: ' . count($importedCities));

        // Connect to MF Imports MySQL database
        $mfImportsDb = DB::connection('mysql_mfimports');

        // Query for unprocessed cities
        $query = $mfImportsDb->table('tblcodigospostales')
            ->select('strPhysicalCity as city', 'strPhysicalState as state')
            ->distinct();

        if ($stateFilter) {
            $query->where('strPhysicalState', $stateFilter);
        }

        $allCities = $query->get();

        // Filter out already imported cities
        $states = State::where('country', 'US')->pluck('id', 'code')->toArray();
        
        $pendingCities = $allCities->filter(function ($city) use ($importedCities, $states) {
            $stateId = $states[$city->state] ?? null;
            if (!$stateId) return false;
            $key = strtolower($city->city) . '|' . $stateId;
            return !in_array($key, $importedCities);
        })->take($limit);

        $this->info('🏙️  Cities to process: ' . $pendingCities->count());

        if ($dryRun) {
            $this->table(['City', 'State'], $pendingCities->map(fn($c) => [$c->city, $c->state])->toArray());
            return 0;
        }

        $bar = $this->output->createProgressBar($pendingCities->count());
        $bar->start();

        foreach ($pendingCities as $cityData) {
            $this->importCity($cityData->city, $cityData->state, $perCity, $minRating);
            $bar->advance();
            sleep($delay);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Imported: ' . $this->importedCount);
        $this->info('⏭️  Skipped: ' . $this->skippedCount);
        $this->info('❌ Errors: ' . $this->errorCount);

        return 0;
    }

    protected function importCity($city, $stateCode, $limit, $minRating)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get('https://api.yelp.com/v3/businesses/search', [
                'location' => $city . ', ' . $stateCode,
                'term' => 'mexican restaurant',
                'categories' => 'mexican,tacos,tex-mex',
                'limit' => min($limit, 50),
                'sort_by' => 'rating',
            ]);

            if (!$response->successful()) {
                $this->errorCount++;
                return;
            }

            $businesses = $response->json()['businesses'] ?? [];
            $state = State::where('code', $stateCode)->where('country', 'US')->first();
            
            if (!$state) return;

            foreach ($businesses as $business) {
                if (($business['rating'] ?? 0) < $minRating) continue;
                
                // Check if already exists
                $exists = Restaurant::where('yelp_id', $business['id'])->exists();
                if ($exists) {
                    $this->skippedCount++;
                    continue;
                }

                // Create restaurant
                Restaurant::create([
                    'name' => $business['name'],
                    'slug' => Str::slug($business['name'] . '-' . $city . '-' . Str::random(4)),
                    'address' => $business['location']['address1'] ?? '',
                    'city' => $business['location']['city'] ?? $city,
                    'state_id' => $state->id,
                    'zip_code' => $business['location']['zip_code'] ?? '',
                    'country' => 'US',
                    'phone' => $business['phone'] ?? null,
                    'latitude' => $business['coordinates']['latitude'] ?? null,
                    'longitude' => $business['coordinates']['longitude'] ?? null,
                    'yelp_id' => $business['id'],
                    'yelp_url' => $business['url'] ?? null,
                    'yelp_rating' => $business['rating'] ?? null,
                    'yelp_reviews_count' => $business['review_count'] ?? 0,
                    'image' => $business['image_url'] ?? null,
                    'status' => 'approved',
                    'is_active' => true,
                ]);

                $this->importedCount++;
            }
        } catch (\Exception $e) {
            $this->errorCount++;
        }
    }
}
