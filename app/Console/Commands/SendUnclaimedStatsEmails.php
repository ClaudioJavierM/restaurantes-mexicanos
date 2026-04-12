<?php

namespace App\Console\Commands;

use App\Jobs\SendUnclaimedStatsEmailJob;
use App\Models\EmailSuppression;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUnclaimedStatsEmails extends Command
{
    protected $signature = 'famer:send-unclaimed-stats
                            {--limit=200 : Maximum number of emails to dispatch}
                            {--dry-run : Preview how many would be dispatched without sending}';

    protected $description = 'Send monthly organic traffic stats emails to unclaimed restaurants';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Emails to suppress
        $suppressedEmails = EmailSuppression::pluck('email')->map(fn ($e) => strtolower(trim($e)))->all();

        $query = Restaurant::query()
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('is_claimed', false)
                  ->orWhereNull('is_claimed');
            })
            ->where(function ($q) {
                $q->whereNotNull('owner_email')
                  ->orWhereNotNull('email');
            })
            ->where(function ($q) {
                $q->whereNull('unclaimed_stats_sent_at')
                  ->orWhere('unclaimed_stats_sent_at', '<', now()->subDays(25));
            })
            ->limit($limit);

        // Exclude suppressed emails at DB level where possible
        if (!empty($suppressedEmails)) {
            $query->where(function ($q) use ($suppressedEmails) {
                // Exclude if owner_email is suppressed (or null) AND email is suppressed (or null)
                $q->whereNotIn('owner_email', $suppressedEmails)
                  ->orWhereNull('owner_email');
            })->where(function ($q) use ($suppressedEmails) {
                $q->whereNotIn('email', $suppressedEmails)
                  ->orWhereNull('email');
            });
        }

        $restaurants = $query->get();

        // Filter out rows where the resolved email (owner_email ?? email) is suppressed
        $toDispatch = $restaurants->filter(function ($restaurant) use ($suppressedEmails) {
            $email = strtolower(trim($restaurant->owner_email ?? $restaurant->email ?? ''));
            if (!$email) return false;
            return !in_array($email, $suppressedEmails);
        });

        $count = $toDispatch->count();

        if ($dryRun) {
            $this->info("[dry-run] Would dispatch unclaimed stats for {$count} restaurants (limit: {$limit}).");
            return Command::SUCCESS;
        }

        $i = 0;
        foreach ($toDispatch as $restaurant) {
            SendUnclaimedStatsEmailJob::dispatch($restaurant->id)
                ->delay(now()->addSeconds($i * 2)); // 2-second stagger
            $i++;
        }

        $this->info("Dispatched unclaimed stats emails for {$count} restaurants.");
        Log::info("famer:send-unclaimed-stats dispatched {$count} jobs (limit: {$limit}).");

        return Command::SUCCESS;
    }
}
