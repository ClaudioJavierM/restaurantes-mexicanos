<?php

namespace App\Console\Commands;

use App\Mail\MilestoneViewsMail;
use App\Models\EmailLog;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMonthlyMilestoneEmails extends Command
{
    protected $signature = 'famer:send-monthly-milestone-views
                            {--milestone=50 : Monthly view milestone (50, 100, or 150)}
                            {--limit=200 : Max emails per run}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send milestone emails based on views THIS MONTH — repeats every month the threshold is hit';

    private const MILESTONE_COLUMNS = [
        50  => 'monthly_milestone_50_month',
        100 => 'monthly_milestone_100_month',
        150 => 'monthly_milestone_150_month',
    ];

    public function handle(): int
    {
        $milestone   = (int) $this->option('milestone');
        $limit       = (int) $this->option('limit');
        $dryRun      = $this->option('dry-run');
        $currentMonth = now()->format('Y-m'); // e.g. "2026-04"

        if (!isset(self::MILESTONE_COLUMNS[$milestone])) {
            $this->error("Invalid milestone. Use 50, 100, or 150.");
            return Command::FAILURE;
        }

        $column = self::MILESTONE_COLUMNS[$milestone];

        $this->info("Monthly milestone {$milestone} views for {$currentMonth} (limit: {$limit})...");

        // Views THIS month only
        $restaurants = DB::table('restaurants as r')
            ->join(
                DB::raw('(SELECT restaurant_id, COUNT(*) as view_count FROM analytics_events WHERE event_type = "page_view" AND YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) GROUP BY restaurant_id) as v'),
                'v.restaurant_id', '=', 'r.id'
            )
            ->where('r.status', 'approved')
            ->where('r.country', 'US')
            ->whereNotNull('r.email')
            ->where('r.email', '!=', '')
            ->where('v.view_count', '>=', $milestone)
            ->where(function ($q) use ($column, $currentMonth) {
                // Not sent this month yet
                $q->whereNull("r.{$column}")
                  ->orWhere("r.{$column}", '!=', $currentMonth);
            })
            ->select('r.id', 'r.name', 'r.email', 'r.subscription_tier', 'r.subscription_status', 'r.is_claimed', 'v.view_count')
            ->orderByDesc('v.view_count')
            ->limit($limit)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info("No restaurants pending monthly milestone {$milestone} for {$currentMonth}.");
            return Command::SUCCESS;
        }

        $this->info("Found {$restaurants->count()} restaurants.");

        $sent = 0;
        $errors = 0;

        foreach ($restaurants as $row) {
            $restaurant = Restaurant::find($row->id);
            if (!$restaurant) continue;

            $status = $this->detectStatus($row);

            if ($dryRun) {
                $this->line("DRY RUN [monthly-{$milestone}][{$status}]: {$restaurant->name} — {$row->view_count} views this month");
                continue;
            }

            try {
                $mailable = new MilestoneViewsMail($restaurant, (int) $row->view_count, $milestone, $status);
                Mail::to($restaurant->email)->send($mailable);

                EmailLog::create([
                    'restaurant_id' => $restaurant->id,
                    'from_email'    => config('mail.from.address'),
                    'to_email'      => $restaurant->email,
                    'subject'       => "Monthly milestone {$milestone} views [{$status}] {$currentMonth}",
                    'category'      => "monthly_milestone_{$milestone}_{$status}",
                    'status'        => 'sent',
                    'sent_at'       => now(),
                ]);

                // Store month string so next month it fires again automatically
                $restaurant->update([$column => $currentMonth]);

                $sent++;
                $this->line("✓ [monthly-{$milestone}][{$status}] {$restaurant->name} ({$row->view_count} views/mes)");

                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                Log::error("Monthly milestone {$milestone} error for {$restaurant->name}: " . $e->getMessage());
                $this->error("✗ {$restaurant->name}: " . $e->getMessage());
            }
        }

        if (!$dryRun) {
            $this->newLine();
            $this->info("Sent: {$sent}");
            if ($errors > 0) $this->error("Errors: {$errors}");
        }

        return Command::SUCCESS;
    }

    private function detectStatus(object $row): string
    {
        if ($row->subscription_tier === 'elite' && $row->subscription_status === 'active') return 'elite';
        if ($row->subscription_tier === 'premium' && $row->subscription_status === 'active') return 'premium';
        if ($row->is_claimed) return 'claimed';
        return 'unclaimed';
    }
}
