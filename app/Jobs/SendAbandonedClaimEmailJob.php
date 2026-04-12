<?php

namespace App\Jobs;

use App\Mail\AbandonedClaimMail;
use App\Models\EmailLog;
use App\Models\EmailSuppression;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedClaimEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $restaurantId) {}

    public function handle(): void
    {
        try {
            $restaurant = Restaurant::with('state')->find($this->restaurantId);

            if (!$restaurant) {
                Log::warning("SendAbandonedClaimEmailJob: restaurant {$this->restaurantId} not found.");
                return;
            }

            // Skip if already claimed
            if ($restaurant->user_id || $restaurant->is_claimed) {
                Log::info("SendAbandonedClaimEmailJob: restaurant {$restaurant->id} is already claimed — skipping.");
                return;
            }

            // Skip if already sent
            if ($restaurant->claim_abandoned_sent_at) {
                Log::info("SendAbandonedClaimEmailJob: abandoned claim email already sent for restaurant {$restaurant->id} — skipping.");
                return;
            }

            // Resolve email
            $email = $restaurant->owner_email ?? $restaurant->email ?? null;

            if (!$email) {
                Log::info("SendAbandonedClaimEmailJob: restaurant {$restaurant->id} has no email — skipping.");
                return;
            }

            // Check suppression
            if (EmailSuppression::isSuppressed($email)) {
                Log::info("SendAbandonedClaimEmailJob: {$email} is suppressed — skipping restaurant {$restaurant->id}.");
                return;
            }

            // Send email
            Mail::to($email)->send(new AbandonedClaimMail($restaurant));

            // Update tracking timestamp
            $restaurant->update(['claim_abandoned_sent_at' => now()]);

            // Create EmailLog entry
            EmailLog::log([
                'type'           => EmailLog::TYPE_CAMPAIGN,
                'category'       => 'abandoned_claim',
                'to_email'       => $email,
                'from_email'     => 'hello@restaurantesmexicanosfamosos.com',
                'from_name'      => 'FAMER',
                'subject'        => "{$restaurant->name} — ¿terminaste de reclamar tu perfil?",
                'mailable_class' => AbandonedClaimMail::class,
                'template'       => 'emails.abandoned-claim',
                'restaurant_id'  => $restaurant->id,
                'metadata'       => [
                    'claim_started_at' => $restaurant->claim_started_at?->toIso8601String(),
                ],
            ]);

            Log::info("SendAbandonedClaimEmailJob: sent abandoned claim email to {$email} for restaurant {$restaurant->id}.");

        } catch (\Exception $e) {
            Log::error("SendAbandonedClaimEmailJob: failed for restaurant {$this->restaurantId} — " . $e->getMessage());
        }
    }
}
