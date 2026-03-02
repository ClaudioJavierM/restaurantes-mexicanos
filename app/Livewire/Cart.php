<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MenuItem;
use App\Models\Restaurant;

class Cart extends Component
{
    public $restaurantId;
    public $items = [];
    public $isOpen = false;
    
    protected $listeners = [
        'addToCart' => 'add',
        'openCart' => 'open',
        'closeCart' => 'close',
        'refreshCart' => '$refresh',
    ];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = session()->get('cart', []);
        $this->items = $cart['items'] ?? [];
        $this->restaurantId = $cart['restaurant_id'] ?? null;
    }

    public function saveCart()
    {
        session()->put('cart', [
            'restaurant_id' => $this->restaurantId,
            'items' => $this->items,
        ]);
    }

    public function add($menuItemId, $quantity = 1, $modifiers = [], $instructions = '')
    {
        $menuItem = MenuItem::with('category.restaurant')->find($menuItemId);
        
        if (!$menuItem) {
            return;
        }
        
        $newRestaurantId = $menuItem->category->restaurant_id;
        
        // If cart has items from different restaurant, ask to clear
        if ($this->restaurantId && $this->restaurantId !== $newRestaurantId && !empty($this->items)) {
            $this->dispatch('confirmClearCart', [
                'newRestaurantId' => $newRestaurantId,
                'menuItemId' => $menuItemId,
                'quantity' => $quantity,
            ]);
            return;
        }
        
        $this->restaurantId = $newRestaurantId;
        
        // Calculate modifier prices
        $modifierTotal = collect($modifiers)->sum('price');
        $itemPrice = $menuItem->sale_price ?? $menuItem->price;
        $totalPrice = ($itemPrice + $modifierTotal) * $quantity;
        
        // Create unique key for item + modifiers
        $itemKey = $menuItemId . '_' . md5(json_encode($modifiers) . $instructions);
        
        // Check if item already exists
        if (isset($this->items[$itemKey])) {
            $this->items[$itemKey]['quantity'] += $quantity;
            $this->items[$itemKey]['total_price'] = 
                ($itemPrice + $modifierTotal) * $this->items[$itemKey]['quantity'];
        } else {
            $this->items[$itemKey] = [
                'menu_item_id' => $menuItemId,
                'name' => $menuItem->name,
                'description' => $menuItem->description,
                'unit_price' => $itemPrice,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'modifiers' => $modifiers,
                'special_instructions' => $instructions,
                'image' => $menuItem->image,
            ];
        }
        
        $this->saveCart();
        $this->isOpen = true;
        
        $this->dispatch('cartUpdated', ['count' => $this->getItemCount()]);
        $this->dispatch('notify', ['message' => '¡Agregado al carrito!', 'type' => 'success']);
    }

    public function updateQuantity($itemKey, $quantity)
    {
        if ($quantity <= 0) {
            $this->remove($itemKey);
            return;
        }
        
        if (isset($this->items[$itemKey])) {
            $modifierTotal = collect($this->items[$itemKey]['modifiers'] ?? [])->sum('price');
            $this->items[$itemKey]['quantity'] = $quantity;
            $this->items[$itemKey]['total_price'] = 
                ($this->items[$itemKey]['unit_price'] + $modifierTotal) * $quantity;
            $this->saveCart();
            $this->dispatch('cartUpdated', ['count' => $this->getItemCount()]);
        }
    }

    public function remove($itemKey)
    {
        unset($this->items[$itemKey]);
        
        if (empty($this->items)) {
            $this->restaurantId = null;
        }
        
        $this->saveCart();
        $this->dispatch('cartUpdated', ['count' => $this->getItemCount()]);
    }

    public function clear()
    {
        $this->items = [];
        $this->restaurantId = null;
        session()->forget('cart');
        $this->dispatch('cartUpdated', ['count' => 0]);
    }

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function getItemCount(): int
    {
        return collect($this->items)->sum('quantity');
    }

    public function getSubtotal(): float
    {
        return collect($this->items)->sum('total_price');
    }

    public function getRestaurant()
    {
        if (!$this->restaurantId) {
            return null;
        }
        return Restaurant::find($this->restaurantId);
    }

    public function checkout()
    {
        if (empty($this->items)) {
            return;
        }
        
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.cart', [
            'restaurant' => $this->getRestaurant(),
            'subtotal' => $this->getSubtotal(),
            'itemCount' => $this->getItemCount(),
        ]);
    }
}
