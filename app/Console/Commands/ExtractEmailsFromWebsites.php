<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExtractEmailsFromWebsites extends Command
{
    protected $signature = 'restaurants:extract-emails
                            {--limit=100 : Maximum number of restaurants to process}
                            {--delay=1 : Delay in seconds between requests}
                            {--test : Test mode - process only 10 restaurants}';

    protected $description = 'Extract emails from restaurant websites for campaigns';

    protected int $extracted = 0;
    protected int $notFound = 0;
    protected int $errors = 0;

    public function handle(): int
    {
        $this->info('=== Email Extraction from Websites ===');
        $this->newLine();

        // Get restaurants with website but no email
        $query = Restaurant::where('status', 'approved')
            ->whereNotNull('website')
            ->where('website', '!=', '')
            ->where(function($q) {
                $q->whereNull('email')
                   ->orWhere('email', '');
            });

        $total = $query->count();
        $this->info("Restaurants with website but no email: {$total}");

        $limit = $this->option('test') ? 10 : (int) $this->option('limit');
        $restaurants = $query->limit($limit)->get();

        $this->info("Processing: {$limit}");
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need email extraction.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $delay = (int) $this->option('delay');

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Emails extracted', $this->extracted],
                ['❌ No email found', $this->notFound],
                ['⚠️  Errors', $this->errors],
                ['📊 Remaining', max(0, $total - $limit)],
            ]
        );

        // Show sample of extracted emails
        if ($this->extracted > 0) {
            $this->showExtractedSamples();
        }

        return Command::SUCCESS;
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            $email = $this->extractEmailFromWebsite($restaurant->website);

            if ($email) {
                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update([
                        'email' => $email,
                        'updated_at' => now(),
                    ]);

                $this->extracted++;
                Log::info("Email extracted: {$restaurant->name} -> {$email}");
            } else {
                $this->notFound++;
            }
        } catch (\Exception $e) {
            $this->errors++;
            Log::debug("Error extracting email for {$restaurant->name}: " . $e->getMessage());
        }
    }

    protected function extractEmailFromWebsite(?string $websiteUrl): ?string
    {
        if (empty($websiteUrl)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($websiteUrl);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // Email regex pattern
            $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
            
            if (preg_match_all($pattern, $html, $matches)) {
                $emails = array_unique($matches[0]);
                
                // Filter out common non-business emails
                $blacklist = ['example.com', 'domain.com', 'email.com', 'yoursite.com', 'website.com', 'test.com', 'wixpress.com', 'sentry.io', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'squarespace.com', 'godaddy.com'];
                
                foreach ($emails as $email) {
                    $email = strtolower($email);
                    $skip = false;
                    
                    foreach ($blacklist as $blocked) {
                        if (str_contains($email, $blocked)) {
                            $skip = true;
                            break;
                        }
                    }
                    
                    // Skip image file extensions
                    if (preg_match('/\.(png|jpg|jpeg|gif|svg|webp)$/i', $email)) {
                        continue;
                    }
                    
                    if (!$skip) {
                        return $email;
                    }
                }
            }

            // Try mailto: links
            if (preg_match('/mailto:([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $html, $match)) {
                return strtolower($match[1]);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function showExtractedSamples(): void
    {
        $this->newLine();
        $this->info('Recent extracted emails:');

        $samples = Restaurant::whereNotNull('email')
            ->where('email', '!=', '')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['name', 'city', 'email', 'website']);

        $rows = $samples->map(function($r) {
            return [
                \Illuminate\Support\Str::limit($r->name, 25),
                $r->city,
                $r->email,
                \Illuminate\Support\Str::limit($r->website, 30),
            ];
        })->toArray();

        if (!empty($rows)) {
            $this->table(
                ['Name', 'City', 'Email', 'Website'],
                $rows
            );
        }
    }
}
