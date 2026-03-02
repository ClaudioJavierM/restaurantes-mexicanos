<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-amber-600 via-orange-600 to-red-700 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="text-center">
                <!-- Trophy Icon -->
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 rounded-full mb-8 animate-bounce">
                    <span class="text-6xl">🏆</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 tracking-tight">
                    FAMER Awards
                    <span class="block text-yellow-300">2026</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-amber-100 max-w-3xl mx-auto mb-8">
                    Buscamos a los <strong>Mejores Restaurantes Mexicanos</strong> de Estados Unidos.
                    <br>12 meses de evaluación. Tu voto decide.
                </p>

                <!-- Countdown -->
                @if($daysUntilStart > 0)
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-8 py-4 mb-8">
                    <span class="text-lg mr-2">⏰</span>
                    <span class="text-xl font-bold">{{ $daysUntilStart }} días</span>
                    <span class="text-amber-200 ml-2">para el inicio</span>
                </div>
                @else
                <div class="inline-flex items-center bg-green-500/20 backdrop-blur-sm rounded-full px-8 py-4 mb-8">
                    <span class="text-lg mr-2">🎉</span>
                    <span class="text-xl font-bold">¡Ya comenzó!</span>
                </div>
                @endif

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button 
                        wire:click="toggleNominationForm"
                        class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-bold rounded-full shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all">
                        <span class="mr-2">📝</span>
                        Nominar un Restaurante
                    </button>
                    <a href="{{ url('/guia') }}" 
                       class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition-all">
                        <span class="mr-2">📊</span>
                        Ver Rankings Actuales
                    </a>
                </div>
            </div>
        </div>

        <!-- Wave -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
            </svg>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-white py-12 -mt-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 shadow-lg">
                    <div class="text-5xl font-extrabold text-amber-600">{{ number_format($totalRestaurants) }}</div>
                    <div class="text-gray-600 mt-2">Restaurantes Evaluados</div>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-8 shadow-lg">
                    <div class="text-5xl font-extrabold text-orange-600">{{ number_format($totalCities) }}</div>
                    <div class="text-gray-600 mt-2">Ciudades Cubiertas</div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 shadow-lg">
                    <div class="text-5xl font-extrabold text-red-600">{{ $totalStates }}</div>
                    <div class="text-gray-600 mt-2">Estados Participantes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- How it Works -->
    <div class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Cómo Funciona?</h2>
                <p class="text-xl text-gray-600">Un proceso transparente de 12 meses</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                        <span class="text-3xl">📊</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">1. Recopilamos Datos</h3>
                    <p class="text-gray-600">Integramos calificaciones de Google, Yelp, Facebook y nuestra comunidad.</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mb-4">
                        <span class="text-3xl">🗳️</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">2. Tú Votas</h3>
                    <p class="text-gray-600">Cada mes puedes votar por tus restaurantes favoritos en tu ciudad.</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                        <span class="text-3xl">🏅</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">3. Premiamos Mensual</h3>
                    <p class="text-gray-600">Cada mes anunciamos al "Restaurante del Mes" de cada ciudad.</p>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                        <span class="text-3xl">🏆</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">4. Gran Final</h3>
                    <p class="text-gray-600">En diciembre certificamos a los mejores del año por ciudad, estado y nacional.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Categorías de Premios</h2>
                <p class="text-xl text-gray-600">Reconocemos a los mejores en cada nivel</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-yellow-400 to-amber-500 rounded-2xl p-8 text-white shadow-xl">
                    <div class="text-4xl mb-4">🏙️</div>
                    <h3 class="text-2xl font-bold mb-2">Top 10 por Ciudad</h3>
                    <p class="text-amber-100">Los 10 mejores restaurantes mexicanos de cada ciudad. Competencia local.</p>
                    <div class="mt-4 text-sm bg-white/20 rounded-lg px-4 py-2 inline-block">
                        +1,500 ciudades participando
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl p-8 text-white shadow-xl">
                    <div class="text-4xl mb-4">📍</div>
                    <h3 class="text-2xl font-bold mb-2">Top 100 por Estado</h3>
                    <p class="text-orange-100">Los 100 mejores de cada estado. Competencia estatal.</p>
                    <div class="mt-4 text-sm bg-white/20 rounded-lg px-4 py-2 inline-block">
                        50 estados evaluados
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-600 to-pink-600 rounded-2xl p-8 text-white shadow-xl">
                    <div class="text-4xl mb-4">🇺🇸</div>
                    <h3 class="text-2xl font-bold mb-2">Top 100 Nacional</h3>
                    <p class="text-red-100">Los 100 restaurantes mexicanos que DEBES visitar en USA.</p>
                    <div class="mt-4 text-sm bg-white/20 rounded-lg px-4 py-2 inline-block">
                        La élite nacional
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nomination Form Modal -->
    @if($showNominationForm)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" wire:click.self="toggleNominationForm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            @if($nominationSubmitted)
            <div class="p-8 text-center">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Gracias por tu nominación!</h3>
                <p class="text-gray-600 mb-6">Revisaremos el restaurante y te notificaremos cuando sea agregado.</p>
                <button wire:click="toggleNominationForm" class="px-6 py-3 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700">
                    Cerrar
                </button>
            </div>
            @else
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">📝 Nominar un Restaurante</h3>
                    <button wire:click="toggleNominationForm" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-600 mt-1">¿Conoces un restaurante mexicano que debemos incluir?</p>
            </div>
            <form wire:submit="submitNomination" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Restaurante *</label>
                    <input type="text" wire:model="restaurantName" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="Ej: Taquería El Güero">
                    @error('restaurantName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad *</label>
                        <input type="text" wire:model="city" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="Dallas">
                        @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select wire:model="stateCode" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">Seleccionar</option>
                            @foreach($this->states as $state)
                                <option value="{{ $state->code }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('stateCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" wire:model="address" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="123 Main St">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link de Google Maps</label>
                    <input type="url" wire:model="googleMapsUrl" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="https://maps.google.com/...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">¿Por qué lo nominás?</label>
                    <textarea wire:model="whyNominate" rows="3" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="Cuéntanos qué lo hace especial..."></textarea>
                </div>
                <hr>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tu nombre</label>
                    <input type="text" wire:model="nominatorName" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="Tu nombre">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tu email *</label>
                    <input type="email" wire:model="nominatorEmail" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" placeholder="tu@email.com">
                    @error('nominatorEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all">
                    Enviar Nominación
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif

    <!-- For Restaurants Section -->
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">¿Eres dueño de un restaurante?</h2>
                    <p class="text-xl text-gray-300 mb-8">
                        Inscríbete en FAMER Awards 2026 y obtén visibilidad, credibilidad y más clientes.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <span class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">✓</span>
                            Badge verificado en tu perfil
                        </li>
                        <li class="flex items-center">
                            <span class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">✓</span>
                            Certificado descargable si ganas
                        </li>
                        <li class="flex items-center">
                            <span class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">✓</span>
                            Promoción en nuestras redes
                        </li>
                        <li class="flex items-center">
                            <span class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">✓</span>
                            Dashboard con tu posición en tiempo real
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-amber-500 text-white font-bold rounded-full hover:bg-amber-600 transition-all">
                        Registrar mi Restaurante
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-8 shadow-2xl">
                    <div class="text-center">
                        <div class="text-6xl mb-4">🏆</div>
                        <div class="text-sm uppercase tracking-wide text-amber-200">Certificado Oficial</div>
                        <div class="text-3xl font-bold my-4">FAMER Awards 2026</div>
                        <div class="text-lg text-amber-100">Top 10 Dallas, TX</div>
                        <div class="mt-6 border-t border-amber-400/30 pt-6">
                            <div class="text-sm text-amber-200">Basado en evaluaciones de</div>
                            <div class="flex justify-center gap-4 mt-2 text-2xl">
                                <span title="Google">📍</span>
                                <span title="Yelp">⭐</span>
                                <span title="Facebook">👍</span>
                                <span title="FAMER">🏆</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Preguntas Frecuentes</h2>
            
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">¿Cómo se calculan los rankings?</h3>
                    <p class="text-gray-600">Combinamos calificaciones de Google (12%), Yelp (10%), Facebook (8%), TripAdvisor (10%) y métricas propias de FAMER (60%) incluyendo votos de usuarios, reseñas verificadas y actividad del negocio.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">¿Cuántas veces puedo votar?</h3>
                    <p class="text-gray-600">Puedes votar una vez por restaurante por mes. Esto significa que puedes votar por varios restaurantes diferentes cada mes.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">¿Qué ganan los restaurantes?</h3>
                    <p class="text-gray-600">Los ganadores reciben un certificado oficial, badge en su perfil, promoción en nuestras redes sociales y reconocimiento como uno de los mejores de su categoría.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">¿Cómo nomino un restaurante?</h3>
                    <p class="text-gray-600">Haz clic en "Nominar un Restaurante" arriba y llena el formulario. Revisaremos la nominación y agregaremos el restaurante si cumple los criterios.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                ¿Listo para encontrar los mejores restaurantes mexicanos?
            </h2>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button wire:click="toggleNominationForm" class="px-8 py-4 bg-white text-orange-600 font-bold rounded-full shadow-xl hover:shadow-2xl transition-all">
                    📝 Nominar Restaurante
                </button>
                <a href="{{ url('/guia') }}" class="px-8 py-4 bg-orange-700 text-white font-bold rounded-full hover:bg-orange-800 transition-all">
                    📊 Ver Rankings
                </a>
            </div>
        </div>
    </div>
</div>
