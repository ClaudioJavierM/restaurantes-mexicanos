<?php

namespace App\Console\Commands;

use App\Services\ReviewRequestService;
use Illuminate\Console\Command;

class SendReviewRequests extends Command
{
    protected $signature = 'reviews:send-requests
                            {--dry-run : Preview recipients without sending any SMS}';

    protected $description = 'Send review-request SMS to customers whose order/reservation was completed 1–4 hours ago.';

    public function handle(ReviewRequestService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY-RUN mode — no SMS will be sent.');
        }

        $this->info('Scanning completed orders and reservations (1–4 h window)…');

        $stats = $service->sendPendingRequests(dryRun: $dryRun);

        $label = $dryRun ? 'Would send' : 'Sent';

        $this->table(
            ['Result', 'Count'],
            [
                [$label,    $stats['sent']],
                ['Skipped', $stats['skipped']],
                ['Failed',  $stats['failed']],
            ]
        );

        $this->info("Done. {$stats['sent']} review request(s) " . ($dryRun ? 'queued (dry-run).' : 'sent.'));

        // Return non-zero exit code only when failures exceed sent, signalling a real problem.
        return ($stats['failed'] > 0 && $stats['sent'] === 0)
            ? self::FAILURE
            : self::SUCCESS;
    }
}
