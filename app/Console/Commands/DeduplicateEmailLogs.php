<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeduplicateEmailLogs extends Command
{
    protected $signature = 'famer:deduplicate-email-logs
                            {--dry-run : Show how many would be deleted without deleting}';

    protected $description = 'Remove duplicate email_log records, keeping the best one per send (opened > delivered > sent, prefer records with message_id)';

    // Priority order: higher = better record to keep
    private const STATUS_PRIORITY = [
        'clicked'   => 6,
        'opened'    => 5,
        'delivered' => 4,
        'bounced'   => 3,
        'complained'=> 2,
        'delayed'   => 1,
        'sent'      => 0,
        'failed'    => -1,
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Finding duplicate email_log groups...');

        // Find all duplicate groups (same recipient + subject within same minute)
        $groups = DB::table('email_logs')
            ->selectRaw('to_email, subject, DATE_FORMAT(sent_at, "%Y-%m-%d %H:%i") as bucket')
            ->whereNotNull('from_email')
            ->groupByRaw('to_email, subject, DATE_FORMAT(sent_at, "%Y-%m-%d %H:%i")')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($groups->isEmpty()) {
            $this->info('No duplicates found. Database is clean.');
            return Command::SUCCESS;
        }

        $this->info("Found {$groups->count()} duplicate groups.");

        $toDelete  = [];
        $bar = $this->output->createProgressBar($groups->count());
        $bar->start();

        foreach ($groups as $group) {
            // Get all records in this duplicate group
            $records = DB::table('email_logs')
                ->whereNotNull('from_email')
                ->where('to_email', $group->to_email)
                ->where('subject', $group->subject)
                ->whereRaw('DATE_FORMAT(sent_at, "%Y-%m-%d %H:%i") = ?', [$group->bucket])
                ->orderByRaw('
                    CASE status
                        WHEN "clicked"    THEN 6
                        WHEN "opened"     THEN 5
                        WHEN "delivered"  THEN 4
                        WHEN "bounced"    THEN 3
                        WHEN "complained" THEN 2
                        WHEN "delayed"    THEN 1
                        WHEN "sent"       THEN 0
                        ELSE -1
                    END DESC,
                    (message_id IS NOT NULL AND message_id != "") DESC,
                    id DESC
                ')
                ->get(['id', 'status', 'message_id', 'opened_at', 'clicked_at', 'delivered_at']);

            // The first record (after ORDER BY) is the winner — delete the rest
            $winner = $records->shift();

            // If the winner is missing tracking data that a loser has, merge before deleting
            // (e.g., winner is "sent" but a duplicate has message_id)
            $updates = [];
            foreach ($records as $loser) {
                if (!$winner->message_id && $loser->message_id) {
                    $updates['message_id'] = $loser->message_id;
                }
                if (!$winner->opened_at && $loser->opened_at) {
                    $updates['opened_at'] = $loser->opened_at;
                    $updates['status']    = 'opened';
                }
                if (!$winner->clicked_at && $loser->clicked_at) {
                    $updates['clicked_at'] = $loser->clicked_at;
                    $updates['status']     = 'clicked';
                }
                if (!$winner->delivered_at && $loser->delivered_at) {
                    $updates['delivered_at'] = $loser->delivered_at;
                }
            }

            if (!empty($updates) && !$dryRun) {
                DB::table('email_logs')->where('id', $winner->id)->update($updates);
            }

            // Collect loser IDs for batch delete
            foreach ($records as $loser) {
                $toDelete[] = $loser->id;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $count = count($toDelete);

        if ($dryRun) {
            $this->warn("[dry-run] Would delete {$count} duplicate records across {$groups->count()} groups.");
            return Command::SUCCESS;
        }

        // Batch delete in chunks to avoid huge IN() clauses
        $chunks = array_chunk($toDelete, 500);
        foreach ($chunks as $chunk) {
            DB::table('email_logs')->whereIn('id', $chunk)->delete();
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Duplicate groups processed', $groups->count()],
                ['Records deleted',            $count],
                ['Records kept (winners)',     $groups->count()],
            ]
        );

        $this->info('Done. Each email send now has exactly one log record with the best available tracking data.');

        return Command::SUCCESS;
    }
}
