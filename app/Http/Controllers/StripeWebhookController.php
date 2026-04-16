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
        if (!$subscriptionId) {
            \Log::warning('Webhook: subscription ID is null for session ' . $session->id);
            // Try to get it from the customer
            if ($session->customer) {
                try {
                    $subs = \Stripe\Subscription::all([
                        'customer' => $session->customer,
                        'status' => 'active',
                        'limit' => 1,
                    ]);
                    $subscriptionId = $subs->data[0]->id ?? null;
                } catch (\Exception $e) {
                    \Log::error('Webhook: could not fetch subscription for customer ' . $session->customer . ': ' . $e->getMessage());
                }
            }
        }

        if (!$subscriptionId) {
            \Log::error('Webhook: unable to resolve subscription ID for restaurant ' . $restaurantId);
            return;
        }

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

        // Send email notification to restaurant owner about payment failure
        if ($restaurant->user_id) {
            $user = User::find($restaurant->user_id);
            if ($user) {
                // Get Stripe billing portal URL for card update
                try {
                    $stripe = new \Stripe\StripeClient(config('stripe.secret'));
                    $session = $stripe->billingPortal->sessions->create([
                        'customer' => $restaurant->stripe_customer_id,
                        'return_url' => url('/owner/dashboard'),
                    ]);
                    $updateUrl = $session->url;
                } catch (\Exception $e) {
                    \Log::warning("PaymentFailed: could not create billing portal session for restaurant {$restaurant->id}: " . $e->getMessage());
                    $updateUrl = url('/owner/dashboard');
                }

                try {
                    Mail::to($user->email)->send(new \App\Mail\PaymentFailedMail($restaurant, $user, $updateUrl));
                    \Log::info("PaymentFailed email sent for restaurant {$restaurant->id} to {$user->email}");
                } catch (\Exception $e) {
                    \Log::error("PaymentFailed: could not send email for restaurant {$restaurant->id}: " . $e->getMessage());
                }
            }
        }
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
                // Ensure stripe_subscription_id is set
                $subscriptionId = $session->subscription;
                if (!$subscriptionId && $restaurant->stripe_customer_id) {
                    try {
                        $stripe = new \Stripe\StripeClient(config('stripe.secret'));
                        $subs = $stripe->subscriptions->all([
                            'customer' => $restaurant->stripe_customer_id,
                            'status' => 'active',
                            'limit' => 1,
                        ]);
                        $subscriptionId = $subs->data[0]->id ?? null;
                    } catch (\Exception $e) {
                        \Log::warning('Could not fetch subscription for customer: ' . $restaurant->stripe_customer_id);
                    }
                }
                $restaurant->stripe_subscription_id = $subscriptionId;
                $restaurant->save();

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
                        'role' => 'owner',
                        'email_verified_at' => now(),
                    ]);
                    $isNewUser = true;
                } else {
                    // Promote existing user to owner and verify email
                    $needsSave = false;
                    if ($user->role !== 'owner' && $user->role !== 'admin') {
                        $user->role = 'owner';
                        $needsSave = true;
                    }
                    if (!$user->email_verified_at) {
                        $user->email_verified_at = now();
                        $needsSave = true;
                    }
                    if ($needsSave) $user->save();
                }

                // Link user to restaurant if not already linked, and mark as claimed
                $restaurantUpdates = [];
                if (!$restaurant->user_id) {
                    $restaurantUpdates['user_id'] = $user->id;
                }
                if (!$restaurant->is_claimed) {
                    $restaurantUpdates['is_claimed'] = true;
                    $restaurantUpdates['claimed_at'] = now();
                }
                if (!empty($restaurantUpdates)) {
                    $restaurant->update($restaurantUpdates);
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

                // Fallback: also attempt to create user from session data if restaurant email is missing
                $claimEmail   = session('claim_owner_email');
                $claimPassword = session('claim_owner_email') ? session('claim_password') : null; // already hashed
                $claimName    = session('claim_owner_name');

                if ($claimEmail && $claimPassword && !$user) {
                    $user = \App\Models\User::firstOrCreate(
                        ['email' => $claimEmail],
                        [
                            'name'               => $claimName ?? 'Propietario',
                            'password'           => $claimPassword,
                            'email_verified_at'  => now(),
                        ]
                    );
                    \Illuminate\Support\Facades\Auth::login($user);
                }

                // Send welcome email with FAMER30 coupon
                try {
                    \App\Mail\ClaimWelcomeMail::sendAndLog(
                        $restaurant,
                        $restaurant->owner_name ?? $user->name,
                        $restaurant->owner_email ?? $user->email,
                        $plan
                    );
                } catch (\Exception $e) {
                    \Log::warning('ClaimWelcomeMail (paid) failed: ' . $e->getMessage());
                }

                // Clean up claim session keys
                session()->forget(['claim_password', 'claim_owner_email', 'claim_owner_name', 'claim_owner_phone', 'claim_restaurant_id']);

                // Retrieve subscription object if available
                $subscription = null;
                if ($restaurant->stripe_subscription_id) {
                    try {
                        $subscription = \Stripe\Subscription::retrieve($restaurant->stripe_subscription_id);
                    } catch (\Exception $e) {
                        \Log::warning('Could not retrieve subscription object: ' . $e->getMessage());
                    }
                }

                return view('claim.success', [
                    'sessionId'    => $sessionId,
                    'restaurant'   => $restaurant,
                    'plan'         => $plan,
                    'user'         => $user,
                    'subscription' => $subscription,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in claim success: ' . $e->getMessage());
        }

        return view('claim.success', [
            'sessionId'    => $sessionId,
            'plan'         => null,
            'restaurant'   => null,
            'subscription' => null,
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
                $this->stripeService->handleSuccessfulSubscription($session->subscription ?? '', $restaurant, $plan);

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
