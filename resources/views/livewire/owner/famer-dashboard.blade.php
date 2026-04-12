<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">FAMER Awards 2026</h1>
                <p class="text-amber-100">Dashboard de {{ $restaurant->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold">{{ $monthlyVotes }}</div>
                <div class="text-amber-200 text-sm">Votos este mes</div>
            </div>
        </div>
    </div>


    <!-- Upgrade Banner (solo free) -->
    <livewire:owner.upgrade-banner :restaurant="$restaurant" />

    <!-- Plan Features Section -->
    @if(in_array($restaurant->subscription_tier, ['premium', 'elite']))
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-gradient-to-r {{ $restaurant->subscription_tier === 'elite' ? 'from-yellow-600 to-amber-500' : 'from-red-600 to-red-500' }} px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($restaurant->subscription_tier === 'elite')
                    <span class="text-3xl">&#127942;</span>
                @else
                    <span class="text-3xl">&#11088;</span>
                @endif
                <div>
                    <h2 class="text-lg font-bold text-white">Plan {{ ucfirst($restaurant->subscription_tier) }}</h2>
                    <p class="text-sm text-white/80">Funciones activas de tu suscripcion</p>
                </div>
            </div>
            <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full">Activo</span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Aparece en el directorio</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Info basica (nombre, direccion, telefono)</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Integracion con Google Maps</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Verificar propiedad del restaurante</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Badge Destacado</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Top 3 en busquedas locales</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Menu Digital + QR Code</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Sistema de Reservaciones</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Dashboard de Analiticas</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Chatbot AI (ES/EN) 24/7</span>
                </div>
            </div>
        </div>
    </div>
    @elseif($restaurant->subscription_tier === 'free' || !$restaurant->subscription_tier)
    <!-- Free Plan - Upgrade CTA -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-500 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-3xl">&#128274;</span>
                <div>
                    <h2 class="text-lg font-bold text-white">Plan Gratuito</h2>
                    <p class="text-sm text-white/80">Actualiza para desbloquear mas funciones</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Aparece en el directorio</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Info basica (nombre, direccion, telefono)</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Integracion con Google Maps</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                    <span class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-800">Verificar propiedad del restaurante</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Badge Destacado</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Top 3 en busquedas locales</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Menu Digital + QR Code</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Sistema de Reservaciones</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Dashboard de Analiticas</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-100 rounded-lg opacity-60">
                    <span class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <span class="text-sm font-medium text-gray-400">Chatbot AI (ES/EN) 24/7</span>
                </div>
            </div>
            <div class="mt-5 text-center">
                <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-500 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-600 transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Actualizar a Premium
                </a>
            </div>
        </div>
    </div>
    @endif

        <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-4xl font-bold text-amber-500">
                @if($cityRank) #{{ $cityRank }} @else - @endif
            </div>
            <div class="text-gray-500 text-sm">Posicion en {{ $restaurant->city }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-4xl font-bold text-orange-500">
                @if($stateRank) #{{ $stateRank }} @else - @endif
            </div>
            <div class="text-gray-500 text-sm">Posicion en {{ $restaurant->state?->name }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-4xl font-bold text-red-500">
                @if($nationalRank) #{{ $nationalRank }} @else - @endif
            </div>
            <div class="text-gray-500 text-sm">Posicion Nacional</div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <div class="text-4xl font-bold text-purple-600">{{ number_format($totalVotes) }}</div>
            <div class="text-gray-500 text-sm">Votos totales 2026</div>
        </div>
    </div>

    <!-- Monthly Votes Chart -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Historial de Votos</h3>
        <div class="flex items-end justify-between h-40 gap-2">
            @foreach($monthlyVotesHistory as $data)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full bg-amber-100 rounded-t" style="height: {{ max(10, min(100, $data['votes'] * 10)) }}px"></div>
                <span class="text-xs text-gray-500 mt-2">{{ $data['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Share Section -->

    <!-- QR Code Section -->
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row gap-6 items-center">
            <div class="flex-shrink-0">
                <img src="{{ $this->qrCodeUrl }}" alt="QR Code para votar" class="w-48 h-48 rounded-lg border-4 border-amber-500">
            </div>
            <div class="flex-1 text-center md:text-left">
                <h3 class="text-xl font-bold text-gray-900">Codigo QR para Votacion</h3>
                <p class="text-gray-600 mt-2">
                    Imprime este codigo QR y colocalo en tu restaurante. Tus clientes pueden escanearlo 
                    para votar por ti en los FAMER Awards 2026.
                </p>
                <div class="mt-4 flex flex-wrap gap-3 justify-center md:justify-start">
                    <a href="{{ $this->qrCodeUrl }}" download="qr-{{ $restaurant->slug }}.png" 
                       class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Descargar QR
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimir
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <span class="font-medium">URL directa:</span> 
                    <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $this->voteUrl }}</code>
                </p>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
        <h3 class="text-xl font-bold">Comparte y consigue mas votos!</h3>
        <p class="text-blue-100 mt-1">Invita a tus clientes a votar por tu restaurante</p>
        <div class="flex gap-3 mt-4">
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($this->shareUrl) }}" target="_blank" class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30">Facebook</a>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode($this->shareUrl) }}" target="_blank" class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30">Twitter</a>
        </div>
    </div>

    <!-- Technical FAQ for Owners -->
    <div class="bg-white rounded-xl shadow p-6" x-data="{ openFaq: null }">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Preguntas Frecuentes
        </h3>
        
        <div class="divide-y divide-gray-100">
            <div class="py-3">
                <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full flex justify-between items-center text-left">
                    <span class="font-medium text-gray-800">Como funciona el codigo QR?</span>
                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': openFaq === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openFaq === 1" x-collapse class="mt-2 text-sm text-gray-600">
                    Cuando tus clientes escanean el QR, van directamente a una pagina de votacion. Pueden votar una vez al mes sin necesidad de crear cuenta. Los votos se suman a tu ranking.
                </div>
            </div>
            
            <div class="py-3">
                <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full flex justify-between items-center text-left">
                    <span class="font-medium text-gray-800">Como se calculan los rankings?</span>
                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': openFaq === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openFaq === 2" x-collapse class="mt-2 text-sm text-gray-600">
                    Los rankings combinan: calificacion de Yelp (40%), calificacion de Google (40%), y votos de la comunidad FAMER (20%). Se actualizan periodicamente.
                </div>
            </div>
            
            <div class="py-3">
                <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full flex justify-between items-center text-left">
                    <span class="font-medium text-gray-800">Cada cuanto se actualizan las estadisticas?</span>
                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': openFaq === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openFaq === 3" x-collapse class="mt-2 text-sm text-gray-600">
                    Las estadisticas de votos se actualizan en tiempo real. Los rankings y calificaciones se recalculan semanalmente.
                </div>
            </div>
            
            <div class="py-3">
                <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full flex justify-between items-center text-left">
                    <span class="font-medium text-gray-800">Pueden votar varias veces desde el mismo telefono?</span>
                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': openFaq === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openFaq === 4" x-collapse class="mt-2 text-sm text-gray-600">
                    No, cada dispositivo puede votar una vez al mes por tu restaurante. Esto evita manipulacion y asegura votos genuinos de diferentes clientes.
                </div>
            </div>
            
            <div class="py-3">
                <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full flex justify-between items-center text-left">
                    <span class="font-medium text-gray-800">Donde puedo poner el QR?</span>
                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{ 'rotate-180': openFaq === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="openFaq === 5" x-collapse class="mt-2 text-sm text-gray-600">
                    Recomendamos colocar el QR en: mesas, menu, ventana de entrada, recibos, o en un cartel cerca de la caja. Puedes descargar e imprimir en cualquier tamano.
                </div>
            </div>
        </div>
    </div>
</div>
