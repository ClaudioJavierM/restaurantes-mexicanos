<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class OrderPaymentController extends Controller
{
    public function __construct(private readonly OrderPaymentService $paymentService)
    {
    }

    /**
     * POST /orders/{order}/payment-intent
     *
     * Creates a Stripe PaymentIntent for the given order and returns the
     * client_secret needed by Stripe.js on the frontend.
     */
    public function createIntent(Order $order): JsonResponse
    {
        try {
            $result = $this->paymentService->createPaymentIntent($order);

            return response()->json([
                'success'          => true,
                'client_secret'    => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('OrderPaymentController::createIntent Stripe error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment service unavailable. Please try again.',
            ], 422);
        } catch (\Throwable $e) {
            Log::error('OrderPaymentController::createIntent unexpected error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * POST /stripe/order-webhook
     *
     * Handles Stripe webhook events for order payments.
     * Always returns 200 so Stripe marks the event as delivered.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Verify webhook signature
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('stripe.webhook_secret')
            );
        } catch (UnexpectedValueException $e) {
            Log::warning('OrderPaymentController::handleWebhook invalid payload', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('OrderPaymentController::handleWebhook signature mismatch', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Route to the appropriate handler
        try {
            $paymentIntentId = $event->data->object->id ?? null;

            match ($event->type) {
                'payment_intent.succeeded'      => $paymentIntentId
                    ? $this->paymentService->handlePaymentSuccess($paymentIntentId)
                    : null,
                'payment_intent.payment_failed' => $paymentIntentId
                    ? $this->paymentService->handlePaymentFailed($paymentIntentId)
                    : null,
                default => Log::info('OrderPaymentController::handleWebhook unhandled event', [
                    'type' => $event->type,
                ]),
            };
        } catch (\Throwable $e) {
            // Log but still return 200 — Stripe must not retry on our internal errors
            Log::error('OrderPaymentController::handleWebhook handler exception', [
                'event_type' => $event->type ?? 'unknown',
                'error'      => $e->getMessage(),
            ]);
        }

        return response()->json(['message' => 'Webhook received']);
    }
}
