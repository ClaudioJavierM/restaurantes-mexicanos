<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #111827;">
            {{ __('Mi Panel') }}
        </h2>
    </x-slot>

    @php
        $hasRestaurants = auth()->user()->restaurants()->exists();
        $myRestaurant = $hasRestaurants ? auth()->user()->restaurants()->first() : null;
        $favoritesCount = auth()->user()->favorites()->count();
        $upcomingReservations = auth()->user()->reservations()
            ->with('restaurant')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->limit(3)
            ->get();
        $recentOrders = auth()->user()->orders()
            ->with('restaurant')
            ->latest()
            ->limit(3)
            ->get();
        $recentReviews = auth()->user()->reviews()
            ->with('restaurant')
            ->latest()
            ->limit(3)
            ->get();
        $loyalty = \App\Models\LoyaltyPoints::getOrCreate(auth()->user()->id);
        $checkInsCount = \App\Models\CheckIn::where('user_id', auth()->user()->id)->count();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Hero Section --}}
            <div class="bg-gradient-to-r from-red-600 via-red-700 to-orange-600 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-8 md:p-12">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                                ¡Hola, {{ Auth::user()->name }}!
                            </h1>
                            @if($hasRestaurants)
                                <p class="text-xl text-white/90 max-w-2xl">
                                    Administra tu restaurante y hazlo crecer con las herramientas de FAMER.
                                </p>
                            @else
                                <p class="text-xl text-white/90 max-w-2xl">
                                    Descubre los mejores restaurantes mexicanos cerca de ti, guarda tus favoritos y vota por los mejores.
                                </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <div class="text-8xl">{{ $hasRestaurants ? '🚀' : '🌮' }}</div>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="mt-8 flex flex-wrap gap-4 justify-center md:justify-start">
                        @if($hasRestaurants)
                            <a href="/owner/my-restaurants"
                               class="inline-flex items-center px-6 py-3 bg-white text-red-700 font-bold rounded-xl shadow-lg hover:bg-gray-100 transition transform hover:scale-105">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Mi Restaurante
                            </a>
                        @endif
                        <a href="{{ route('restaurants.index') }}"
                           class="inline-flex items-center px-6 py-3 {{ $hasRestaurants ? 'bg-white/20 text-white border-2 border-white/30' : 'bg-white text-red-700' }} font-semibold rounded-xl hover:bg-white/30 transition">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Explorar Restaurantes
                        </a>
                        @if(!$hasRestaurants)
                            <a href="/my-favorites"
                               class="inline-flex items-center px-6 py-3 bg-white/20 text-white font-semibold rounded-xl border-2 border-white/30 hover:bg-white/30 transition">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                Mis Favoritos ({{ $favoritesCount }})
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($hasRestaurants)
                {{-- OWNER CONTENT --}}
                {{-- Stats Cards --}}
                @php
                    $monthlyViews = \App\Models\AnalyticsEvent::where('restaurant_id', $myRestaurant->id)
                        ->where('event_type', 'page_view')
                        ->where('created_at', '>=', now()->subDays(30))
                        ->count();

                    $userPlan = 'Free';
                    if($myRestaurant->is_premium) {
                        $userPlan = 'Premium';
                    } elseif($myRestaurant->is_claimed) {
                        $userPlan = 'Claimed';
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Vistas este mes</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($monthlyViews) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Estado</p>
                                <p class="text-2xl font-bold text-green-600">Activo</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Plan</p>
                                <p class="text-2xl font-bold" style="color: {{ $userPlan == 'Premium' ? '#eab308' : ($userPlan == 'Claimed' ? '#3b82f6' : '#6b7280') }};">{{ $userPlan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Descuentos para dueños --}}
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 overflow-hidden shadow-xl sm:rounded-2xl border border-amber-200">
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center px-4 py-2 bg-red-100 rounded-full mb-4">
                                <span class="text-red-600 font-semibold">🎁 Beneficio Exclusivo para Dueños</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 text-gray-900">
                                ¡Descuentos para tu restaurante!
                            </h3>
                            <p class="text-lg max-w-4xl mx-auto text-gray-700">
                                Descuentos exclusivos en <strong>muebles, equipo de cocina, decoración, food trucks</strong> y más.
                            </p>
                        </div>

                        <div class="border-t border-amber-200 pt-6">
                            <div class="flex flex-wrap justify-center gap-3">
                                <a href="https://mf-imports.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-white rounded-lg border border-gray-200 hover:border-red-500 hover:shadow-md transition-all text-sm text-gray-700">
                                    <span class="mr-2">🪑</span> MF Imports
                                </a>
                                <a href="https://tormexpro.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-white rounded-lg border border-gray-200 hover:border-blue-500 hover:shadow-md transition-all text-sm text-gray-700">
                                    <span class="mr-2">🫓</span> Tormex Pro
                                </a>
                                <a href="https://mftrailers.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-white rounded-lg border border-gray-200 hover:border-orange-500 hover:shadow-md transition-all text-sm text-gray-700">
                                    <span class="mr-2">🚚</span> MF Trailers
                                </a>
                                <a href="https://mueblesmexicanos.com" target="_blank" class="inline-flex items-center px-4 py-2 bg-white rounded-lg border border-gray-200 hover:border-yellow-500 hover:shadow-md transition-all text-sm text-gray-700">
                                    <span class="mr-2">🛋️</span> Muebles Mexicanos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- CUSTOMER CONTENT --}}

                {{-- Quick Actions for Customers --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="/votar" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-amber-100 rounded-full group-hover:bg-amber-200 transition">
                                <span class="text-3xl">🏆</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Vota por los Mejores</h3>
                        <p class="text-sm text-gray-600">Ayuda a elegir los mejores restaurantes mexicanos en los FAMER Awards 2026.</p>
                    </a>

                    <a href="/my-favorites" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-red-100 rounded-full group-hover:bg-red-200 transition">
                                <span class="text-3xl">❤️</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">Mis Favoritos</h3>
                        <p class="text-sm text-gray-600">Tienes {{ $favoritesCount }} restaurantes guardados. ¡Descubre más!</p>
                    </a>

                    <a href="/famer-awards" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-yellow-100 rounded-full group-hover:bg-yellow-200 transition">
                                <span class="text-3xl">⭐</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-2">FAMER Awards</h3>
                        <p class="text-sm text-gray-600">Conoce los restaurantes mejor calificados del año.</p>
                    </a>
                </div>

                {{-- Activity Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Próximas reservaciones --}}
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <span>🗓️</span> Reservaciones
                            </h3>
                            <a href="/my-reservations" class="text-xs text-blue-600 hover:underline font-medium">Ver todas</a>
                        </div>
                        @if($upcomingReservations->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingReservations as $res)
                                    <div class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $res->restaurant->name ?? 'Restaurante' }}</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($res->reservation_date)->format('d M') }} · {{ $res->reservation_time }} · {{ $res->party_size }} pers.</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Sin reservaciones próximas</p>
                            <a href="/restaurantes" class="mt-3 inline-block text-xs text-blue-600 hover:underline">Buscar restaurante</a>
                        @endif
                    </div>

                    {{-- Pedidos recientes --}}
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <span>🛍️</span> Pedidos
                            </h3>
                            <a href="/my-orders" class="text-xs text-green-600 hover:underline font-medium">Ver todos</a>
                        </div>
                        @if($recentOrders->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentOrders as $order)
                                    <div class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-green-400 mt-1.5 flex-shrink-0"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $order->restaurant->name ?? 'Restaurante' }}</p>
                                            <p class="text-xs text-gray-500">${{ number_format($order->total, 2) }} · {{ $order->status_label }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Sin pedidos recientes</p>
                            <a href="/restaurantes" class="mt-3 inline-block text-xs text-green-600 hover:underline">Explorar menús</a>
                        @endif
                    </div>

                    {{-- Reseñas recientes --}}
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <span>⭐</span> Mis Reseñas
                            </h3>
                            <a href="/my-reviews" class="text-xs text-amber-600 hover:underline font-medium">Ver todas</a>
                        </div>
                        @if($recentReviews->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentReviews as $rev)
                                    <div class="flex items-start gap-2">
                                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 flex-shrink-0"></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $rev->restaurant->name ?? 'Restaurante' }}</p>
                                            <p class="text-xs text-gray-500">{{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }} · {{ $rev->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 italic">Aún no has escrito reseñas</p>
                            <a href="/restaurantes" class="mt-3 inline-block text-xs text-amber-600 hover:underline">Escribir reseña</a>
                        @endif
                    </div>

                </div>

                {{-- Loyalty & Check-ins --}}
                @php
                    $loyaltyLevels = \App\Models\LoyaltyPoints::LEVELS;
                    $currentLevel  = $loyalty->level ?? 'bronce';
                    $currentData   = $loyaltyLevels[$currentLevel];
                    $nextLevelKey  = null;
                    $nextLevelData = null;
                    $levelKeys = array_keys($loyaltyLevels);
                    $levelIdx  = array_search($currentLevel, $levelKeys);
                    if ($levelIdx !== false && isset($levelKeys[$levelIdx + 1])) {
                        $nextLevelKey  = $levelKeys[$levelIdx + 1];
                        $nextLevelData = $loyaltyLevels[$nextLevelKey];
                    }
                    $progressPct = $nextLevelData
                        ? min(100, round(($loyalty->points - $currentData['min']) / ($nextLevelData['min'] - $currentData['min']) * 100))
                        : 100;
                @endphp
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-500 to-amber-500 px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                🏅 Mi Nivel FAMER
                            </h3>
                            <p class="text-yellow-100 text-sm">Gana puntos visitando restaurantes y escribiendo reseñas</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black text-white">{{ number_format($loyalty->points) }}</p>
                            <p class="text-yellow-100 text-xs">puntos</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-black uppercase tracking-wide" style="color: {{ $currentData['color'] }}">
                                    {{ ucfirst($currentLevel) }}
                                </span>
                                <span class="text-sm text-gray-600">{{ $currentData['discount'] }}% descuento</span>
                            </div>
                            @if($nextLevelKey)
                                <span class="text-xs text-gray-500">
                                    {{ number_format($nextLevelData['min'] - $loyalty->points) }} pts para {{ ucfirst($nextLevelKey) }}
                                </span>
                            @else
                                <span class="text-xs text-amber-600 font-semibold">¡Nivel máximo! 🏆</span>
                            @endif
                        </div>
                        <!-- Progress bar -->
                        <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden mb-4">
                            <div class="h-full rounded-full bg-gradient-to-r from-yellow-400 to-amber-500 transition-all duration-700"
                                 style="width: {{ $progressPct }}%"></div>
                        </div>
                        <!-- Stats row -->
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xl font-bold text-gray-900">{{ $checkInsCount }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">📍 Check-ins</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xl font-bold text-gray-900">{{ $loyalty->total_reviews ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">⭐ Reseñas</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xl font-bold text-gray-900">{{ $favoritesCount }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">❤️ Favoritos</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA for Restaurant Owners --}}
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-8 md:p-10">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="text-center md:text-left">
                                <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">
                                    ¿Eres dueño de un restaurante mexicano?
                                </h3>
                                <p class="text-lg text-white/90 max-w-xl">
                                    Reclama tu restaurante gratis y accede a herramientas para hacer crecer tu negocio: estadísticas, menú digital, cupones y más.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="/claim"
                                   class="inline-flex items-center px-8 py-4 bg-white text-green-700 font-bold rounded-xl shadow-lg hover:bg-gray-100 transition transform hover:scale-105 text-lg">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Reclamar Mi Restaurante
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Explore Section --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-gray-900">
                            <span class="text-2xl mr-3">🌮</span>
                            Descubre restaurantes cerca de ti
                        </h3>

                        <div class="grid md:grid-cols-3 gap-6">
                            <a href="/restaurantes?type=taqueria" class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <span class="text-4xl">🌮</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Taquerías</h4>
                                    <p class="text-sm text-gray-500">Los mejores tacos de la ciudad</p>
                                </div>
                            </a>

                            <a href="/restaurantes?type=mariscos" class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <span class="text-4xl">🦐</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Mariscos</h4>
                                    <p class="text-sm text-gray-500">Ceviches, aguachiles y más</p>
                                </div>
                            </a>

                            <a href="/restaurantes?type=birria" class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <span class="text-4xl">🍖</span>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Birria</h4>
                                    <p class="text-sm text-gray-500">Quesabirria y consomé</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
