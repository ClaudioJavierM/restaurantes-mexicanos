<?php

namespace App\Console\Commands;

use App\Mail\CouponCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCouponCampaign extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'campaign:send-coupons
                            {--csv= : Path to CSV file with contacts}
                            {--coupon=MFIMPORTS50 : Coupon code to use}
                            {--discount=50 : Discount percentage}
                            {--limit=0 : Limit number of emails (0 = no limit)}
                            {--delay=3 : Delay in seconds between emails}
                            {--test : Only show what would be sent, do not actually send}
                            {--skip-sent : Skip contacts that have already received this campaign}';

    /**
     * The console command description.
     */
    protected $description = 'Send coupon campaign emails to MF Imports contacts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $csvPath = $this->option('csv') ?: base_path('mf_imports_contacts.csv');
        $couponCode = $this->option('coupon');
        $discountPercent = (int) $this->option('discount');
        $limit = (int) $this->option('limit');
        $delay = (int) $this->option('delay');
        $testMode = $this->option('test');
        $skipSent = $this->option('skip-sent');

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found: {$csvPath}");
            return 1;
        }

        $this->info("=== MF Imports Coupon Campaign ===\n");
        $this->info("CSV: {$csvPath}");
        $this->info("Coupon: {$couponCode} ({$discountPercent}% off)");
        $this->info("Mode: " . ($testMode ? "TEST (no emails will be sent)" : "LIVE"));
        $this->info("");

        // Get cache key for tracking sent emails
        $cacheKey = "campaign_sent_{$couponCode}";
        $sentEmails = $skipSent ? (Cache::get($cacheKey, []) ?: []) : [];

        // Read CSV
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // Skip header row

        $contacts = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $contacts[] = [
                    'restaurant' => trim($row[0] ?? ''),
                    'contact' => trim($row[1] ?? ''),
                    'email' => strtolower(trim($row[2] ?? '')),
                    'phone' => trim($row[3] ?? ''),
                    'city' => trim($row[4] ?? ''),
                    'state' => trim($row[5] ?? ''),
                    'orders' => (int) ($row[6] ?? 0),
                    'last_order' => $row[7] ?? '',
                ];
            }
        }
        fclose($handle);

        // Filter valid contacts
        $contacts = array_filter($contacts, function ($contact) {
            return !empty($contact['email']) &&
                   filter_var($contact['email'], FILTER_VALIDATE_EMAIL) &&
                   !empty($contact['restaurant']) &&
                   strlen($contact['restaurant']) > 3;
        });

        $this->info("Total valid contacts: " . count($contacts));

        if ($skipSent && count($sentEmails) > 0) {
            $contacts = array_filter($contacts, function ($contact) use ($sentEmails) {
                return !in_array($contact['email'], $sentEmails);
            });
            $this->info("After skipping sent: " . count($contacts));
        }

        if ($limit > 0) {
            $contacts = array_slice($contacts, 0, $limit);
            $this->info("Limited to: {$limit} contacts");
        }

        $this->info("");

        if (count($contacts) === 0) {
            $this->warn("No contacts to send to.");
            return 0;
        }

        if (!$testMode && !$this->confirm("Ready to send " . count($contacts) . " emails. Continue?")) {
            $this->info("Cancelled.");
            return 0;
        }

        $bar = $this->output->createProgressBar(count($contacts));
        $bar->start();

        $sent = 0;
        $failed = 0;
        $skipped = 0;
        $newSentEmails = [];

        foreach ($contacts as $contact) {
            try {
                if ($testMode) {
                    $this->line("\n[TEST] Would send to: {$contact['email']} ({$contact['restaurant']})");
                } else {
                    Mail::to($contact['email'])->send(new CouponCampaign(
                        restaurantName: $contact['restaurant'],
                        contactName: $contact['contact'],
                        city: $contact['city'] ?: 'tu ciudad',
                        state: $contact['state'] ?: '',
                        couponCode: $couponCode,
                        discountPercent: $discountPercent
                    ));

                    $newSentEmails[] = $contact['email'];

                    Log::info("Coupon campaign sent", [
                        'email' => $contact['email'],
                        'restaurant' => $contact['restaurant'],
                        'coupon' => $couponCode,
                    ]);

                    // Delay between emails to avoid spam detection
                    if ($delay > 0) {
                        sleep($delay);
                    }
                }

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to send to {$contact['email']}: " . $e->getMessage());
                Log::error("Coupon campaign failed", [
                    'email' => $contact['email'],
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Save sent emails to cache (persist for 30 days)
        if (!$testMode && count($newSentEmails) > 0) {
            $allSent = array_unique(array_merge($sentEmails, $newSentEmails));
            Cache::put($cacheKey, $allSent, now()->addDays(30));
        }

        $this->info("=== Campaign Complete ===");
        $this->info("Sent: {$sent}");
        $this->info("Failed: {$failed}");
        $this->info("Skipped: {$skipped}");

        if (!$testMode) {
            $this->info("\nSent emails cached with key: {$cacheKey}");
            $this->info("Use --skip-sent next time to avoid duplicates");
        }

        return 0;
    }
}
