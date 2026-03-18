<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class MyOrders extends Component
{
    use WithPagination;

    public ?int $expandedOrder = null;

    public function toggleOrder($orderId)
    {
        $this->expandedOrder = $this->expandedOrder === $orderId ? null : $orderId;
    }

    public function render()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['restaurant', 'items'])
            ->latest()
            ->paginate(12);

        return view('livewire.my-orders', [
            'orders' => $orders,
        ])->layout('layouts.app', ['title' => 'Mis Pedidos']);
    }
}
