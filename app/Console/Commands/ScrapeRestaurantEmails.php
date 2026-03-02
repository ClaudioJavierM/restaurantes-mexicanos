<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapeRestaurantEmails extends Command
{
    protected $signature = "restaurants:scrape-emails 
                            {--limit=100 : Number of restaurants to process}
                            {--dry-run : Show what would be done without saving}
                            {--state= : Filter by state code (e.g., TX, CA)}
                            {--offset=0 : Skip first N restaurants}";
    
    protected $description = "Scrape emails from restaurant websites";

    private $emailsFound = 0;
    private $processed = 0;
    private $errors = 0;

    // Domains to exclude
    private $excludeDomains = [
        "wix.com", "wixpress.com", "squarespace.com", "godaddy.com", "sentry.io",
        "cloudflare.com", "google.com", "gstatic.com", "googleapis.com",
        "facebook.com", "twitter.com", "instagram.com", "schema.org",
        "jquery.com", "bootstrapcdn.com", "fontawesome.com", "w3.org",
        "googlesyndication.com", "googletagmanager.com", "doubleclick.net",
        "amazon.com", "amazonaws.com", "cloudfront.net", "akamai.net",
        "example.com", "test.com", "localhost"
    ];

    public function handle()
    {
        $limit = (int) $this->option("limit");
        $offset = (int) $this->option("offset");
        $dryRun = $this->option("dry-run");
        $state = $this->option("state");

        $this->info("Starting email scrape...");
        $this->info("Limit: {$limit} | Offset: {$offset} | Dry run: " . ($dryRun ? "Yes" : "No"));

        $query = Restaurant::where("status", "approved")
            ->whereNotNull("website")
            ->where("website", "!=", "")
            ->where(function($q) {
                $q->whereNull("email")->orWhere("email", "");
            });

        if ($state) {
            $query->whereHas("state", function($q) use ($state) {
                $q->where("code", strtoupper($state));
            });
        }

        $restaurants = $query->offset($offset)->limit($limit)->get();
        $this->info("Found {$restaurants->count()} restaurants to process");

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant, $dryRun);
            $bar->advance();
            usleep(200000); // 0.2 seconds
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("=== Results ===");
        $this->info("Processed: {$this->processed}");
        $this->info("Emails found: {$this->emailsFound}");
        $this->info("Errors: {$this->errors}");
        
        $rate = $this->processed > 0 ? round(($this->emailsFound / $this->processed) * 100, 1) : 0;
        $this->info("Success rate: {$rate}%");

        return 0;
    }

    private function processRestaurant(Restaurant $restaurant, bool $dryRun): void
    {
        $this->processed++;

        try {
            $url = $this->normalizeUrl($restaurant->website);
            if (!$url) return;

            $emails = $this->scrapeUrl($url);

            // Try contact pages if no emails found
            if (empty($emails)) {
                foreach (["/contact", "/contact-us", "/contacto", "/about", "/about-us"] as $page) {
                    $emails = $this->scrapeUrl($url . $page);
                    if (!empty($emails)) break;
                }
            }

            if (!empty($emails)) {
                $email = $this->selectBestEmail($emails, $restaurant->name);
                
                if ($email) {
                    $this->emailsFound++;
                    
                    if ($dryRun) {
                        $this->newLine();
                        $this->line("  [FOUND] {$restaurant->name}: {$email}");
                    } else {
                        $restaurant->email = $email;
                        $restaurant->save();
                        
                        Log::info("Email scraped", [
                            "restaurant_id" => $restaurant->id,
                            "email" => $email
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            $this->errors++;
        }
    }

    private function normalizeUrl(string $url): ?string
    {
        $url = trim($url);
        if (!preg_match("/^https?:\/\//i", $url)) {
            $url = "https://" . $url;
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        return rtrim($url, "/");
    }

    private function scrapeUrl(string $url): array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36",
                    "Accept" => "text/html,application/xhtml+xml",
                ])
                ->get($url);

            if (!$response->successful()) return [];
            return $this->extractEmails($response->body());

        } catch (\Exception $e) {
            return [];
        }
    }

    private function extractEmails(string $html): array
    {
        $emails = [];

        // Remove script and style to avoid false positives
        $html = preg_replace("/<script[^>]*>.*?<\/script>/is", "", $html);
        $html = preg_replace("/<style[^>]*>.*?<\/style>/is", "", $html);

        // Extract from mailto: (most reliable)
        preg_match_all("/mailto:([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10})/i", $html, $mailto);
        if (!empty($mailto[1])) {
            $emails = array_merge($emails, $mailto[1]);
        }

        // Extract standard email patterns
        preg_match_all("/\b([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10})\b/", $html, $matches);
        if (!empty($matches[1])) {
            $emails = array_merge($emails, $matches[1]);
        }

        // Clean and filter
        $emails = array_map("strtolower", $emails);
        $emails = array_map("trim", $emails);
        $emails = array_unique($emails);
        $emails = array_filter($emails, [$this, "isValidEmail"]);

        return array_values($emails);
    }

    private function isValidEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        if (strlen($email) < 6 || strlen($email) > 100) return false;

        $parts = explode("@", $email);
        if (count($parts) !== 2) return false;
        
        $local = $parts[0];
        $domain = $parts[1];
        
        // Check local part
        if (strlen($local) < 2) return false;
        if (preg_match("/^[0-9]+$/", $local)) return false; // pure numbers
        
        // Check for CSS/JS patterns
        if (preg_match("/\.(css|js|min|map|json|png|jpg|jpeg|gif|svg|woff|ttf)$/i", $domain)) return false;
        
        // Check excluded domains
        foreach ($this->excludeDomains as $excluded) {
            if (stripos($domain, $excluded) !== false) return false;
        }

        // Exclude noreply patterns
        if (preg_match("/^(noreply|no-reply|donotreply|mailer-daemon|postmaster)/i", $local)) return false;

        return true;
    }

    private function selectBestEmail(array $emails, string $restaurantName): ?string
    {
        if (empty($emails)) return null;

        $priorities = ["info@", "contact@", "hello@", "hola@", "reservations@", "order@", "catering@"];
        
        // Match restaurant name words
        $nameWords = preg_split("/[\s\-_]+/", strtolower($restaurantName));
        foreach ($emails as $email) {
            foreach ($nameWords as $word) {
                if (strlen($word) > 3 && stripos($email, $word) !== false) {
                    return $email;
                }
            }
        }

        // Try priority prefixes
        foreach ($priorities as $prefix) {
            foreach ($emails as $email) {
                if (stripos($email, $prefix) === 0) return $email;
            }
        }

        return $emails[0] ?? null;
    }
}
