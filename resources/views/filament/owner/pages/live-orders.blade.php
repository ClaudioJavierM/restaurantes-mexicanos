<x-filament-panels::page>
    <div 
        x-data="liveOrdersDashboard()"
        x-init="initWebSocket()"
        class="space-y-6"
    >
        <!-- Audio para notificación -->
        <audio id="notification-sound" preload="auto">
            <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
        </audio>

        <!-- Header con contador de nuevos pedidos -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    📋 Panel de Pedidos en Vivo
                </h2>
                <template x-if="newOrdersCount > 0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 animate-pulse">
                        🔔 <span x-text="newOrdersCount"></span> nuevo(s)
                    </span>
                </template>
            </div>
            <button 
                wire:click="loadOrders"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualizar
            </button>
        </div>

        <!-- Grid de columnas por estado -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Columna: Pendientes -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">⏳</span>
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Pendientes</h3>
                    <span class="ml-auto bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm font-bold">
                        {{ count(array_filter($orders, fn($o) => $o['status'] === 'pending')) }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($orders as $order)
                        @if($order['status'] === 'pending')
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md border-l-4 border-yellow-400 animate-pulse-slow">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">#{{ $order['order_number'] }}</span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    <p><strong>{{ $order['customer_name'] }}</strong></p>
                                    <p>{{ $order['order_type'] === 'pickup' ? '🚶 Para llevar' : ($order['order_type'] === 'delivery' ? '🚗 Delivery' : '🍽️ En restaurante') }}</p>
                                </div>
                                <div class="text-sm mb-3">
                                    @foreach($order['items'] ?? [] as $item)
                                        <p>• {{ $item['quantity'] }}x {{ $item['name'] }}</p>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-600">${{ number_format($order['total'], 2) }}</span>
                                    <button 
                                        wire:click="updateOrderStatus({{ $order['id'] }}, 'confirmed')"
                                        class="px-3 py-1 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition"
                                    >
                                        ✓ Confirmar
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Columna: Confirmados -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">✅</span>
                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Confirmados</h3>
                    <span class="ml-auto bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-sm font-bold">
                        {{ count(array_filter($orders, fn($o) => $o['status'] === 'confirmed')) }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($orders as $order)
                        @if($order['status'] === 'confirmed')
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md border-l-4 border-blue-400">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">#{{ $order['order_number'] }}</span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    <p><strong>{{ $order['customer_name'] }}</strong></p>
                                    <p>{{ $order['order_type'] === 'pickup' ? '🚶 Para llevar' : ($order['order_type'] === 'delivery' ? '🚗 Delivery' : '🍽️ En restaurante') }}</p>
                                </div>
                                <div class="text-sm mb-3">
                                    @foreach($order['items'] ?? [] as $item)
                                        <p>• {{ $item['quantity'] }}x {{ $item['name'] }}</p>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-600">${{ number_format($order['total'], 2) }}</span>
                                    <button 
                                        wire:click="updateOrderStatus({{ $order['id'] }}, 'preparing')"
                                        class="px-3 py-1 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600 transition"
                                    >
                                        🍳 Preparar
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Columna: En Preparación -->
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">🍳</span>
                    <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-200">Preparando</h3>
                    <span class="ml-auto bg-orange-200 text-orange-800 px-2 py-1 rounded-full text-sm font-bold">
                        {{ count(array_filter($orders, fn($o) => $o['status'] === 'preparing')) }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($orders as $order)
                        @if($order['status'] === 'preparing')
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md border-l-4 border-orange-400">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">#{{ $order['order_number'] }}</span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    <p><strong>{{ $order['customer_name'] }}</strong></p>
                                    <p>{{ $order['order_type'] === 'pickup' ? '🚶 Para llevar' : ($order['order_type'] === 'delivery' ? '🚗 Delivery' : '🍽️ En restaurante') }}</p>
                                </div>
                                <div class="text-sm mb-3">
                                    @foreach($order['items'] ?? [] as $item)
                                        <p>• {{ $item['quantity'] }}x {{ $item['name'] }}</p>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-600">${{ number_format($order['total'], 2) }}</span>
                                    <button 
                                        wire:click="updateOrderStatus({{ $order['id'] }}, 'ready')"
                                        class="px-3 py-1 bg-emerald-500 text-white rounded-lg text-sm hover:bg-emerald-600 transition"
                                    >
                                        ✨ Listo
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Columna: Listos -->
            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">✨</span>
                    <h3 class="text-lg font-semibold text-emerald-800 dark:text-emerald-200">Listos</h3>
                    <span class="ml-auto bg-emerald-200 text-emerald-800 px-2 py-1 rounded-full text-sm font-bold">
                        {{ count(array_filter($orders, fn($o) => $o['status'] === 'ready')) }}
                    </span>
                </div>
                <div class="space-y-3">
                    @foreach($orders as $order)
                        @if($order['status'] === 'ready')
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md border-l-4 border-emerald-400">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold text-lg">#{{ $order['order_number'] }}</span>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                    <p><strong>{{ $order['customer_name'] }}</strong></p>
                                    <p>{{ $order['order_type'] === 'pickup' ? '🚶 Para llevar' : ($order['order_type'] === 'delivery' ? '🚗 Delivery' : '🍽️ En restaurante') }}</p>
                                    @if($order['customer_phone'])
                                        <p>📞 {{ $order['customer_phone'] }}</p>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-green-600">${{ number_format($order['total'], 2) }}</span>
                                    <button 
                                        wire:click="updateOrderStatus({{ $order['id'] }}, 'completed')"
                                        class="px-3 py-1 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition"
                                    >
                                        📦 Entregado
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Mensaje si no hay pedidos -->
        @if(empty($orders))
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <span class="text-6xl">🍽️</span>
                <h3 class="mt-4 text-xl font-semibold text-gray-700 dark:text-gray-300">No hay pedidos activos</h3>
                <p class="mt-2 text-gray-500">Los nuevos pedidos aparecerán aquí automáticamente</p>
            </div>
        @endif
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
    </style>

    @push('scripts')
    <script>
        function liveOrdersDashboard() {
            return {
                newOrdersCount: @entangle('newOrdersCount'),
                
                initWebSocket() {
                    const restaurantId = {{ $restaurantId ?? 0 }};
                    
                    if (restaurantId && window.Echo) {
                        console.log('🔌 Conectando a canal: restaurant.' + restaurantId);
                        
                        window.Echo.private('restaurant.' + restaurantId)
                            .listen('NewOrderEvent', (e) => {
                                console.log('🔔 Nuevo pedido recibido:', e);
                                this.playNotificationSound();
                                @this.call('handleNewOrder', e);
                            })
                            .listen('OrderStatusUpdatedEvent', (e) => {
                                console.log('📝 Estado actualizado:', e);
                                @this.call('loadOrders');
                            });
                    }
                },
                
                playNotificationSound() {
                    const audio = document.getElementById('notification-sound');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play().catch(err => console.log('Audio blocked:', err));
                    }
                }
            }
        }

        // Listener para sonido de notificación desde Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('play-notification-sound', () => {
                const audio = document.getElementById('notification-sound');
                if (audio) {
                    audio.currentTime = 0;
                    audio.play().catch(err => console.log('Audio blocked:', err));
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
