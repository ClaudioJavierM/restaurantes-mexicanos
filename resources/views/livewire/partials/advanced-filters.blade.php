@if($showAdvancedFilters)
    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #2A2A2A; display:flex; flex-direction:column; gap:1.5rem; background:#111; padding:1.5rem; border-radius:0.5rem;">
        <!-- Title -->
        <div class="flex items-center space-x-2">
            <span class="text-lg font-bold" style="color:#F5F5F5;">Filtros Avanzados 🇲🇽</span>
        </div>

        <!-- Row 1: Business Type & Price -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Business Type -->
            <div>
                <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                    🏪 Tipo de Negocio
                </label>
                <select
                    wire:model.live="selectedBusinessType"
                    style="width:100%; border-radius:0.5rem; border:1px solid #2A2A2A; background:#1A1A1A; color:#F5F5F5; padding:0.5rem 0.75rem;"
                >
                    <option value="">Todos los tipos</option>
                    @foreach($businessTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                    💰 Rango de Precios
                </label>
                <div class="flex space-x-2">
                    @foreach(['$', '$$', '$$$', '$$$$'] as $range)
                        <button
                            type="button"
                            wire:click="$set('selectedPriceRange', '{{ $range }}')"
                            class="flex-1 px-3 py-3 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105"
                            style="{{ $selectedPriceRange === $range ? 'background:#D4AF37; color:#0B0B0B; font-weight:700;' : 'background:#2A2A2A; color:#9CA3AF; border:1px solid #3A3A3A;' }}"
                        >
                            {{ $range }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Row 2: Food Tags -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                🍽️ Tipo de Comida
            </label>
            <div class="flex flex-wrap gap-2">
                @foreach($foodTags as $tag)
                    <button
                        type="button"
                        wire:click="toggleFoodTag({{ $tag->id }})"
                        class="px-3 py-2 rounded-full text-sm font-medium transition-all duration-200"
                        style="{{ in_array($tag->id, $selectedFoodTags) ? 'background:#D4AF37; color:#0B0B0B;' : 'background:#2A2A2A; color:#9CA3AF; border:1px solid #3A3A3A;' }}"
                    >
                        {{ $tag->icon }} {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Row 3: Features by Category -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
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
                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; padding:0.75rem; border-radius:0.5rem;">
                        <p class="text-xs font-bold mb-2" style="color:#9CA3AF;">{{ $categoryNames[$category] ?? $category }}</p>
                        <div class="space-y-1">
                            @foreach($categoryFeatures->take(4) as $feature)
                                <label class="flex items-center cursor-pointer p-1 rounded text-xs">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedFeatures"
                                        value="{{ $feature->slug }}"
                                        class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-3 h-3"
                                    >
                                    <span class="ml-2" style="color:#9CA3AF;">{{ $feature->icon }} {{ $feature->name }}</span>
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
                <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                    🌶️ Nivel de Picante
                </label>
                <div class="flex justify-between items-center p-3 rounded-lg" style="background:#1A1A1A; border:1px solid #2A2A2A;">
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
                <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                    🇲🇽 Región Mexicana
                </label>
                <select
                    wire:model.live="selectedRegion"
                    style="width:100%; border-radius:0.5rem; border:1px solid #2A2A2A; background:#1A1A1A; color:#F5F5F5; padding:0.5rem 0.75rem;"
                >
                    <option value="">Todas las regiones</option>
                    @foreach(\App\Models\Restaurant::getMexicanRegions() as $key => $region)
                        <option value="{{ $key }}">{{ $region }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Authenticity Toggle -->
        <div class="flex items-center space-x-3" style="background:#1A1A1A; border:1px solid #D4AF37; border-radius:0.5rem; padding:1rem;">
            <input
                type="checkbox"
                wire:model.live="authenticOnly"
                id="authenticOnly"
                class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-5 h-5"
            >
            <label for="authenticOnly" class="text-sm font-semibold cursor-pointer" style="color:#F5F5F5;">
                👨‍🍳 Solo Restaurantes Auténticos (Chef certificado, recetas tradicionales o ingredientes importados)
            </label>
        </div>

        <!-- Specialty Filters: Bebidas y Tortillas -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                🫓 Bebidas y Tortillas
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasCafeDeOlla" id="hasCafeDeOlla" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasCafeDeOlla" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">☕ Café de Olla</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasAguasFrescas" id="hasAguasFrescas" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 w-4 h-4">
                    <label for="hasAguasFrescas" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🥤 Aguas Frescas</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasFreshTortillas" id="hasFreshTortillas" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4">
                    <label for="hasFreshTortillas" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🫓 Tortillas Frescas</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasHandmadeTortillas" id="hasHandmadeTortillas" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4">
                    <label for="hasHandmadeTortillas" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">👐 Hechas a Mano</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Platillos Tradicionales -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                🍖 Platillos Tradicionales
            </label>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasBirria" id="hasBirria" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasBirria" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🥘 Birria</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasCarnitas" id="hasCarnitas" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-4 h-4">
                    <label for="hasCarnitas" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🐷 Carnitas</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasBarbacoa" id="hasBarbacoa" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasBarbacoa" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🍖 Barbacoa</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasPozoleMenudo" id="hasPozoleMenudo" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 w-4 h-4">
                    <label for="hasPozoleMenudo" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🍲 Pozole/Menudo</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasTamales" id="hasTamales" class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4">
                    <label for="hasTamales" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🫔 Tamales</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Preparaciones y Métodos -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                👨‍🍳 Preparaciones y Métodos Tradicionales
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasHomemadeSalsa" id="hasHomemadeSalsa" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasHomemadeSalsa" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🌶️ Salsas Caseras</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasHomemadeMole" id="hasHomemadeMole" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasHomemadeMole" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🫕 Mole Casero</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasCharcoalGrill" id="hasCharcoalGrill" class="rounded border-gray-300 text-gray-600 focus:ring-gray-500 w-4 h-4">
                    <label for="hasCharcoalGrill" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🔥 Al Carbón</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasComal" id="hasComal" class="rounded border-gray-300 text-stone-600 focus:ring-stone-500 w-4 h-4">
                    <label for="hasComal" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🫓 Comal</label>
                </div>
            </div>
        </div>

        <!-- Specialty Filters: Panadería, Bebidas y Extras -->
        <div>
            <label class="block text-sm font-semibold mb-3" style="color:#9CA3AF;">
                🍞 Panadería, Bebidas y Extras
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasPanDulce" id="hasPanDulce" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <label for="hasPanDulce" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🍞 Pan Dulce</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasChurros" id="hasChurros" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 w-4 h-4">
                    <label for="hasChurros" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🥖 Churros</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasMezcalTequila" id="hasMezcalTequila" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <label for="hasMezcalTequila" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🥃 Mezcal/Tequila</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasMicheladas" id="hasMicheladas" class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-4 h-4">
                    <label for="hasMicheladas" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🍺 Micheladas</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasMexicanCandy" id="hasMexicanCandy" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500 w-4 h-4">
                    <label for="hasMexicanCandy" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">🍬 Dulces Mex</label>
                </div>
                <div class="flex items-center space-x-2" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.75rem;">
                    <input type="checkbox" wire:model.live="hasImportedProducts" id="hasImportedProducts" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <label for="hasImportedProducts" class="text-sm font-medium cursor-pointer" style="color:#F5F5F5;">📦 Importados</label>
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
            <div class="flex items-center justify-between rounded-lg p-4" style="background:#1A1A1A; border:1px solid #D4AF37;">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium" style="color:#F5F5F5;">
                        {{ $activeFiltersCount }} filtro(s) activo(s)
                    </span>
                </div>
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-sm font-medium underline"
                    style="color:#D4AF37;"
                >
                    Limpiar todos
                </button>
            </div>
        @endif
    </div>
@endif
