<div>
    <!-- Cart Button (floating) -->
    @if($itemCount > 0)
    <button wire:click="open" 
            class="fixed bottom-6 right-6 z-40 bg-red-600 text-white rounded-full p-4 shadow-2xl hover:bg-red-700 transition-all transform hover:scale-110">
        <div class="relative">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="absolute -top-2 -right-2 bg-yellow-400 text-red-800 text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">
                {{ $itemCount }}
            </span>
        </div>
    </button>
    @endif

    <!-- Cart Sidebar -->
    <div x-data="{ open: @entangle('isOpen') }" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50"
         style="display: none;">
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
        
        <!-- Sidebar -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">
            
            <!-- Header -->
            <div class="bg-red-600 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold">Tu Pedido</h2>
                </div>
                <button wire:click="close" class="p-2 hover:bg-red-700 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if($restaurant)
            <div class="bg-red-50 px-6 py-3 border-b">
                <p class="text-sm text-red-800 font-medium">📍 {{ $restaurant->name }}</p>
            </div>
            @endif
            
            <!-- Items -->
            <div class="flex-1 overflow-y-auto p-6">
                @if(count($items) > 0)
                    <div class="space-y-4">
                        @foreach($items as $key => $item)
                        <div class="bg-gray-50 rounded-xl p-4 relative">
                            <button wire:click="remove('{{ $key }}')" 
                                    class="absolute top-2 right-2 text-gray-400 hover:text-red-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $item['name'] }}</h4>
                                    
                                    @if(!empty($item['modifiers']))
                                    <p class="text-xs text-gray-500 mt-1">
                                        @foreach($item['modifiers'] as $mod)
                                            {{ $mod['name'] }}@if(!$loop->last), @endif
                                        @endforeach
                                    </p>
                                    @endif
                                    
                                    @if(!empty($item['special_instructions']))
                                    <p class="text-xs text-orange-600 mt-1 italic">
                                        📝 {{ $item['special_instructions'] }}
                                    </p>
                                    @endif
                                    
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center gap-2">
                                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                                    class="w-8 h-8 bg-white border border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="font-bold text-lg w-8 text-center">{{ $item['quantity'] }}</span>
                                            <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                                    class="w-8 h-8 bg-white border border-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <span class="font-bold text-red-600">${{ number_format($item['total_price'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-500">Tu carrito está vacío</p>
                        <p class="text-sm text-gray-400 mt-2">Agrega platillos deliciosos</p>
                    </div>
                @endif
            </div>
            
            <!-- Footer -->
            @if(count($items) > 0)
            <div class="border-t bg-gray-50 p-6">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Impuestos y propina se calculan en checkout</span>
                    </div>
                </div>
                
                <button wire:click="checkout"
                        class="w-full bg-red-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-red-700 transition flex items-center justify-center gap-2">
                    <span>Continuar al Pago</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </button>
                
                <button wire:click="clear"
                        class="w-full mt-3 text-gray-500 hover:text-red-600 text-sm transition">
                    Vaciar carrito
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
