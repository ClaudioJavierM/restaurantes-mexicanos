<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class OrderPaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Create a Stripe PaymentIntent for a card order.
     *
     * @return array{client_secret: string, payment_intent_id: string}
     *
     * @throws ApiErrorException
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $amount = (int) round($order->total * 100); // convert to cents

            $paymentIntent = PaymentIntent::create([
                'amount'        => $amount,
                'currency'      => 'usd',
                'description'   => "FAMER Order {$order->order_number}",
                'receipt_email' => $order->customer_email,
                'metadata'      => [
                    'order_id'      => $order->id,
                    'order_number'  => $order->order_number,
                    'restaurant_id' => $order->restaurant_id,
                ],
            ]);

            // Persist the PaymentIntent ID on the order immediately
            $order->update(['payment_intent_id' => $paymentIntent->id]);

            Log::info('PaymentIntent created', [
                'order_id'          => $order->id,
                'order_number'      => $order->order_number,
                'payment_intent_id' => $paymentIntent->id,
                'amount_cents'      => $amount,
            ]);

            return [
                'client_secret'    => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe createPaymentIntent failed', [
                'order_id'    => $order->id,
                'order_number' => $order->order_number,
                'error'       => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Mark an order as paid after a successful PaymentIntent.
     */
    public function handlePaymentSuccess(string $paymentIntentId): void
    {
        try {
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            if (! $order) {
                Log::warning('handlePaymentSuccess: Order not found', [
                    'payment_intent_id' => $paymentIntentId,
                ]);

                return;
            }

            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'confirmed_at'   => now(),
            ]);

            Log::info('Order payment succeeded', [
                'order_id'          => $order->id,
                'order_number'      => $order->order_number,
                'payment_intent_id' => $paymentIntentId,
            ]);
        } catch (\Throwable $e) {
            Log::error('handlePaymentSuccess exception', [
                'payment_intent_id' => $paymentIntentId,
                'error'             => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark an order as cancelled after a failed PaymentIntent.
     */
    public function handlePaymentFailed(string $paymentIntentId): void
    {
        try {
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            if (! $order) {
                Log::warning('handlePaymentFailed: Order not found', [
                    'payment_intent_id' => $paymentIntentId,
                ]);

                return;
            }

            $order->update([
                'payment_status'      => 'pending',
                'status'              => 'cancelled',
                'cancellation_reason' => 'Payment failed - please retry',
                'cancelled_at'        => now(),
            ]);

            Log::warning('Order payment failed', [
                'order_id'          => $order->id,
                'order_number'      => $order->order_number,
                'payment_intent_id' => $paymentIntentId,
            ]);
        } catch (\Throwable $e) {
            Log::error('handlePaymentFailed exception', [
                'payment_intent_id' => $paymentIntentId,
                'error'             => $e->getMessage(),
            ]);
        }
    }
}
