<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\PerplexityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FindEmailsWithPerplexity extends Command
{
    protected $signature = 'restaurants:find-emails-perplexity
                            {--limit=100 : Max restaurants to process per run}
                            {--dry-run : Show what would be searched without saving}
                            {--state= : Only process restaurants in specific US state code}';

    protected $description = 'Use Perplexity Sonar AI to find emails for restaurants with no website or email';

    public function handle(PerplexityService $perplexity): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info("Finding emails via Perplexity Sonar (limit: {$limit})...");

        $query = Restaurant::where('status', 'approved')
            ->where('country', 'US')
            ->where(function ($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            // Only restaurants with no website (those with website use ExtractEmailsFromWebsites)
            ->where(function ($q) {
                $q->whereNull('website')->orWhere('website', '');
            })
            // Skip if already attempted in last 30 days (use perplexity_email_searched_at)
            ->where(function ($q) {
                $q->whereNull('perplexity_email_searched_at')
                  ->orWhere('perplexity_email_searched_at', '<', now()->subDays(30));
            })
            ->whereNotNull('phone')
            // Prioritize restaurants with Mexican keywords in name
            ->orderByRaw("CASE WHEN name REGEXP 'taco|taqueria|mexican|burrito|jalisco|cantina|hacienda|mexico|azteca|mariachi|guadalajara|oaxaca|pueblo|rancho' THEN 0 ELSE 1 END")
            ->orderByDesc('profile_views');

        if ($state = $this->option('state')) {
            $query->whereHas('state', fn($q) => $q->where('code', strtoupper($state)));
        }

        $restaurants = $query->limit($limit)->get();

        if ($restaurants->isEmpty()) {
            // Also try without phone requirement
            $restaurants = Restaurant::where('status', 'approved')
                ->where('country', 'US')
                ->where(function ($q) { $q->whereNull('email')->orWhere('email', ''); })
                ->where(function ($q) { $q->whereNull('website')->orWhere('website', ''); })
                ->where(function ($q) {
                    $q->whereNull('perplexity_email_searched_at')
                      ->orWhere('perplexity_email_searched_at', '<', now()->subDays(30));
                })
                ->orderByDesc('profile_views')
                ->limit($limit)
                ->get();
        }

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need Perplexity email search.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$restaurants->count()} restaurants...");

        $found  = 0;
        $missed = 0;
        $errors = 0;

        foreach ($restaurants as $restaurant) {
            $stateCode = $restaurant->state->code ?? '';

            if ($dryRun) {
                $this->line("DRY RUN: {$restaurant->name}, {$restaurant->city}, {$stateCode}");
                continue;
            }

            try {
                $email = $perplexity->findRestaurantEmail(
                    $restaurant->name,
                    $restaurant->city,
                    $stateCode,
                    $restaurant->phone
                );

                $restaurant->update(['perplexity_email_searched_at' => now()]);

                if ($email) {
                    $restaurant->update(['email' => $email]);
                    $found++;
                    $this->line("✓ {$restaurant->name} → {$email}");
                } else {
                    $missed++;
                    $this->line("✗ {$restaurant->name} — not found");
                }

                // Perplexity rate limit — 1 req/sec on free tier
                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                Log::error("Perplexity email search error for {$restaurant->name}: " . $e->getMessage());
                $this->error("✗ {$restaurant->name}: " . $e->getMessage());
            }
        }

        if (!$dryRun) {
            $this->newLine();
            $this->info("Found: {$found} | Not found: {$missed} | Errors: {$errors}");
        }

        return Command::SUCCESS;
    }
}
