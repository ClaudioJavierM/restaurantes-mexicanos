<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Success Animation -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Pedido Confirmado!</h1>
            <p class="text-gray-600">Hemos recibido tu pedido correctamente</p>
        </div>

        <!-- Order Details Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="bg-red-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-sm">Número de pedido</p>
                        <p class="text-2xl font-bold">#{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-red-200 text-sm">Total</p>
                        <p class="text-2xl font-bold">${{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="p-6 border-b">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl">{{ $order->status === 'pending' ? '⏳' : '✅' }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $order->status_label }}</p>
                        <p class="text-sm text-gray-500">
                            @if($order->scheduled_for)
                                Programado para {{ $order->scheduled_for->format('d/m/Y') }} a las {{ $order->scheduled_for->format('H:i') }}
                            @else
                                Tiempo estimado: 20-35 minutos
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Restaurant Info -->
            <div class="p-6 border-b">
                <h3 class="font-semibold text-gray-900 mb-2">📍 {{ $order->restaurant->name }}</h3>
                <p class="text-gray-600">{{ $order->restaurant->address }}</p>
                @if($order->restaurant->phone)
                <p class="text-gray-600">📞 {{ $order->restaurant->phone }}</p>
                @endif
            </div>

            <!-- Order Type -->
            <div class="p-6 border-b">
                <div class="flex items-center gap-2 text-gray-900">
                    <span class="text-xl">{{ $order->order_type === 'delivery' ? '🚗' : '🏃' }}</span>
                    <span class="font-semibold">{{ $order->order_type_label }}</span>
                </div>
                @if($order->order_type === 'delivery')
                <p class="text-gray-600 mt-2">
                    {{ $order->delivery_address }}<br>
                    {{ $order->delivery_city }}, {{ $order->delivery_zip }}
                </p>
                @if($order->delivery_instructions)
                <p class="text-gray-500 text-sm mt-1">📝 {{ $order->delivery_instructions }}</p>
                @endif
                @endif
            </div>

            <!-- Items -->
            <div class="p-6 border-b">
                <h3 class="font-semibold text-gray-900 mb-4">Tu Pedido</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between">
                        <div>
                            <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-sm font-bold mr-2">{{ $item->quantity }}</span>
                            <span class="text-gray-900">{{ $item->name }}</span>
                            @if($item->modifiers_text)
                            <p class="text-xs text-gray-500 ml-8">{{ $item->modifiers_text }}</p>
                            @endif
                        </div>
                        <span class="text-gray-900">${{ number_format($item->total_price, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Totals -->
            <div class="p-6 bg-gray-50">
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Impuesto</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    @if($order->delivery_fee > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery</span>
                        <span>${{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    @endif
                    @if($order->tip > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Propina</span>
                        <span>${{ number_format($order->tip, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                        <span>Total</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="p-6 border-t">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Método de pago</span>
                    <span class="font-semibold text-gray-900">
                        {{ $order->payment_method === 'cash' ? '💵 Efectivo' : '💳 Tarjeta' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h3 class="font-semibold text-gray-900 mb-4">Tu Información</h3>
            <div class="space-y-2 text-gray-600">
                <p>👤 {{ $order->customer_name }}</p>
                <p>📧 {{ $order->customer_email }}</p>
                <p>📞 {{ $order->customer_phone }}</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col gap-4">
            <a href="{{ route('restaurants.show', $order->restaurant->slug) }}"
               class="w-full bg-red-600 text-white py-4 rounded-xl font-bold text-center hover:bg-red-700 transition">
                Volver al Restaurante
            </a>
            <a href="{{ route('home') }}"
               class="w-full bg-gray-200 text-gray-700 py-4 rounded-xl font-bold text-center hover:bg-gray-300 transition">
                Ir al Inicio
            </a>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            Recibirás un email de confirmación en {{ $order->customer_email }}
        </p>
    </div>
</div>
