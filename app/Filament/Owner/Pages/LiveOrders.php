<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class LiveOrders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Pedidos en Vivo';
    protected static ?string $title = 'Pedidos en Tiempo Real';
    protected static ?int $navigationSort = 0;
    
    protected static string $view = 'filament.owner.pages.live-orders';

    public $orders = [];
    public $restaurantId;
    public $newOrdersCount = 0;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $teamMember = \App\Models\RestaurantTeamMember::where('user_id', $user->id)
            ->where('status', 'active')->first();
        if ($teamMember && $teamMember->role !== 'admin') {
            $permissions = $teamMember->permissions ?? [];
            if (!($permissions['orders'] ?? false)) {
                return false;
            }
        }
        return true;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        $user = Auth::user();
        $restaurant = $user->allAccessibleRestaurants()->first();
        
        if ($restaurant) {
            $this->restaurantId = $restaurant->id;
            $this->loadOrders();
        }
    }

    public function loadOrders(): void
    {
        if (!$this->restaurantId) {
            return;
        }

        $this->orders = Order::where('restaurant_id', $this->restaurantId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['items'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function updateOrderStatus(int $orderId, string $newStatus): void
    {
        $order = Order::where('id', $orderId)
            ->where('restaurant_id', $this->restaurantId)
            ->first();

        if ($order) {
            $order->update(['status' => $newStatus]);
            
            if ($newStatus === 'completed') {
                $order->update(['completed_at' => now()]);
            } elseif ($newStatus === 'confirmed') {
                $order->update(['confirmed_at' => now()]);
            }

            // Dispatch status update event
            event(new \App\Events\OrderStatusUpdatedEvent($order));
            
            $this->loadOrders();
        }
    }

    public function getListeners(): array
    {
        return [
            'echo-private:restaurant.' . $this->restaurantId . ',NewOrderEvent' => 'handleNewOrder',
        ];
    }

    public function handleNewOrder($event): void
    {
        $this->newOrdersCount++;
        $this->loadOrders();
        
        // Play notification sound
        $this->dispatch('play-notification-sound');
    }

    public function resetNewOrdersCount(): void
    {
        $this->newOrdersCount = 0;
    }
}
