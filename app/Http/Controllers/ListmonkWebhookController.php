<?php

namespace App\Http\Controllers;

use App\Models\NewsletterEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Listmonk Webhook Handler
 *
 * Configure in Listmonk Admin → Settings → Messengers/Webhooks:
 *   Webhook URL: https://restaurantesmexicanosfamosos.com.mx/webhooks/listmonk
 *
 * Listmonk fires these events (field "event" in payload):
 *   subscriber.optin      → user confirmed subscription
 *   subscriber.blacklist  → user unsubscribed / marked as blacklisted
 *   campaign.send         → campaign email was sent to subscriber
 *   link.click            → subscriber clicked a link in campaign
 *   campaign.bounced      → email bounced
 */
class ListmonkWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $event   = $payload['event'] ?? null;

        Log::info('Listmonk webhook received', ['event' => $event]);

        if (!$event) {
            return response()->json(['status' => 'ignored', 'reason' => 'no event'], 200);
        }

        try {
            $this->processEvent($event, $payload);
            return response()->json(['status' => 'ok'], 200);
        } catch (\Throwable $e) {
            Log::error('Listmonk webhook failed', ['event' => $event, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function processEvent(string $event, array $payload): void
    {
        // Listmonk payload structure varies by event type
        $subscriber = $payload['data']['subscriber'] ?? $payload['subscriber'] ?? [];
        $campaign   = $payload['data']['campaign']   ?? $payload['campaign']   ?? [];
        $link       = $payload['data']['link']        ?? $payload['link']       ?? null;

        $email        = $subscriber['email']        ?? null;
        $name         = $subscriber['name']         ?? null;
        $subscriberId = $subscriber['id']           ?? null;
        $campaignId   = $campaign['id']             ?? null;
        $campaignName = $campaign['name']           ?? null;
        $occurredAt   = $payload['created_at']      ?? now()->toISOString();

        // Map Listmonk event names to our standardized event_type
        $eventTypeMap = [
            'subscriber.optin'     => 'subscribed',
            'subscriber.blacklist' => 'unsubscribed',
            'campaign.send'        => 'sent',
            'link.click'           => 'clicked',
            'campaign.bounced'     => 'bounced',
            // Some versions use these names:
            'subscribe'            => 'subscribed',
            'unsubscribe'          => 'unsubscribed',
        ];

        $eventType = $eventTypeMap[$event] ?? $event;

        // Record the event
        NewsletterEvent::create([
            'source'        => 'listmonk',
            'event_type'    => $eventType,
            'email'         => $email,
            'name'          => $name,
            'subscriber_id' => $subscriberId,
            'campaign_id'   => $campaignId,
            'campaign_name' => $campaignName,
            'link_url'      => is_string($link) ? $link : ($link['url'] ?? null),
            'list_name'     => $payload['data']['lists'][0]['name'] ?? null,
            'raw_payload'   => $payload,
            'occurred_at'   => \Carbon\Carbon::parse($occurredAt),
        ]);

        // Keep users table in sync
        if ($email) {
            $user = User::where('email', $email)->first();

            if ($eventType === 'subscribed' && $user) {
                $user->update([
                    'newsletter_subscribed'    => true,
                    'newsletter_subscribed_at' => $user->newsletter_subscribed_at ?? now(),
                ]);
            }

            if ($eventType === 'unsubscribed' && $user) {
                $user->update(['newsletter_subscribed' => false]);
            }

            if ($subscriberId && $user && !$user->listmonk_subscriber_id) {
                $user->update(['listmonk_subscriber_id' => $subscriberId]);
            }
        }
    }
}
