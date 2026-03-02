<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YelpImportService;
use Illuminate\Support\Facades\DB;

class ImportYelpSmart extends Command
{
    protected $signature = 'yelp:import-smart
                            {--states=* : Specific states to import (e.g., CA, TX)}
                            {--cities=20 : Number of cities to process per run}
                            {--limit=50 : Maximum restaurants per city}
                            {--min-rating=3.0 : Minimum Yelp rating}
                            {--delay=2 : Delay between API calls}
                            {--exhausted-threshold=80 : Duplicate percentage to mark city as exhausted}
                            {--reset-exhausted : Reset exhausted cities to try again}';

    protected $description = 'Smart import that tracks progress per city and skips exhausted cities';

    public function handle(YelpImportService $importService): int
    {
        $states = $this->option('states');
        $citiesPerRun = (int) $this->option('cities');
        $limit = (int) $this->option('limit');
        $minRating = (float) $this->option('min-rating');
        $delay = (int) $this->option('delay');
        $exhaustedThreshold = (int) $this->option('exhausted-threshold');

        if ($this->option('reset-exhausted')) {
            $this->resetExhaustedCities($states);
            return 0;
        }

        $this->info('🧠 Smart Yelp Import - Tracking city progress');
        $this->newLine();

        $cities = $this->getCitiesToProcess($states, $citiesPerRun);

        if ($cities->isEmpty()) {
            $this->warn('No cities available to process. All cities may be exhausted.');
            $this->info('Use --reset-exhausted to reset exhausted cities.');
            return 0;
        }

        $this->info("Processing {$cities->count()} cities...");
        $this->newLine();

        $totalStats = [
            'cities_processed' => 0,
            'total_imported' => 0,
            'total_duplicates' => 0,
            'cities_exhausted' => 0,
        ];

        foreach ($cities as $cityRecord) {
            $offset = $cityRecord->last_offset;
            $nextOffset = min($offset + 50, 950);

            $this->info("📍 {$cityRecord->city}, {$cityRecord->state_code} (offset: {$offset})");

            try {
                // importFromLocation(city, state, options)
                $result = $importService->importFromLocation(
                    $cityRecord->city,
                    $cityRecord->state_code,
                    [
                        'limit' => $limit,
                        'offset' => $offset,
                        'min_rating' => $minRating,
                    ]
                );

                $imported = $result['imported'] ?? 0;
                $duplicates = $result['skipped_duplicates'] ?? 0;
                $found = $result['total_found'] ?? ($imported + $duplicates);
                
                $duplicateRate = $found > 0 ? ($duplicates / $found) * 100 : 0;
                $isExhausted = $duplicateRate >= $exhaustedThreshold || $nextOffset >= 950;

                DB::table('city_import_progress')
                    ->where('id', $cityRecord->id)
                    ->update([
                        'last_offset' => $nextOffset,
                        'total_imported' => DB::raw("total_imported + {$imported}"),
                        'total_duplicates' => DB::raw("total_duplicates + {$duplicates}"),
                        'last_duplicate_rate' => $duplicateRate,
                        'is_exhausted' => $isExhausted ? 1 : 0,
                        'last_import_at' => now(),
                        'updated_at' => now(),
                    ]);

                $status = $isExhausted ? '🔴 EXHAUSTED' : '🟢 OK';
                $this->line("   Found: {$found} | New: {$imported} | Dups: {$duplicates} (" . round($duplicateRate) . "%) {$status}");

                $totalStats['cities_processed']++;
                $totalStats['total_imported'] += $imported;
                $totalStats['total_duplicates'] += $duplicates;
                if ($isExhausted) $totalStats['cities_exhausted']++;

                sleep($delay);

            } catch (\Exception $e) {
                $this->error("   Error: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Cities processed', $totalStats['cities_processed']],
                ['New restaurants', $totalStats['total_imported']],
                ['Duplicates skipped', $totalStats['total_duplicates']],
                ['Cities marked exhausted', $totalStats['cities_exhausted']],
            ]
        );

        $remaining = DB::table('city_import_progress')
            ->where('is_exhausted', 0)
            ->when(!empty($states), fn($q) => $q->whereIn('state_code', $states))
            ->count();
        
        $this->info("🏙️  Remaining active cities: {$remaining}");

        return 0;
    }

    protected function getCitiesToProcess(array $states, int $limit)
    {
        return DB::table('city_import_progress')
            ->where('is_exhausted', 0)
            ->when(!empty($states), fn($q) => $q->whereIn('state_code', $states))
            ->orderByRaw('last_import_at IS NULL DESC')
            ->orderBy('total_imported', 'desc')
            ->orderBy('last_offset', 'asc')
            ->limit($limit)
            ->get();
    }

    protected function resetExhaustedCities(array $states): void
    {
        $query = DB::table('city_import_progress')->where('is_exhausted', 1);
        
        if (!empty($states)) {
            $query->whereIn('state_code', $states);
        }

        $count = $query->update([
            'is_exhausted' => 0,
            'last_offset' => 0,
            'updated_at' => now(),
        ]);

        $this->info("Reset {$count} exhausted cities.");
    }
}
