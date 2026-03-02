<?php

namespace App\Jobs;

use App\Services\SmsAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSmsAutomations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300; // 5 minutes

    public function __construct()
    {
        //
    }

    public function handle(SmsAutomationService $service): void
    {
        Log::info('ProcessSmsAutomations: Starting...');

        try {
            $results = $service->processAllAutomations();

            Log::info('ProcessSmsAutomations: Completed', [
                'processed' => $results['processed'],
                'sent' => $results['sent'],
                'failed' => $results['failed'],
                'skipped' => $results['skipped'],
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessSmsAutomations: Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessSmsAutomations: Job failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
