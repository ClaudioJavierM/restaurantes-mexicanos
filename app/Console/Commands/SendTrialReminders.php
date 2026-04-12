<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingMail;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTrialReminders extends Command
{
    protected $signature = 'famer:send-trial-reminders
                            {--dry-run : Preview how many would be sent without actually sending}
                            {--limit=50 : Maximum number of emails to send per run}';

    protected $description = 'Send trial-ending reminder emails to Elite owners whose trial expires within 4 days';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Find Elite restaurants whose trial ends within the next 4 days
        // and have not yet received a reminder
        $restaurants = Restaurant::query()
            ->where('subscription_tier', 'elite')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(4)])
            ->whereNull('trial_reminder_sent_at')
            ->whereNotNull('user_id')
            ->with('user')
            ->limit($limit)
            ->get();

        $count = $restaurants->count();

        if ($dryRun) {
            $this->info("[dry-run] Would send trial reminder emails to {$count} Elite owners (limit: {$limit}).");

            foreach ($restaurants as $restaurant) {
                $daysLeft = (int) now()->diffInDays($restaurant->trial_ends_at, false);
                $daysLeft = max(0, $daysLeft);
                $this->line("  - [{$restaurant->country}] {$restaurant->name} (trial ends: {$restaurant->trial_ends_at->toDateString()}, days left: {$daysLeft}, user: {$restaurant->user?->email})");
            }

            return Command::SUCCESS;
        }

        $sent = 0;

        foreach ($restaurants as $index => $restaurant) {
            $user = $restaurant->user;

            if (! $user || ! $user->email) {
                Log::warning("famer:send-trial-reminders — skipping restaurant #{$restaurant->id} ({$restaurant->name}): no user or email.");
                continue;
            }

            $daysLeft = (int) now()->diffInDays($restaurant->trial_ends_at, false);
            $daysLeft = max(0, $daysLeft);

            try {
                Mail::to($user->email, $user->name)
                    ->send(new TrialEndingMail($restaurant, $user, $daysLeft));

                $restaurant->update(['trial_reminder_sent_at' => now()]);

                $this->line("  Sent to {$user->email} — {$restaurant->name} ({$daysLeft} days left)");
                $sent++;

                Log::info("famer:send-trial-reminders — sent to {$user->email} for restaurant #{$restaurant->id} ({$restaurant->name}), {$daysLeft} days left.");

            } catch (\Exception $e) {
                Log::error("famer:send-trial-reminders — failed for restaurant #{$restaurant->id} ({$restaurant->name}): {$e->getMessage()}");
                $this->error("  Failed: {$restaurant->name} — {$e->getMessage()}");
            }

            // 2-second sleep between sends to avoid rate-limit spikes
            if ($index < $count - 1) {
                sleep(2);
            }
        }

        $this->info("famer:send-trial-reminders complete — {$sent}/{$count} emails sent.");
        Log::info("famer:send-trial-reminders completed: {$sent}/{$count} sent (limit: {$limit}).");

        return Command::SUCCESS;
    }
}
