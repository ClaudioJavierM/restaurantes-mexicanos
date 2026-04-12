<?php

namespace App\Console\Commands;

use App\Jobs\SendTrialEndingReminderJob;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTrialEndingReminders extends Command
{
    protected $signature   = 'famer:send-trial-ending-reminders';
    protected $description = 'Send trial ending reminder emails to Elite owners 5 days before trial expires';

    public function handle(): int
    {
        $restaurants = Restaurant::where('subscription_tier', 'elite')
            ->where('subscription_status', 'active')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now()->addDays(4), now()->addDays(6)])
            ->get();

        $count = 0;
        foreach ($restaurants as $restaurant) {
            SendTrialEndingReminderJob::dispatch($restaurant->id);
            $count++;
        }

        $this->info("Dispatched trial ending reminders for {$count} restaurants.");
        Log::info("famer:send-trial-ending-reminders dispatched {$count} jobs.");

        return Command::SUCCESS;
    }
}
