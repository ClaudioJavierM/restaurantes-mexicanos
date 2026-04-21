<?php

namespace App\Livewire\Owner;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Computed;

class LiveOrdersBoard extends Component
{
    public ?int $restaurantId = null;
    public string $filter = 'active'; // 'active' | 'today' | 'completed'
    public bool $showCancelModal = false;
    public ?int $cancelOrderId = null;
    public string $cancelReason = '';

    public function mount(): void
    {
        $restaurant = auth()->user()?->restaurants()->first();
        $this->restaurantId = $restaurant?->id;
    }

    #[Computed]
    public function orders(): array
    {
        if (! $this->restaurantId) {
            return [];
        }

        $query = Order::with('items')
            ->where('restaurant_id', $this->restaurantId)
            ->orderBy('created_at', 'asc');

        if ($this->filter === 'active') {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($this->filter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->filter === 'completed') {
            $query->whereIn('status', ['completed', 'cancelled']);
        }

        $all = $query->get();

        $columns = [
            'pending'          => [],
            'confirmed'        => [],
            'preparing'        => [],
            'ready'            => [],
            'out_for_delivery' => [],
        ];

        if ($this->filter === 'completed') {
            $columns['completed']  = [];
            $columns['cancelled']  = [];
        }

        foreach ($all as $order) {
            if (array_key_exists($order->status, $columns)) {
                $columns[$order->status][] = $order;
            }
        }

        return $columns;
    }

    public function confirmOrder(int $orderId): void
    {
        $order = $this->resolveOrder($orderId);
        if (! $order) return;

        $previousStatus = $order->status;
        $order->updateStatus('confirmed');
        event(new OrderStatusUpdated($order, $previousStatus));

        unset($this->orders);
    }

    public function startPreparing(int $orderId): void
    {
        $order = $this->resolveOrder($orderId);
        if (! $order) return;

        $previousStatus = $order->status;
        $order->updateStatus('preparing');
        event(new OrderStatusUpdated($order, $previousStatus));

        unset($this->orders);
    }

    public function markReady(int $orderId): void
    {
        $order = $this->resolveOrder($orderId);
        if (! $order) return;

        $previousStatus = $order->status;
        $order->updateStatus('ready');
        event(new OrderStatusUpdated($order, $previousStatus));

        unset($this->orders);
    }

    public function markDelivered(int $orderId): void
    {
        $order = $this->resolveOrder($orderId);
        if (! $order) return;

        $previousStatus = $order->status;
        $order->updateStatus('completed');
        event(new OrderStatusUpdated($order, $previousStatus));

        unset($this->orders);
    }

    public function openCancelModal(int $orderId): void
    {
        $this->cancelOrderId = $orderId;
        $this->cancelReason  = '';
        $this->showCancelModal = true;
    }

    public function cancelOrder(): void
    {
        if (! $this->cancelOrderId) return;

        $order = $this->resolveOrder($this->cancelOrderId);
        if (! $order) {
            $this->showCancelModal = false;
            return;
        }

        if (! $order->canBeCancelled()) {
            $this->showCancelModal = false;
            return;
        }

        $previousStatus = $order->status;
        $order->cancellation_reason = $this->cancelReason ?: 'Cancelado por el restaurante';
        $order->save();
        $order->updateStatus('cancelled');
        event(new OrderStatusUpdated($order, $previousStatus));

        $this->showCancelModal = false;
        $this->cancelOrderId   = null;
        $this->cancelReason    = '';
        unset($this->orders);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        unset($this->orders);
    }

    public function render()
    {
        return view('livewire.owner.live-orders-board');
    }

    // --------------- private helpers ---------------

    private function resolveOrder(int $orderId): ?Order
    {
        $order = Order::find($orderId);

        if (! $order || $order->restaurant_id !== $this->restaurantId) {
            return null;
        }

        return $order;
    }
}
