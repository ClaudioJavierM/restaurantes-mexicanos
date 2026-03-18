<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">🛍️ Mis Pedidos</h1>
            <p class="mt-2 text-gray-600">Historial de tus pedidos en restaurantes mexicanos</p>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-6 mb-8">
                @foreach($orders as $order)
                    @php
                        // Steps for delivery vs pickup/dine-in
                        $isDelivery = $order->order_type === 'delivery';
                        $steps = $isDelivery
                            ? ['pending','confirmed','preparing','ready','out_for_delivery','completed']
                            : ['pending','confirmed','preparing','ready','completed'];

                        $statusLabels = [
                            'pending'          => 'Recibido',
                            'confirmed'        => 'Confirmado',
                            'preparing'        => 'Preparando',
                            'ready'            => 'Listo',
                            'out_for_delivery' => 'En camino',
                            'completed'        => 'Entregado',
                            'cancelled'        => 'Cancelado',
                        ];

                        $currentIndex = array_search($order->status, $steps);
                        $isCancelled  = $order->status === 'cancelled';
                    @endphp

                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Card header: restaurant + date + total -->
                        <div class="flex items-center gap-4 p-4 border-b border-gray-100">
                            <!-- Restaurant image thumbnail -->
                            @if($order->restaurant && $order->restaurant->image)
                                <img src="{{ Storage::url($order->restaurant->image) }}"
                                     alt="{{ $order->restaurant->name }}"
                                     class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-lg bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0">
                                    <span class="text-2xl">🌮</span>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <a href="{{ $order->restaurant ? route('restaurants.show', $order->restaurant->slug) : '#' }}"
                                   class="font-bold text-gray-900 hover:text-red-600 transition-colors text-base truncate block">
                                    {{ $order->restaurant->name ?? 'Restaurante' }}
                                </a>
                                <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                    <span class="font-mono">{{ $order->order_number }}</span>
                                    <span>·</span>
                                    <span>{{ $order->order_type_label }}</span>
                                    <span>·</span>
                                    <span>{{ $order->created_at->format('d M Y') }}</span>
                                </div>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="text-lg font-bold text-gray-900">${{ number_format($order->total, 2) }}</p>
                                @if($isCancelled)
                                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">❌ Cancelado</span>
                                @endif
                            </div>
                        </div>

                        <!-- Timeline de status -->
                        @if(!$isCancelled)
                            <div class="px-4 py-4">
                                <div class="flex items-center justify-between relative">
                                    <!-- Connecting line behind -->
                                    <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-0"></div>
                                    @if($currentIndex !== false && $currentIndex > 0)
                                        <div class="absolute top-4 left-4 h-0.5 bg-green-500 z-0 transition-all duration-500"
                                             style="width: calc({{ ($currentIndex / (count($steps) - 1)) * 100 }}% - 2rem)"></div>
                                    @endif

                                    @foreach($steps as $i => $step)
                                        @php
                                            $done    = $currentIndex !== false && $i <= $currentIndex;
                                            $current = $currentIndex !== false && $i === $currentIndex;
                                        @endphp
                                        <div class="flex flex-col items-center z-10 {{ $i === 0 ? 'items-start' : ($i === count($steps)-1 ? 'items-end' : 'items-center') }}">
                                            <!-- Circle -->
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all
                                                {{ $current  ? 'bg-green-500 border-green-500 ring-4 ring-green-100 scale-110' :
                                                   ($done     ? 'bg-green-500 border-green-500' :
                                                                'bg-white border-gray-300') }}">
                                                @if($done && !$current)
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @elseif($current)
                                                    <span class="w-2 h-2 rounded-full bg-white {{ $step !== 'completed' ? 'animate-pulse' : '' }}"></span>
                                                @endif
                                            </div>
                                            <!-- Label -->
                                            <p class="text-xs mt-1.5 font-medium whitespace-nowrap
                                                {{ $current ? 'text-green-600' : ($done ? 'text-gray-700' : 'text-gray-400') }}">
                                                {{ $statusLabels[$step] ?? $step }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Items + reorder -->
                        <div class="px-4 pb-4">
                            <!-- Toggle items -->
                            <button
                                wire:click="toggleOrder({{ $order->id }})"
                                class="w-full text-left text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 mb-2">
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
                                <div class="bg-gray-50 rounded-lg p-3 mb-3 space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between text-sm text-gray-700">
                                            <span>{{ $item->quantity }}× {{ $item->name }}</span>
                                            <span class="font-medium">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </div>
                                    @endforeach
                                    <div class="border-t border-gray-200 pt-1 mt-1 flex justify-between text-sm font-bold text-gray-900">
                                        <span>Total</span>
                                        <span>${{ number_format($order->total, 2) }}</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-gray-500 mb-3 line-clamp-1">
                                    {{ $order->items->take(3)->pluck('name')->join(', ') }}
                                    @if($order->items->count() > 3) y {{ $order->items->count() - 3 }} más @endif
                                </p>
                            @endif

                            <!-- Pedir de nuevo -->
                            @if($order->restaurant && !$isCancelled)
                                <a href="{{ route('restaurants.show', $order->restaurant->slug) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Pedir de nuevo
                                </a>
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
