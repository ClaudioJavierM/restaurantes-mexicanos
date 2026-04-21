<?php

namespace App\Services;

use App\Models\Order;

class TicketFormatterService
{
    private const WIDTH = 32;

    public function format(Order $order): string
    {
        $order->loadMissing('items');

        $lines = [];

        $lines[] = str_repeat('=', self::WIDTH);
        $lines[] = $this->center('*** FAMER ORDERS ***');
        $lines[] = str_repeat('=', self::WIDTH);

        $lines[] = 'PEDIDO #' . $order->order_number;

        $typeLabel = match ($order->order_type) {
            'delivery' => 'DELIVERY',
            'pickup'   => 'PICKUP',
            default    => 'AQUI',
        };
        $lines[] = 'TIPO: ' . $typeLabel;

        $lines[] = now()->setTimezone('America/Chicago')->format('d/m/Y  g:i A');

        $lines[] = str_repeat('-', self::WIDTH);

        foreach ($order->items as $item) {
            $qty   = (string) $item->quantity;
            $name  = $item->name;
            $price = '$' . number_format($item->price * $item->quantity, 2);

            // Build " 2  Tacos al Pastor     $14.00"
            // Format: " Q  NAME" left + "PRICE" right, total WIDTH chars
            $left  = ' ' . str_pad($qty, 2) . ' ' . $name;
            $lines[] = $this->line($left, $price);

            if (!empty($item->special_instructions)) {
                $lines[] = '  - ' . $item->special_instructions;
            }
        }

        $lines[] = str_repeat('=', self::WIDTH);

        $lines[] = $this->line('Subtotal:', '$' . number_format($order->subtotal, 2));
        $lines[] = $this->line('Tax:', '$' . number_format($order->tax, 2));

        if ($order->delivery_fee > 0) {
            $lines[] = $this->line('Delivery:', '$' . number_format($order->delivery_fee, 2));
        }

        if (!empty($order->tip) && $order->tip > 0) {
            $lines[] = $this->line('Propina:', '$' . number_format($order->tip, 2));
        }

        $lines[] = $this->line('TOTAL:', '$' . number_format($order->total, 2));
        $lines[] = str_repeat('=', self::WIDTH);

        // Customer info
        $lines[] = 'CLIENTE: ' . $order->customer_name;

        if (!empty($order->customer_phone)) {
            $lines[] = 'TEL: ' . $order->customer_phone;
        }

        if ($order->order_type === 'pickup') {
            $lines[] = str_repeat('-', self::WIDTH);
            $lines[] = $this->center('*** RECOGER EN TIENDA ***');
        } elseif ($order->order_type === 'delivery') {
            if (!empty($order->delivery_address)) {
                $lines[] = 'ENTREGA: ' . $order->delivery_address;
            }
            if (!empty($order->delivery_city)) {
                $lines[] = '         ' . $order->delivery_city;
            }
        }

        if (!empty($order->special_instructions)) {
            $lines[] = str_repeat('-', self::WIDTH);
            $lines[] = 'NOTAS: ' . $order->special_instructions;
        }

        $lines[] = str_repeat('=', self::WIDTH);

        // Feed lines for paper advance before cut
        $lines[] = '';
        $lines[] = '';
        $lines[] = '';
        $lines[] = '';

        return implode("\n", $lines);
    }

    private function line(string $left, string $right = '', int $width = self::WIDTH): string
    {
        if ($right === '') {
            return $left;
        }

        $available = $width - strlen($right);
        return str_pad($left, $available) . $right;
    }

    private function center(string $text, int $width = self::WIDTH): string
    {
        $len     = strlen($text);
        $padding = (int) floor(($width - $len) / 2);

        return str_repeat(' ', max(0, $padding)) . $text;
    }
}
