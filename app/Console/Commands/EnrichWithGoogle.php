<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\GooglePlacesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EnrichWithGoogle extends Command
{
    protected $signature = 'google:enrich-restaurants
                            {--limit=100 : Maximum number of restaurants to process}
                            {--source= : Filter by import_source (e.g., mf_imports)}
                            {--state= : Filter by state code (e.g., TX)}
                            {--test : Test mode - process only 5 restaurants}
                            {--force : Re-enrich even if already has Google data}
                            {--delay=1 : Delay in seconds between API calls}';

    protected $description = 'Enrich restaurants with Google Places data (hours, services, accessibility, etc.)';

    protected GooglePlacesService $googleService;
    protected int $enriched = 0;
    protected int $skipped = 0;
    protected int $notFound = 0;
    protected int $errors = 0;

    public function handle(GooglePlacesService $googleService): int
    {
        $this->googleService = $googleService;

        $this->info('=== Google Places Enrichment (Full Data) ===');
        $this->newLine();

        // Build query - restaurants without google_place_id (or all if --force)
        $query = Restaurant::where('status', 'approved');

        if (!$this->option('force')) {
            $query->whereNull('google_place_id');
        }

        // Filter by source if specified
        if ($source = $this->option('source')) {
            $query->where('import_source', $source);
            $this->info("Filtering by source: {$source}");
        }

        // Filter by state if specified
        if ($stateCode = $this->option('state')) {
            $query->whereHas('state', function ($q) use ($stateCode) {
                $q->where('code', strtoupper($stateCode));
            });
            $this->info("Filtering by state: {$stateCode}");
        }

        $total = $query->count();
        $this->info("Restaurants to process: {$total}");

        // Apply limit
        $limit = $this->option('test') ? 5 : (int) $this->option('limit');
        $restaurants = $query->with('state')->limit($limit)->get();

        $this->info("Processing: {$limit}");
        $this->newLine();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants need Google enrichment.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();

        $delay = (int) $this->option('delay');

        foreach ($restaurants as $restaurant) {
            $this->processRestaurant($restaurant);
            $bar->advance();

            // Rate limiting
            if ($delay > 0) {
                sleep($delay);
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Enriched with Google', $this->enriched],
                ['❌ Not found on Google', $this->notFound],
                ['⏭️  Skipped (already has data)', $this->skipped],
                ['⚠️  Errors', $this->errors],
                ['📊 Remaining', max(0, $total - $limit)],
            ]
        );

        // Show sample of enriched data
        if ($this->enriched > 0) {
            $this->showEnrichedSamples();
        }

        return Command::SUCCESS;
    }

    protected function processRestaurant(Restaurant $restaurant): void
    {
        try {
            // Skip if already has Google data (unless --force)
            if (!$this->option('force') && $restaurant->google_place_id) {
                $this->skipped++;
                return;
            }

            $stateCode = $restaurant->state?->code ?? '';

            // Search on Google Places
            $place = $this->googleService->findPlace(
                $restaurant->name,
                $restaurant->address ?? '',
                $restaurant->city,
                $stateCode
            );

            if (!$place || !isset($place['place_id'])) {
                $this->notFound++;
                Log::info("Google: Not found - {$restaurant->name}, {$restaurant->city}, {$stateCode}");
                return;
            }

            // Check if the business is operational
            $businessStatus = $place['business_status'] ?? 'OPERATIONAL';
            if ($businessStatus === 'CLOSED_PERMANENTLY') {
                DB::table('restaurants')
                    ->where('id', $restaurant->id)
                    ->update([
                        'status' => 'closed',
                        'business_status' => 'permanently_closed',
                        'updated_at' => now(),
                    ]);
                $this->notFound++;
                Log::info("Google: CLOSED - {$restaurant->name}, {$restaurant->city}");
                return;
            }

            // Get full details
            $details = $this->googleService->getPlaceDetails($place['place_id']);

            if (!$details) {
                $this->notFound++;
                return;
            }

            // Parse structured data
            $services = $this->googleService->parseServices($details);
            $accessibility = $this->googleService->parseAccessibility($details);
            $paymentMethods = $this->googleService->parsePaymentMethods($details);
            $openingHours = $this->googleService->parseOpeningHours($details);

            // Prepare update data
            $updateData = [
                'google_place_id' => $place['place_id'],
                'google_maps_url' => $details['url'] ?? null,
                'google_rating' => $details['rating'] ?? null,
                'google_reviews_count' => $details['user_ratings_total'] ?? 0,
                'latitude' => $place['geometry']['location']['lat'] ?? $restaurant->latitude,
                'longitude' => $place['geometry']['location']['lng'] ?? $restaurant->longitude,
                'google_verified' => true,
                'last_google_verification' => now(),
                'updated_at' => now(),
            ];

            // Update phone if not set
            if (empty($restaurant->phone) && !empty($details['formatted_phone_number'])) {
                $updateData['phone'] = $details['formatted_phone_number'];
            }

            // Update website if not set
            if (empty($restaurant->website) && !empty($details['website'])) {
                $updateData['website'] = $details['website'];
            }

            // Extract email from website if not set
            if (empty($restaurant->email)) {
                $websiteToScrape = $updateData["website"] ?? $restaurant->website;
                if ($websiteToScrape) {
                    $extractedEmail = $this->extractEmailFromWebsite($websiteToScrape);
                    if ($extractedEmail) {
                        $updateData["email"] = $extractedEmail;
                        Log::info("Email extracted: {$restaurant->name} -> {$extractedEmail}");
                    }
                }
            }

            // Update average rating if not set
            if (empty($restaurant->average_rating) && !empty($details['rating'])) {
                $updateData['average_rating'] = $details['rating'];
                $updateData['total_reviews'] = $details['user_ratings_total'] ?? 0;
            }

            // Add price level
            if (isset($details['price_level'])) {
                $updateData['google_price_level'] = $details['price_level'];
            }

            // Add photos count
            if (isset($details['photos'])) {
                $updateData['google_photos_count'] = count($details['photos']);
            }

            // Add opening hours (JSON)
            if (!empty($openingHours)) {
                $updateData['opening_hours'] = json_encode($openingHours);
            }

            // Add services (JSON) - merge with existing
            if (!empty($services)) {
                $existingServices = $restaurant->services ?? [];
                if (is_string($existingServices)) {
                    $existingServices = json_decode($existingServices, true) ?? [];
                }
                $mergedServices = array_unique(array_merge($existingServices, $services));
                $updateData['services'] = json_encode(array_values($mergedServices));
            }

            // Add accessibility (JSON)
            if (!empty($accessibility)) {
                $existingAccess = $restaurant->accessibility ?? [];
                if (is_string($existingAccess)) {
                    $existingAccess = json_decode($existingAccess, true) ?? [];
                }
                $mergedAccess = array_unique(array_merge($existingAccess, $accessibility));
                $updateData['accessibility'] = json_encode(array_values($mergedAccess));
            }

            // Add payment methods (JSON)
            if (!empty($paymentMethods)) {
                $existingPayment = $restaurant->payment_methods ?? [];
                if (is_string($existingPayment)) {
                    $existingPayment = json_decode($existingPayment, true) ?? [];
                }
                $mergedPayment = array_unique(array_merge($existingPayment, $paymentMethods));
                $updateData['payment_methods'] = json_encode(array_values($mergedPayment));
            }

            // Update business status
            $status = $details['business_status'] ?? 'OPERATIONAL';
            $updateData['business_status'] = match($status) {
                'OPERATIONAL' => 'operational',
                'CLOSED_TEMPORARILY' => 'temporarily_closed',
                'CLOSED_PERMANENTLY' => 'permanently_closed',
                default => 'operational',
            };

            // Use direct DB update to avoid model events
            DB::table('restaurants')
                ->where('id', $restaurant->id)
                ->update($updateData);

            $this->enriched++;
            Log::info("Google: Enriched - {$restaurant->name} (services: " . count($services) . ", hours: " . (!empty($openingHours) ? 'yes' : 'no') . ")");

        } catch (\Exception $e) {
            $this->errors++;
            Log::error("Error enriching {$restaurant->name}: {$e->getMessage()}");
        }
    }


    /**
     * Extract email from restaurant website
     */
    protected function extractEmailFromWebsite(?string $websiteUrl): ?string
    {
        if (empty($websiteUrl)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
                ])
                ->get($websiteUrl);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // Email regex pattern
            $pattern = "/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/";
            
            if (preg_match_all($pattern, $html, $matches)) {
                $emails = array_unique($matches[0]);
                
                // Filter out common non-business emails
                $blacklist = ["example.com", "domain.com", "email.com", "yoursite.com", "website.com", "test.com", "wixpress.com", "sentry.io", "wordpress.org", "w3.org", "schema.org", "googleapis.com"];
                
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
                    if (preg_match("/\.(png|jpg|jpeg|gif|svg|webp)$/i", $email)) {
                        continue;
                    }
                    
                    if (!$skip) {
                        return $email;
                    }
                }
            }

            // Try mailto: links
            if (preg_match("/mailto:([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/", $html, $match)) {
                return strtolower($match[1]);
            }

            return null;
        } catch (\Exception $e) {
            Log::debug("Error extracting email from {$websiteUrl}: " . $e->getMessage());
            return null;
        }
    }

    protected function showEnrichedSamples(): void
    {
        $this->newLine();
        $this->info('Sample of enriched restaurants:');

        $samples = Restaurant::whereNotNull('google_place_id')
            ->whereNotNull('opening_hours')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['name', 'city', 'google_rating', 'opening_hours', 'services']);

        $rows = $samples->map(function($r) {
            $hours = $r->opening_hours;
            if (is_string($hours)) {
                $hours = json_decode($hours, true);
            }
            $hasHours = !empty($hours['weekday_text']);

            $services = $r->services;
            if (is_string($services)) {
                $services = json_decode($services, true) ?? [];
            }

            return [
                \Illuminate\Support\Str::limit($r->name, 20),
                $r->city,
                $r->google_rating ?? 'N/A',
                $hasHours ? '✅' : '❌',
                count($services ?? []),
            ];
        })->toArray();

        if (!empty($rows)) {
            $this->table(
                ['Name', 'City', 'Rating', 'Hours', 'Services'],
                $rows
            );
        }
    }
}
