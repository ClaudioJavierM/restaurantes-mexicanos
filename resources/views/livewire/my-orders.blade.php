<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🛍️ Mis Pedidos</h1>
            <p class="mt-2 text-gray-600">Historial de tus pedidos en restaurantes mexicanos</p>
        </div>

        @if($orders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Restaurant image -->
                        <a href="{{ $order->restaurant ? route('restaurants.show', $order->restaurant->slug) : '#' }}" class="block">
                            @if($order->restaurant && $order->restaurant->image)
                                <img src="{{ Storage::url($order->restaurant->image) }}"
                                     alt="{{ $order->restaurant->name }}"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            <!-- Restaurant + status -->
                            <div class="flex items-start justify-between mb-2">
                                <a href="{{ $order->restaurant ? route('restaurants.show', $order->restaurant->slug) : '#' }}">
                                    <h3 class="text-base font-bold text-gray-900 hover:text-red-600 transition-colors leading-tight">
                                        {{ $order->restaurant->name ?? 'Restaurante' }}
                                    </h3>
                                </a>
                            </div>

                            <!-- Status badge -->
                            <div class="mb-2">
                                <span class="text-sm font-semibold">{{ $order->status_label }}</span>
                            </div>

                            <!-- Order number + type + date -->
                            <div class="text-xs text-gray-500 mb-1 font-mono">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-500 mb-1">{{ $order->order_type_label }}</div>
                            <div class="text-xs text-gray-400 mb-3">{{ $order->created_at->format('d M Y, h:i A') }}</div>

                            <!-- Total -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-gray-600">Total</span>
                                <span class="text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</span>
                            </div>

                            <!-- Items summary / expandable -->
                            @if($order->items->count() > 0)
                                <button
                                    wire:click="toggleOrder({{ $order->id }})"
                                    class="w-full text-left text-xs text-blue-600 hover:text-blue-800 font-medium mb-2 flex items-center gap-1"
                                >
                                    @if($expandedOrder === $order->id)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                        Ocultar artículos
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                        Ver {{ $order->items->count() }} {{ $order->items->count() == 1 ? 'artículo' : 'artículos' }}
                                    @endif
                                </button>

                                @if($expandedOrder === $order->id)
                                    <div class="border-t border-gray-100 pt-2 space-y-1">
                                        @foreach($order->items as $item)
                                            <div class="flex justify-between text-sm text-gray-700">
                                                <span>{{ $item->quantity }}x {{ $item->name }}</span>
                                                <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-gray-500 line-clamp-1">
                                        {{ $order->items->take(3)->pluck('name')->join(', ') }}
                                        @if($order->items->count() > 3)
                                            y {{ $order->items->count() - 3 }} más
                                        @endif
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tienes pedidos aún</h3>
                <p class="text-gray-600 mb-6">Cuando hagas un pedido en un restaurante mexicano aparecerá aquí.</p>
                <a href="/restaurantes" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explorar Restaurantes
                </a>
            </div>
        @endif
    </div>
</div>
