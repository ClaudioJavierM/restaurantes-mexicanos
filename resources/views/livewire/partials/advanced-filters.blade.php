@if($showAdvancedFilters)
    <div class="mt-4 pt-4 border-t border-gray-200 space-y-6 bg-gradient-to-br from-emerald-50 to-red-50 p-6 rounded-lg">
        <!-- Title -->
        <div class="flex items-center space-x-2">
            <span class="text-lg font-bold text-gray-900">Filtros Avanzados 🇲🇽</span>
        </div>

        <!-- Row 1: Business Type & Price -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Business Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    🏪 Tipo de Negocio
                </label>
                <select
                    wire:model.live="selectedBusinessType"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-white text-gray-900"
                >
                    <option value="">Todos los tipos</option>
                    @foreach($businessTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    💰 Rango de Precios
                </label>
                <div class="flex space-x-2">
                    @foreach(['$', '$$', '$$$', '$$$$'] as $range)
                        <button
                            type="button"
                            wire:click="$set('selectedPriceRange', '{{ $range }}')"
                            class="flex-1 px-3 py-3 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 {{ $selectedPriceRange === $range ? 'bg-emerald-600 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300' }}"
                        >
                            {{ $range }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Row 2: Food Tags -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                🍽️ Tipo de Comida
            </label>
            <div class="flex flex-wrap gap-2">
                @foreach($foodTags as $tag)
                    <button
                        type="button"
                        wire:click="toggleFoodTag({{ $tag->id }})"
                        class="px-3 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ in_array($tag->id, $selectedFoodTags) ? 'bg-red-600 text-white shadow-md' : 'bg-white text-gray-700 border border-gray-300 hover:border-red-400' }}"
                    >
                        {{ $tag->icon }} {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Row 3: Features by Category -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                ✨ Características
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $featuresByCategory = $features->groupBy('category');
                    $categoryNames = [
                        'service' => '🚗 Servicio',
                        'ambiance' => '🎭 Ambiente',
                        'ideal_for' => '👥 Ideal Para',
                        'facilities' => '🏠 Facilidades',
                        'dietary' => '🥗 Dietético',
                        'reservations' => '📅 Reservaciones',
                    ];
                @endphp
                
                @foreach($featuresByCategory as $category => $categoryFeatures)
                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                        <p class="text-xs font-bold text-gray-600 mb-2">{{ $categoryNames[$category] ?? $category }}</p>
                        <div class="space-y-1">
                            @foreach($categoryFeatures->take(4) as $feature)
                                <label class="flex items-center cursor-pointer hover:bg-gray-50 p-1 rounded text-xs">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedFeatures"
                                        value="{{ $feature->slug }}"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-3 h-3"
                                    >
                                    <span class="ml-2 text-gray-700">{{ $feature->icon }} {{ $feature->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Row 4: Spice Level & Region -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Spice Level -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    🌶️ Nivel de Picante
                </label>
                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-300">
                    @for($i = 1; $i <= 5; $i++)
                        <button
                            type="button"
                            wire:click="toggleSpiceLevel({{ $i }})"
                            class="text-2xl transition-all duration-200 transform hover:scale-125 {{ in_array($i, $selectedSpiceLevel) ? 'scale-125 opacity-100 drop-shadow-lg' : 'opacity-30 hover:opacity-60 grayscale' }}"
                            title="Nivel {{ $i }}"
                        >
                            🌶️
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Mexican Region -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    🇲🇽 Región Mexicana
                </label>
                <select
                    wire:model.live="selectedRegion"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 bg-white text-gray-900"
                >
                    <option value="">Todas las regiones</option>
                    @foreach(\App\Models\Restaurant::getMexicanRegions() as $key => $region)
                        <option value="{{ $key }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Authenticity Toggle -->
        <div class="flex items-center space-x-3 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <input
                type="checkbox"
                wire:model.live="authenticOnly"
                id="authenticOnly"
                class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-5 h-5"
            >
            <label for="authenticOnly" class="text-sm font-semibold text-gray-900 cursor-pointer">
                👨‍🍳 Solo Restaurantes Auténticos (Chef certificado, recetas tradicionales o ingredientes importados)
            </label>
        </div>

        <!-- Specialty Filters: Bebidas y Tortillas -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                🫓 Bebidas y Tortillas
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center space-x-2 bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <input type="checkbox" wire:model.live="hasCafeDeOlla" id="hasCafeDeOlla" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasCafeDeOlla" class="text-sm font-medium text-gray-900 cursor-pointer">☕ Café de Olla</label>
                </div>
                <div class="flex items-center space-x-2 bg-cyan-50 p-3 rounded-lg border border-cyan-200">
                    <input type="checkbox" wire:model.live="hasAguasFrescas" id="hasAguasFrescas" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 w-4 h-4">
                    <label for="hasAguasFrescas" class="text-sm font-medium text-gray-900 cursor-pointer">🥤 Aguas Frescas</label>
                </div>
                <div class="flex items-center space-x-2 bg-orange-50 p-3 rounded-lg border border-orange-200">
                    <input type="checkbox" wire:model.live="hasFreshTortillas" id="hasFreshTortillas" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4">
                    <label for="hasFreshTortillas" class="text-sm font-medium text-gray-900 cursor-pointer">🫓 Tortillas Frescas</label>
                </div>
                <div class="flex items-center space-x-2 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <input type="checkbox" wire:model.live="hasHandmadeTortillas" id="hasHandmadeTortillas" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4">
                    <label for="hasHandmadeTortillas" class="text-sm font-medium text-gray-900 cursor-pointer">👐 Hechas a Mano</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Platillos Tradicionales -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                🍖 Platillos Tradicionales
            </label>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="flex items-center space-x-2 bg-red-50 p-3 rounded-lg border border-red-200">
                    <input type="checkbox" wire:model.live="hasBirria" id="hasBirria" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasBirria" class="text-sm font-medium text-gray-900 cursor-pointer">🥘 Birria</label>
                </div>
                <div class="flex items-center space-x-2 bg-orange-50 p-3 rounded-lg border border-orange-200">
                    <input type="checkbox" wire:model.live="hasCarnitas" id="hasCarnitas" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4">
                    <label for="hasCarnitas" class="text-sm font-medium text-gray-900 cursor-pointer">🐷 Carnitas</label>
                </div>
                <div class="flex items-center space-x-2 bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <input type="checkbox" wire:model.live="hasBarbacoa" id="hasBarbacoa" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasBarbacoa" class="text-sm font-medium text-gray-900 cursor-pointer">🍖 Barbacoa</label>
                </div>
                <div class="flex items-center space-x-2 bg-rose-50 p-3 rounded-lg border border-rose-200">
                    <input type="checkbox" wire:model.live="hasPozoleMenudo" id="hasPozoleMenudo" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 w-4 h-4">
                    <label for="hasPozoleMenudo" class="text-sm font-medium text-gray-900 cursor-pointer">🍲 Pozole/Menudo</label>
                </div>
                <div class="flex items-center space-x-2 bg-green-50 p-3 rounded-lg border border-green-200">
                    <input type="checkbox" wire:model.live="hasTamales" id="hasTamales" class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4">
                    <label for="hasTamales" class="text-sm font-medium text-gray-900 cursor-pointer">🫔 Tamales</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Preparaciones y Métodos -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                👨‍🍳 Preparaciones y Métodos Tradicionales
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center space-x-2 bg-red-50 p-3 rounded-lg border border-red-200">
                    <input type="checkbox" wire:model.live="hasHomemadeSalsa" id="hasHomemadeSalsa" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasHomemadeSalsa" class="text-sm font-medium text-gray-900 cursor-pointer">🌶️ Salsas Caseras</label>
                </div>
                <div class="flex items-center space-x-2 bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <input type="checkbox" wire:model.live="hasHomemadeMole" id="hasHomemadeMole" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasHomemadeMole" class="text-sm font-medium text-gray-900 cursor-pointer">🫕 Mole Casero</label>
                </div>
                <div class="flex items-center space-x-2 bg-gray-100 p-3 rounded-lg border border-gray-300">
                    <input type="checkbox" wire:model.live="hasCharcoalGrill" id="hasCharcoalGrill" class="rounded border-gray-300 text-gray-600 focus:ring-gray-500 w-4 h-4">
                    <label for="hasCharcoalGrill" class="text-sm font-medium text-gray-900 cursor-pointer">🔥 Al Carbón</label>
                </div>
                <div class="flex items-center space-x-2 bg-stone-100 p-3 rounded-lg border border-stone-300">
                    <input type="checkbox" wire:model.live="hasComal" id="hasComal" class="rounded border-gray-300 text-stone-600 focus:ring-stone-500 w-4 h-4">
                    <label for="hasComal" class="text-sm font-medium text-gray-900 cursor-pointer">🫓 Comal</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Panadería, Bebidas y Extras -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                🍞 Panadería, Bebidas y Extras
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="flex items-center space-x-2 bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <input type="checkbox" wire:model.live="hasPanDulce" id="hasPanDulce" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasPanDulce" class="text-sm font-medium text-gray-900 cursor-pointer">🍞 Pan Dulce</label>
                </div>
                <div class="flex items-center space-x-2 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <input type="checkbox" wire:model.live="hasChurros" id="hasChurros" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4">
                    <label for="hasChurros" class="text-sm font-medium text-gray-900 cursor-pointer">🥖 Churros</label>
                </div>
                <div class="flex items-center space-x-2 bg-blue-50 p-3 rounded-lg border border-blue-200">
                    <input type="checkbox" wire:model.live="hasMezcalTequila" id="hasMezcalTequila" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <label for="hasMezcalTequila" class="text-sm font-medium text-gray-900 cursor-pointer">🥃 Mezcal/Tequila</label>
                </div>
                <div class="flex items-center space-x-2 bg-red-50 p-3 rounded-lg border border-red-200">
                    <input type="checkbox" wire:model.live="hasMicheladas" id="hasMicheladas" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasMicheladas" class="text-sm font-medium text-gray-900 cursor-pointer">🍺 Micheladas</label>
                </div>
                <div class="flex items-center space-x-2 bg-pink-50 p-3 rounded-lg border border-pink-200">
                    <input type="checkbox" wire:model.live="hasMexicanCandy" id="hasMexicanCandy" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-4 h-4">
                    <label for="hasMexicanCandy" class="text-sm font-medium text-gray-900 cursor-pointer">🍬 Dulces Mex</label>
                </div>
                <div class="flex items-center space-x-2 bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                    <input type="checkbox" wire:model.live="hasImportedProducts" id="hasImportedProducts" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <label for="hasImportedProducts" class="text-sm font-medium text-gray-900 cursor-pointer">📦 Importados</label>
                </div>
            </div>
        </div>

        <!-- Active Filters Summary -->
        @php
            $activeFiltersCount = 0;
            if($selectedBusinessType) $activeFiltersCount++;
            if($selectedPriceRange) $activeFiltersCount++;
            if(!empty($selectedSpiceLevel)) $activeFiltersCount++;
            if($selectedRegion) $activeFiltersCount++;
            if(!empty($selectedFoodTags)) $activeFiltersCount += count($selectedFoodTags);
            if(!empty($selectedFeatures)) $activeFiltersCount += count($selectedFeatures);
            if($authenticOnly) $activeFiltersCount++;
            // Bebidas y Tortillas
            if($hasCafeDeOlla) $activeFiltersCount++;
            if($hasAguasFrescas) $activeFiltersCount++;
            if($hasFreshTortillas) $activeFiltersCount++;
            if($hasHandmadeTortillas) $activeFiltersCount++;
            // Platillos Tradicionales
            if($hasBirria) $activeFiltersCount++;
            if($hasCarnitas) $activeFiltersCount++;
            if($hasBarbacoa) $activeFiltersCount++;
            if($hasPozoleMenudo) $activeFiltersCount++;
            if($hasTamales) $activeFiltersCount++;
            // Preparaciones y Métodos
            if($hasHomemadeSalsa) $activeFiltersCount++;
            if($hasHomemadeMole) $activeFiltersCount++;
            if($hasCharcoalGrill) $activeFiltersCount++;
            if($hasComal) $activeFiltersCount++;
            // Panadería, Bebidas y Extras
            if($hasPanDulce) $activeFiltersCount++;
            if($hasChurros) $activeFiltersCount++;
            if($hasMezcalTequila) $activeFiltersCount++;
            if($hasMicheladas) $activeFiltersCount++;
            if($hasMexicanCandy) $activeFiltersCount++;
            if($hasImportedProducts) $activeFiltersCount++;
        @endphp

        @if($activeFiltersCount > 0)
            <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-900">
                        {{ $activeFiltersCount }} filtro(s) activo(s)
                    </span>
                </div>
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700 underline"
                >
                    Limpiar todos
                </button>
            </div>
        @endif
    </div>
@endif
