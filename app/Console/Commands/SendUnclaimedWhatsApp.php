<?php

namespace App\Console\Commands;

use App\Jobs\SendUnclaimedWhatsAppJob;
use App\Models\Restaurant;
use Illuminate\Console\Command;

class SendUnclaimedWhatsApp extends Command
{
    protected $signature = 'famer:send-unclaimed-whatsapp
                            {--limit=50 : Max restaurants to process}
                            {--dry-run : Show what would be sent without dispatching jobs}';

    protected $description = 'Send WhatsApp outreach messages to unclaimed US restaurants that have a phone number';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $query = Restaurant::query()
            ->where('status', 'approved')
            ->whereNull('user_id')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->whereNull('whatsapp_outreach_sent_at')
            ->where('country', 'US')
            ->limit($limit);

        $restaurants = $query->get(['id', 'name', 'phone', 'slug']);

        if ($restaurants->isEmpty()) {
            $this->info('No unclaimed US restaurants with phone found.');
            return self::SUCCESS;
        }

        $this->info("Found {$restaurants->count()} restaurants to process" . ($dryRun ? ' [DRY RUN]' : '') . '.');

        foreach ($restaurants as $restaurant) {
            if ($dryRun) {
                $this->line("  [dry-run] Would send WA to restaurant #{$restaurant->id}: {$restaurant->name} ({$restaurant->phone})");
                continue;
            }

            SendUnclaimedWhatsAppJob::dispatch($restaurant->id);
            $this->line("  Dispatched job for restaurant #{$restaurant->id}: {$restaurant->name}");

            // Stagger dispatches to respect Twilio rate limits
            sleep(3);
        }

        if (!$dryRun) {
            $this->info("Dispatched {$restaurants->count()} WhatsApp jobs.");
        }

        return self::SUCCESS;
    }
}
