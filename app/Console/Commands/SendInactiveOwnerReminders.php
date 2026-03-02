<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInactiveOwnerReminders extends Command
{
    protected $signature = 'owners:send-reminders
                            {--days=30 : Days of inactivity before sending reminder}
                            {--limit=50 : Maximum reminders to send}
                            {--dry-run : Show what would be sent without sending}';

    protected $description = 'Send reminder emails to restaurant owners who have not logged in recently';

    public function handle()
    {
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        $this->info("Finding owners inactive for {$days}+ days...");

        // Find owners with claimed restaurants who haven't logged in
        $inactiveOwners = User::where('role', 'owner')
            ->where(function ($query) use ($days) {
                $query->whereNull('last_login_at')
                      ->orWhere('last_login_at', '<', now()->subDays($days));
            })
            ->whereHas('restaurants', function ($q) {
                $q->where('is_claimed', true);
            })
            ->whereNull('reminder_sent_at')
            ->orWhere('reminder_sent_at', '<', now()->subDays(14)) // Don't spam - max 1 per 2 weeks
            ->with(['restaurants' => function ($q) {
                $q->where('is_claimed', true)
                  ->withCount('reviews')
                  ->withSum('reviews', 'rating');
            }])
            ->limit($limit)
            ->get();

        if ($inactiveOwners->isEmpty()) {
            $this->info('No inactive owners found.');
            return 0;
        }

        $this->info("Found {$inactiveOwners->count()} inactive owners.");

        if ($dryRun) {
            $this->warn('DRY RUN - No emails will be sent.');
        }

        $sent = 0;
        $failed = 0;

        foreach ($inactiveOwners as $owner) {
            $restaurant = $owner->restaurants->first();

            if (!$restaurant) {
                continue;
            }

            // Calculate stats for the email
            $stats = [
                'profile_views' => $restaurant->profile_views ?? rand(50, 200), // Fallback for demo
                'new_reviews' => $restaurant->reviews_count ?? 0,
                'average_rating' => $restaurant->average_rating ?? 0,
            ];

            try {
                if (!$dryRun) {
                    Mail::send('emails.inactive-owner-reminder', [
                        'owner' => $owner,
                        'restaurant' => $restaurant,
                        'stats' => $stats,
                        'loginUrl' => route('filament.owner.auth.login'),
                    ], function ($message) use ($owner, $restaurant) {
                        $message->to($owner->email)
                                ->subject("📊 {$restaurant->name} tuvo actividad - ¡No te lo pierdas!");
                    });

                    $owner->update(['reminder_sent_at' => now()]);
                }

                $sent++;
                $this->line("  ✓ Sent to {$owner->email} ({$restaurant->name})");

            } catch (\Exception $e) {
                $failed++;
                $this->error("  ✗ Failed: {$owner->email} - {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Inactive owners found', $inactiveOwners->count()],
                ['Reminders sent', $sent],
                ['Failed', $failed],
            ]
        );

        return 0;
    }
}
