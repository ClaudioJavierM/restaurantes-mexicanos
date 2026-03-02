<div class="space-y-6">
    <!-- Popular Dishes Section -->
    @if($popularMenuItems->count() > 0)
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-lg p-6">
        <div class="flex items-center mb-6">
            <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900">Platillos Populares</h3>
            <span class="ml-auto text-sm text-yellow-600 font-medium">Los más pedidos</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($popularMenuItems as $item)
            <div role="button" tabindex="0"
                wire:click="showMenuItem({{ $item->id }})"
                class="bg-white rounded-lg p-4 shadow-md hover:shadow-xl transition-shadow text-left group cursor-pointer"
            >
                <div class="menu-image-container" style="position: relative !important; height: 160px !important; max-height: 160px !important; min-height: 160px !important; margin-bottom: 0.75rem !important; border-radius: 0.5rem !important; overflow: hidden !important; background-color: #E5E7EB !important; display: block !important; contain: layout !important;">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        @include("livewire.partials.menu-item-placeholder", ["height" => "h-40"])
                    @endif
                </div>

                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-bold text-gray-900 group-hover:text-red-600 transition-colors flex-1">{{ $item->name }}</h4>
                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>

                @if($item->description)
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->description }}</p>
                @endif

                <div class="flex items-center justify-between mt-2">
                    @if($item->price)
                    <span class="text-lg font-bold text-red-600">${{ number_format($item->price, 2) }}</span>
                    @endif

                    @if(in_array($restaurant->subscription_tier ?? 'free', ['premium', 'elite']))
                    <div role="button" tabindex="0" wire:click.stop="$dispatch('addToCart', { menuItemId: {{ $item->id }}, quantity: 1 })"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Menu Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Menú Completo</h3>

            @if($selectedCategory !== 'all' || !empty($selectedDietaryFilter) || $selectedSpiceFilter !== null)
            <div role="button" tabindex="0"
                wire:click="clearMenuFilters"
                class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar filtros
            </div>
            @endif
        </div>

        <!-- Category Pills -->
        @if($availableCategories->count() > 1)
        <div class="mb-6">
            <p class="text-sm font-medium text-gray-700 mb-3">Categorías:</p>
            <div class="flex flex-wrap gap-2">
                <div role="button" tabindex="0"
                    wire:click="filterByCategory('all')"
                    class="{{ $selectedCategory === 'all' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-full text-sm font-medium transition-colors"
                >
                    Todos
                </div>

                @foreach($availableCategories as $category)
                <div role="button" tabindex="0"
                    wire:click="filterByCategory({{ $category->id }})"
                    class="{{ $selectedCategory == $category->id ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-full text-sm font-medium transition-colors"
                >
                    {{ $category->name }}
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Dietary & Spice Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Dietary Options -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Opciones Dietéticas:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\MenuItem::getDietaryOptions() as $key => $label)
                    <div role="button" tabindex="0"
                        wire:click="toggleDietaryFilter('{{ $key }}')"
                        class="{{ in_array($key, $selectedDietaryFilter) ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-3 py-1.5 rounded-full text-xs font-medium transition-colors"
                    >
                        {{ $label }}
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Spice Level Filter -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Nivel de Picante:</p>
                <div class="flex flex-wrap gap-2">
                    @for($i = 0; $i <= 5; $i++)
                    <div role="button" tabindex="0"
                        wire:click="filterBySpice({{ $i }})"
                        class="{{ $selectedSpiceFilter === $i ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-3 py-1.5 rounded-full text-xs font-medium transition-colors"
                    >
                        @if($i === 0)
                            Sin picante
                        @else
                            {{ str_repeat('🌶️', $i) }}
                        @endif
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items Grid -->
    @if($menuItems->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($menuItems as $item)
            <div role="button" tabindex="0"
                wire:click="showMenuItem({{ $item->id }})"
                class="bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-red-500 hover:shadow-lg transition-all text-left group"
            >
                    <div class="menu-image-container" style="position: relative !important; height: 128px !important; max-height: 128px !important; min-height: 128px !important; margin-bottom: 0.75rem !important; border-radius: 0.5rem !important; overflow: hidden !important; background-color: #E5E7EB !important; display: block !important; contain: layout !important;">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        @include("livewire.partials.menu-item-placeholder", ["height" => "h-32"])
                    @endif
                </div>

                <h4 class="font-bold text-gray-900 group-hover:text-red-600 transition-colors mb-1">{{ $item->name }}</h4>

                @if($item->category)
                <span class="inline-block text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded mb-2">
                    {{ $item->category->name ?? "" }}
                </span>
                @endif

                @if($item->description)
                <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $item->description }}</p>
                @endif

                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    @if($item->price)
                    <span class="text-base font-bold text-red-600">${{ number_format($item->price, 2) }}</span>
                    @endif

                    @if(in_array($restaurant->subscription_tier ?? 'free', ['premium', 'elite']))
                    <div role="button" tabindex="0" wire:click.stop="$dispatch('addToCart', { menuItemId: {{ $item->id }}, quantity: 1 })"
                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-2 py-1 rounded-lg transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agregar
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-500 text-lg">No se encontraron platillos con los filtros seleccionados.</p>
        <div role="button" tabindex="0"
            wire:click="clearMenuFilters"
            class="mt-4 text-red-600 hover:text-red-700 font-medium"
        >
            Limpiar filtros
        </div>
    </div>
    @endif
</div>
