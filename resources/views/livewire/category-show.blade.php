<div>
    @section('title', "Restaurantes de {$category->name} - Los Mejores en Estados Unidos")
    @section('meta_description', "Descubre los mejores restaurantes de {$category->name} mexicana en Estados Unidos. {$restaurants->total()} restaurantes con reseñas verificadas.")

    @push('meta')
    <meta property="og:type" content="website">
    <meta property="og:title" content="Restaurantes de {{ $category->name }} | FAMER">
    <meta property="og:description" content="Encuentra los mejores restaurantes de {{ $category->name }} mexicana. {{ $restaurants->total() }} opciones verificadas.">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Restaurantes de {{ $category->name }}">
    @endpush

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-red-600 to-orange-500 text-white py-10">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center gap-4">
                <span class="text-5xl">{{ $category->icon ?? '🍽️' }}</span>
                <div>
                    <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                    <p class="text-red-100">{{ number_format($restaurants->total()) }} restaurantes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-lg p-5 sticky top-4 space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-lg">Filtros</h2>
                        @if($activeFilters > 0)
                            <button wire:click="clearFilters" class="text-sm text-red-600 font-medium">Limpiar ({{ $activeFilters }})</button>
                        @endif
                    </div>

                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">🔍 Buscar</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre o ciudad..." class="w-full rounded-lg border-gray-300 text-sm">
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">📍 Estado</label>
                        <select wire:model.live="selectedState" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Todos</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">💰 Precio</label>
                        <div class="flex gap-1">
                            @foreach(['$', '$$', '$$$', '$$$$'] as $p)
                                <button wire:click="$set('selectedPriceRange', '{{ $p }}')" class="flex-1 py-2 rounded-lg text-sm font-bold {{ $selectedPriceRange === $p ? 'bg-red-600 text-white' : 'bg-gray-100' }}">{{ $p }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">🏪 Tipo</label>
                        <select wire:model.live="selectedBusinessType" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Todos</option>
                            @foreach($businessTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Food Tags -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">🍽️ Especialidades</label>
                        <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                            @foreach($foodTags->take(12) as $tag)
                                <button wire:click="toggleFoodTag({{ $tag->id }})" class="px-2 py-1 rounded-full text-xs {{ in_array($tag->id, $selectedFoodTags) ? 'bg-red-600 text-white' : 'bg-gray-100' }}">
                                    {{ $tag->icon }} {{ $tag->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main -->
            <main class="flex-1">
                <!-- Sort -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex items-center justify-between">
                    <span class="text-sm text-gray-600"><span class="font-semibold">{{ number_format($restaurants->total()) }}</span> resultados</span>
                    <select wire:model.live="sortBy" class="rounded-lg border-gray-300 text-sm">
                        <option value="rating">⭐ Mejor valorados</option>
                        <option value="reviews">💬 Más reseñas</option>
                        <option value="name">🔤 Nombre A-Z</option>
                        <option value="newest">🆕 Más recientes</option>
                    </select>
                </div>

                <!-- Grid -->
                @if($restaurants->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($restaurants as $restaurant)
                            <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="group bg-white rounded-xl shadow-md hover:shadow-xl transition overflow-hidden">
                                <div class="aspect-[4/3] relative bg-gray-100">
                                    @if($restaurant->image)
                                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="w-full h-full items-center justify-center bg-gradient-to-br from-red-100 to-orange-100 absolute inset-0" style="display:none;">
                                            <span class="text-5xl">{{ $category->icon ?? '🍽️' }}</span>
                                        </div>
                                    @elseif($restaurant->hasMedia('images'))
                                        <img src="{{ $restaurant->hasMedia('images') ? $restaurant->getFirstMediaUrl('images') : $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-100 to-orange-100">
                                            <span class="text-5xl">{{ $category->icon ?? '🍽️' }}</span>
                                        </div>
                                    @endif
                                    @if($restaurant->price_range)
                                        <span class="absolute top-3 right-3 bg-white/90 px-2 py-1 rounded-full text-xs font-bold">{{ $restaurant->price_range }}</span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 group-hover:text-red-600 line-clamp-1">{{ $restaurant->name }}</h3>
                                    @php $catWeightedRating = $restaurant->getWeightedRating(); @endphp
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="flex text-yellow-400">
                                            @for($i = 0; $i < 5; $i++)
                                                <svg class="w-4 h-4 {{ $i < floor($catWeightedRating) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600">{{ number_format($catWeightedRating, 1) }} ({{ $restaurant->getCombinedReviewCount() }})</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">📍 {{ $restaurant->city }}, {{ $restaurant->state->abbreviation ?? '' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $restaurants->links() }}</div>
                @else
                    <div class="bg-white rounded-xl shadow-md p-12 text-center">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold mb-2">No se encontraron restaurantes</h3>
                        <p class="text-gray-600 mb-4">Intenta ajustar los filtros.</p>
                        @if($activeFilters > 0)
                            <button wire:click="clearFilters" class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700">Limpiar filtros</button>
                        @endif
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
