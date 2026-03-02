<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClaimSuccessNotification;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));
        $webhookSecret = config('stripe.webhook_secret');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            default:
                // Unexpected event type
                return response()->json(['error' => 'Unexpected event type'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Handle successful checkout session
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $restaurantId = $session->metadata->restaurant_id ?? $session->client_reference_id;
        $plan = $session->metadata->plan;

        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            return;
        }

        // Get subscription ID from session
        $subscriptionId = $session->subscription;

        // Update restaurant with subscription info
        $this->stripeService->handleSuccessfulSubscription($subscriptionId, $restaurant, $plan);
    }

    /**
     * Handle subscription update
     */
    protected function handleSubscriptionUpdated($subscription)
    {
        $restaurant = Restaurant::where('stripe_subscription_id', $subscription->id)->first();

        if (!$restaurant) {
            return;
        }

        $status = $subscription->status;

        $restaurant->update([
            'subscription_status' => $status,
            'subscription_expires_at' => $subscription->current_period_end
                ? now()->createFromTimestamp($subscription->current_period_end)
                : null,
        ]);
    }

    /**
     * Handle subscription cancellation
     */
    protected function handleSubscriptionDeleted($subscription)
    {
        $restaurant = Restaurant::where('stripe_subscription_id', $subscription->id)->first();

        if (!$restaurant) {
            return;
        }

        $restaurant->update([
            'subscription_status' => 'canceled',
            'subscription_tier' => 'free',
            'is_featured' => false,
            'premium_analytics' => false,
            'premium_seo' => false,
            'premium_featured' => false,
            'premium_coupons' => false,
            'premium_email_marketing' => false,
        ]);
    }

    /**
     * Handle successful invoice payment
     */
    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice->subscription;

        $restaurant = Restaurant::where('stripe_subscription_id', $subscriptionId)->first();

        if (!$restaurant) {
            return;
        }

        $restaurant->update([
            'subscription_status' => 'active',
            'subscription_expires_at' => $invoice->period_end
                ? now()->createFromTimestamp($invoice->period_end)
                : null,
        ]);
    }

    /**
     * Handle failed invoice payment
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        $subscriptionId = $invoice->subscription;

        $restaurant = Restaurant::where('stripe_subscription_id', $subscriptionId)->first();

        if (!$restaurant) {
            return;
        }

        $restaurant->update([
            'subscription_status' => 'past_due',
        ]);

        // TODO: Send email notification to restaurant owner about payment failure
    }

    /**
     * Handle successful payment callback
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('claim.restaurant')->with('error', 'Invalid session');
        }

        try {
            \Stripe\Stripe::setApiKey(config('stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            $restaurantId = $session->metadata->restaurant_id ?? $session->client_reference_id;
            $plan = $session->metadata->plan ?? 'premium';

            $restaurant = Restaurant::find($restaurantId);

            if ($restaurant) {
                // Create or find the user account for the owner
                $password = Str::random(12);
                $isNewUser = false;
                $user = User::where('email', $restaurant->owner_email)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $restaurant->owner_name,
                        'email' => $restaurant->owner_email,
                        'password' => bcrypt($password),
                        'phone' => $restaurant->owner_phone ?? null,
                    ]);
                    $isNewUser = true;
                }

                // Link user to restaurant if not already linked
                if (!$restaurant->user_id) {
                    $restaurant->update(['user_id' => $user->id]);
                }

                // Log the user in
                auth()->login($user);

                // Send welcome email with credentials and dashboard link
                try {
                    Mail::to($restaurant->owner_email)->send(
                        new ClaimSuccessNotification($restaurant, $user, $plan, $isNewUser ? $password : '(usa tu contrasena actual)')
                    );
                } catch (\Exception $e) {
                    \Log::error('Error sending claim success email: ' . $e->getMessage());
                }

                return view('claim.success', [
                    'sessionId' => $sessionId,
                    'restaurant' => $restaurant,
                    'plan' => $plan,
                    'user' => $user,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in claim success: ' . $e->getMessage());
        }

        return view('claim.success', [
            'sessionId' => $sessionId,
        ]);
    }

    /**
     * Handle canceled payment callback

    /**
     * Handle successful upgrade checkout
     */
    public function upgradeSuccess(Request $request)
    {
        $sessionId = $request->get("session_id");

        if (!$sessionId) {
            return redirect("/owner/upgrade-subscription")->with("error", "Sesion de pago no encontrada.");
        }

        try {
            \Stripe\Stripe::setApiKey(config("stripe.secret"));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            $restaurantId = $session->metadata->restaurant_id ?? $session->client_reference_id;
            $plan = $session->metadata->plan;

            $restaurant = Restaurant::find($restaurantId);

            if ($restaurant) {
                $this->stripeService->handleSuccessfulSubscription($restaurant, $plan, $session);

                if ($coupon = $restaurant->subscriberCoupon) {
                    $coupon->update(["tier" => $plan]);
                }

                return redirect("/owner/my-benefits")->with("success", "Tu plan ha sido actualizado a " . ucfirst($plan) . "!");
            }

            return redirect("/owner/upgrade-subscription")->with("error", "Restaurante no encontrado.");
        } catch (\Exception $e) {
            return redirect("/owner/upgrade-subscription")->with("error", "Error: " . $e->getMessage());
        }
    }

    /**
     * Handle canceled payment callback
     */
    public function cancel()
    {
        return redirect()->route("claim.restaurant")->with("error", "El pago fue cancelado. Por favor intenta nuevamente.");
    }
}
