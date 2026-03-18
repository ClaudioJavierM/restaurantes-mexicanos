<div>
    @section('title', 'Restaurantes Mexicanos en Estados Unidos - Directorio Completo')
    @section('meta_description', 'Encuentra los mejores restaurantes mexicanos cerca de ti. Directorio completo con reseñas de Google, Yelp y nuestra comunidad. Tacos, burritos, enchiladas y más.')

    @push('meta')
    <meta property="og:type" content="website">
    <meta property="og:title" content="Restaurantes Mexicanos en Estados Unidos | FAMER">
    <meta property="og:description" content="Directorio completo de restaurantes mexicanos auténticos. Encuentra tacos, burritos, enchiladas y más cerca de ti.">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Restaurantes Mexicanos en USA">
    @endpush

    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 border-b-4 border-[#D4A54A] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-2">Restaurantes Mexicanos</h1>
            <p class="text-gray-300">Descubre los mejores restaurantes mexicanos en Estados Unidos</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Location Permission Banner -->
        @if($showLocationBanner && $locationSource !== 'browser')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex items-center justify-between" x-data="{ requesting: false }">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">Activa tu ubicacion para ver restaurantes cercanos</p>
                    <p class="text-xs text-blue-600">Ordenaremos los resultados por distancia</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button
                    x-show="!requesting"
                    @click="requesting = true; navigator.geolocation.getCurrentPosition(
                        (pos) => { $wire.setUserLocation(pos.coords.latitude, pos.coords.longitude); requesting = false; },
                        (err) => { requesting = false; $wire.dismissLocationBanner(); },
                        { enableHighAccuracy: true, timeout: 10000 }
                    )"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Permitir ubicacion
                </button>
                <button
                    x-show="requesting"
                    disabled
                    class="px-4 py-2 bg-blue-400 text-white text-sm font-medium rounded-lg cursor-wait"
                >
                    <svg class="animate-spin h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Obteniendo...
                </button>
                <button
                    wire:click="dismissLocationBanner"
                    class="text-blue-600 hover:text-blue-800 p-1"
                    title="Cerrar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Location Status Indicator -->
        @if($locationSource === 'browser')
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6 flex items-center justify-between">
            <div class="flex items-center text-sm text-green-800">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Mostrando restaurantes ordenados por distancia desde tu ubicacion
            </div>
        </div>
        @endif
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar
                    </label>
                    <input
                        type="text"
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Nombre, ciudad, descripción..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                </div>

                <!-- State Filter -->
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado
                    </label>
                    <select
                        id="state"
                        wire:model.live="selectedState"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                        <option value="">Todos los estados</option>
                        @foreach($states as $state)
                            <option value="{{ $state->name }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Categoría
                    </label>
                    <select
                        id="category"
                        wire:model.live="selectedCategory"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    >
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Advanced Filters Toggle -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <button
                    wire:click="toggleAdvancedFilters"
                    type="button"
                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-red-600 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span>Filtros Avanzados Mexicanos 🇲🇽</span>
                    <svg class="w-4 h-4 transition-transform duration-200 {{ $showAdvancedFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Advanced Filters Panel -->
            @include('livewire.partials.advanced-filters')


            <!-- Sort and Clear -->
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Ordenar por:</label>
                    <div class="flex flex-wrap gap-2">
                        @if($userLatitude && $userLongitude)
                        <button
                            wire:click="$set('sortBy', 'nearby')"
                            class="px-3 py-1 text-sm rounded-md flex items-center {{ $sortBy === 'nearby' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Cerca de mi
                        </button>
                        @else
                        <button
                            type="button"
                            x-data
                            @click="navigator.geolocation.getCurrentPosition(
                                (pos) => { $wire.setUserLocation(pos.coords.latitude, pos.coords.longitude); },
                                (err) => { alert('No pudimos obtener tu ubicacion. Por favor activa los permisos de ubicacion en tu navegador.'); },
                                { enableHighAccuracy: true, timeout: 10000 }
                            )"
                            class="px-3 py-1 text-sm rounded-md flex items-center bg-blue-100 text-blue-700 hover:bg-blue-200 border border-blue-300"
                            title="Activa tu ubicacion para ordenar por cercania"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Cerca de mi
                        </button>
                        @endif
                        <button
                            wire:click="$set('sortBy', 'name')"
                            class="px-3 py-1 text-sm rounded-md {{ $sortBy === 'name' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            Nombre
                        </button>
                        <button
                            wire:click="$set('sortBy', 'rating')"
                            class="px-3 py-1 text-sm rounded-md {{ $sortBy === 'rating' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            Calificacion
                        </button>
                        <button
                            wire:click="$set('sortBy', 'newest')"
                            class="px-3 py-1 text-sm rounded-md {{ $sortBy === 'newest' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            Mas recientes
                        </button>
                    </div>
                </div>

                @php
                    $defaultSort = ($userLatitude && $userLongitude) ? 'nearby' : 'rating';
                @endphp
                @if($search || $selectedState || $selectedCategory || $sortBy !== $defaultSort)
                    <button
                        wire:click="clearFilters"
                        class="text-sm text-red-600 hover:text-red-700 font-medium"
                    >
                        Limpiar filtros
                    </button>
                @endif
            </div>
        </div>

        <!-- Restaurant Grid with Map -->
        @if($restaurants->count() > 0)
            <div class="md:flex md:gap-6">
                <!-- Left Column: Restaurant List -->
                <div class="md:w-3/5 lg:w-2/3">
                    <!-- Results Count -->
                    <div class="mb-4 text-sm text-gray-600">
                        Mostrando {{ $restaurants->count() }} de {{ $restaurants->total() }} restaurantes
                    </div>

                    <!-- Restaurant Grid - 2 columns on larger screens when map is showing -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
                        @foreach($restaurants as $index => $restaurant)
                            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                               class="group"
                               @mouseenter="$dispatch('highlight-marker', { index: {{ $index }} })">
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200 relative">
                                    <!-- Numbered Badge -->
                                    <span class="absolute top-3 left-3 z-10 bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold shadow-lg">
                                        {{ $index + 1 }}
                                    </span>

                                    <!-- Restaurant Image -->
                                    <div class="aspect-video bg-gray-200 overflow-hidden relative">
                                        @if($restaurant->hasMedia('images'))
                                            <img
                                                src="{{ $restaurant->getFirstMediaUrl('images') }}"
                                                alt="{{ $restaurant->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-100 to-red-200">
                                                <svg class="w-16 h-16 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                </svg>
                                            </div>
                                        @endif

                                        {{-- Ranking badge on card image --}}
                                        @php
                                            $cardRanking = $restaurant->rankings->first();
                                        @endphp
                                        @if($cardRanking)
                                            <div class="absolute top-2 right-2 z-10" style="background:linear-gradient(135deg, #B8860B, #D4AF37, #F5D060); border-radius:8px; padding:3px 10px; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; gap:4px; border:1px solid rgba(255,255,255,0.25);">
                                                <svg width="12" height="12" fill="#1a1a2e" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v1a2 2 0 002 2h1.06a7.04 7.04 0 003.272 4.35L8.12 15.7A2 2 0 009.98 18h.04a2 2 0 001.86-2.3l-1.212-4.35A7.04 7.04 0 0013.94 7H15a2 2 0 002-2V4a2 2 0 00-2-2H5z" clip-rule="evenodd"/></svg>
                                                <span style="font-size:11px; font-weight:800; color:#1a1a2e; white-space:nowrap;">
                                                    {{ $cardRanking->position <= 3 ? '#' . $cardRanking->position : 'Top ' . $cardRanking->position }}
                                                    {{ $cardRanking->ranking_type === 'city' ? $cardRanking->ranking_scope : ($cardRanking->ranking_type === 'state' ? $cardRanking->ranking_scope : 'USA') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Restaurant Info -->
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                                    {{ $restaurant->name }}
                                                </h3>
                                                <p class="text-sm text-gray-600">
                                                    {{ $restaurant->category->name }}
                                                </p>
                                            </div>
                                            @if($restaurant->is_featured)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    Destacado
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Rating (weighted average from Google, Yelp, and internal) -->
                                        @php
                                            $listWeightedRating = $restaurant->getWeightedRating();
                                        @endphp
                                        @if($listWeightedRating > 0)
                                            <div class="flex items-center mb-2">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $listWeightedRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600">
                                                    {{ number_format($listWeightedRating, 1) }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Location -->
                                        <div class="flex items-center text-sm text-gray-600 mb-2">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $restaurant->city }}, {{ $restaurant->state->code }}</span>
                                            @if($userLatitude && $userLongitude && $restaurant->latitude && $restaurant->longitude)
                                                @php
                                                    $distance = $this->getDistanceToRestaurant($restaurant);
                                                @endphp
                                                @if($distance !== null)
                                                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                                        {{ number_format($distance, 1) }} mi
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        <!-- Description -->
                                        @if($restaurant->description)
                                            <p class="text-sm text-gray-600 line-clamp-2">
                                                {{ $restaurant->description }}
                                            </p>
                                        @endif

                                        <!-- Horario de hoy -->
                                        @php
                                            $todayHours = $restaurant->getTodayHours();
                                        @endphp
                                        @if($todayHours && !($todayHours['closed'] ?? false) && isset($todayHours['open'], $todayHours['close']))
                                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Hoy: {{ $todayHours['open'] }} – {{ $todayHours['close'] }}
                                            </p>
                                        @endif

                                        <!-- Advanced Mexican Badges -->
                                        @include('livewire.partials.restaurant-advanced-badges', ['restaurant' => $restaurant])


                                        <!-- Badges -->
                                        <div class="flex flex-wrap gap-1 mt-3">
                                            {{-- Badge: Verificado por plan de suscripcion --}}
                                            @if($restaurant->is_claimed)
                                                @if($restaurant->subscription_plan === 'elite')
                                                    {{-- Elite: Diamante dorado premium --}}
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 text-amber-900 shadow-lg border border-amber-300 ring-1 ring-amber-400/50" title="Restaurante Elite - Maxima distincion">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2L9.19 8.63L2 9.24L7.46 13.97L5.82 21L12 17.27L18.18 21L16.54 13.97L22 9.24L14.81 8.63L12 2Z"/>
                                                        </svg>
                                                        Elite Verificado
                                                    </span>
                                                @elseif($restaurant->subscription_plan === 'premium')
                                                    {{-- Premium: Estrella azul/morada --}}
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-md" title="Restaurante Premium Verificado">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                                        </svg>
                                                        Premium Verificado
                                                    </span>
                                                @else
                                                    {{-- Free: Palomita verde --}}
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gradient-to-r from-emerald-400 to-teal-500 text-white shadow-sm" title="Restaurante verificado por su propietario">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Verificado
                                                    </span>
                                                @endif
                                            @endif

                                            {{-- Badge: Google --}}
                                            @if($restaurant->google_verified)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white text-gray-700 border border-gray-200 shadow-sm" title="Datos verificados con Google">
                                                    <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24">
                                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                                    </svg>
                                                    Google
                                                </span>
                                            @endif

                                            {{-- Badge: Yelp --}}
                                            @if($restaurant->yelp_id || $restaurant->import_source === 'yelp')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#AF0606] text-white shadow-sm" title="Datos verificados con Yelp">
                                                    <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711zm-6.227 7.726a1.07 1.07 0 01-1.253.69 9.195 9.195 0 01-3.891-2.532c-.643-.72-.08-1.835.85-1.68l4.804.803c.984.164 1.25 1.733.49 2.719zm-6.178-2.818a1.07 1.07 0 01-.337 1.387 9.2 9.2 0 01-4.436 1.564 1.072 1.072 0 01-1.11-1.398l1.86-4.573c.36-.887 1.63-.994 1.914-.16l1.11 3.18zm-.765-4.742c.36.887-.537 1.763-1.35 1.32L1.553 11.8a1.07 1.07 0 01-.174-1.603 9.2 9.2 0 013.56-2.702c.9-.36 1.798.573 1.438 1.455l-1.388 3.41zm8.78-6.078l-3.085 3.73c-.596.72-1.76.427-1.76-.44V5.31a1.07 1.07 0 01.922-1.06 9.19 9.19 0 014.504.872 1.073 1.073 0 01-.581 1.46z"/>
                                                    </svg>
                                                    Yelp
                                                </span>
                                            @endif

                                            {{-- Badge: Datos Fusionados (cuando tiene ambas fuentes) --}}
                                            @include('livewire.partials.data-completeness-badge', ['restaurant' => $restaurant])

                                            @if($restaurant->business_status === 'coming_soon')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    Próximamente
                                                </span>
                                            @else
                                                @php $openStatus = $restaurant->isOpen(); @endphp
                                                @if($openStatus === true)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Abierto
                                                    </span>
                                                @elseif($openStatus === false)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                        Cerrado
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $restaurants->links() }}
                    </div>
                </div>

                <!-- Right Column: Sticky Map -->
                <div class="hidden md:block md:w-2/5 lg:w-1/3">
                    <div class="md:sticky md:top-4">
                        <x-restaurants-map
                            :restaurants="$restaurants"
                            :user-latitude="$userLatitude"
                            :user-longitude="$userLongitude"
                            height="calc(100vh - 120px)"
                        />
                    </div>
                </div>

                @php
                    $mapRestaurants = collect($restaurants instanceof \Illuminate\Pagination\LengthAwarePaginator ? $restaurants->items() : $restaurants)
                        ->map(fn($r, $i) => [
                            'id' => $r->id, 'name' => $r->name,
                            'lat' => (float) $r->latitude, 'lng' => (float) $r->longitude,
                            'address' => $r->address ?? '', 'city' => $r->city ?? '',
                            'state' => $r->state?->code ?? '',
                            'rating' => round($r->getWeightedRating(), 1),
                            'slug' => $r->slug, 'number' => $i + 1,
                            'image' => $r->hasMedia('images') ? $r->getFirstMediaUrl('images') : null,
                        ])->filter(fn($r) => $r['lat'] && $r['lng'])->values();
                @endphp
                <script>
                    (function() {
                        var mapData = {!! $mapRestaurants->toJson() !!};
                        function dispatchMapUpdate() {
                            window.dispatchEvent(new CustomEvent('update-map-markers', { detail: mapData }));
                        }
                        // Retry until map is initialized (covers SPA navigation & initial load)
                        function tryUpdate(attempts) {
                            if (attempts <= 0) return;
                            var mapEl = document.getElementById('restaurants-map');
                            if (mapEl && mapEl.__gm) {
                                dispatchMapUpdate();
                            } else {
                                setTimeout(function() { tryUpdate(attempts - 1); }, 300);
                            }
                        }
                        // On Livewire SPA navigation
                        document.addEventListener('livewire:navigated', function() { tryUpdate(20); });
                        // On initial load or Livewire morph
                        tryUpdate(20);
                    })();
                </script>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No se encontraron restaurantes</h3>
                <p class="mt-1 text-sm text-gray-500">Intenta ajustar tus filtros de búsqueda.</p>
                <div class="mt-6">
                    <button
                        wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        Limpiar filtros
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
