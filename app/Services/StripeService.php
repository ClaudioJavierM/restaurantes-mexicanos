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
    public function createCheckoutSession(Restaurant $restaurant, string $plan, string $successUrl, string $cancelUrl, ?string $couponCode = null): Session
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

            // If a coupon code is provided, validate and apply it
            if ($couponCode) {
                $promotionCode = $this->validatePromotionCode($couponCode);
                if ($promotionCode) {
                    $sessionData['discounts'] = [[
                        'promotion_code' => $promotionCode->id,
                    ]];
                    unset($sessionData['allow_promotion_codes']);
                }
            }
            // Auto-apply introductory pricing for premium ($9.99 first month)
            elseif ($plan === 'premium' && !isset($sessionData['discounts'])) {
                $introCouponId = config('stripe.intro_coupon_premium');
                if ($introCouponId) {
                    $sessionData['discounts'] = [[
                        'coupon' => $introCouponId,
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

    /**
     * Create an Embedded Checkout Session (ui_mode=embedded)
     * Returns a session with client_secret for front-end initEmbeddedCheckout()
     */
    public function createEmbeddedCheckoutSession(Restaurant $restaurant, string $plan, string $returnUrl, ?string $couponCode = null): Session
    {
        try {
            $priceId = config("stripe.prices.{$plan}");

            if (!$priceId) {
                throw new Exception("Invalid plan: {$plan}");
            }

            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($restaurant);

            // Prepare session data — ui_mode=embedded uses return_url instead of success/cancel URLs
            $sessionData = [
                'customer'             => $customer->id,
                'ui_mode'              => 'embedded',
                'payment_method_types' => ['card'], // Disable Stripe Link to keep user on FAMER domain
                'line_items'           => [[
                    'price'    => $priceId,
                    'quantity' => 1,
                ]],
                'mode'                 => 'subscription',
                'return_url'           => $returnUrl,
                'client_reference_id'  => $restaurant->id,
                'metadata'             => [
                    'restaurant_id' => $restaurant->id,
                    'plan'          => $plan,
                ],
                'subscription_data'    => [
                    'metadata' => [
                        'restaurant_id' => $restaurant->id,
                        'plan'          => $plan,
                    ],
                ],
                // Allow promotion codes via Stripe's own coupon field inside embedded UI
                'allow_promotion_codes' => true,
            ];

            // If a coupon code is provided, validate and apply it
            if ($couponCode) {
                $promotionCode = $this->validatePromotionCode($couponCode);
                if ($promotionCode) {
                    $sessionData['discounts'] = [[
                        'promotion_code' => $promotionCode->id,
                    ]];
                    unset($sessionData['allow_promotion_codes']);
                }
            }
            // Auto-apply introductory pricing for premium ($9.99 first month)
            elseif ($plan === 'premium' && !isset($sessionData['discounts'])) {
                $introCouponId = config('stripe.intro_coupon_premium');
                if ($introCouponId) {
                    $sessionData['discounts'] = [[
                        'coupon' => $introCouponId,
                    ]];
                    unset($sessionData['allow_promotion_codes']);
                }
            }

            return Session::create($sessionData);
        } catch (Exception $e) {
            throw new Exception("Error creating embedded checkout session: " . $e->getMessage());
        }
    }

    /**
     * Create a pending Stripe Subscription and return the PaymentIntent client_secret
     * for use with Stripe Elements (PaymentElement) on a custom checkout page.
     * The subscription starts in 'incomplete' status until payment is confirmed.
     */
    public function createPendingSubscription(Restaurant $restaurant, string $plan, ?string $couponCode = null): array
    {
        try {
            $priceId = config("stripe.prices.{$plan}");
            if (!$priceId) {
                throw new Exception("Invalid plan: {$plan}");
            }

            $customer = $this->getOrCreateCustomer($restaurant);

            $subscriptionData = [
                'customer'         => $customer->id,
                'items'            => [['price' => $priceId]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                'expand'           => ['latest_invoice.payment_intent'],
                'metadata'         => [
                    'restaurant_id' => $restaurant->id,
                    'plan'          => $plan,
                ],
            ];

            // Apply intro coupon for premium ($9.99 first month) if no custom coupon
            $resolvedCoupon = null;
            if ($couponCode) {
                $promo = $this->validatePromotionCode($couponCode);
                if ($promo) {
                    $subscriptionData['discounts'] = [['promotion_code' => $promo->id]];
                    $resolvedCoupon = $couponCode;
                }
            } elseif ($plan === 'premium') {
                $introCouponId = config('stripe.intro_coupon_premium');
                if ($introCouponId) {
                    $subscriptionData['discounts'] = [['coupon' => $introCouponId]];
                }
            } elseif ($plan === 'elite') {
                $introCouponId = config('stripe.intro_coupon_elite');
                if ($introCouponId) {
                    $subscriptionData['discounts'] = [['coupon' => $introCouponId]];
                }
            }

            $subscription = \Stripe\Subscription::create($subscriptionData);

            // Save subscription ID to restaurant immediately
            $restaurant->update(['stripe_subscription_id' => $subscription->id]);

            $paymentIntent = $subscription->latest_invoice->payment_intent;

            return [
                'subscription_id' => $subscription->id,
                'client_secret'   => $paymentIntent->client_secret,
                'amount'          => $paymentIntent->amount,
                'currency'        => $paymentIntent->currency,
            ];
        } catch (Exception $e) {
            throw new Exception("Error creating pending subscription: " . $e->getMessage());
        }
    }

    /**
     * Create a SetupIntent for embedded Stripe Payment Element
     * Returns clientSecret for the front-end to initialize Stripe Elements
     */
    public function createSubscriptionSetupIntent(Restaurant $restaurant, string $plan, ?string $couponCode = null): array
    {
        try {
            $customer = $this->getOrCreateCustomer($restaurant);

            $setupIntent = \Stripe\SetupIntent::create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'usage' => 'off_session',
                'metadata' => [
                    'restaurant_id' => $restaurant->id,
                    'plan' => $plan,
                ],
            ]);

            return [
                'clientSecret' => $setupIntent->client_secret,
                'customerId' => $customer->id,
            ];
        } catch (Exception $e) {
            throw new Exception("Error creating SetupIntent: " . $e->getMessage());
        }
    }

    /**
     * Create a subscription using the payment method from a confirmed SetupIntent
     */
    public function createSubscriptionFromSetupIntent(string $setupIntentId, Restaurant $restaurant, string $plan, ?string $couponCode = null): Subscription
    {
        try {
            $priceId = config("stripe.prices.{$plan}");

            if (!$priceId) {
                throw new Exception("Invalid plan: {$plan}");
            }

            // Retrieve SetupIntent to get the confirmed payment method
            $setupIntent = \Stripe\SetupIntent::retrieve($setupIntentId);
            $paymentMethodId = $setupIntent->payment_method;

            if (!$paymentMethodId) {
                throw new Exception("No payment method found on SetupIntent {$setupIntentId}");
            }

            $customer = $this->getOrCreateCustomer($restaurant);

            // Attach payment method to customer (may already be attached)
            try {
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
                if (!$paymentMethod->customer) {
                    $paymentMethod->attach(['customer' => $customer->id]);
                }
            } catch (Exception $e) {
                // Already attached — continue
            }

            // Set as default payment method on customer
            Customer::update($customer->id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            // Build subscription data
            $subscriptionData = [
                'customer' => $customer->id,
                'items' => [['price' => $priceId]],
                'default_payment_method' => $paymentMethodId,
                'metadata' => [
                    'restaurant_id' => $restaurant->id,
                    'plan' => $plan,
                ],
            ];

            // Apply coupon if provided
            if ($couponCode) {
                $promotionCode = $this->validatePromotionCode($couponCode);
                if ($promotionCode) {
                    $subscriptionData['promotion_code'] = $promotionCode->id;
                }
            } elseif ($plan === 'premium') {
                $introCouponId = config('stripe.intro_coupon_premium');
                if ($introCouponId) {
                    $subscriptionData['coupon'] = $introCouponId;
                }
            }

            return Subscription::create($subscriptionData);
        } catch (Exception $e) {
            throw new Exception("Error creating subscription from SetupIntent: " . $e->getMessage());
        }
    }
}
