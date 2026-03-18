<div class="min-h-screen bg-gray-50">
    {{-- Header --}}
    <div class="bg-gray-900 text-white px-4 py-3 sticky top-0 z-30 shadow-md">
        <div class="max-w-lg mx-auto flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400">{{ $restaurant->name }}</p>
                <h1 class="font-bold text-sm">{{ $table->name }}</h1>
            </div>
            <div class="flex items-center gap-3">
                @if($cartCount > 0)
                <div class="flex items-center gap-1.5 bg-red-600 rounded-full px-3 py-1">
                    <span class="text-xs font-bold">🛒 {{ $cartCount }}</span>
                    <span class="text-xs">${{ number_format($cartTotal, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($orderPlaced)
    {{-- Order Confirmation --}}
    <div class="max-w-lg mx-auto p-4">
        <div class="bg-white rounded-2xl shadow-lg p-6 text-center mt-6">
            <div class="text-5xl mb-4">✅</div>
            <h2 class="text-xl font-bold text-gray-900 mb-1">¡Orden Enviada!</h2>
            <p class="text-gray-500 text-sm mb-3">Tu orden ha sido recibida por la cocina</p>
            <div class="bg-gray-100 rounded-xl p-3 mb-4">
                <p class="text-xs text-gray-500">Número de orden</p>
                <p class="text-2xl font-bold text-gray-900">{{ $orderNumber }}</p>
            </div>
            <p class="text-xs text-gray-400">El mesero vendrá a confirmar tu pedido</p>
            <button wire:click="$set('orderPlaced', false)"
                class="mt-4 w-full bg-gray-900 text-white font-semibold py-2.5 rounded-xl text-sm">
                Hacer otro pedido
            </button>
        </div>
    </div>
    @else

    {{-- Category tabs --}}
    @if($categories->count() > 1)
    <div class="sticky top-[52px] z-20 bg-white border-b border-gray-200 overflow-x-auto">
        <div class="flex gap-1 px-4 py-2 whitespace-nowrap">
            <button wire:click="$set('activeCategory', 'all')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $activeCategory === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600' }}">
                Todo
            </button>
            @foreach($categories as $cat)
            <button wire:click="$set('activeCategory', '{{ $cat->id }}')"
                class="px-3 py-1.5 rounded-full text-xs font-semibold transition {{ $activeCategory == $cat->id ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600' }}">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Menu Items --}}
    <div class="max-w-lg mx-auto p-4 space-y-3 pb-40">
        @forelse($menuItems as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 flex items-center gap-3">
            @if($item->image)
            <img src="{{ $item->image }}" alt="{{ $item->name }}"
                class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
            @else
            <div class="w-16 h-16 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 text-2xl">🍽️</div>
            @endif
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $item->name }}</h3>
                @if($item->description)
                <p class="text-xs text-gray-500 truncate">{{ Str::limit($item->description, 50) }}</p>
                @endif
                <p class="text-red-600 font-bold text-sm mt-0.5">${{ number_format($item->price, 2) }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @if(isset($cart[$item->id]))
                <button wire:click="removeItem({{ $item->id }})"
                    class="w-7 h-7 rounded-full bg-gray-200 text-gray-700 font-bold text-sm flex items-center justify-center">−</button>
                <span class="font-bold text-sm w-5 text-center">{{ $cart[$item->id]['quantity'] }}</span>
                @endif
                <button wire:click="addItem({{ $item->id }})"
                    class="w-7 h-7 rounded-full bg-red-600 text-white font-bold text-sm flex items-center justify-center">+</button>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">
            <p class="text-3xl mb-2">🍽️</p>
            <p class="text-sm">No hay platillos disponibles</p>
        </div>
        @endforelse
    </div>

    {{-- Order summary footer --}}
    @if($cartCount > 0)
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-xl p-4 z-30">
        <div class="max-w-lg mx-auto">
            @if($error)
            <p class="text-red-500 text-xs mb-2">{{ $error }}</p>
            @endif
            <div class="flex items-center gap-3 mb-3">
                <input wire:model="customerName" type="text"
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                    placeholder="Tu nombre (opcional)">
            </div>
            <button wire:click="placeOrder"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="placeOrder">Enviar Orden · ${{ number_format($cartTotal, 2) }}</span>
                <span wire:loading wire:target="placeOrder">Enviando...</span>
            </button>
        </div>
    </div>
    @endif
    @endif
</div>
