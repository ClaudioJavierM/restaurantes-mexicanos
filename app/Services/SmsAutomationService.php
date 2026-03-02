<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\RestaurantCustomer;
use App\Models\SmsAutomation;
use App\Models\SmsLog;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsAutomationService
{
    protected TwilioService $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    /**
     * Process all active automations
     */
    public function processAllAutomations(): array
    {
        $results = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        $automations = SmsAutomation::active()->with('restaurant')->get();

        foreach ($automations as $automation) {
            $automationResults = $this->processAutomation($automation);
            $results['processed']++;
            $results['sent'] += $automationResults['sent'];
            $results['failed'] += $automationResults['failed'];
            $results['skipped'] += $automationResults['skipped'];
        }

        return $results;
    }

    /**
     * Process a single automation
     */
    public function processAutomation(SmsAutomation $automation): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'skipped' => 0];

        $customers = $this->getEligibleCustomers($automation);

        foreach ($customers as $customer) {
            if (!$this->canSendSms($customer, $automation)) {
                $results['skipped']++;
                continue;
            }

            try {
                $this->sendAutomatedSms($automation, $customer);
                $results['sent']++;
            } catch (\Exception $e) {
                Log::error("SMS Automation failed", [
                    'automation_id' => $automation->id,
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Get eligible customers for an automation
     */
    protected function getEligibleCustomers(SmsAutomation $automation): \Illuminate\Support\Collection
    {
        $query = RestaurantCustomer::where('restaurant_id', $automation->restaurant_id)
            ->where('sms_subscribed', true)
            ->whereNotNull('phone');

        switch ($automation->trigger_type) {
            case SmsAutomation::TRIGGER_ABANDONED_CART:
                return $this->getAbandonedCartCustomers($query, $automation);

            case SmsAutomation::TRIGGER_WINBACK:
                return $this->getWinbackCustomers($query, $automation);

            case SmsAutomation::TRIGGER_BIRTHDAY:
                return $this->getBirthdayCustomers($query, $automation);

            case SmsAutomation::TRIGGER_LOYALTY_MILESTONE:
                return $this->getLoyaltyMilestoneCustomers($query, $automation);

            default:
                return collect();
        }
    }

    /**
     * Get customers with abandoned carts
     */
    protected function getAbandonedCartCustomers($query, SmsAutomation $automation): \Illuminate\Support\Collection
    {
        $delayMinutes = $automation->delay_minutes;

        return $query
            ->whereNotNull('cart_abandoned_at')
            ->where('cart_abandoned_at', '<=', now()->subMinutes($delayMinutes))
            ->where('cart_reminder_sent', false)
            ->where('cart_total', '>', 0)
            ->get();
    }

    /**
     * Get customers for win-back campaign
     */
    protected function getWinbackCustomers($query, SmsAutomation $automation): \Illuminate\Support\Collection
    {
        $conditions = $automation->conditions ?? [];
        $inactiveDays = $conditions['inactive_days'] ?? 45;
        $minOrders = $conditions['min_orders'] ?? 2;

        return $query
            ->where('visits_count', '>=', $minOrders)
            ->where(function ($q) use ($inactiveDays) {
                $q->where('last_visit_at', '<', now()->subDays($inactiveDays))
                  ->orWhereNull('last_visit_at');
            })
            ->where(function ($q) use ($inactiveDays) {
                $q->whereNull('winback_sent_at')
                  ->orWhere('winback_sent_at', '<', now()->subDays($inactiveDays));
            })
            ->get();
    }

    /**
     * Get customers with birthday today
     */
    protected function getBirthdayCustomers($query, SmsAutomation $automation): \Illuminate\Support\Collection
    {
        return $query
            ->whereNotNull('birthday')
            ->whereMonth('birthday', now()->month)
            ->whereDay('birthday', now()->day)
            ->where(function ($q) {
                $q->whereNull('birthday_sms_sent_at')
                  ->orWhereYear('birthday_sms_sent_at', '<', now()->year);
            })
            ->get();
    }

    /**
     * Get customers reaching loyalty milestones
     */
    protected function getLoyaltyMilestoneCustomers($query, SmsAutomation $automation): \Illuminate\Support\Collection
    {
        $conditions = $automation->conditions ?? [];
        $milestonePoints = $conditions['milestone_points'] ?? 500;

        return $query
            ->where('points', '>=', $milestonePoints)
            ->whereNull('last_sms_sent_at')
            ->orWhere('last_sms_sent_at', '<', now()->subDays(30))
            ->get();
    }

    /**
     * Check if we can send SMS to customer
     */
    protected function canSendSms(RestaurantCustomer $customer, SmsAutomation $automation): bool
    {
        // Check if subscribed
        if (!$customer->sms_subscribed) {
            return false;
        }

        // Check phone number
        if (empty($customer->phone)) {
            return false;
        }

        // Check rate limiting (max 1 SMS per hour per customer)
        if ($customer->last_sms_sent_at && $customer->last_sms_sent_at->gt(now()->subHour())) {
            return false;
        }

        // Check if already sent this automation recently
        $recentSms = SmsLog::where('restaurant_customer_id', $customer->id)
            ->where('sms_automation_id', $automation->id)
            ->where('created_at', '>', now()->subDays(1))
            ->exists();

        if ($recentSms) {
            return false;
        }

        return true;
    }

    /**
     * Send automated SMS
     */
    public function sendAutomatedSms(SmsAutomation $automation, RestaurantCustomer $customer): SmsLog
    {
        $message = $this->parseMessageTemplate($automation, $customer);
        
        // Create log entry
        $log = SmsLog::create([
            'restaurant_id' => $automation->restaurant_id,
            'sms_automation_id' => $automation->id,
            'restaurant_customer_id' => $customer->id,
            'phone' => $customer->phone,
            'message' => $message,
            'type' => SmsLog::TYPE_AUTOMATION,
            'trigger_type' => $automation->trigger_type,
            'status' => SmsLog::STATUS_PENDING,
        ]);

        try {
            // Send via Twilio
            $result = $this->twilio->sendSms($customer->phone, $message);

            if ($result['success']) {
                $log->markAsSent($result['sid'] ?? '');
                $automation->incrementSends();

                // Update customer tracking
                $this->updateCustomerAfterSms($customer, $automation);
            } else {
                $log->markAsFailed($result['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
            throw $e;
        }

        return $log;
    }

    /**
     * Parse message template with customer data
     */
    protected function parseMessageTemplate(SmsAutomation $automation, RestaurantCustomer $customer): string
    {
        $restaurant = $automation->restaurant;
        $message = $automation->message_template;

        $replacements = [
            '{customer_name}' => $customer->name ?? 'Amigo',
            '{restaurant_name}' => $restaurant->name,
            '{cart_total}' => number_format($customer->cart_total ?? 0, 2),
            '{points}' => number_format($customer->points ?? 0),
            '{days_since_order}' => $customer->last_visit_at 
                ? $customer->last_visit_at->diffInDays(now()) 
                : 30,
            '{coupon_code}' => $automation->coupon_code ?? 'FAMER10',
            '{coupon_discount}' => $this->formatDiscount($automation),
            '{order_url}' => url("/restaurante/{$restaurant->slug}/menu"),
            '{rewards_url}' => url("/restaurante/{$restaurant->slug}/rewards"),
            '{review_url}' => url("/restaurante/{$restaurant->slug}/review"),
            '{points_reward}' => '50',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Format discount for display
     */
    protected function formatDiscount(SmsAutomation $automation): string
    {
        if (!$automation->coupon_discount) {
            return '10% OFF';
        }

        if ($automation->coupon_type === 'percent') {
            return $automation->coupon_discount . '% OFF';
        }

        return '$' . number_format($automation->coupon_discount, 2) . ' OFF';
    }

    /**
     * Update customer after sending SMS
     */
    protected function updateCustomerAfterSms(RestaurantCustomer $customer, SmsAutomation $automation): void
    {
        $updates = [
            'last_sms_sent_at' => now(),
            'sms_sends_count' => $customer->sms_sends_count + 1,
        ];

        // Specific updates based on trigger type
        switch ($automation->trigger_type) {
            case SmsAutomation::TRIGGER_ABANDONED_CART:
                $updates['cart_reminder_sent'] = true;
                break;

            case SmsAutomation::TRIGGER_WINBACK:
                $updates['winback_sent_at'] = now();
                break;

            case SmsAutomation::TRIGGER_BIRTHDAY:
                $updates['birthday_sms_sent_at'] = now();
                break;
        }

        $customer->update($updates);
    }

    /**
     * Send transactional SMS (order confirmation, etc.)
     */
    public function sendTransactionalSms(
        Restaurant $restaurant,
        string $phone,
        string $message,
        string $triggerType = 'transactional',
        ?RestaurantCustomer $customer = null
    ): SmsLog {
        $log = SmsLog::create([
            'restaurant_id' => $restaurant->id,
            'restaurant_customer_id' => $customer?->id,
            'phone' => $phone,
            'message' => $message,
            'type' => SmsLog::TYPE_TRANSACTIONAL,
            'trigger_type' => $triggerType,
            'status' => SmsLog::STATUS_PENDING,
        ]);

        try {
            $result = $this->twilio->sendSms($phone, $message);

            if ($result['success']) {
                $log->markAsSent($result['sid'] ?? '');
            } else {
                $log->markAsFailed($result['error'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $log->markAsFailed($e->getMessage());
        }

        return $log;
    }

    /**
     * Send order confirmation SMS
     */
    public function sendOrderConfirmation(Order $order): ?SmsLog
    {
        if (empty($order->customer_phone)) {
            return null;
        }

        $restaurant = $order->restaurant;
        $message = "✅ ¡Gracias por tu pedido #{$order->order_number}!\n\n" .
                   "{$restaurant->name}\n" .
                   "Total: \$" . number_format($order->total, 2) . "\n\n";

        if ($order->order_type === 'pickup' && $order->scheduled_for) {
            $message .= "Recógelo a las: " . $order->scheduled_for->format('h:i A');
        } else {
            $message .= "Te notificaremos cuando esté listo.";
        }

        $message .= "\n\nReply STOP to unsubscribe";

        return $this->sendTransactionalSms(
            $restaurant,
            $order->customer_phone,
            $message,
            'order_confirmation'
        );
    }

    /**
     * Track cart abandonment
     */
    public function trackCartAbandonment(RestaurantCustomer $customer, array $cartItems, float $cartTotal): void
    {
        $customer->update([
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal,
            'cart_updated_at' => now(),
            'cart_abandoned_at' => now(),
            'cart_reminder_sent' => false,
        ]);
    }

    /**
     * Clear cart after order
     */
    public function clearCartTracking(RestaurantCustomer $customer): void
    {
        $customer->update([
            'cart_items' => null,
            'cart_total' => null,
            'cart_updated_at' => null,
            'cart_abandoned_at' => null,
            'cart_reminder_sent' => false,
        ]);
    }

    /**
     * Get SMS stats for a restaurant
     */
    public function getRestaurantStats(int $restaurantId, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $logs = SmsLog::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', $startDate);

        $sent = (clone $logs)->sent()->count();
        $failed = (clone $logs)->failed()->count();
        $clicked = (clone $logs)->where('status', SmsLog::STATUS_CLICKED)->count();

        return [
            'total_sent' => $sent,
            'total_failed' => $failed,
            'total_clicked' => $clicked,
            'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 1) : 0,
            'by_trigger' => SmsLog::where('restaurant_id', $restaurantId)
                ->where('created_at', '>=', $startDate)
                ->sent()
                ->selectRaw('trigger_type, COUNT(*) as count')
                ->groupBy('trigger_type')
                ->pluck('count', 'trigger_type')
                ->toArray(),
        ];
    }
}
