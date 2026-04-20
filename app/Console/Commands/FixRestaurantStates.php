<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FixRestaurantStates extends Command
{
    protected $signature = 'restaurants:fix-states
                            {--dry-run : Show what would be fixed without saving}
                            {--limit=200 : Max restaurants to fix per run}
                            {--state= : Only check restaurants currently tagged to this state code (e.g. AL)}';

    protected $description = 'Fix state_id for US restaurants whose coordinates do not match their stored state';

    // Bounding boxes: [lat_min, lat_max, lng_min, lng_max]
    private array $bounds = [
        'AL' => [30.19, 35.01, -88.47, -84.89],
        'AK' => [54.78, 71.38, -179.18, -129.99],
        'AZ' => [31.33, 37.00, -114.82, -109.05],
        'AR' => [33.00, 36.50, -94.62, -89.64],
        'CA' => [32.53, 42.01, -124.41, -114.13],
        'CO' => [36.99, 41.00, -109.05, -102.04],
        'CT' => [40.99, 42.05, -73.73, -71.79],
        'DE' => [38.45, 39.84, -75.79, -74.98],
        'FL' => [24.52, 31.00, -87.63, -80.03],
        'GA' => [30.36, 35.00, -85.61, -80.84],
        'HI' => [18.92, 28.40, -178.37, -154.81],
        'ID' => [41.99, 49.00, -117.24, -111.04],
        'IL' => [36.97, 42.51, -91.51, -87.02],
        'IN' => [37.77, 41.77, -88.10, -84.78],
        'IA' => [40.38, 43.50, -96.64, -90.14],
        'KS' => [36.99, 40.00, -102.05, -94.59],
        'KY' => [36.50, 39.15, -89.57, -81.96],
        'LA' => [28.93, 33.02, -94.04, -88.82],
        'ME' => [43.05, 47.46, -71.08, -66.95],
        'MD' => [37.91, 39.72, -79.49, -75.05],
        'MA' => [41.19, 42.89, -73.51, -69.93],
        'MI' => [41.70, 48.30, -90.42, -82.42],
        'MN' => [43.50, 49.38, -97.24, -89.49],
        'MS' => [30.18, 35.01, -91.66, -88.10],
        'MO' => [35.99, 40.61, -95.77, -89.10],
        'MT' => [44.36, 49.00, -116.05, -104.04],
        'NE' => [40.00, 43.00, -104.06, -95.31],
        'NV' => [35.00, 42.00, -120.00, -114.04],
        'NH' => [42.70, 45.31, -72.56, -70.61],
        'NJ' => [38.93, 41.36, -75.56, -73.90],
        'NM' => [31.33, 37.00, -109.05, -103.00],
        'NY' => [40.50, 45.01, -79.76, -71.86],
        'NC' => [33.84, 36.59, -84.32, -75.46],
        'ND' => [45.94, 49.00, -104.05, -96.55],
        'OH' => [38.40, 41.98, -84.82, -80.52],
        'OK' => [33.62, 37.00, -103.00, -94.43],
        'OR' => [41.99, 46.26, -124.57, -116.46],
        'PA' => [39.72, 42.51, -80.52, -74.69],
        'RI' => [41.15, 42.01, -71.91, -71.12],
        'SC' => [32.05, 35.22, -83.37, -78.54],
        'SD' => [42.48, 45.95, -104.06, -96.44],
        'TN' => [34.98, 36.68, -90.31, -81.65],
        'TX' => [25.84, 36.50, -106.65, -93.51],
        'UT' => [36.99, 42.00, -114.05, -109.04],
        'VT' => [42.73, 45.02, -73.44, -71.46],
        'VA' => [36.54, 39.47, -83.68, -75.24],
        'WA' => [45.55, 49.00, -124.76, -116.92],
        'WV' => [37.20, 40.64, -82.64, -77.72],
        'WI' => [42.49, 47.31, -92.89, -86.25],
        'WY' => [41.00, 45.01, -111.06, -104.05],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit   = (int) $this->option('limit');
        $filterState = $this->option('state');

        $allStates = State::where('id', '<=', 50)->get()->keyBy('code');

        $query = Restaurant::with('state')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('state', fn($q) => $q->where('id', '<=', 50));

        if ($filterState) {
            $query->whereHas('state', fn($q) => $q->where('code', strtoupper($filterState)));
        }

        // Chunk through restaurants, find misclassified ones
        $fixed = 0;
        $skipped = 0;
        $apiErrors = 0;

        $query->chunkById(500, function ($restaurants) use (
            $allStates, $dryRun, $limit, &$fixed, &$skipped, &$apiErrors
        ) {
            foreach ($restaurants as $restaurant) {
                if ($fixed >= $limit) return false; // stop chunking

                $stateCode = $restaurant->state?->code;
                if (!$stateCode || !isset($this->bounds[$stateCode])) {
                    $skipped++;
                    continue;
                }

                $lat = (float) $restaurant->latitude;
                $lng = (float) $restaurant->longitude;

                [$latMin, $latMax, $lngMin, $lngMax] = $this->bounds[$stateCode];

                // If within the bounding box, likely correct — skip
                if ($lat >= $latMin && $lat <= $latMax && $lng >= $lngMin && $lng <= $lngMax) {
                    continue;
                }

                // First: try bounding box match against all states (fast, no API)
                $candidates = [];
                foreach ($this->bounds as $code => [$bLatMin, $bLatMax, $bLngMin, $bLngMax]) {
                    if ($lat >= $bLatMin && $lat <= $bLatMax && $lng >= $bLngMin && $lng <= $bLngMax) {
                        $candidates[] = $code;
                    }
                }

                $correctCode = null;

                if (count($candidates) === 1) {
                    // Unambiguous bounding box match
                    $correctCode = $candidates[0];
                } elseif (count($candidates) > 1) {
                    // Overlapping boxes — use Nominatim to disambiguate
                    $correctCode = $this->reverseGeocode($lat, $lng);
                    if ($correctCode) {
                        usleep(1100000); // Nominatim: max 1 req/sec
                    }
                } else {
                    // No bounding box matches — use Nominatim
                    $correctCode = $this->reverseGeocode($lat, $lng);
                    if ($correctCode) {
                        usleep(1100000);
                    } else {
                        $apiErrors++;
                    }
                }

                if (!$correctCode || $correctCode === $stateCode) {
                    continue;
                }

                $newState = $allStates[$correctCode] ?? null;
                if (!$newState) {
                    continue;
                }

                $this->line(sprintf(
                    '[%s] %s | %s, %s → %s (%.4f, %.4f)',
                    $dryRun ? 'DRY' : 'FIX',
                    $restaurant->name,
                    $restaurant->city,
                    $stateCode,
                    $correctCode,
                    $lat,
                    $lng
                ));

                if (!$dryRun) {
                    $restaurant->update(['state_id' => $newState->id]);
                }

                $fixed++;
            }
        });

        $this->newLine();
        $this->info("Done — Fixed: {$fixed} | Skipped: {$skipped} | API errors: {$apiErrors}");
        if ($dryRun) {
            $this->warn('Dry run: no changes saved.');
        }

        return self::SUCCESS;
    }

    private function reverseGeocode(float $lat, float $lng): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'FAMER-Directory/1.0 contact:isaacjv79@gmail.com',
            ])->timeout(8)->get('https://nominatim.openstreetmap.org/reverse', [
                'lat'            => $lat,
                'lon'            => $lng,
                'format'         => 'json',
                'zoom'           => 5,
                'addressdetails' => 1,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $address      = $response->json('address', []);
            $countryCode  = $address['country_code'] ?? null;

            if ($countryCode !== 'us') {
                return null;
            }

            $stateName = $address['state'] ?? null;
            if (!$stateName) {
                return null;
            }

            // Match state name to our states table code
            $matched = State::where('id', '<=', 50)
                ->whereRaw('LOWER(name) = ?', [strtolower($stateName)])
                ->value('code');

            return $matched;

        } catch (\Exception) {
            return null;
        }
    }
}
