<?php
namespace App\Console\Commands;

use App\Jobs\SendWeeklyStatsEmailJob;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyStats extends Command
{
    protected $signature   = 'famer:send-weekly-stats {--tier= : Filter by tier (free,claimed,premium,elite)}';
    protected $description = 'Send weekly stats emails to all restaurant owners';

    public function handle(): int
    {
        $query = Restaurant::with('user')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNotNull('owner_email')
                  ->orWhereHas('user', fn($u) => $u->whereNotNull('email'));
            });

        if ($tier = $this->option('tier')) {
            $query->where('subscription_tier', $tier);
        }

        $restaurants = $query->get();
        $count = 0;

        foreach ($restaurants as $restaurant) {
            // Stagger dispatch to avoid hitting Resend rate limit (5/sec)
            SendWeeklyStatsEmailJob::dispatch($restaurant->id)
                ->delay(now()->addSeconds($count * 1)); // 1 email/sec = 3,600/hr
            $count++;
        }

        $this->info("Dispatched weekly stats for {$count} restaurants.");
        Log::info("famer:send-weekly-stats dispatched {$count} jobs.");

        return Command::SUCCESS;
    }
}
