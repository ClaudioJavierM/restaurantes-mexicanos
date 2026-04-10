<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\OgImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateOgImages extends Command
{
    protected $signature = 'famer:generate-og-images
                            {--limit=100 : Maximum number of restaurants to process}
                            {--force    : Regenerate images even if they already exist}
                            {--state=   : Filter by state name (e.g. Texas)}';

    protected $description = 'Pre-generate dynamic OG images for approved restaurants with photos';

    public function __construct(private readonly OgImageService $ogImageService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');
        $state = $this->option('state');

        $query = Restaurant::approved()
            ->with(['state', 'category'])
            ->orderByDesc('total_reviews'); // prioritize popular restaurants first

        // Only process restaurants that have at least one photo source
        $query->where(function ($q) {
            $q->whereNotNull('image')
              ->orWhereNotNull('yelp_photos')
              ->orWhereNotNull('photos');
        });

        if ($state) {
            $query->whereHas('state', fn($q) => $q->where('name', 'like', "%{$state}%"));
        }

        if (! $force) {
            // Skip restaurants whose OG image already exists — we'll filter after chunking
            // (can't check file existence in SQL, so we filter in PHP below)
            $this->info('Skipping restaurants with existing OG images (use --force to regenerate all).');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('No restaurants to process.');
            return 0;
        }

        $this->info("Found {$total} candidate restaurants. Processing up to {$limit}.");

        $generated = 0;
        $skipped   = 0;
        $errors    = 0;
        $processed = 0;

        $bar = $this->output->createProgressBar($limit);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->setMessage('Starting…');
        $bar->start();

        $query->chunk(50, function ($restaurants) use (
            $force, $limit,
            &$generated, &$skipped, &$errors, &$processed, $bar
        ) {
            foreach ($restaurants as $restaurant) {
                if ($processed >= $limit) {
                    return false; // stop chunk iteration
                }

                $processed++;
                $bar->setMessage($this->sanitizeForConsole($restaurant->name));

                // Skip if already exists (unless --force)
                if (! $force && $this->ogImageService->exists($restaurant)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                try {
                    $this->ogImageService->generate($restaurant, $force);
                    $generated++;
                } catch (\Throwable $e) {
                    $errors++;
                    Log::warning("GenerateOgImages: failed for #{$restaurant->id} ({$restaurant->slug}): " . $e->getMessage());
                }

                $bar->advance();

                // Small pause to avoid overwhelming disk I/O
                usleep(30000); // 30 ms
            }
        });

        $bar->setMessage('Done');
        $bar->finish();
        $this->newLine(2);

        $this->info(sprintf(
            'Generated: %d | Skipped: %d | Errors: %d | Total processed: %d',
            $generated,
            $skipped,
            $errors,
            $processed
        ));

        if ($errors > 0) {
            $this->warn("Check laravel.log for details on the {$errors} failed images.");
        }

        return $errors > 0 ? 1 : 0;
    }

    private function sanitizeForConsole(string $text): string
    {
        // Strip characters that could corrupt terminal output
        return substr(preg_replace('/[^\x20-\x7E]/', '', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text), 0, 60);
    }
}
