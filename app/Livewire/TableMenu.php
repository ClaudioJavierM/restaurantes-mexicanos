<?php

namespace App\Livewire;

use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\TableOrder;
use Livewire\Component;

class TableMenu extends Component
{
    public Restaurant $restaurant;
    public RestaurantTable $table;

    public array $cart = []; // [menu_item_id => [name, price, quantity, notes]]
    public string $customerName = '';
    public string $orderNotes = '';
    public string $activeCategory = 'all';

    public bool $orderPlaced = false;
    public ?string $orderNumber = null;
    public ?string $error = null;

    public function mount(string $restaurantSlug, string $tableCode): void
    {
        $this->restaurant = Restaurant::where('slug', $restaurantSlug)
            ->where('is_claimed', true)
            ->firstOrFail();

        $this->table = RestaurantTable::where('restaurant_id', $this->restaurant->id)
            ->where('table_code', $tableCode)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function addItem(int $menuItemId): void
    {
        $item = MenuItem::find($menuItemId);
        if (!$item || !$item->is_available) return;

        if (isset($this->cart[$menuItemId])) {
            $cart = $this->cart;
            $cart[$menuItemId]['quantity']++;
            $this->cart = $cart;
        } else {
            $cart = $this->cart;
            $cart[$menuItemId] = [
                'name'     => $item->name,
                'price'    => (float) $item->price,
                'quantity' => 1,
                'notes'    => '',
            ];
            $this->cart = $cart;
        }
    }

    public function removeItem(int $menuItemId): void
    {
        $cart = $this->cart;
        if (isset($cart[$menuItemId])) {
            if ($cart[$menuItemId]['quantity'] > 1) {
                $cart[$menuItemId]['quantity']--;
            } else {
                unset($cart[$menuItemId]);
            }
        }
        $this->cart = $cart;
    }

    public function getCartTotalProperty(): float
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function placeOrder(): void
    {
        $this->error = null;

        if (empty($this->cart)) {
            $this->error = 'Agrega al menos un platillo.';
            return;
        }

        $items = collect($this->cart)->map(fn($item, $id) => array_merge($item, ['menu_item_id' => $id]))->values()->all();

        $order = TableOrder::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id'      => $this->table->id,
            'order_number'  => TableOrder::generateNumber(),
            'customer_name' => $this->customerName ?: null,
            'items'         => $items,
            'subtotal'      => $this->cartTotal,
            'notes'         => $this->orderNotes ?: null,
            'status'        => 'pending',
        ]);

        $this->orderPlaced = true;
        $this->orderNumber = $order->order_number;
        $this->cart = [];
    }

    public function render()
    {
        $categories = MenuCategory::where('restaurant_id', $this->restaurant->id)
            ->active()
            ->whereHas('items', fn($q) => $q->where('is_available', true))
            ->ordered()
            ->get();

        $itemsQuery = MenuItem::where('restaurant_id', $this->restaurant->id)
            ->where('is_available', true)
            ->orderBy('sort_order');

        if ($this->activeCategory !== 'all') {
            $itemsQuery->where('menu_category_id', $this->activeCategory);
        }

        return view('livewire.table-menu', [
            'categories' => $categories,
            'menuItems'  => $itemsQuery->get(),
        ])->layout('layouts.app', [
            'title' => $this->table->name . ' — ' . $this->restaurant->name,
        ]);
    }
}
