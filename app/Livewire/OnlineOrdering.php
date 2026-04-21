<?php

namespace App\Livewire;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OnlineOrdering extends Component
{
    // Restaurant context
    public Restaurant $restaurant;

    // Step flow: menu | checkout | confirmation
    public string $step = 'menu';

    // Cart: [menu_item_id => ['name', 'price', 'quantity']]
    public array $cart = [];

    // Checkout form fields
    public string $customer_name = '';
    public string $customer_email = '';
    public string $customer_phone = '';
    public string $order_type = 'pickup';
    public string $delivery_address = '';
    public string $delivery_city = '';
    public string $payment_method = 'cash';
    public string $special_instructions = '';

    // Confirmation
    public ?string $confirmedOrderNumber = null;
    public ?int $confirmedOrderId = null;

    // Active category filter (null = all)
    public ?int $activeCategoryId = null;

    // Tax rate for order calculations (NY average)
    const TAX_RATE = 0.08875;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    #[Computed]
    public function categories(): Collection
    {
        return MenuCategory::where('restaurant_id', $this->restaurant->id)
            ->active()
            ->ordered()
            ->with(['activeItems' => fn ($q) => $q->ordered()])
            ->get()
            ->filter(fn ($cat) => $cat->activeItems->isNotEmpty());
    }

    #[Computed]
    public function filteredCategories(): Collection
    {
        if ($this->activeCategoryId) {
            return $this->categories->where('id', $this->activeCategoryId)->values();
        }
        return $this->categories;
    }

    #[Computed]
    public function cartSubtotal(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    }

    #[Computed]
    public function cartTax(): float
    {
        return $this->cartSubtotal * self::TAX_RATE;
    }

    #[Computed]
    public function cartTotal(): float
    {
        return $this->cartSubtotal + $this->cartTax;
    }

    #[Computed]
    public function cartCount(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function addToCart(int $menuItemId): void
    {
        $item = MenuItem::find($menuItemId);

        if (!$item || !$item->is_available) {
            return;
        }

        $price = $item->sale_price && $item->sale_price < $item->price
            ? (float) $item->sale_price
            : (float) $item->price;

        if (isset($this->cart[$menuItemId])) {
            $this->cart[$menuItemId]['quantity']++;
        } else {
            $this->cart[$menuItemId] = [
                'name'     => $item->name,
                'price'    => $price,
                'quantity' => 1,
            ];
        }
    }

    public function removeFromCart(int $menuItemId): void
    {
        if (isset($this->cart[$menuItemId])) {
            if ($this->cart[$menuItemId]['quantity'] > 1) {
                $this->cart[$menuItemId]['quantity']--;
            } else {
                unset($this->cart[$menuItemId]);
            }
        }
    }

    public function deleteFromCart(int $menuItemId): void
    {
        unset($this->cart[$menuItemId]);
    }

    public function updateQuantity(int $menuItemId, int $qty): void
    {
        if ($qty <= 0) {
            unset($this->cart[$menuItemId]);
            return;
        }

        if (isset($this->cart[$menuItemId])) {
            $this->cart[$menuItemId]['quantity'] = $qty;
        }
    }

    public function setActiveCategory(?int $categoryId): void
    {
        $this->activeCategoryId = $categoryId;
    }

    public function goToCheckout(): void
    {
        if (empty($this->cart)) {
            return;
        }
        $this->step = 'checkout';
    }

    public function backToMenu(): void
    {
        $this->step = 'menu';
    }

    public function placeOrder(): void
    {
        $rules = [
            'customer_name'  => 'required|string|min:2|max:100',
            'customer_email' => 'required|email|max:150',
            'customer_phone' => 'required|string|min:7|max:20',
            'order_type'     => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:cash,card',
        ];

        if ($this->order_type === 'delivery') {
            $rules['delivery_address'] = 'required|string|min:5|max:255';
            $rules['delivery_city']    = 'required|string|min:2|max:100';
        }

        $this->validate($rules, [
            'customer_name.required'  => 'Your name is required.',
            'customer_email.required' => 'Email is required.',
            'customer_email.email'    => 'Please enter a valid email.',
            'customer_phone.required' => 'Phone number is required.',
            'delivery_address.required' => 'Delivery address is required.',
            'delivery_city.required'    => 'City is required.',
        ]);

        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        $subtotal    = $this->cartSubtotal;
        $tax         = round($subtotal * self::TAX_RATE, 2);
        $deliveryFee = 0.00;
        $total       = round($subtotal + $tax + $deliveryFee, 2);

        $order = Order::create([
            'order_number'         => Order::generateOrderNumber(),
            'restaurant_id'        => $this->restaurant->id,
            'customer_name'        => $this->customer_name,
            'customer_email'       => $this->customer_email,
            'customer_phone'       => $this->customer_phone,
            'order_type'           => $this->order_type,
            'delivery_address'     => $this->order_type === 'delivery' ? $this->delivery_address : null,
            'delivery_city'        => $this->order_type === 'delivery' ? $this->delivery_city : null,
            'payment_method'       => $this->payment_method,
            'special_instructions' => $this->special_instructions ?: null,
            'subtotal'             => $subtotal,
            'tax'                  => $tax,
            'delivery_fee'         => $deliveryFee,
            'tip'                  => 0,
            'discount'             => 0,
            'total'                => $total,
            'payment_status'       => 'pending',
            'status'               => 'pending',
        ]);

        foreach ($this->cart as $menuItemId => $cartItem) {
            $unitPrice  = $cartItem['price'];
            $qty        = $cartItem['quantity'];
            $totalPrice = round($unitPrice * $qty, 2);

            OrderItem::create([
                'order_id'     => $order->id,
                'menu_item_id' => $menuItemId,
                'name'         => $cartItem['name'],
                'unit_price'   => $unitPrice,
                'quantity'     => $qty,
                'total_price'  => $totalPrice,
            ]);
        }

        $this->confirmedOrderNumber = $order->order_number;
        $this->confirmedOrderId     = $order->id;

        $this->dispatch('order-placed', orderId: $order->id);

        $this->step = 'confirmation';

        $this->redirect('/pedido/' . $order->order_number);
    }

    public function render()
    {
        return view('livewire.online-ordering');
    }
}
