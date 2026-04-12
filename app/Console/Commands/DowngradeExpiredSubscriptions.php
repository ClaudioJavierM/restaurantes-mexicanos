<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DowngradeExpiredSubscriptions extends Command
{
    protected $signature = 'famer:downgrade-expired
                            {--dry-run : Preview which subscriptions would be downgraded without making changes}';

    protected $description = 'Downgrade claimed restaurants with expired subscriptions back to the free tier';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $restaurants = Restaurant::query()
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', now())
            ->where('subscription_tier', '!=', 'free')
            ->where('is_claimed', true)
            ->get();

        $count = $restaurants->count();

        if ($dryRun) {
            $this->info("[dry-run] Would downgrade {$count} restaurant(s) to free tier.");

            foreach ($restaurants as $restaurant) {
                $this->line("  - #{$restaurant->id} {$restaurant->name} (tier: {$restaurant->subscription_tier}, expired: {$restaurant->subscription_expires_at->toDateTimeString()})");
            }

            return Command::SUCCESS;
        }

        $downgraded = 0;

        foreach ($restaurants as $restaurant) {
            $previousTier = $restaurant->subscription_tier;

            try {
                $restaurant->update([
                    'subscription_tier'      => 'free',
                    'subscription_status'    => 'active',
                    'is_featured'            => false,
                    'premium_analytics'      => false,
                    'premium_seo'            => false,
                    'premium_featured'       => false,
                    'premium_coupons'        => false,
                    'premium_email_marketing'=> false,
                ]);

                $downgraded++;

                Log::info("famer:downgrade-expired — downgraded restaurant #{$restaurant->id} ({$restaurant->name}) from '{$previousTier}' to 'free'. Expired: {$restaurant->subscription_expires_at->toDateTimeString()}.");

                $this->line("  Downgraded: #{$restaurant->id} {$restaurant->name} ({$previousTier} → free)");

            } catch (\Exception $e) {
                Log::error("famer:downgrade-expired — failed for restaurant #{$restaurant->id} ({$restaurant->name}): {$e->getMessage()}");
                $this->error("  Failed: #{$restaurant->id} {$restaurant->name} — {$e->getMessage()}");
            }
        }

        $this->info("famer:downgrade-expired complete — {$downgraded}/{$count} subscriptions downgraded.");
        Log::info("famer:downgrade-expired completed: {$downgraded}/{$count} downgraded.");

        return Command::SUCCESS;
    }
}
