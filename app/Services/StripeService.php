<?php

namespace App\Services;

use App\Models\Restaurant;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\Price;
use Stripe\Coupon;
use Stripe\PromotionCode;
use Stripe\Invoice;
use Stripe\BillingPortal\Session as PortalSession;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for subscription
     */
    public function createCheckoutSession(Restaurant $restaurant, string $plan, string $successUrl, string $cancelUrl, ?string $couponCode = null, ?string $stripePromotionCodeId = null): Session
    {
        try {
            $priceId = config("stripe.prices.{$plan}");

            if (!$priceId) {
                throw new Exception("Invalid plan: {$plan}");
            }

            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($restaurant);

            // Prepare session data
            $sessionData = [
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $restaurant->id,
                'metadata' => [
                    'restaurant_id' => $restaurant->id,
                    'plan' => $plan,
                ],
                'subscription_data' => [
                    'metadata' => [
                        'restaurant_id' => $restaurant->id,
                        'plan' => $plan,
                    ],
                ],
                // Allow promotion codes to be entered
                'allow_promotion_codes' => true,
            ];

            // If a Stripe promotion code ID is provided directly, use it
            if ($stripePromotionCodeId) {
                $sessionData['discounts'] = [[
                    'promotion_code' => $stripePromotionCodeId,
                ]];
                unset($sessionData['allow_promotion_codes']);
            }
            // Otherwise, if a coupon code text is provided, validate it in Stripe
            elseif ($couponCode) {
                $promotionCode = $this->validatePromotionCode($couponCode);
                if ($promotionCode) {
                    $sessionData['discounts'] = [[
                        'promotion_code' => $promotionCode->id,
                    ]];
                    unset($sessionData['allow_promotion_codes']);
                }
            }
            // Apply introductory pricing coupon for premium plan (first month $9.99)
            elseif ($plan === 'premium') {
                $introCoupon = config('stripe.intro_coupon_premium');
                if ($introCoupon) {
                    $sessionData['discounts'] = [[
                        'coupon' => $introCoupon,
                    ]];
                    unset($sessionData['allow_promotion_codes']);
                }
            }

            // Create checkout session
            $session = Session::create($sessionData);

            return $session;
        } catch (Exception $e) {
            throw new Exception("Error creating checkout session: " . $e->getMessage());
        }
    }

    /**
     * Validate a promotion code
     */
    public function validatePromotionCode(string $code): ?PromotionCode
    {
        try {
            $promotionCodes = PromotionCode::all(['code' => $code, 'active' => true, 'limit' => 1]);

            if ($promotionCodes->data && count($promotionCodes->data) > 0) {
                return $promotionCodes->data[0];
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create a coupon in Stripe
     */
    public function createCoupon(array $data): Coupon
    {
        try {
            return Coupon::create([
                'percent_off' => $data['percent_off'] ?? null,
                'amount_off' => $data['amount_off'] ?? null,
                'currency' => $data['currency'] ?? 'usd',
                'duration' => $data['duration'] ?? 'once', // once, repeating, forever
                'duration_in_months' => $data['duration_in_months'] ?? null,
                'name' => $data['name'] ?? null,
                'max_redemptions' => $data['max_redemptions'] ?? null,
                'redeem_by' => $data['redeem_by'] ?? null,
            ]);
        } catch (Exception $e) {
            throw new Exception("Error creating coupon: " . $e->getMessage());
        }
    }

    /**
     * Create a promotion code in Stripe
     */
    public function createPromotionCode(string $couponId, string $code, array $options = []): PromotionCode
    {
        try {
            return PromotionCode::create([
                'coupon' => $couponId,
                'code' => strtoupper($code),
                'active' => $options['active'] ?? true,
                'max_redemptions' => $options['max_redemptions'] ?? null,
                'expires_at' => $options['expires_at'] ?? null,
                'restrictions' => $options['restrictions'] ?? null,
            ]);
        } catch (Exception $e) {
            throw new Exception("Error creating promotion code: " . $e->getMessage());
        }
    }

    /**
     * Get or create a Stripe customer for the restaurant
     */
    protected function getOrCreateCustomer(Restaurant $restaurant): Customer
    {
        // If restaurant already has a Stripe customer ID, retrieve it
        if ($restaurant->stripe_customer_id) {
            try {
                return Customer::retrieve($restaurant->stripe_customer_id);
            } catch (Exception $e) {
                // Customer not found, create new one
            }
        }

        // Create new customer
        $customer = Customer::create([
            'email' => $restaurant->owner_email,
            'name' => $restaurant->owner_name,
            'phone' => $restaurant->owner_phone,
            'metadata' => [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name,
            ],
        ]);

        // Save customer ID to restaurant
        $restaurant->update([
            'stripe_customer_id' => $customer->id,
        ]);

        return $customer;
    }

    /**
     * Handle successful subscription payment
     */
    public function handleSuccessfulSubscription(string $subscriptionId, Restaurant $restaurant, string $plan): void
    {
        try {
            $subscription = Subscription::retrieve($subscriptionId);

            $restaurant->update([
                'is_claimed' => true,
                'claimed_at' => now(),
                'subscription_tier' => $plan,
                'stripe_subscription_id' => $subscriptionId,
                'subscription_started_at' => now(),
                'subscription_expires_at' => now()->addMonth(),
                'subscription_status' => 'active',
                // Enable premium features based on plan
                'premium_analytics' => in_array($plan, ['claimed', 'premium', 'elite']),
                'premium_seo' => in_array($plan, ['premium', 'elite']),
                'premium_featured' => in_array($plan, ['premium', 'elite']),
                'premium_coupons' => in_array($plan, ['premium', 'elite']),
                'premium_email_marketing' => in_array($plan, ['premium', 'elite']),
            ]);

            // If premium or elite, mark as featured
            if (in_array($plan, ['premium', 'elite'])) {
                $restaurant->update(['is_featured' => true]);
            }

            // Enviar email de bienvenida segun el plan
            if ($restaurant->user) {
                try {
                    $restaurant->user->notify(new \App\Notifications\WelcomeSubscription($restaurant, $plan));
                } catch (\Exception $e) {
                    \Log::error('Error sending welcome subscription email: ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error handling successful subscription: " . $e->getMessage());
        }
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Restaurant $restaurant): bool
    {
        try {
            if (!$restaurant->stripe_subscription_id) {
                return false;
            }

            $subscription = Subscription::retrieve($restaurant->stripe_subscription_id);
            $subscription->cancel();

            $restaurant->update([
                'subscription_status' => 'canceled',
                'subscription_expires_at' => now()->addDays(30), // Grace period
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error canceling subscription: " . $e->getMessage());
        }
    }

    /**
     * Update subscription to a new plan
     */
    public function updateSubscription(Restaurant $restaurant, string $newPlan): bool
    {
        try {
            if (!$restaurant->stripe_subscription_id) {
                return false;
            }

            $subscription = Subscription::retrieve($restaurant->stripe_subscription_id);
            $newPriceId = config("stripe.prices.{$newPlan}");

            if (!$newPriceId) {
                throw new Exception("Invalid plan: {$newPlan}");
            }

            // Update subscription
            Subscription::update($restaurant->stripe_subscription_id, [
                'items' => [[
                    'id' => $subscription->items->data[0]->id,
                    'price' => $newPriceId,
                ]],
                'proration_behavior' => 'create_prorations',
            ]);

            $restaurant->update([
                'subscription_tier' => $newPlan,
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error updating subscription: " . $e->getMessage());
        }
    }

    /**
     * Create Stripe Customer Portal session for self-service billing management
     */
    public function createBillingPortalSession(string $customerId, string $returnUrl): string
    {
        try {
            $session = PortalSession::create([
                'customer' => $customerId,
                'return_url' => $returnUrl,
            ]);

            return $session->url;
        } catch (Exception $e) {
            throw new Exception("Error creating billing portal session: " . $e->getMessage());
        }
    }

    /**
     * Get invoices for a Stripe customer
     */
    public function getInvoices(string $customerId, int $limit = 24): array
    {
        try {
            $invoices = Invoice::all([
                'customer' => $customerId,
                'limit' => $limit,
                'expand' => ['data.charge'],
            ]);

            return $invoices->data ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get an upcoming invoice for a customer
     */
    public function getUpcomingInvoice(string $customerId): ?Invoice
    {
        try {
            $client = new \Stripe\StripeClient(config('stripe.secret'));
            return $client->invoices->upcoming(['customer' => $customerId]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create upgrade checkout session (alias with explicit name)
     */
    public function createUpgradeCheckoutSession(Restaurant $restaurant, string $plan, string $successUrl, string $cancelUrl): Session
    {
        return $this->createCheckoutSession($restaurant, $plan, $successUrl, $cancelUrl);
    }

    /**
     * Get plan details
     */
    public function getPlanDetails(string $plan): array
    {
        return config("stripe.plans.{$plan}", []);
    }

    /**
     * Get all available plans
     */
    public function getAllPlans(): array
    {
        return config('stripe.plans', []);
    }
}
