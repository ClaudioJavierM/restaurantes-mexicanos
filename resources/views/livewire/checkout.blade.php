<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="text-red-600 hover:text-red-700 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al menú
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600">Pedido de {{ $restaurant->name }}</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-danger-100 border border-danger-400 text-danger-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Forms -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        Tu Información
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" wire:model="customerName"
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="Tu nombre completo">
                            @error('customerName') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                            <input type="tel" wire:model="customerPhone"
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="(123) 456-7890">
                            @error('customerPhone') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" wire:model="customerEmail"
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="tu@email.com">
                            @error('customerEmail') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Type -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        Tipo de Pedido
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="orderType" value="pickup" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $orderType === 'pickup' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">🏃</div>
                                <div class="font-semibold text-gray-900">Para Llevar</div>
                                <div class="text-sm text-gray-500">Recoge en el restaurante</div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="orderType" value="delivery" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $orderType === 'delivery' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">🚗</div>
                                <div class="font-semibold text-gray-900">Delivery</div>
                                <div class="text-sm text-gray-500">+${{ number_format($this->deliveryFee, 2) }}</div>
                            </div>
                        </label>
                    </div>

                    <!-- Delivery Address -->
                    @if($orderType === 'delivery')
                    <div class="mt-6 space-y-4 pt-6 border-t">
                        <h3 class="font-semibold text-gray-900">Dirección de Entrega</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                            <input type="text" wire:model="deliveryAddress"
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="123 Main St, Apt 4B">
                            @error('deliveryAddress') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                                <input type="text" wire:model="deliveryCity"
                                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                @error('deliveryCity') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ZIP *</label>
                                <input type="text" wire:model="deliveryZip"
                                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                @error('deliveryZip') <span class="text-danger-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instrucciones de entrega</label>
                            <textarea wire:model="deliveryInstructions" rows="2"
                                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                      placeholder="Ej: Dejar en la puerta, llamar al timbre..."></textarea>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Schedule -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                        ¿Cuándo lo quieres?
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="scheduleType" value="asap" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $scheduleType === 'asap' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">⚡</div>
                                <div class="font-semibold text-gray-900">Lo antes posible</div>
                                <div class="text-sm text-gray-500">20-35 min</div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="scheduleType" value="scheduled" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $scheduleType === 'scheduled' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">📅</div>
                                <div class="font-semibold text-gray-900">Programar</div>
                                <div class="text-sm text-gray-500">Elige fecha y hora</div>
                            </div>
                        </label>
                    </div>

                    @if($scheduleType === 'scheduled')
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <input type="date" wire:model="scheduledDate"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                            <select wire:model="scheduledTime"
                                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                                <option value="">Seleccionar...</option>
                                @for($hour = 10; $hour <= 21; $hour++)
                                    <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                    <option value="{{ sprintf('%02d:30', $hour) }}">{{ sprintf('%02d:30', $hour) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="bg-red-100 text-red-600 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">4</span>
                        Método de Pago
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="cash" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $paymentMethod === 'cash' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">💵</div>
                                <div class="font-semibold text-gray-900">Efectivo</div>
                                <div class="text-sm text-gray-500">Pagar al recibir</div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="card" class="sr-only">
                            <div class="p-4 rounded-xl border-2 transition {{ $paymentMethod === 'card' ? 'border-red-600 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="text-2xl mb-2">💳</div>
                                <div class="font-semibold text-gray-900">Tarjeta</div>
                                <div class="text-sm text-gray-500">Pago seguro</div>
                            </div>
                        </label>
                    </div>

                    <!-- Stripe Card Element -->
                    @if($paymentMethod === 'card')
                    <div class="mt-6 pt-6 border-t" x-data="stripePayment()" x-init="init()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Datos de la Tarjeta</label>
                            <div id="card-element" class="p-4 border border-gray-300 rounded-lg bg-white"></div>
                            <div id="card-errors" class="mt-2 text-danger-600 text-sm" role="alert"></div>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>Pago seguro con encriptación SSL</span>
                        </div>

                        <div class="flex items-center justify-center gap-3 mt-4 opacity-60">
                            <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/flags/4x3/us.svg" alt="Visa" class="h-6">
                            <span class="text-xs text-gray-500">Visa</span>
                            <span class="text-xs text-gray-500">•</span>
                            <span class="text-xs text-gray-500">Mastercard</span>
                            <span class="text-xs text-gray-500">•</span>
                            <span class="text-xs text-gray-500">Amex</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Special Instructions -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Instrucciones Especiales</h2>
                    <textarea wire:model="specialInstructions" rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                              placeholder="Alergias, preferencias, instrucciones especiales..."></textarea>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tu Pedido</h2>

                    <!-- Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-start gap-2">
                                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-sm font-bold">{{ $item['quantity'] }}</span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $item['name'] }}</p>
                                        @if(!empty($item['modifiers']))
                                        <p class="text-xs text-gray-500">
                                            @foreach($item['modifiers'] as $mod)
                                                {{ $mod['name'] }}@if(!$loop->last), @endif
                                            @endforeach
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="font-medium text-gray-900">${{ number_format($item['total_price'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Tip -->
                    <div class="border-t pt-4 mb-4">
                        <h3 class="font-medium text-gray-900 mb-3">Propina</h3>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach([10, 15, 18, 20] as $percent)
                            <button wire:click="setTipPercent({{ $percent }})"
                                    class="py-2 rounded-lg text-sm font-medium transition
                                           {{ $tipType === 'percent' && $tipPercent == $percent
                                              ? 'bg-red-600 text-white'
                                              : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $percent }}%
                            </button>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button wire:click="setCustomTip"
                                    class="text-sm text-red-600 hover:text-red-700">
                                Otra cantidad
                            </button>
                            @if($tipType === 'custom')
                            <input type="number" wire:model.live="customTip"
                                   class="mt-2 w-full rounded-lg border-gray-300 text-sm"
                                   placeholder="$0.00" min="0" step="0.5">
                            @endif
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Impuesto ({{ $taxRate * 100 }}%)</span>
                            <span>${{ number_format($this->tax, 2) }}</span>
                        </div>
                        @if($orderType === 'delivery')
                        <div class="flex justify-between text-gray-600">
                            <span>Delivery</span>
                            <span>${{ number_format($this->deliveryFee, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-gray-600">
                            <span>Propina</span>
                            <span>${{ number_format($this->tipAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                            <span>Total</span>
                            <span>${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    @if($paymentMethod === 'cash')
                        <button wire:click="placeOrder"
                                wire:loading.attr="disabled"
                                class="w-full mt-6 bg-red-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-wait">
                            <span wire:loading.remove wire:target="placeOrder">Confirmar Pedido</span>
                            <span wire:loading wire:target="placeOrder">Procesando...</span>
                        </button>
                    @else
                        <button id="submit-payment"
                                class="w-full mt-6 bg-red-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-wait">
                            <span id="button-text">Pagar ${{ number_format($this->total, 2) }}</span>
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Procesando pago...
                            </span>
                        </button>
                    @endif

                    <p class="text-xs text-gray-500 text-center mt-4">
                        Al confirmar, aceptas nuestros términos y condiciones
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    function stripePayment() {
        return {
            stripe: null,
            elements: null,
            cardElement: null,
            clientSecret: null,

            init() {
                this.stripe = Stripe('{{ $stripeKey }}');
                this.elements = this.stripe.elements();

                const style = {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#dc2626',
                        iconColor: '#dc2626'
                    }
                };

                this.cardElement = this.elements.create('card', { style: style });
                this.cardElement.mount('#card-element');

                this.cardElement.on('change', (event) => {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });

                // Listen for client secret from Livewire
                Livewire.on('stripeReady', (data) => {
                    this.clientSecret = data.clientSecret;
                });

                // Handle form submission
                const submitButton = document.getElementById('submit-payment');
                if (submitButton) {
                    submitButton.addEventListener('click', () => this.handlePayment());
                }
            },

            async handlePayment() {
                const submitButton = document.getElementById('submit-payment');
                const buttonText = document.getElementById('button-text');
                const spinner = document.getElementById('spinner');

                submitButton.disabled = true;
                buttonText.classList.add('hidden');
                spinner.classList.remove('hidden');

                if (!this.clientSecret) {
                    // Wait for client secret
                    await new Promise(resolve => setTimeout(resolve, 1000));

                    if (!this.clientSecret) {
                        const displayError = document.getElementById('card-errors');
                        displayError.textContent = 'Error al preparar el pago. Intenta de nuevo.';
                        submitButton.disabled = false;
                        buttonText.classList.remove('hidden');
                        spinner.classList.add('hidden');
                        return;
                    }
                }

                const { paymentIntent, error } = await this.stripe.confirmCardPayment(
                    this.clientSecret,
                    {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: {
                                name: @this.customerName,
                                email: @this.customerEmail,
                                phone: @this.customerPhone,
                            },
                        },
                    }
                );

                if (error) {
                    const displayError = document.getElementById('card-errors');
                    displayError.textContent = error.message;
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                } else if (paymentIntent.status === 'succeeded') {
                    // Payment successful, create the order
                    @this.call('placeOrder', paymentIntent.id);
                }
            }
        };
    }
</script>
@endpush
