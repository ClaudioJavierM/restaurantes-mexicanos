<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;

class OrderConfirmation extends Component
{
    public Order $order;

    public function mount($order_number)
    {
        $this->order = Order::with(['restaurant', 'items'])
            ->where('order_number', $order_number)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.order-confirmation')
            ->layout('layouts.app', ['title' => 'Pedido Confirmado - ' . $this->order->order_number]);
    }
}
