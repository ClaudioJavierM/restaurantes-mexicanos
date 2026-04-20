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

    protected $description = 'Send congratulatory emails when restaurants reach 50, 100, or 150 view milestones';

    // Each milestone has its own tracking column so restaurants receive all 3 emails as they grow
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

        $restaurants = DB::table('restaurants as r')
            ->join(DB::raw('(SELECT restaurant_id, COUNT(*) as view_count FROM analytics_events WHERE event_type = "page_view" GROUP BY restaurant_id) as v'), 'v.restaurant_id', '=', 'r.id')
            ->where('r.status', 'approved')
            ->where('r.country', 'US')
            ->whereNull('r.subscription_status')
            ->whereNotNull('r.email')
            ->where('r.email', '!=', '')
            ->where('v.view_count', '>=', $milestone)
            ->whereNull("r.{$column}")
            ->select('r.id', 'r.name', 'r.email', 'v.view_count')
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

        foreach ($restaurants as $row) {
            $restaurant = Restaurant::find($row->id);
            if (!$restaurant) continue;

            if ($dryRun) {
                $this->line("DRY RUN [{$milestone}]: {$restaurant->name} <{$restaurant->email}> — {$row->view_count} views");
                continue;
            }

            try {
                $mailable = new MilestoneViewsMail($restaurant, (int) $row->view_count, $milestone);
                Mail::to($restaurant->email)->send($mailable);

                EmailLog::create([
                    'restaurant_id' => $restaurant->id,
                    'from_email'    => config('mail.from.address'),
                    'to_email'      => $restaurant->email,
                    'subject'       => "🎉 {$restaurant->name} alcanzó {$milestone} visitas en FAMER",
                    'category'      => "milestone_views_{$milestone}",
                    'status'        => 'sent',
                    'sent_at'       => now(),
                ]);

                $restaurant->update([$column => now()]);

                $sent++;
                $this->line("✓ [{$milestone}] {$restaurant->name} ({$row->view_count} views)");

                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                Log::error("MilestoneViews {$milestone} error for {$restaurant->name}: " . $e->getMessage());
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
}
