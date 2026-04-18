<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncResendEmailStatus extends Command
{
    protected $signature = 'famer:sync-resend-status
                            {--limit=500 : Max records to sync per run}
                            {--force : Re-sync records that already have a final status}';

    protected $description = 'Sync email delivery/open/click status from Resend API for records with a message_id';

    // Map Resend last_event → our status
    private const EVENT_MAP = [
        'sent'             => 'sent',
        'delivered'        => 'delivered',
        'delivery_delayed' => 'delayed',
        'bounced'          => 'bounced',
        'complained'       => 'complained',
        'opened'           => 'opened',
        'clicked'          => 'clicked',
    ];

    // Final statuses that don't need re-syncing (unless --force)
    private const FINAL_STATUSES = ['opened', 'clicked', 'bounced', 'complained'];

    public function handle(): int
    {
        $limit  = (int) $this->option('limit');
        $force  = $this->option('force');
        $apiKey = config('services.resend.key') ?? env('RESEND_API_KEY');

        if (!$apiKey) {
            $this->error('RESEND_API_KEY not set.');
            return Command::FAILURE;
        }

        $query = EmailLog::whereNotNull('message_id')
            ->where('message_id', '!=', '')
            ->whereNotNull('from_email'); // FAMER emails only (excludes SDV)

        if (!$force) {
            $query->whereNotIn('status', self::FINAL_STATUSES);
        }

        $logs = $query->limit($limit)->get();

        if ($logs->isEmpty()) {
            $this->info('No records to sync.');
            return Command::SUCCESS;
        }

        $this->info("Syncing {$logs->count()} email records from Resend...");
        $bar = $this->output->createProgressBar($logs->count());
        $bar->start();

        $updated = 0;
        $errors  = 0;

        foreach ($logs as $log) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(5)
                    ->get("https://api.resend.com/emails/{$log->message_id}");

                if ($response->failed()) {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $data      = $response->json();
                $lastEvent = $data['last_event'] ?? null;
                $newStatus = self::EVENT_MAP[$lastEvent] ?? null;

                if (!$newStatus || $newStatus === $log->status) {
                    $bar->advance();
                    continue;
                }

                $changes = ['status' => $newStatus];

                // Set timestamps for events we track
                if ($newStatus === 'delivered' && !$log->delivered_at) {
                    $changes['delivered_at'] = now();
                }
                if ($newStatus === 'opened' && !$log->opened_at) {
                    $changes['opened_at'] = now();
                }
                if ($newStatus === 'clicked' && !$log->clicked_at) {
                    $changes['clicked_at'] = now();
                }
                if ($newStatus === 'bounced' && !$log->bounced_at) {
                    $changes['bounced_at'] = now();
                }

                $log->update($changes);
                $updated++;

            } catch (\Throwable $e) {
                Log::warning("SyncResendEmailStatus: failed for log {$log->id} — {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
            usleep(50000); // 50ms between requests → ~20 req/sec, well within Resend limits
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Records checked', $logs->count()],
                ['Updated',         $updated],
                ['Errors / skipped', $errors],
            ]
        );

        return Command::SUCCESS;
    }
}
