<div class="min-h-screen bg-gradient-to-b from-amber-50 to-white">
    @section('title', "FAMER Awards {$year} - Los Mejores Restaurantes Mexicanos en Estados Unidos")
    @section('meta_description', "Rankings oficiales FAMER Awards {$year}. Descubre los mejores restaurantes mexicanos en USA basados en evaluaciones verificadas de Google, Yelp y nuestra comunidad.")

    @push('meta')
    <meta property="og:type" content="website">
    <meta property="og:title" content="FAMER Awards {{ $year }} - Mejores Restaurantes Mexicanos">
    <meta property="og:description" content="Rankings oficiales de los mejores restaurantes mexicanos en Estados Unidos. Votaciones y evaluaciones verificadas.">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FAMER Awards {{ $year }}">
    @endpush

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-red-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-6">
                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                🏆 FAMER Awards {{ $year }}
            </h1>
            <p class="text-xl text-amber-100 max-w-3xl mx-auto">
                Los Mejores Restaurantes Mexicanos en Estados Unidos - 
                Rankings basados en evaluaciones verificadas de Google, Yelp, Facebook y nuestra comunidad
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Scope Tabs -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <button 
                wire:click="setScope('national')" 
                class="px-6 py-3 rounded-full font-semibold transition-all {{ $scope === 'national' ? 'bg-amber-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-amber-50 border border-gray-200' }}">
                🇺🇸 Nacional
            </button>
            <button 
                wire:click="setScope('state')" 
                class="px-6 py-3 rounded-full font-semibold transition-all {{ $scope === 'state' ? 'bg-amber-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-amber-50 border border-gray-200' }}">
                📍 Por Estado
            </button>
            <button 
                wire:click="setScope('city')" 
                class="px-6 py-3 rounded-full font-semibold transition-all {{ $scope === 'city' ? 'bg-amber-600 text-white shadow-lg' : 'bg-white text-gray-700 hover:bg-amber-50 border border-gray-200' }}">
                🏙️ Por Ciudad
            </button>
        </div>

        <!-- Filters -->
        @if($scope !== 'national')
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="stateId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Selecciona un estado</option>
                        @foreach($this->states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($scope === 'city' && $stateId)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                    <select wire:model.live="city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Selecciona una ciudad</option>
                        @foreach($this->cities as $cityOption)
                            <option value="{{ $cityOption }}">{{ $cityOption }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Top Cities Quick Links (for national view) -->
        @if($scope === 'national' && $this->topCities->count() > 0)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">🌟 Ver Rankings por Ciudad</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($this->topCities as $topCity)
                    <button 
                        wire:click="selectCity('{{ $topCity->ranking_scope }}', {{ $topCity->restaurant->state_id }})"
                        class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:bg-amber-50 hover:border-amber-300 transition">
                        {{ $topCity->ranking_scope }}, {{ $topCity->restaurant->state?->code ?? '' }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Rankings List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    @if($scope === 'national')
                        Top Restaurantes Mexicanos en Estados Unidos
                    @elseif($scope === 'state' && $stateId)
                        Top Restaurantes en {{ $this->states->find($stateId)?->name ?? 'Estado' }}
                    @elseif($scope === 'city' && $city)
                        Top Restaurantes en {{ $city }}
                    @else
                        Rankings FAMER {{ $year }}
                    @endif
                </h2>
            </div>

            @if($this->rankings->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($this->rankings as $ranking)
                    <div class="p-6 hover:bg-amber-50 transition-colors flex items-center gap-4">
                        <!-- Position -->
                        <div class="flex-shrink-0 w-16 text-center">
                            @if($ranking->position <= 3)
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full 
                                    {{ $ranking->position == 1 ? 'bg-gradient-to-br from-yellow-300 to-yellow-500' : '' }}
                                    {{ $ranking->position == 2 ? 'bg-gradient-to-br from-gray-300 to-gray-400' : '' }}
                                    {{ $ranking->position == 3 ? 'bg-gradient-to-br from-amber-600 to-amber-700' : '' }}">
                                    <span class="text-xl font-bold {{ $ranking->position == 1 ? 'text-yellow-900' : 'text-white' }}">
                                        {{ $ranking->position == 1 ? '🥇' : ($ranking->position == 2 ? '🥈' : '🥉') }}
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100">
                                    <span class="text-lg font-bold text-gray-600">#{{ $ranking->position }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Restaurant Info -->
                        <div class="flex-grow">
                            <a href="{{ route('restaurants.show', $ranking->restaurant->slug) }}" 
                               class="text-lg font-semibold text-gray-900 hover:text-amber-600 transition">
                                {{ $ranking->restaurant->name }}
                            </a>
                            <div class="text-sm text-gray-500 mt-1">
                                📍 {{ $ranking->restaurant->city }}, {{ $ranking->restaurant->state?->name ?? '' }}
                                @if($ranking->restaurant->category)
                                    <span class="mx-2">•</span>
                                    {{ $ranking->restaurant->category->name }}
                                @endif
                            </div>
                            
                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($ranking->position <= 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $ranking->position <= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-amber-100 text-amber-800' }}">
                                        🏆 {{ $ranking->badge_name }}
                                    </span>
                                @endif
                                @if($ranking->restaurant->google_rating)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <img src="/images/google-icon.svg" class="w-3 h-3 mr-1" alt="Google"> {{ $ranking->restaurant->google_rating }}
                                    </span>
                                @endif
                                @if($ranking->restaurant->yelp_rating)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <img src="/images/yelp-icon.svg" class="w-3 h-3 mr-1" alt="Yelp"> {{ $ranking->restaurant->yelp_rating }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Score -->
                        <div class="flex-shrink-0 text-right">
                            <div class="text-2xl font-bold text-amber-600">
                                {{ number_format($ranking->final_score, 1) }}
                            </div>
                            <div class="text-xs text-gray-500">puntos FAMER</div>
                        </div>

                        <!-- FAMER Rating (weighted average) -->
                        @php $rankingWeightedRating = $ranking->restaurant->getWeightedRating(); @endphp
                        <div class="flex-shrink-0 flex items-center">
                            <div class="flex text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($rankingWeightedRating))
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $this->rankings->links() }}
            </div>
            @else
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay rankings disponibles</h3>
                <p class="text-gray-500">
                    @if($scope === 'state' && !$stateId)
                        Selecciona un estado para ver los rankings.
                    @elseif($scope === 'city' && (!$stateId || !$city))
                        Selecciona un estado y ciudad para ver los rankings.
                    @else
                        Aún no se han calculado rankings para esta categoría.
                    @endif
                </p>
            </div>
            @endif
        </div>

        <!-- Methodology Section -->
        <div class="mt-12 bg-white rounded-xl shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📊 Metodología de Evaluación</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-semibold text-amber-600 mb-3">Plataformas Externas (40%)</h4>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Google Reviews (12%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Yelp (10%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            Facebook (8%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            TripAdvisor (10%)
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-amber-600 mb-3">Métricas FAMER (60%)</h4>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Calificación FAMER (15%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Cantidad de Reseñas (8%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Reseñas Verificadas (8%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Respuestas del Dueño (7%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Fotos y Media (7%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Perfil Completo (8%)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Actividad Reciente (7%)
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
