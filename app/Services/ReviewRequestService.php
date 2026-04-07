<?php

namespace App\Services;

use App\Models\PickupOrder;
use App\Models\Reservation;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class ReviewRequestService
{
    public function __construct(protected TwilioService $twilio) {}

    /**
     * Send review request SMS to customers who completed an order/reservation
     * 1–4 hours ago, skipping duplicates via SmsLog.
     *
     * @param  bool  $dryRun  When true, counts but does not send or log.
     * @return array{sent: int, skipped: int, failed: int}
     */
    public function sendPendingRequests(bool $dryRun = false): array
    {
        $stats = ['sent' => 0, 'skipped' => 0, 'failed' => 0];

        $window = [now()->subHours(4), now()->subHour()];

        // --- PickupOrders ---
        PickupOrder::with('restaurant')
            ->where('status', 'completed')
            ->whereBetween('updated_at', $window)
            ->each(function (PickupOrder $order) use (&$stats, $dryRun) {
                $phone = $order->customer_phone ?? null;
                $name  = $order->customer_name ?? 'Cliente';
                $slug  = $order->restaurant?->slug;
                $restaurantName = $order->restaurant?->name ?? 'el restaurante';
                $restaurantId   = $order->restaurant_id;

                $result = $this->process(
                    phone: $phone,
                    name: $name,
                    restaurantName: $restaurantName,
                    restaurantSlug: $slug,
                    restaurantId: $restaurantId,
                    sourceType: 'pickup_order',
                    sourceId: $order->id,
                    dryRun: $dryRun,
                );

                $stats[$result]++;
            });

        // --- Reservations ---
        Reservation::with('restaurant')
            ->where('status', 'completed')
            ->whereBetween('updated_at', $window)
            ->each(function (Reservation $reservation) use (&$stats, $dryRun) {
                $phone = $reservation->guest_phone ?? null;
                $name  = $reservation->guest_name ?? 'Cliente';
                $slug  = $reservation->restaurant?->slug;
                $restaurantName = $reservation->restaurant?->name ?? 'el restaurante';
                $restaurantId   = $reservation->restaurant_id;

                $result = $this->process(
                    phone: $phone,
                    name: $name,
                    restaurantName: $restaurantName,
                    restaurantSlug: $slug,
                    restaurantId: $restaurantId,
                    sourceType: 'reservation',
                    sourceId: $reservation->id,
                    dryRun: $dryRun,
                );

                $stats[$result]++;
            });

        return $stats;
    }

    /**
     * Handle a single contact: deduplicate → send → log.
     *
     * @return 'sent'|'skipped'|'failed'
     */
    protected function process(
        ?string $phone,
        string $name,
        string $restaurantName,
        ?string $restaurantSlug,
        ?int $restaurantId,
        string $sourceType,
        int $sourceId,
        bool $dryRun,
    ): string {
        if (empty($phone)) {
            Log::debug("ReviewRequest: no phone for {$sourceType} #{$sourceId} — skipped.");
            return 'skipped';
        }

        if (empty($restaurantSlug)) {
            Log::warning("ReviewRequest: no slug for restaurant_id={$restaurantId} — skipped.");
            return 'skipped';
        }

        // Deduplication: skip if we already sent a review request to this phone
        // within the last 7 days.
        if ($this->alreadySent($phone)) {
            Log::debug("ReviewRequest: duplicate for {$phone} ({$sourceType} #{$sourceId}) — skipped.");
            return 'skipped';
        }

        $url     = "https://restaurantesmexicanosfamosos.com/restaurante/{$restaurantSlug}#reviews";
        $message = "¡Gracias por visitar {$restaurantName}! ¿Cómo fue tu experiencia? "
                 . "Deja tu reseña aquí: {$url} "
                 . "Responde STOP para no recibir más mensajes.";

        if ($dryRun) {
            Log::info("ReviewRequest [DRY-RUN] Would send to {$phone} ({$sourceType} #{$sourceId}): {$message}");
            return 'sent'; // count it as "would send"
        }

        $success = $this->twilio->sendSms($phone, $message);

        $status = $success ? SmsLog::STATUS_SENT : SmsLog::STATUS_FAILED;

        SmsLog::create([
            'restaurant_id'  => $restaurantId,
            'phone'          => $phone,
            'message'        => $message,
            'type'           => SmsLog::TYPE_TRANSACTIONAL,
            'trigger_type'   => 'review_request',
            'status'         => $status,
            'sent_at'        => $success ? now() : null,
            'metadata'       => [
                'source_type' => $sourceType,
                'source_id'   => $sourceId,
            ],
        ]);

        if ($success) {
            Log::info("ReviewRequest: sent to {$phone} ({$sourceType} #{$sourceId}).");
            return 'sent';
        }

        Log::warning("ReviewRequest: SMS failed for {$phone} ({$sourceType} #{$sourceId}).");
        return 'failed';
    }

    /**
     * Returns true if a review-request SMS was already sent to this phone
     * within the last 7 days.
     */
    protected function alreadySent(string $phone): bool
    {
        return SmsLog::where('phone', $phone)
            ->where('trigger_type', 'review_request')
            ->where('status', '!=', SmsLog::STATUS_FAILED)
            ->where('created_at', '>', now()->subDays(7))
            ->exists();
    }
}
