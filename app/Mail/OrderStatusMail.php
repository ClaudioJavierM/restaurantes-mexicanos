<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        $number     = $this->order->order_number;
        $restaurant = $this->order->restaurant->name ?? 'el restaurante';

        $subject = match ($this->order->status) {
            'confirmed'        => "✅ Pedido #{$number} confirmado — ¡Manos a la obra!",
            'preparing'        => "👨‍🍳 Tu pedido #{$number} está en preparación",
            'ready'            => "🔔 ¡Tu pedido #{$number} está listo!",
            'out_for_delivery' => "🚗 Tu pedido #{$number} está en camino",
            'completed'        => "⭐ ¿Cómo estuvo tu experiencia en {$restaurant}?",
            'cancelled'        => "❌ Tu pedido #{$number} fue cancelado",
            default            => "Actualización de tu pedido #{$number}",
        };

        return new Envelope(
            from: new Address('pedidos@restaurantesmexicanosfamosos.com', 'FAMER'),
            subject: $subject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe'      => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence'            => 'bulk',
                'X-Mailer'              => 'FAMER-Platform',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
