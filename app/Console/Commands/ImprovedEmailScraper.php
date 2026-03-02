<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImprovedEmailScraper extends Command
{
    protected $signature = 'restaurants:find-emails 
                            {--limit=100 : Number of restaurants to process}
                            {--delay=2 : Delay between requests}
                            {--state= : Filter by state code}';
    
    protected $description = 'Find emails by checking multiple pages (home, contact, about)';

    private $found = 0;
    private $processed = 0;

    private $contactPaths = ['', '/contact', '/contact-us', '/contacto', '/about', '/about-us', '/location', '/locations'];
    
    private $excludeDomains = [
        'wix.com', 'squarespace.com', 'godaddy.com', 'sentry.io',
        'cloudflare.com', 'google.com', 'facebook.com', 'twitter.com',
        'instagram.com', 'schema.org', 'jquery.com', 'w3.org',
        'googletagmanager.com', 'example.com', 'email.com', 'yourdomain.com'
    ];

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');
        $state = $this->option('state');

        $query = Restaurant::whereNotNull('website')
            ->where('website', '!=', '')
            ->where(function($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->where('status', 'approved');

        if ($state) {
            $query->whereHas('state', fn($q) => $q->where('code', $state));
        }

        $total = $query->count();
        $restaurants = $query->limit($limit)->get();

        $this->info("Finding emails for restaurants...");
        $this->info("Total without email: {$total} | Processing: {$restaurants->count()}");
        $this->newLine();

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $email = $this->findEmailForRestaurant($restaurant);
            
            if ($email) {
                $restaurant->email = $email;
                $restaurant->save();
                $this->found++;
            }
            
            $this->processed++;
            $bar->advance();
            sleep($delay);
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Metric', 'Value'], [
            ['Processed', $this->processed],
            ['Emails found', $this->found],
            ['Success rate', $this->processed > 0 ? round(($this->found / $this->processed) * 100, 1) . '%' : '0%'],
            ['Remaining', $total - $this->found],
        ]);

        return 0;
    }

    private function findEmailForRestaurant(Restaurant $restaurant): ?string
    {
        $baseUrl = rtrim($restaurant->website, '/');
        
        // Try each contact path
        foreach ($this->contactPaths as $path) {
            $url = $baseUrl . $path;
            
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; RestaurantBot/1.0)'])
                    ->get($url);

                if ($response->successful()) {
                    $emails = $this->extractEmails($response->body());
                    
                    if (!empty($emails)) {
                        // Return the first valid email
                        return $emails[0];
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
            
            usleep(500000); // 0.5s between page requests
        }

        return null;
    }

    private function extractEmails(string $html): array
    {
        $emails = [];

        // Clean HTML
        $html = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);

        // Extract mailto: links (most reliable)
        preg_match_all('/mailto:([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10})/i', $html, $mailto);
        if (!empty($mailto[1])) {
            $emails = array_merge($emails, $mailto[1]);
        }

        // Extract standard email patterns
        preg_match_all('/\b([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10})\b/', $html, $matches);
        if (!empty($matches[1])) {
            $emails = array_merge($emails, $matches[1]);
        }

        // Clean and filter
        $emails = array_map('strtolower', $emails);
        $emails = array_map('trim', $emails);
        $emails = array_unique($emails);
        $emails = array_filter($emails, fn($e) => $this->isValidEmail($e));

        return array_values($emails);
    }

    private function isValidEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Check excluded domains
        foreach ($this->excludeDomains as $domain) {
            if (stripos($email, '@' . $domain) !== false || stripos($email, '.' . $domain) !== false) {
                return false;
            }
        }

        // Exclude common fake/system emails
        $excludePatterns = ['noreply', 'no-reply', 'info@example', 'test@', 'admin@', 'webmaster@'];
        foreach ($excludePatterns as $pattern) {
            if (stripos($email, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }
}
