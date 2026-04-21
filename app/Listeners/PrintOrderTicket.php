<?php

namespace App\Listeners;

use App\Events\NewOrderPlaced;
use App\Models\PrintJob;
use App\Models\RestaurantPrinter;
use App\Services\TicketFormatterService;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrintOrderTicket implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private TicketFormatterService $formatter) {}

    public function handle(NewOrderPlaced $event): void
    {
        $order = $event->order;

        // Solo imprimir si hay impresora activa para este restaurante
        $printer = RestaurantPrinter::where('restaurant_id', $order->restaurant_id)
            ->where('is_active', true)
            ->first();

        if (!$printer) return;

        // Formatear ticket
        $content = $this->formatter->format($order);

        // Crear print job
        PrintJob::create([
            'restaurant_id' => $order->restaurant_id,
            'order_id'      => $order->id,
            'content'       => $content,
            'status'        => 'pending',
        ]);
    }
}
