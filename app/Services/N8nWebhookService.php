<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nWebhookService
{
    protected ?string $webhookUrl;

    public function __construct()
    {
        // Use the unified notifications hub webhook
        $this->webhookUrl = config('services.n8n.webhook_url') ?: 'https://n8n.mefimports.com/webhook/notify';
    }

    /**
     * Send job failure notification to n8n
     */
    public function notifyJobFailure(string $jobName, string $description, ?string $errorMessage = null): bool
    {
        if (!$this->webhookUrl) {
            Log::warning('N8N webhook URL not configured');
            return false;
        }

        try {
            // Get recent error logs for context
            $recentErrors = $this->getRecentErrors();

            $payload = [
                'event' => 'job_failure',
                'timestamp' => now()->toIso8601String(),
                'server' => [
                    'hostname' => gethostname(),
                    'ip' => request()->server('SERVER_ADDR') ?? '160.153.183.38',
                    'path' => base_path(),
                ],
                'job' => [
                    'name' => $jobName,
                    'description' => $description,
                    'scheduled_at' => now()->format('Y-m-d H:i:s'),
                ],
                'error' => [
                    'message' => $errorMessage ?? 'Unknown error',
                    'recent_logs' => $recentErrors,
                ],
                'context' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'environment' => app()->environment(),
                ],
            ];

            $response = Http::timeout(30)->post($this->webhookUrl, $payload);

            if ($response->successful()) {
                Log::info("N8N webhook sent successfully for job: {$jobName}");
                return true;
            }

            Log::error("N8N webhook failed", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error("N8N webhook exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send custom event to n8n
     */
    public function sendEvent(string $eventType, array $data): bool
    {
        if (!$this->webhookUrl) {
            return false;
        }

        try {
            $payload = [
                'event' => $eventType,
                'timestamp' => now()->toIso8601String(),
                'data' => $data,
            ];

            $response = Http::timeout(30)->post($this->webhookUrl, $payload);
            return $response->successful();

        } catch (\Exception $e) {
            Log::error("N8N webhook exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent error logs for AI context
     */
    protected function getRecentErrors(int $lines = 50): array
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return [];
        }

        try {
            // Get last N lines of log file
            $output = [];
            $fp = fopen($logFile, 'r');

            if (!$fp) {
                return [];
            }

            // Read file backwards to get recent entries
            fseek($fp, -1, SEEK_END);
            $pos = ftell($fp);
            $lastLine = '';
            $lineCount = 0;

            while ($pos > 0 && $lineCount < $lines) {
                $char = fgetc($fp);
                if ($char === "\n") {
                    if ($lastLine !== '') {
                        // Only include ERROR lines
                        if (str_contains($lastLine, 'ERROR') || str_contains($lastLine, 'CRITICAL')) {
                            array_unshift($output, trim($lastLine));
                            $lineCount++;
                        }
                    }
                    $lastLine = '';
                } else {
                    $lastLine = $char . $lastLine;
                }
                fseek($fp, --$pos);
            }

            fclose($fp);

            return array_slice($output, 0, 20); // Limit to 20 error lines

        } catch (\Exception $e) {
            return ["Error reading logs: " . $e->getMessage()];
        }
    }

    /**
     * Send daily summary to n8n
     */
    public function sendDailySummary(array $stats): bool
    {
        return $this->sendEvent('daily_summary', $stats);
    }

    /**
     * Notify API key expiration
     */
    public function notifyApiKeyExpired(string $service, string $keyName): bool
    {
        return $this->sendEvent('api_key_expired', [
            'service' => $service,
            'key_name' => $keyName,
            'detected_at' => now()->toIso8601String(),
        ]);
    }
}
