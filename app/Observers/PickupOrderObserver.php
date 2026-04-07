<?php

namespace App\Observers;

use App\Models\PickupOrder;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class PickupOrderObserver
{
    public function created(PickupOrder $order): void
    {
        try {
            $restaurant = $order->restaurant()->with('user')->first();

            if (!$restaurant || !$restaurant->user) {
                return;
            }

            $ownerPhone = $restaurant->user->phone;

            if (empty($ownerPhone)) {
                return;
            }

            $twilio = app(TwilioService::class);

            if (!$twilio->isConfigured()) {
                return;
            }

            $items = collect($order->items);
            $itemCount = $items->sum(fn($item) => $item['quantity'] ?? 1);

            $pickupLine = $order->pickup_time
                ? "\nRecoger: " . $order->pickup_time->format('d/m/Y h:i A')
                : '';

            $notesLine = $order->special_instructions
                ? "\nNotas: {$order->special_instructions}"
                : '';

            $message  = "🍽️ NUEVO PEDIDO #{$order->order_number} en {$restaurant->name}\n";
            $message .= "Cliente: {$order->customer_name}\n";
            $message .= "Tel: {$order->customer_phone}\n";
            $message .= "Artículos: {$itemCount} items\n";
            $message .= "Total: \$" . number_format($order->total, 2);
            $message .= $pickupLine;
            $message .= $notesLine;

            $twilio->sendOwnerWhatsApp($ownerPhone, $message);
        } catch (\Exception $e) {
            Log::error('PickupOrderObserver: failed to send owner WhatsApp notification. ' . $e->getMessage());
        }
    }
}
