<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;

class MiCuentaPedidos extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public function render()
    {
        $query = Order::with(['restaurant', 'items'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.mi-cuenta-pedidos', [
            'orders' => $query->paginate(10),
        ])->layout('layouts.app', ['title' => 'Mis Pedidos — FAMER']);
    }
}
