<?php

namespace App\Console\Commands;

use App\Mail\MilestoneViewsMail;
use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMilestoneViewsEmails extends Command
{
    protected $signature = 'famer:send-milestone-views
                            {--milestone=50 : View milestone to target (50, 100, or 150)}
                            {--limit=200 : Max emails to send per run}
                            {--dry-run : Show what would be sent without sending}';

    protected $description = 'Send personalized milestone emails based on restaurant status (unclaimed/claimed/premium/elite)';

    private const MILESTONE_COLUMNS = [
        50  => 'milestone_50_sent_at',
        100 => 'milestone_100_sent_at',
        150 => 'milestone_150_sent_at',
    ];

    public function handle(): int
    {
        $milestone = (int) $this->option('milestone');
        $limit     = (int) $this->option('limit');
        $dryRun    = $this->option('dry-run');

        if (!isset(self::MILESTONE_COLUMNS[$milestone])) {
            $this->error("Invalid milestone. Use 50, 100, or 150.");
            return Command::FAILURE;
        }

        $column = self::MILESTONE_COLUMNS[$milestone];

        $this->info("Milestone {$milestone} views emails (limit: {$limit})...");

        // All approved restaurants with email that haven't received this milestone yet
        $restaurants = DB::table('restaurants as r')
            ->join(
                DB::raw('(SELECT restaurant_id, COUNT(*) as view_count FROM analytics_events WHERE event_type = "page_view" GROUP BY restaurant_id) as v'),
                'v.restaurant_id', '=', 'r.id'
            )
            ->where('r.status', 'approved')
            ->where('r.country', 'US')
            ->whereNotNull('r.email')
            ->where('r.email', '!=', '')
            ->where('v.view_count', '>=', $milestone)
            ->whereNull("r.{$column}")
            ->select('r.id', 'r.name', 'r.email', 'r.subscription_tier', 'r.subscription_status', 'r.is_claimed', 'v.view_count')
            ->orderByDesc('v.view_count')
            ->limit($limit)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info("No restaurants pending milestone {$milestone} email.");
            return Command::SUCCESS;
        }

        $this->info("Found {$restaurants->count()} restaurants.");

        $sent = 0;
        $errors = 0;
        $byStatus = ['unclaimed' => 0, 'claimed' => 0, 'premium' => 0, 'elite' => 0];

        foreach ($restaurants as $row) {
            $restaurant = Restaurant::find($row->id);
            if (!$restaurant) continue;

            $status = $this->detectStatus($row);
            $byStatus[$status]++;

            if ($dryRun) {
                $this->line("DRY RUN [{$milestone}][{$status}]: {$restaurant->name} — {$row->view_count} views");
                continue;
            }

            try {
                $mailable = new MilestoneViewsMail($restaurant, (int) $row->view_count, $milestone, $status);
                Mail::to($restaurant->email)->send($mailable);

                EmailLog::create([
                    'restaurant_id' => $restaurant->id,
                    'from_email'    => config('mail.from.address'),
                    'to_email'      => $restaurant->email,
                    'subject'       => "Milestone {$milestone} views [{$status}]",
                    'category'      => "milestone_{$milestone}_{$status}",
                    'status'        => 'sent',
                    'sent_at'       => now(),
                ]);

                $restaurant->update([$column => now()]);

                $sent++;
                $this->line("✓ [{$milestone}][{$status}] {$restaurant->name} ({$row->view_count} views)");

                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                Log::error("Milestone {$milestone} email error for {$restaurant->name}: " . $e->getMessage());
                $this->error("✗ {$restaurant->name}: " . $e->getMessage());
            }
        }

        if (!$dryRun) {
            $this->newLine();
            $this->info("Sent: {$sent}");
            $this->table(['Status', 'Count'], collect($byStatus)->map(fn($v, $k) => [$k, $v])->values()->toArray());
            if ($errors > 0) $this->error("Errors: {$errors}");
        }

        return Command::SUCCESS;
    }

    private function detectStatus(object $row): string
    {
        if ($row->subscription_tier === 'elite' && $row->subscription_status === 'active') {
            return 'elite';
        }
        if ($row->subscription_tier === 'premium' && $row->subscription_status === 'active') {
            return 'premium';
        }
        if ($row->is_claimed) {
            return 'claimed';
        }
        return 'unclaimed';
    }
}
