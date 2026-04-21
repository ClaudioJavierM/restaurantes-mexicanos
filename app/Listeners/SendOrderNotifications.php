<?php

namespace App\Listeners;

use App\Events\NewOrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderNotifications
{
    /**
     * Statuses that warrant a customer notification email.
     */
    private const NOTIFIABLE_STATUSES = [
        'confirmed',
        'preparing',
        'ready',
        'out_for_delivery',
        'completed',
        'cancelled',
    ];

    /**
     * Handle a NewOrderPlaced event — notify customer and restaurant owner.
     */
    public function handleOrderPlaced(NewOrderPlaced $event): void
    {
        $order = $event->order;

        // Guard: need a customer email
        if (empty($order->customer_email)) {
            Log::warning("OrderNotifications: order #{$order->order_number} has no customer_email — skipping.");
            return;
        }

        // Email to customer
        Mail::to($order->customer_email)
            ->queue(new OrderPlacedMail($order));

        Log::info("OrderNotifications: queued OrderPlacedMail to {$order->customer_email} for order #{$order->order_number}");

        // Email to restaurant owner (if available)
        $ownerEmail = $order->restaurant->owner_email ?? null;

        if ($ownerEmail) {
            // Simple plain-text notification so the owner knows ASAP
            Mail::raw(
                "Nuevo pedido #{$order->order_number} recibido en {$order->restaurant->name}.\n"
                . "Cliente: {$order->customer_name} ({$order->customer_email})\n"
                . "Total: \${$order->total}\n"
                . "Tipo: {$order->order_type_label}\n\n"
                . "Ingresa a tu panel para confirmarlo.",
                function ($message) use ($ownerEmail, $order) {
                    $message
                        ->from('pedidos@restaurantesmexicanosfamosos.com', 'FAMER')
                        ->to($ownerEmail)
                        ->subject("🌮 Nuevo pedido #{$order->order_number} en {$order->restaurant->name}");
                }
            );

            Log::info("OrderNotifications: notified owner at {$ownerEmail} for order #{$order->order_number}");
        }
    }

    /**
     * Handle an OrderStatusUpdated event — notify customer of status change.
     */
    public function handleStatusUpdated(OrderStatusUpdated $event): void
    {
        $order = $event->order;

        if (! in_array($order->status, self::NOTIFIABLE_STATUSES, true)) {
            return;
        }

        if (empty($order->customer_email)) {
            Log::warning("OrderNotifications: order #{$order->order_number} has no customer_email — skipping status email.");
            return;
        }

        Mail::to($order->customer_email)
            ->queue(new OrderStatusMail($order));

        Log::info("OrderNotifications: queued OrderStatusMail [{$order->status}] to {$order->customer_email} for order #{$order->order_number}");
    }
}
