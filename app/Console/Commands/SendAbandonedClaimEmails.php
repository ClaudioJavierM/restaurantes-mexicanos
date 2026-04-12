<?php

namespace App\Console\Commands;

use App\Jobs\SendAbandonedClaimEmailJob;
use App\Models\EmailSuppression;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAbandonedClaimEmails extends Command
{
    protected $signature = 'famer:send-abandoned-claims
                            {--limit=200 : Maximum number of emails to dispatch}
                            {--dry-run : Preview how many would be dispatched without sending}';

    protected $description = 'Send abandoned claim recovery emails to restaurants that started but did not finish claiming';

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Suppressed emails list
        $suppressedEmails = EmailSuppression::pluck('email')
            ->map(fn ($e) => strtolower(trim($e)))
            ->all();

        // Window: 20h–48h since claim_started_at, user_id NULL, not yet sent, has email
        $query = Restaurant::query()
            ->where('status', 'approved')
            ->whereNotNull('claim_started_at')
            ->whereNull('user_id')
            ->whereNull('claim_abandoned_sent_at')
            ->where('claim_started_at', '<=', now()->subHours(20))
            ->where('claim_started_at', '>=', now()->subHours(48))
            ->where(function ($q) {
                $q->whereNotNull('owner_email')
                  ->orWhereNotNull('email');
            })
            ->limit($limit);

        // Exclude suppressed at DB level
        if (!empty($suppressedEmails)) {
            $query->where(function ($q) use ($suppressedEmails) {
                $q->whereNotIn('owner_email', $suppressedEmails)
                  ->orWhereNull('owner_email');
            })->where(function ($q) use ($suppressedEmails) {
                $q->whereNotIn('email', $suppressedEmails)
                  ->orWhereNull('email');
            });
        }

        $restaurants = $query->get();

        // Final filter: resolve email and check suppression
        $toDispatch = $restaurants->filter(function ($restaurant) use ($suppressedEmails) {
            $email = strtolower(trim($restaurant->owner_email ?? $restaurant->email ?? ''));
            if (!$email) return false;
            return !in_array($email, $suppressedEmails);
        });

        $count = $toDispatch->count();

        if ($dryRun) {
            $this->info("[dry-run] Would dispatch abandoned claim emails for {$count} restaurants (limit: {$limit}).");
            return Command::SUCCESS;
        }

        $i = 0;
        foreach ($toDispatch as $restaurant) {
            SendAbandonedClaimEmailJob::dispatch($restaurant->id)
                ->delay(now()->addSeconds($i * 2)); // 2-second stagger
            $i++;
        }

        $this->info("Dispatched abandoned claim emails for {$count} restaurants.");
        Log::info("famer:send-abandoned-claims dispatched {$count} jobs (limit: {$limit}).");

        return Command::SUCCESS;
    }
}
