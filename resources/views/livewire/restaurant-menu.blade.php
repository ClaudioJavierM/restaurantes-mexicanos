<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-700 to-orange-600 text-white">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="flex items-center gap-4">
                @if($restaurant->logo)
                    <img src="{{ Storage::url($restaurant->logo) }}" 
                         alt="{{ $restaurant->name }}" 
                         class="w-20 h-20 rounded-xl object-cover bg-white p-1">
                @else
                    <div class="w-20 h-20 rounded-xl bg-white/20 flex items-center justify-center text-4xl">
                        🍽️
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold">{{ $restaurant->name }}</h1>
                    <p class="text-white/80 text-sm mt-1">{{ $restaurant->address }}</p>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                        @if($restaurant->phone)
                            <a href="tel:{{ $restaurant->phone }}" class="hover:underline">📞 {{ $restaurant->phone }}</a>
                        @endif
                        @if($restaurant->average_rating)
                            <span>⭐ {{ number_format($restaurant->average_rating, 1) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Tabs -->
    @if($categories->count() > 0)
        <div class="sticky top-0 z-10 bg-white border-b shadow-sm">
            <div class="max-w-4xl mx-auto px-4">
                <div class="flex overflow-x-auto gap-1 py-2 scrollbar-hide">
                    @foreach($categories as $category)
                        <button 
                            wire:click="setCategory({{ $category->id }})"
                            class="flex-shrink-0 px-4 py-2 rounded-full font-medium text-sm transition
                                {{ $activeCategory === $category->id 
                                    ? 'bg-red-600 text-white' 
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            {{ $category->icon }} {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Menu Content -->
    <div class="max-w-4xl mx-auto px-4 py-6">
        @if($categories->count() > 0)
            @foreach($categories as $category)
                <div 
                    id="category-{{ $category->id }}"
                    class="mb-8 {{ $activeCategory && $activeCategory !== $category->id ? 'hidden' : '' }}"
                    wire:key="category-{{ $category->id }}"
                >
                    <!-- Category Header -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">{{ $category->icon ?? '🍽️' }}</span>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-gray-500 text-sm">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <div class="space-y-3">
                        @foreach($category->items as $item)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                                <div class="flex">
                                    <!-- Item Image -->
                                    @if($item->image)
                                        <div class="w-28 h-28 flex-shrink-0">
                                            <img src="{{ Storage::url($item->image) }}" 
                                                 alt="{{ $item->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    
                                    <!-- Item Details -->
                                    <div class="flex-1 p-4">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                                    {{ $item->name }}
                                                    @if($item->is_popular)
                                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full">⭐ Popular</span>
                                                    @endif
                                                    @if($item->is_new)
                                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full">Nuevo</span>
                                                    @endif
                                                </h3>
                                                @if($item->description)
                                                    <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ $item->description }}</p>
                                                @endif
                                                
                                                <!-- Dietary Tags -->
                                                @if($item->dietary_tags && count($item->dietary_tags) > 0)
                                                    <div class="flex gap-1 mt-2">
                                                        @foreach($item->dietary_tags as $tag)
                                                            <span class="text-xs px-2 py-0.5 bg-gray-100 rounded-full text-gray-600">
                                                                @switch($tag)
                                                                    @case('vegetarian')
                                                                        🥬 Vegetariano
                                                                        @break
                                                                    @case('vegan')
                                                                        🌱 Vegano
                                                                        @break
                                                                    @case('gluten-free')
                                                                        🌾 Sin Gluten
                                                                        @break
                                                                    @case('spicy')
                                                                        🌶️ Picante
                                                                        @break
                                                                    @default
                                                                        {{ $tag }}
                                                                @endswitch
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Price -->
                                            <div class="text-right ml-4">
                                                @if($item->hasDiscount())
                                                    <p class="text-gray-400 line-through text-sm">${{ number_format($item->price, 2) }}</p>
                                                    <p class="text-red-600 font-bold text-lg">${{ number_format($item->sale_price, 2) }}</p>
                                                @else
                                                    <p class="text-gray-900 font-bold text-lg">${{ number_format($item->price, 2) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <!-- No Menu Message -->
            <div class="text-center py-12">
                <div class="text-5xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-900">Menú próximamente</h3>
                <p class="text-gray-500 mt-2">Este restaurante aún no ha publicado su menú.</p>
                <a href="{{ route('restaurants.show', $restaurant->slug) }}" 
                   class="inline-block mt-4 text-red-600 hover:underline">
                    ← Volver al restaurante
                </a>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="bg-white border-t py-6">
        <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
            <a href="{{ route('restaurants.show', $restaurant->slug) }}" 
               class="text-gray-600 hover:text-red-600 transition flex items-center gap-2">
                ← Volver al restaurante
            </a>
            
            <button 
                wire:click="toggleQrModal"
                class="flex items-center gap-2 text-gray-600 hover:text-red-600 transition"
            >
                📱 Compartir Menú
            </button>
        </div>
    </div>

    <!-- QR Modal -->
    @if($showQrModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="toggleQrModal">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full text-center">
                <h3 class="text-lg font-bold mb-4">📱 Comparte este menú</h3>
                <div class="bg-white p-4 rounded-xl inline-block border">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(request()->url()) }}" 
                         alt="QR Code" 
                         class="w-48 h-48">
                </div>
                <p class="text-gray-500 text-sm mt-4">
                    Escanea este código QR para ver el menú
                </p>
                <button 
                    wire:click="toggleQrModal"
                    class="mt-4 px-6 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                >
                    Cerrar
                </button>
            </div>
        </div>
    @endif
</div>
