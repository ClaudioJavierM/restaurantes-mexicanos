<?php

namespace App\Filament\Owner\Pages;

use App\Models\RestaurantTable;
use App\Models\TableOrder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MenuQrCode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR & Mesas';
    protected static ?string $title = 'QR de Mesas y Pedidos';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.owner.pages.menu-qr-code';

    public $restaurant = null;
    public array $tables = [];
    public array $activeOrders = [];
    public string $newTableName = '';
    public int $newTableCapacity = 4;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public function mount(): void
    {
        $selectedId = session('selected_restaurant_id');
        if ($selectedId) {
            $this->restaurant = Auth::user()->allAccessibleRestaurants()
                ->where('restaurants.id', $selectedId)->first();
        }
        if (!$this->restaurant) {
            $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        }
        $this->loadTables();
        $this->loadActiveOrders();
    }

    protected function loadTables(): void
    {
        if (!$this->restaurant) return;

        $this->tables = RestaurantTable::where('restaurant_id', $this->restaurant->id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    protected function loadActiveOrders(): void
    {
        if (!$this->restaurant) return;

        $this->activeOrders = TableOrder::where('restaurant_id', $this->restaurant->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->with('table')
            ->latest()
            ->get()
            ->toArray();
    }

    public function addTable(): void
    {
        $this->validate([
            'newTableName'     => 'required|string|max:50',
            'newTableCapacity' => 'required|integer|min:1|max:30',
        ]);

        RestaurantTable::create([
            'restaurant_id' => $this->restaurant->id,
            'name'          => $this->newTableName,
            'table_code'    => RestaurantTable::generateCode(),
            'capacity'      => $this->newTableCapacity,
            'is_active'     => true,
        ]);

        $this->newTableName = '';
        $this->newTableCapacity = 4;
        $this->loadTables();

        Notification::make()->title('Mesa agregada')->success()->send();
    }

    public function deleteTable(int $tableId): void
    {
        RestaurantTable::where('restaurant_id', $this->restaurant->id)
            ->where('id', $tableId)
            ->delete();

        $this->loadTables();
        Notification::make()->title('Mesa eliminada')->success()->send();
    }

    public function updateOrderStatus(int $orderId, string $status): void
    {
        TableOrder::where('restaurant_id', $this->restaurant->id)
            ->where('id', $orderId)
            ->update([
                'status'       => $status,
                'confirmed_at' => $status === 'confirmed' ? now() : null,
                'ready_at'     => $status === 'ready' ? now() : null,
            ]);

        $this->loadActiveOrders();
        Notification::make()->title('Estado actualizado')->success()->send();
    }
}
