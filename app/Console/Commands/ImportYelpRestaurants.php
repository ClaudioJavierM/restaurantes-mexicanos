<?php

namespace App\Console\Commands;

use App\Services\YelpImportService;
use Illuminate\Console\Command;

class ImportYelpRestaurants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yelp:import
                            {city : City name}
                            {state : State name or code}
                            {--limit=50 : Maximum number of restaurants to import per request}
                            {--offset=0 : Offset for pagination}
                            {--min-rating=3.0 : Minimum Yelp rating to import}
                            {--enrich : Enrich with Google Places data}
                            {--update : Update existing restaurants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Mexican restaurants from Yelp for a specific city and state';

    /**
     * Execute the console command.
     */
    public function handle(YelpImportService $importService)
    {
        $city = $this->argument('city');
        $state = $this->argument('state');

        $this->info("🔍 Importing Mexican restaurants from Yelp...");
        $this->info("📍 Location: {$city}, {$state}");
        $this->line('');

        $options = [
            'limit' => (int) $this->option('limit'),
            'offset' => (int) $this->option('offset'),
            'min_rating' => (float) $this->option('min-rating'),
            'enrich_with_google' => $this->option('enrich'),
            'update_existing' => $this->option('update'),
        ];

        try {
            $stats = $importService->importFromLocation($city, $state, $options);

            $this->newLine();
            $this->info('✅ Import completed!');
            $this->newLine();

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Found on Yelp', $stats['total_found']],
                    ['✅ Imported', $stats['imported']],
                    ['⏭️  Skipped (duplicates)', $stats['skipped_duplicates']],
                    ['❌ Errors', $stats['errors']],
                ]
            );

            if ($stats['imported'] > 0) {
                $this->newLine();
                $this->info('Imported restaurants:');
                foreach ($stats['restaurants'] as $restaurant) {
                    $this->line("  • {$restaurant->name} - {$restaurant->city} ({$restaurant->yelp_rating} ⭐)");
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Import failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
