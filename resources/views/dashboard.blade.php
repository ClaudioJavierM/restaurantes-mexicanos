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
