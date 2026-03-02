<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmationMail;
use App\Services\TwilioService;
use App\Events\NewOrderEvent;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class Checkout extends Component
{
    // Customer Info
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';

    // Order Type
    public $orderType = 'pickup'; // pickup, delivery

    // Delivery Address
    public $deliveryAddress = '';
    public $deliveryCity = '';
    public $deliveryZip = '';
    public $deliveryInstructions = '';

    // Scheduling
    public $scheduleType = 'asap'; // asap, scheduled
    public $scheduledDate = '';
    public $scheduledTime = '';

    // Tip
    public $tipType = 'percent'; // percent, custom
    public $tipPercent = 15;
    public $customTip = 0;

    // Payment
    public $paymentMethod = 'cash'; // card, cash
    public $stripePaymentIntentId = null;
    public $stripeClientSecret = null;

    // Special Instructions
    public $specialInstructions = '';

    // Cart data
    public $cart = [];
    public $restaurant = null;

    // Tax rate (configurable per restaurant/state)
    public $taxRate = 0.0825; // 8.25%

    // Processing state
    public $isProcessing = false;

    protected $rules = [
        'customerName' => 'required|min:2',
        'customerEmail' => 'required|email',
        'customerPhone' => 'required|min:10',
        'orderType' => 'required|in:pickup,delivery',
        'deliveryAddress' => 'required_if:orderType,delivery',
        'deliveryCity' => 'required_if:orderType,delivery',
        'deliveryZip' => 'required_if:orderType,delivery',
    ];

    protected $messages = [
        'customerName.required' => 'Tu nombre es requerido',
        'customerEmail.required' => 'Tu email es requerido',
        'customerEmail.email' => 'Email inválido',
        'customerPhone.required' => 'Tu teléfono es requerido',
        'deliveryAddress.required_if' => 'La dirección es requerida para delivery',
        'deliveryCity.required_if' => 'La ciudad es requerida para delivery',
        'deliveryZip.required_if' => 'El código postal es requerido',
    ];

    public function mount()
    {
        $cartData = session()->get('cart', []);
        $this->cart = $cartData['items'] ?? [];
        $restaurantId = $cartData['restaurant_id'] ?? null;

        if (empty($this->cart) || !$restaurantId) {
            return redirect()->route('home');
        }

        $this->restaurant = Restaurant::find($restaurantId);

        // Pre-fill user info if logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName = $user->name;
            $this->customerEmail = $user->email;
            $this->customerPhone = $user->phone ?? '';
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum('total_price');
    }

    public function getTaxProperty()
    {
        return $this->subtotal * $this->taxRate;
    }

    public function getDeliveryFeeProperty()
    {
        if ($this->orderType === 'delivery') {
            return 3.99; // Fixed delivery fee, could be dynamic
        }
        return 0;
    }

    public function getTipAmountProperty()
    {
        if ($this->tipType === 'percent') {
            return $this->subtotal * ($this->tipPercent / 100);
        }
        return (float) $this->customTip;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->tax + $this->deliveryFee + $this->tipAmount;
    }

    public function setTipPercent($percent)
    {
        $this->tipType = 'percent';
        $this->tipPercent = $percent;
        $this->customTip = 0;
    }

    public function setCustomTip()
    {
        $this->tipType = 'custom';
        $this->tipPercent = 0;
    }

    public function updatedPaymentMethod($value)
    {
        if ($value === 'card') {
            $this->createPaymentIntent();
        } else {
            $this->stripePaymentIntentId = null;
            $this->stripeClientSecret = null;
        }
    }

    public function createPaymentIntent()
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $amount = (int) round($this->total * 100); // Convert to cents

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'restaurant_id' => $this->restaurant->id,
                    'restaurant_name' => $this->restaurant->name,
                    'customer_email' => $this->customerEmail,
                ],
            ]);

            $this->stripePaymentIntentId = $paymentIntent->id;
            $this->stripeClientSecret = $paymentIntent->client_secret;

            $this->dispatch('stripeReady', clientSecret: $this->stripeClientSecret);

        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed: ' . $e->getMessage());
            session()->flash('error', 'Error al preparar el pago. Intenta de nuevo.');
        }
    }

    public function updatePaymentIntentAmount()
    {
        if (!$this->stripePaymentIntentId) {
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $amount = (int) round($this->total * 100);

            PaymentIntent::update($this->stripePaymentIntentId, [
                'amount' => $amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent update failed: ' . $e->getMessage());
        }
    }

    public function placeOrder($paymentIntentId = null)
    {
        $this->validate();

        $this->isProcessing = true;

        try {
            DB::beginTransaction();

            // For card payments, verify the payment was successful
            if ($this->paymentMethod === 'card') {
                if (!$paymentIntentId) {
                    throw new \Exception('Payment not completed');
                }

                Stripe::setApiKey(config('services.stripe.secret'));
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                if ($paymentIntent->status !== 'succeeded') {
                    throw new \Exception('Payment was not successful');
                }
            }

            // Calculate scheduled time
            $scheduledFor = null;
            if ($this->scheduleType === 'scheduled' && $this->scheduledDate && $this->scheduledTime) {
                $scheduledFor = \Carbon\Carbon::parse("{$this->scheduledDate} {$this->scheduledTime}");
            }

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'restaurant_id' => $this->restaurant->id,
                'user_id' => auth()->id(),
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'order_type' => $this->orderType,
                'delivery_address' => $this->deliveryAddress,
                'delivery_city' => $this->deliveryCity,
                'delivery_zip' => $this->deliveryZip,
                'delivery_instructions' => $this->deliveryInstructions,
                'scheduled_for' => $scheduledFor,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'delivery_fee' => $this->deliveryFee,
                'tip' => $this->tipAmount,
                'discount' => 0,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => $this->paymentMethod === 'card' ? 'paid' : 'pending',
                'payment_intent_id' => $paymentIntentId,
                'status' => 'pending',
                'special_instructions' => $this->specialInstructions,
            ]);

            // Create order items
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                    'modifiers' => $item['modifiers'] ?? null,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);
            }

            DB::commit();

            // Clear cart
            session()->forget('cart');

            // Send order confirmation email to customer
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                Log::error("Failed to send order confirmation email: " . $e->getMessage());
            }

            // Send SMS notification to restaurant
            try {
                $twilioService = new TwilioService();
                $twilioService->sendNewOrderNotification($order, $this->restaurant->phone);
            } catch (\Exception $e) {
                Log::error("Failed to send SMS notification: " . $e->getMessage());
            }

            // Broadcast new order event for real-time dashboard
            try {
                event(new NewOrderEvent($order));
            } catch (\Exception $e) {
                Log::error("Failed to broadcast new order event: " . $e->getMessage());
            }

            return redirect()->route('order.confirmation', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->isProcessing = false;
            session()->flash('error', 'Error al procesar tu pedido: ' . $e->getMessage());
            Log::error('Checkout error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.checkout', [
            'stripeKey' => config('services.stripe.key'),
        ])->layout('layouts.app', ['title' => 'Checkout - ' . ($this->restaurant->name ?? '')]);
    }
}
