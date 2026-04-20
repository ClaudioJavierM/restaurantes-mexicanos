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
                            {--limit=200 : Max emails to send per run}
                            {--min-views=50 : Minimum view count threshold}
                            {--dry-run : Show what would be sent without sending}';

    protected $description = 'Send congratulatory emails to unclaimed restaurants that have reached view milestones';

    public function handle(): int
    {
        $limit    = (int) $this->option('limit');
        $minViews = (int) $this->option('min-views');
        $dryRun   = $this->option('dry-run');

        $this->info("Sending milestone views emails (min: {$minViews} views, limit: {$limit})...");

        // Get restaurants with enough views, unclaimed, with email, not recently emailed
        $restaurants = DB::table('restaurants as r')
            ->join(DB::raw('(SELECT restaurant_id, COUNT(*) as view_count FROM analytics_events WHERE event_type = "page_view" GROUP BY restaurant_id) as v'), 'v.restaurant_id', '=', 'r.id')
            ->where('r.status', 'approved')
            ->where('r.country', 'US')
            ->whereNull('r.subscription_status')
            ->whereNotNull('r.email')
            ->where('r.email', '!=', '')
            ->where('v.view_count', '>=', $minViews)
            ->where(function ($q) {
                $q->whereNull('r.milestone_views_sent_at')
                  ->orWhere('r.milestone_views_sent_at', '<', now()->subDays(90));
            })
            ->select('r.id', 'r.name', 'r.email', 'v.view_count')
            ->orderByDesc('v.view_count')
            ->limit($limit)
            ->get();

        if ($restaurants->isEmpty()) {
            $this->info('No restaurants to email.');
            return Command::SUCCESS;
        }

        $this->info("Found {$restaurants->count()} restaurants to email.");

        $sent = 0;
        $errors = 0;

        foreach ($restaurants as $row) {
            $restaurant = Restaurant::find($row->id);
            if (!$restaurant) continue;

            if ($dryRun) {
                $this->line("DRY RUN: {$restaurant->name} <{$restaurant->email}> — {$row->view_count} views");
                continue;
            }

            try {
                $mailable = new MilestoneViewsMail($restaurant, (int) $row->view_count);
                $result = Mail::to($restaurant->email)->send($mailable);

                $messageId = null;
                if (method_exists($result, 'getSymfonyMessage')) {
                    $messageId = $result->getSymfonyMessage()->generateMessageId();
                }

                EmailLog::create([
                    'restaurant_id' => $restaurant->id,
                    'from_email'    => config('mail.from.address'),
                    'to_email'      => $restaurant->email,
                    'subject'       => "🎉 {$restaurant->name} tuvo {$row->view_count} visitas",
                    'category'      => 'milestone_views',
                    'status'        => 'sent',
                    'resend_id'     => $messageId,
                    'sent_at'       => now(),
                ]);

                $restaurant->update(['milestone_views_sent_at' => now()]);

                $sent++;
                $this->line("✓ {$restaurant->name} <{$restaurant->email}> ({$row->view_count} views)");

                sleep(1);

            } catch (\Exception $e) {
                $errors++;
                Log::error("MilestoneViews email error for {$restaurant->name}: " . $e->getMessage());
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
