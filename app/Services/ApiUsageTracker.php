<?php

namespace App\Services;

use App\Models\ApiCallLog;

use App\Models\ApiUsage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApiUsageTracker
{
    // Google Places API pricing (per 1,000 requests)
    const COST_TEXT_SEARCH = 32.00;      // $32 per 1,000
    const COST_PLACE_DETAILS = 17.00;    // $17 per 1,000
    const COST_STREET_VIEW = 7.00;       // $7 per 1,000

    /**
     * Track API usage
     */
    public static function track(string $service, string $endpoint, int $requestCount = 1, array $metadata = []): void
    {
        $cost = self::calculateCost($service, $requestCount);

        // Also log to api_call_logs for dashboard
        try {
            ApiCallLog::create([
                'service' => str_replace('google_places_', '', $service),
                'endpoint' => $endpoint,
                'status_code' => 200,
                'success' => true,
                'cost' => $cost,
                'params' => $metadata,
                'called_at' => now(),
            ]);
        } catch (\Exception $e) {}

        // Original ApiUsage tracking (accumulate, not overwrite)
        $record = ApiUsage::firstOrCreate([
            'usage_date' => today(),
            'service' => $service,
            'endpoint' => $endpoint,
        ], [
            'requests_count' => 0,
            'estimated_cost' => 0.0,
            'metadata' => $metadata,
        ]);

        $record->requests_count += $requestCount;
        $record->estimated_cost = (float) $record->estimated_cost + $cost;
        $record->metadata = $metadata;
        $record->save();

        // Check if we're approaching limits
        self::checkLimits();
    }

    /**
     * Calculate cost based on service type
     */
    private static function calculateCost(string $service, int $requestCount): float
    {
        $costPer1000 = match($service) {
            'google_places_text_search' => self::COST_TEXT_SEARCH,
            'google_places_details' => self::COST_PLACE_DETAILS,
            'google_street_view' => self::COST_STREET_VIEW,
            'google_places_photo' => 7.00,   // $7 per 1,000 photo requests
            default => 0,
        };

        return ($costPer1000 / 1000) * $requestCount;
    }

    /**
     * Check if request can be made
     */
    public static function canMakeRequest(string $service, int $requestCount = 1): array
    {
        $estimatedCost = self::calculateCost($service, $requestCount);
        
        // Check daily limit
        if (!ApiUsage::canMakeRequest($requestCount)) {
            return [
                'allowed' => false,
                'reason' => 'daily_limit_exceeded',
                'message' => 'Daily request limit exceeded. Try again tomorrow.',
                'daily_requests' => ApiUsage::getTodayRequests(),
            ];
        }

        // Check monthly budget
        if (!ApiUsage::withinMonthlyBudget($estimatedCost)) {
            return [
                'allowed' => false,
                'reason' => 'monthly_budget_exceeded',
                'message' => 'Monthly budget limit exceeded. Wait until next month.',
                'monthly_cost' => ApiUsage::getCurrentMonthCost(),
            ];
        }

        return [
            'allowed' => true,
            'estimated_cost' => $estimatedCost,
        ];
    }

    /**
     * Check limits and send alerts
     */
    private static function checkLimits(): void
    {
        $monthlyBudget = (float) config('services.google.monthly_budget_limit', 180);
        $alertThreshold = (float) config('services.google.alert_threshold', 150);
        $currentCost = ApiUsage::getCurrentMonthCost();

        // Alert if approaching limit (75% of budget)
        if ($currentCost >= $alertThreshold && $currentCost < $monthlyBudget) {
            Log::warning('Google API usage approaching limit', [
                'current_cost' => $currentCost,
                'budget' => $monthlyBudget,
                'percentage' => ($currentCost / $monthlyBudget) * 100,
            ]);

            self::sendTelegramAlert(
                "⚠️ *Google API — Limite Proximo*\n" .
                "Costo actual: \${$currentCost}\n" .
                "Presupuesto: \${$monthlyBudget}\n" .
                'Uso: ' . round(($currentCost / $monthlyBudget) * 100, 1) . '%'
            );
        }

        // Critical alert if exceeded
        if ($currentCost >= $monthlyBudget) {
            Log::critical('Google API monthly budget exceeded!', [
                'current_cost' => $currentCost,
                'budget' => $monthlyBudget,
            ]);

            self::sendTelegramAlert(
                "🚨 *Google API — Presupuesto EXCEDIDO*\n" .
                "Costo actual: \${$currentCost}\n" .
                "Presupuesto: \${$monthlyBudget}\n" .
                'Las llamadas pueden estar siendo bloqueadas.'
            );
        }
    }

    /**
     * Send a Telegram alert. Silently skips if token/chat_id not configured.
     */
    private static function sendTelegramAlert(string $message): void
    {
        $token  = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            return;
        }

        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::warning('ApiUsageTracker: Telegram notification failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get usage statistics
     */
    public static function getStats(): array
    {
        return [
            'today' => [
                'requests' => ApiUsage::getTodayRequests(),
                'cost' => ApiUsage::getTodayCost(),
            ],
            'this_month' => [
                'cost' => ApiUsage::getCurrentMonthCost(),
                'budget' => (float) config('services.google.monthly_budget_limit', 180),
                'remaining' => (float) config('services.google.monthly_budget_limit', 180) - ApiUsage::getCurrentMonthCost(),
                'percentage_used' => (ApiUsage::getCurrentMonthCost() / (float) config('services.google.monthly_budget_limit', 180)) * 100,
            ],
            'limits' => [
                'daily_requests' => (int) config('services.google.daily_request_limit', 200),
                'monthly_budget' => (float) config('services.google.monthly_budget_limit', 180),
                'alert_threshold' => (float) config('services.google.alert_threshold', 150),
            ],
        ];
    }
}
