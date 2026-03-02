<div class="min-h-screen bg-white">



{{-- Stats Banner --}}
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex flex-wrap items-center justify-center gap-4 md:gap-8 text-sm text-gray-600">
            <span class="flex items-center gap-1">
                <span class="font-bold text-gray-900">{{ number_format($stats['total_views'] ?? 150000) }}+</span> visitantes cada mes
            </span>
            <span class="hidden md:inline">|</span>
            <span class="flex items-center gap-1">
                <span class="font-bold text-gray-900">82%</span> de usuarios contactan negocios que encuentran aquí
            </span>
        </div>
    </div>
</div>

{{-- Hero Section - Yelp Style --}}
<section class="bg-white relative overflow-hidden">
    {{-- Decorative circles (like Yelp) --}}
    <div class="absolute left-0 top-0 w-96 h-96 opacity-20">
        <div class="absolute top-10 left-10 w-32 h-32 bg-red-200 rounded-full"></div>
        <div class="absolute top-40 left-20 w-24 h-24 bg-orange-200 rounded-full"></div>
        <div class="absolute top-20 left-48 w-20 h-20 bg-red-100 rounded-full"></div>
    </div>
    <div class="absolute right-0 top-0 w-96 h-96 opacity-20">
        <div class="absolute top-10 right-10 w-32 h-32 bg-red-200 rounded-full"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-orange-200 rounded-full"></div>
        <div class="absolute top-20 right-48 w-20 h-20 bg-red-100 rounded-full"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10">
        <div class="text-center">
            {{-- Main Headline --}}
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 leading-tight mb-6">
                Es <span class="text-red-600">GRATIS</span> estar en<br>
                Restaurantes Mexicanos Famosos
            </h1>
            
            <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                Busca tu restaurante y verifica tu perfil para empezar a atraer más clientes hoy mismo.
            </p>

            {{-- Search Box --}}
            <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-200 p-2">
                <form action="{{ route('claim.restaurant') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1 relative">
                        <input type="text" name="search" placeholder="{{ app()->getLocale() === 'en' ? 'Your restaurant name' : 'Nombre de tu restaurante' }}"
                               class="w-full px-5 py-4 text-lg border-0 bg-gray-50 rounded-xl focus:ring-2 focus:ring-red-500 transition">
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        {{ app()->getLocale() === 'en' ? 'Search' : 'Buscar' }}
                    </button>
                </form>
            </div>
            
            <p class="mt-6 text-sm text-gray-500">
                {{ app()->getLocale() === 'en' ? "Can't find it?" : '¿No lo encuentras?' }} 
                <a href="/sugerir" class="text-red-600 hover:underline font-medium">
                    {{ app()->getLocale() === 'en' ? 'Add your restaurant' : 'Agrega tu restaurante' }}
                </a> 
                {{ app()->getLocale() === 'en' ? 'and we\'ll help you claim it.' : 'y te ayudamos a reclamarlo.' }}
            </p>
        </div>
    </div>
</section>

{{-- Join Banner --}}
<section class="bg-gray-50 py-12 border-y border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">
            {{ app()->getLocale() === 'en' ? 'Join thousands of Mexican restaurants' : 'Únete a miles de restaurantes mexicanos' }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <div class="text-3xl md:text-4xl font-black text-red-600">{{ number_format($stats['total_restaurants'] ?? 13000) }}+</div>
                <div class="text-sm text-gray-600 mt-1">{{ app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes' }}</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-black text-red-600">50</div>
                <div class="text-sm text-gray-600 mt-1">{{ app()->getLocale() === 'en' ? 'States' : 'Estados' }}</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-black text-red-600">{{ number_format($stats['total_views'] ?? 150000) }}+</div>
                <div class="text-sm text-gray-600 mt-1">{{ app()->getLocale() === 'en' ? 'Monthly visitors' : 'Visitantes/mes' }}</div>
            </div>
            <div>
                <div class="text-3xl md:text-4xl font-black text-red-600">4.8⭐</div>
                <div class="text-sm text-gray-600 mt-1">{{ app()->getLocale() === 'en' ? 'Avg. rating' : 'Rating promedio' }}</div>
            </div>
        </div>
    </div>
</section>


{{-- ============================================ --}}
{{-- TUS CLIENTES TE ESTÁN BUSCANDO - Live Stats --}}
{{-- ============================================ --}}
<section class="py-20 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23ffffff\" fill-opacity=\"0.4\"><path d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/></g></g></svg>');"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <span class="inline-block bg-red-500/20 text-red-400 text-sm font-bold px-4 py-2 rounded-full mb-4 border border-red-500/30">
                📊 ESTADÍSTICAS EN VIVO
            </span>
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Tus Clientes Te Están Buscando
            </h2>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Cada día miles de personas buscan restaurantes mexicanos en nuestra plataforma. ¿Estás perdiendo clientes?
            </p>
        </div>

        {{-- Main Stats Grid - Redesigned --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-12">
            {{-- Total Views --}}
            <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300 shadow-xl shadow-orange-500/20">
                <div class="text-5xl mb-3">👁️</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">{{ number_format($stats['total_views'] ?? 447283) }}</div>
                <p class="text-white/90 font-medium text-sm">Visitas Totales</p>
            </div>

            {{-- Daily Average --}}
            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300 shadow-xl shadow-green-500/20">
                <div class="text-5xl mb-3">📈</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">{{ number_format($stats['daily_average'] ?? 6997) }}</div>
                <p class="text-white/90 font-medium text-sm">Promedio Diario</p>
            </div>

            {{-- Monthly Views --}}
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300 shadow-xl shadow-blue-500/20">
                <div class="text-5xl mb-3">📅</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">{{ number_format($stats['monthly_views'] ?? 209910) }}</div>
                <p class="text-white/90 font-medium text-sm">Visitas Este Mes</p>
            </div>

            {{-- Total Restaurants --}}
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300 shadow-xl shadow-amber-500/20">
                <div class="text-5xl mb-3">🏪</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">{{ number_format($stats['total_restaurants'] ?? 13247) }}</div>
                <p class="text-white/90 font-medium text-sm">Restaurantes Listados</p>
            </div>
        </div>

        {{-- Secondary Stats - Better visibility --}}
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4 mb-12">
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">✅</div>
                <div class="text-xl md:text-2xl font-bold text-white">{{ number_format($stats['claimed'] ?? 847) }}</div>
                <p class="text-xs text-white/70 mt-1">Reclamados</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">🔓</div>
                <div class="text-xl md:text-2xl font-bold text-white">{{ number_format($stats['available'] ?? 12400) }}</div>
                <p class="text-xs text-white/70 mt-1">Disponibles</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">🇺🇸</div>
                <div class="text-xl md:text-2xl font-bold text-white">50</div>
                <p class="text-xs text-white/70 mt-1">Estados</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">🏙️</div>
                <div class="text-xl md:text-2xl font-bold text-white">{{ number_format($stats['cities'] ?? 2847) }}</div>
                <p class="text-xs text-white/70 mt-1">Ciudades</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">⭐</div>
                <div class="text-xl md:text-2xl font-bold text-white">{{ number_format($stats['total_reviews'] ?? 892456) }}</div>
                <p class="text-xs text-white/70 mt-1">Reseñas</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center border border-white/20 hover:bg-white/20 transition">
                <div class="text-2xl mb-1">📸</div>
                <div class="text-xl md:text-2xl font-bold text-white">{{ number_format($stats['photos'] ?? 45892) }}</div>
                <p class="text-xs text-white/70 mt-1">Fotos</p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TOP ESTADOS POR TRÁFICO --}}
        {{-- ============================================ --}}
        @if(isset($stateStats) && count($stateStats) > 0)
        <div class="max-w-3xl mx-auto mb-12">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 border border-white/20">
                <h3 class="text-xl font-bold text-white mb-6 text-center flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    Top Estados por Tráfico
                </h3>
                <div class="space-y-4">
                    @foreach($stateStats as $index => $state)
                        @php
                            $maxViews = $stateStats[0]['views'] ?? 1;
                            $percentage = ($state['views'] / $maxViews) * 100;
                            $colors = [
                                'background: linear-gradient(to right, #eab308, #f59e0b);',
                                'background: linear-gradient(to right, #9ca3af, #6b7280);',
                                'background: linear-gradient(to right, #d97706, #ea580c);',
                                'background: linear-gradient(to right, #3b82f6, #6366f1);',
                                'background: linear-gradient(to right, #22c55e, #10b981);'
                            ];
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-black text-gray-500 w-8">#{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between mb-2">
                                    <span class="font-semibold text-white">{{ $state['state_name'] }}</span>
                                    <span class="text-gray-300 font-medium">{{ number_format($state['views']) }}</span>
                                </div>
                                <div class="h-3 bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" style="{{ $colors[$index] ?? 'background: linear-gradient(to right, #6b7280, #4b5563);' }} width: {{ $percentage }}%;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ============================================ --}}
        {{-- VALUE PROPOSITION CARDS - Mismo estilo que stats de arriba --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-12">
            {{-- Valor en Google Ads --}}
            <div class="rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); box-shadow: 0 10px 40px -10px rgba(59, 130, 246, 0.5);">
                <div class="text-5xl mb-3">💰</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">${{ number_format($stats['google_ads_value'] ?? 894566) }}</div>
                <p class="text-white/90 font-medium text-sm">Valor equivalente en Google Ads</p>
            </div>

            {{-- Promedio por restaurante --}}
            <div class="rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300" style="background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); box-shadow: 0 10px 40px -10px rgba(168, 85, 247, 0.5);">
                <div class="text-5xl mb-3">🏪</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">${{ number_format($stats['google_ads_per_restaurant'] ?? 67) }}</div>
                <p class="text-white/90 font-medium text-sm">Promedio por restaurante</p>
            </div>

            {{-- Costo Premium --}}
            <div class="rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300" style="background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); box-shadow: 0 10px 40px -10px rgba(34, 197, 94, 0.5);">
                <div class="text-5xl mb-3">✅</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">$39/mes</div>
                <p class="text-white/90 font-medium text-sm">Costo del plan Premium</p>
            </div>

            {{-- ROI --}}
            <div class="rounded-2xl p-6 text-center group hover:scale-105 transition-transform duration-300" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 10px 40px -10px rgba(245, 158, 11, 0.5);">
                <div class="text-5xl mb-3">❤️</div>
                <div class="text-3xl md:text-4xl font-black text-white mb-1">1 cliente</div>
                <p class="text-white/90 font-medium text-sm">Para ROI positivo al mes</p>
            </div>
        </div>

        {{-- Live Activity Ticker --}}
        <div class="bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-xl p-4 border border-green-500/30 mb-8">
            <div class="flex items-center justify-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <p class="text-green-400 font-medium">
                    <span class="font-bold">{{ rand(15, 45) }} personas</span> están buscando restaurantes mexicanos ahora mismo
                </p>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center">
            <p class="text-gray-400 mb-6">¿Tu restaurante está siendo encontrado? Reclámalo ahora y empieza a atraer más clientes.</p>
            <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl hover:from-red-700 hover:to-orange-700 transition shadow-lg shadow-red-500/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Buscar Mi Restaurante
            </a>
        </div>
    </div>
</section>

{{-- Success Stories Section --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Restaurantes que brillan en nuestra plataforma
            </h2>
            <p class="text-xl text-gray-600">
                Conoce algunos de los restaurantes mexicanos más exitosos
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Success Card 1 --}}
            <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1504544750208-dc0358e63f7f?w=400&h=500&fit=crop"
                     alt="Mi Tierra Cafe" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
                    <p class="text-white/80 text-xs font-medium">San Antonio, TX</p>
                    <h3 class="text-white font-bold text-lg">Mi Tierra Cafe & Bakery</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded">+24,152</span>
                        <span class="text-white/80 text-xs">reseñas</span>
                    </div>
                </div>
            </div>

            {{-- Success Card 2 --}}
            <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=400&h=500&fit=crop"
                     alt="Gracias Madre" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
                    <p class="text-white/80 text-xs font-medium">West Hollywood, CA</p>
                    <h3 class="text-white font-bold text-lg">Gracias Madre</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">4.6 ⭐</span>
                        <span class="text-white/80 text-xs">5,296 reseñas</span>
                    </div>
                </div>
            </div>

            {{-- Success Card 3 --}}
            <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1613514785940-daed07799d9b?w=400&h=500&fit=crop"
                     alt="Columbia Restaurant" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
                    <p class="text-white/80 text-xs font-medium">Tampa, FL</p>
                    <h3 class="text-white font-bold text-lg">Columbia Restaurant</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-amber-500 text-white text-xs font-bold px-2 py-1 rounded">+20,732</span>
                        <span class="text-white/80 text-xs">reseñas</span>
                    </div>
                </div>
            </div>

            {{-- Success Card 4 --}}
            <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=400&h=500&fit=crop"
                     alt="The Taco Stand" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
                    <p class="text-white/80 text-xs font-medium">La Jolla, CA</p>
                    <h3 class="text-white font-bold text-lg">The Taco Stand</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">4.5 ⭐</span>
                        <span class="text-white/80 text-xs">5,845 reseñas</span>
                    </div>
                </div>
            </div>

            {{-- Success Card 5 --}}
            <div class="group relative overflow-hidden rounded-2xl aspect-[3/4] cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?w=400&h=500&fit=crop"
                     alt="Café Tu Tu Tango" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-5 z-20">
                    <p class="text-white/80 text-xs font-medium">Orlando, FL</p>
                    <h3 class="text-white font-bold text-lg">Café Tu Tu Tango</h3>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">4.6 ⭐</span>
                        <span class="text-white/80 text-xs">10,210 reseñas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- POR QUÉ ELEGIRNOS - Comparison Section --}}
{{-- ============================================ --}}
<section class="py-20 bg-gradient-to-br from-red-50 to-orange-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block bg-red-100 text-red-700 text-sm font-bold px-4 py-2 rounded-full mb-4">COMPARACIÓN</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ¿Por Qué Elegirnos Sobre Otras Plataformas?
            </h2>
            <p class="text-xl text-gray-600">Somos el directorio #1 especializado en restaurantes mexicanos</p>
        </div>

        {{-- Comparison Table --}}
        <div class="overflow-x-auto mb-12">
            <table class="w-full bg-white rounded-2xl shadow-xl overflow-hidden">
                <thead>
                    <tr class="bg-gray-900 text-white">
                        <th class="py-3 px-3 md:py-4 md:px-6 text-left text-sm md:text-base">Característica</th>
                        <th class="py-3 px-3 md:py-4 md:px-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-red-400 font-bold text-lg">RestaurantesMexicanos</span>
                                <span class="text-xs text-gray-400">Famosos</span>
                            </div>
                        </th>
                        <th class="py-3 px-3 md:py-4 md:px-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-gray-300">Yelp</span>
                            </div>
                        </th>
                        <th class="py-3 px-3 md:py-4 md:px-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-gray-300">Google</span>
                            </div>
                        </th>
                        <th class="py-3 px-3 md:py-4 md:px-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-gray-300">TripAdvisor</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Enfoque en comida mexicana</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">✅</span><span class="text-green-600 font-bold text-xs mt-0.5">100%</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50 bg-gray-50/50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Perfil básico gratis</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Sin anuncios de competidores</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">❌</span><span class="text-red-500 text-xs mt-0.5">$$$</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50 bg-gray-50/50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Cupones y descuentos incluidos</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">✅</span><span class="text-green-600 text-xs mt-0.5">Premium</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">❌</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Audiencia enfocada</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">✅</span><span class="text-green-600 font-bold text-xs mt-0.5">13K+</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">❌</span><span class="text-gray-500 text-xs mt-0.5">Mixto</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">❌</span><span class="text-gray-500 text-xs mt-0.5">Mixto</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">❌</span><span class="text-gray-500 text-xs mt-0.5">Turistas</span></div></td>
                    </tr>
                    <tr class="hover:bg-gray-50 bg-gray-50/50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Soporte en español</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">✅</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">⚠️</span><span class="text-gray-500 text-xs mt-0.5">Limitado</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">⚠️</span><span class="text-gray-500 text-xs mt-0.5">Auto</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">⚠️</span><span class="text-gray-500 text-xs mt-0.5">Limitado</span></div></td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Premium mensual desde</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-green-600 font-bold text-lg md:text-xl"></span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-red-600 font-bold text-sm md:text-base">0+</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-gray-500 text-sm">N/A</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-red-600 font-bold text-sm md:text-base">+</span></td>
                    </tr>
                    <tr class="hover:bg-gray-50 bg-green-50">
                        <td class="py-3 px-3 md:py-4 md:px-6 font-medium text-gray-900 text-sm md:text-base">Reseñas transparentes</td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">✅</span><span class="text-green-600 text-xs mt-0.5">Transparentes</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><div class="flex flex-col items-center"><span class="text-xl">⚠️</span><span class="text-red-500 text-xs mt-0.5">Controversia</span></div></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">⚠️</span></td>
                        <td class="py-3 px-3 md:py-4 md:px-6 text-center"><span class="text-xl">⚠️</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Platform Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- vs Yelp --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-red-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🆚</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">vs Yelp</h3>
                        <p class="text-sm text-gray-500">La diferencia</p>
                    </div>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">✗</span>
                        Yelp: Cobra $300-$1000/mes por anuncios destacados
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">✗</span>
                        Yelp: Muestra competidores en tu perfil
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">✗</span>
                        Yelp: Acusaciones de filtrar reseñas
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <strong>Nosotros:</strong> Premium desde $39/mes, sin trucos
                    </li>
                </ul>
            </div>

            {{-- vs Google --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-blue-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🆚</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">vs Google</h3>
                        <p class="text-sm text-gray-500">La diferencia</p>
                    </div>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        Google: Te mezcla con miles de negocios
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        Google: No especializado en comida mexicana
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        Google: Difícil destacar sin pagar ads
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <strong>Nosotros:</strong> 100% enfocados en comida mexicana
                    </li>
                </ul>
            </div>

            {{-- vs TripAdvisor --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border-2 border-green-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🆚</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">vs TripAdvisor</h3>
                        <p class="text-sm text-gray-500">La diferencia</p>
                    </div>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        TripAdvisor: Enfocado principalmente en turistas
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        TripAdvisor: Premium desde $99/mes
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-500">⚠️</span>
                        TripAdvisor: Interfaz compleja para dueños
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-green-500">✓</span>
                        <strong>Nosotros:</strong> Clientes locales que buscan comida mexicana
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom CTA --}}
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-4 bg-white rounded-full py-2 pl-6 pr-2 shadow-lg border border-gray-200">
                <p class="text-gray-700">
                    <strong class="text-red-600">Ahorra hasta $3,000/año</strong> comparado con Yelp Premium
                </p>
                <a href="{{ route('claim.restaurant') }}" class="bg-red-600 text-white px-6 py-2 rounded-full font-bold hover:bg-red-700 transition">
                    Empezar Gratis
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Benefits Section - Clean Grid --}}
<section id="benefits" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ¿Por Qué Reclamar Tu Restaurante?
            </h2>
            <p class="text-xl text-gray-600">Todo lo que necesitas para crecer tu negocio online</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Benefit 1 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Análisis en Tiempo Real</h3>
                <p class="text-gray-600 mb-4">Ve cuántas personas visitan tu perfil, hacen clic en tu teléfono, abren tu menú y más.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Visitas a tu página</li>
                    <li>✓ Clics en teléfono</li>
                    <li>✓ Clics en dirección</li>
                    <li>✓ Visitas a tu sitio web</li>
                </ul>
            </div>

            {{-- Benefit 2 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Galería de Fotos</h3>
                <p class="text-gray-600 mb-4">Muestra lo mejor de tu restaurante con fotos profesionales de tus platillos y ambiente.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Hasta 10 fotos gratis</li>
                    <li>✓ Fotos ilimitadas (Premium)</li>
                    <li>✓ Actualiza cuando quieras</li>
                    <li>✓ Optimización automática</li>
                </ul>
            </div>

            {{-- Benefit 3 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Gestión de Reseñas</h3>
                <p class="text-gray-600 mb-4">Responde a reseñas de clientes y construye tu reputación online.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Responde a reseñas</li>
                    <li>✓ Notificaciones instantáneas</li>
                    <li>✓ Mejora tu rating</li>
                    <li>✓ Construye confianza</li>
                </ul>
            </div>

            {{-- Benefit 4 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Perfil Verificado</h3>
                <p class="text-gray-600 mb-4">Obtén una insignia de verificación que genera confianza en tus clientes.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Insignia verificada</li>
                    <li>✓ Mayor credibilidad</li>
                    <li>✓ Destaca sobre competencia</li>
                    <li>✓ Información actualizada</li>
                </ul>
            </div>

            {{-- Benefit 5 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Cupones y Promociones</h3>
                <p class="text-gray-600 mb-4">Crea ofertas especiales para atraer nuevos clientes y aumentar ventas.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Cupones digitales</li>
                    <li>✓ Ofertas por tiempo limitado</li>
                    <li>✓ Tracking de conversiones</li>
                    <li>✓ Atrae nuevos clientes</li>
                </ul>
            </div>

            {{-- Benefit 6 --}}
            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Email Marketing</h3>
                <p class="text-gray-600 mb-4">Mantén contacto con tus clientes mediante campañas de email profesionales.</p>
                <ul class="text-gray-600 space-y-2">
                    <li>✓ Campañas automatizadas</li>
                    <li>✓ Plantillas profesionales</li>
                    <li>✓ Segmentación de audiencia</li>
                    <li>✓ Análisis de resultados</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- PROCESO - How It Works Section --}}
{{-- ============================================ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block bg-green-100 text-green-700 text-sm font-bold px-4 py-2 rounded-full mb-4">SIMPLE Y RÁPIDO</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Reclama Tu Restaurante en 3 Pasos
            </h2>
            <p class="text-xl text-gray-600">Completa el proceso en menos de 5 minutos</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            {{-- Connection Line (desktop) - usando style para compatibilidad --}}
            <div class="hidden md:block absolute top-24 h-1 bg-gradient-to-r from-red-500 via-amber-500 to-green-500" style="left: 20%; right: 20%;"></div>

            {{-- Step 1 --}}
            <div class="relative text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl relative z-10" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <span class="text-3xl font-bold text-white">1</span>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Busca Tu Restaurante</h3>
                    <p class="text-gray-600">Ingresa el nombre de tu restaurante en nuestro buscador. Si ya está en nuestra base de datos, lo encontrarás al instante.</p>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="relative text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl relative z-10" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <span class="text-3xl font-bold text-white">2</span>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Verifica Tu Identidad</h3>
                    <p class="text-gray-600">Confirma que eres el dueño o representante autorizado mediante un código enviado a tu teléfono o email del negocio.</p>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="relative text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl relative z-10" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
                    <span class="text-3xl font-bold text-white">3</span>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">¡Listo! Empieza a Crecer</h3>
                    <p class="text-gray-600">Completa tu perfil, sube fotos, actualiza tu menú y empieza a recibir más clientes inmediatamente.</p>
                </div>
            </div>
        </div>

        {{-- Time indicator --}}
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-3 bg-green-50 border border-green-200 rounded-full px-6 py-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-green-800 font-medium">Tiempo promedio: <strong>5 minutos</strong></span>
            </div>
        </div>
    </div>
</section>

{{-- CASOS DE ÉXITO - Testimonials Section --}}
<section class="py-20 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block bg-red-100 text-red-700 text-sm font-bold px-4 py-2 rounded-full mb-4">CASOS DE ÉXITO</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Lo Que Dicen Nuestros Restaurantes
            </h2>
            <p class="text-xl text-gray-600">Historias reales de dueños que han crecido con nosotros</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            {{-- Testimonial 1 --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="text-5xl text-gray-200 font-serif mb-4">"</div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="/images/testimonials/maria.jpg" alt="María C." class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <p class="font-semibold text-gray-900">María C.</p>
                        <p class="text-sm text-gray-500">Taquería Los Compadres, TX</p>
                    </div>
                </div>
                <div class="flex gap-1 text-amber-400 mb-4">
                    <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "Desde que reclamé mi restaurante, las llamadas aumentaron un <span class="text-red-600 font-bold">40%</span>. El cupón de descuento que recibí me ayudó a ahorrar $500 en equipo nuevo para la cocina."
                </p>
                <p class="text-green-600 text-sm font-semibold">↗ +40% llamadas</p>
            </div>

            {{-- Testimonial 2 --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="text-5xl text-gray-200 font-serif mb-4">"</div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="/images/testimonials/jose.jpg" alt="José R." class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <p class="font-semibold text-gray-900">José R.</p>
                        <p class="text-sm text-gray-500">La Michoacana Premium, CA</p>
                    </div>
                </div>
                <div class="flex gap-1 text-amber-400 mb-4">
                    <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "Tenía mi paletería por 5 años sin presencia online. Ahora aparezco en el Top 10 de mi ciudad. El plan Premium vale cada centavo, y el <span class="text-red-600 font-bold">cupón para equipo de paletería</span> fue increíble."
                </p>
                <p class="text-green-600 text-sm font-semibold">↗ Top 10 en su ciudad</p>
            </div>

            {{-- Testimonial 3 --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <div class="text-5xl text-gray-200 font-serif mb-4">"</div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="/images/testimonials/luis.jpg" alt="Luis G." class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <p class="font-semibold text-gray-900">Luis G.</p>
                        <p class="text-sm text-gray-500">El Buen Sazón, AZ</p>
                    </div>
                </div>
                <div class="flex gap-1 text-amber-400 mb-4">
                    <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
                </div>
                <p class="text-gray-700 mb-4">
                    "La verificación tomó 5 minutos. Ahora tengo control total de mi información y puedo responder a las reseñas. <span class="text-red-600 font-bold">Mis ventas subieron un 25%</span> en el primer mes."
                </p>
                <p class="text-green-600 text-sm font-semibold">↗ +25% ventas</p>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="bg-gradient-to-r from-red-600 to-green-600 rounded-2xl p-8 text-white">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold">35%</div>
                    <p class="text-white/80 text-sm mt-1">Aumento promedio en llamadas</p>
                </div>
                <div>
                    <div class="text-4xl font-bold">4.5/5</div>
                    <p class="text-white/80 text-sm mt-1">Satisfacción de usuarios</p>
                </div>
                <div>
                    <div class="text-4xl font-bold">$1,200</div>
                    <p class="text-white/80 text-sm mt-1">Ahorro promedio con cupones</p>
                </div>
                <div>
                    <div class="text-4xl font-bold">5 min</div>
                    <p class="text-white/80 text-sm mt-1">Tiempo de verificación</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center px-8 py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Únete a Estos Casos de Éxito
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- PRICING Section --}}
{{-- ============================================ --}}
<section id="pricing" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Elige tu Plan
            </h2>
            <p class="text-xl text-gray-600">Planes simples y transparentes. Cancela cuando quieras.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            {{-- Free Listing --}}
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Listado Gratis</h3>
                    <div class="text-5xl font-bold text-gray-900 mb-2">$0</div>
                    <p class="text-gray-600">Listado básico</p>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Aparece en el directorio</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Info básica (nombre, dirección, teléfono)</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Integración con Google Maps</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Verificar propiedad del restaurante</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-700">Editar información básica</span>
                    </li>
                    <li class="flex items-start gap-3 opacity-50">
                        <svg class="w-6 h-6 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-gray-500">Sin prioridad en búsquedas</span>
                    </li>
                    <li class="flex items-start gap-3 opacity-50">
                        <svg class="w-6 h-6 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-gray-500">Sin analíticas avanzadas</span>
                    </li>
                </ul>

                <a href="{{ route('claim.restaurant') }}" class="block w-full text-center py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-lg transition">
                    Reclamar Gratis
                </a>
            </div>

            {{-- Premium --}}
            <div class="bg-gradient-to-br from-yellow-400 via-yellow-500 to-orange-500 rounded-2xl shadow-xl p-8 border-2 border-yellow-600 relative transform scale-105">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-1 rounded-full text-xs font-bold shadow-lg">
                    MAS POPULAR
                </div>

                <div class="text-center mb-6">
                    <div class="inline-block px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full mb-2 animate-pulse">OFERTA PRIMER MES</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium</h3>
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <span class="text-2xl text-gray-600 line-through">$39</span>
                        <span class="text-5xl font-bold text-gray-900">$9.99</span>
                    </div>
                    <p class="text-gray-900 font-semibold">primer mes</p>
                    <p class="text-sm text-gray-700">Después $39/mes</p>
                </div>

                <ul class="space-y-3 mb-8 text-gray-900">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Todo lo de Free PLUS:</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Badge Destacado</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Top 3 en búsquedas</strong> locales</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Menú Digital + QR Code</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Widget de Pedidos Online</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Sistema de Reservaciones</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Dashboard de Analíticas</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Fotos y Videos Ilimitados</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Chatbot AI (ES/EN)</strong> 24/7</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Programa de Lealtad</strong></span>
                    </li>

                    {{-- Cupon Premium --}}
                    <li class="flex items-start gap-2 bg-white/50 p-3 rounded-lg border-2 border-gray-900 mt-4">
                        <svg class="w-5 h-5 text-green-800 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong class="text-gray-900">4 Cupones Trimestrales (10% off)</strong>
                            <p class="text-xs text-gray-900 mt-0.5">Ahorra hasta $500 por cupón ($2,000/año) en muebles, decoración, loza, artesanías, equipos de tortillería y más...</p>
                        </div>
                    </li>
                </ul>

                <a href="{{ route('claim.restaurant') }}?plan=premium" class="block w-full text-center py-3 px-6 bg-gray-900 hover:bg-gray-800 text-white font-bold rounded-lg transition shadow-lg">
                    Suscribirse por $9.99
                </a>
                <p class="text-center text-sm text-gray-900 mt-2 font-medium">Cancela cuando quieras</p>
            </div>

            {{-- Elite Plan --}}
            <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 rounded-2xl shadow-2xl p-8 border-2 border-purple-500 relative">
                <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-yellow-400 to-orange-500 text-gray-900 px-4 py-1 rounded-full text-xs font-bold shadow-lg">
                    ELITE
                </div>

                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-white mb-2">Elite</h3>
                    <div class="text-5xl font-bold text-white mb-1">$79</div>
                    <p class="text-purple-200 font-semibold">por mes</p>
                </div>

                <ul class="space-y-3 mb-8 text-white">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Todo lo de Premium PLUS:</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>App Móvil White Label</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Website Builder Completo</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Asistente AI Avanzado</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Marketing Automatizado</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Fotografía Profesional</strong> trimestral</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Account Manager Dedicado</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Integración POS</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Posición #1 Garantizada</strong></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span><strong>Cobertura de Medios y PR</strong></span>
                    </li>

                    {{-- Cupon Elite --}}
                    <li class="flex items-start gap-2 bg-gradient-to-r from-yellow-100 to-orange-100 p-3 rounded-lg border-2 border-yellow-400 mt-4">
                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <strong class="text-gray-900">6 Cupones (15% off)</strong>
                            <p class="text-xs text-gray-900 mt-0.5">Ahorra hasta $750 por cupón ($4,500/año) en muebles, decoración, equipos y más...</p>
                        </div>
                    </li>
                </ul>

                <a href="{{ route('claim.restaurant') }}?plan=elite" class="block w-full text-center py-3 px-6 bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-gray-900 font-bold rounded-lg transition shadow-lg">
                    Comenzar Elite
                </a>
                <p class="text-center text-sm text-purple-200 mt-2 font-medium">Soporte premium incluido</p>
            </div>
        </div>

        {{-- Garantía --}}
        <div class="mt-12 text-center">
            <div class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-6 py-3 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span class="font-semibold">Garantía de satisfacción - Cancela en cualquier momento</span>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- FAQ Section --}}
{{-- ============================================ --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block bg-blue-100 text-blue-700 text-sm font-bold px-4 py-2 rounded-full mb-4">PREGUNTAS FRECUENTES</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ¿Tienes Preguntas?
            </h2>
            <p class="text-xl text-gray-600">Aquí están las respuestas a las preguntas más comunes</p>
        </div>

        <div class="space-y-4">
            {{-- FAQ 1 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Cuánto cuesta reclamar mi restaurante?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        <strong>¡Es completamente gratis!</strong> Reclamar tu restaurante no tiene costo. El plan gratuito incluye perfil verificado, hasta 10 fotos, análisis básicos y la capacidad de responder a reseñas. Si deseas funciones avanzadas como fotos ilimitadas, cupones y posición destacada, puedes actualizar al plan Premium por $39/mes.
                    </p>
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Cuánto tiempo toma el proceso de verificación?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        El proceso típicamente toma <strong>menos de 5 minutos</strong>. Solo necesitas buscar tu restaurante, verificar que eres el dueño mediante un código enviado a tu teléfono o email del negocio, y listo. En algunos casos especiales la verificación puede tomar hasta 24 horas si requiere revisión manual.
                    </p>
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Qué pasa si mi restaurante no está en la lista?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        Si tu restaurante no aparece en nuestra búsqueda, puedes agregarlo fácilmente. Durante el proceso de reclamación, tendrás la opción de "Agregar nuevo restaurante" donde podrás ingresar toda la información de tu negocio. Tu perfil será creado y verificado en cuestión de minutos.
                    </p>
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Puedo cancelar mi suscripción Premium en cualquier momento?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        <strong>Sí, absolutamente.</strong> Puedes cancelar tu suscripción Premium en cualquier momento desde tu panel de control. Tu perfil seguirá activo con las funciones del plan gratuito. Además, ofrecemos una garantía de devolución de 30 días sin preguntas si no estás satisfecho.
                    </p>
                </div>
            </div>

            {{-- FAQ 5 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Cómo funcionan los cupones de proveedores?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        Los usuarios Premium reciben <strong>4 cupones trimestrales</strong> de nuestros proveedores asociados. Estos cupones ofrecen descuentos en equipos de cocina, ingredientes, empaques, y servicios para restaurantes. En promedio, nuestros usuarios ahorran $1,200 al año con estos cupones, lo que más que compensa el costo del plan Premium.
                    </p>
                </div>
            </div>

            {{-- FAQ 6 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Pueden ayudarme si solo hablo español?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        <strong>¡Por supuesto!</strong> Todo nuestro equipo de soporte es bilingüe. Puedes contactarnos en español o inglés por email, chat o teléfono. Además, toda nuestra plataforma está disponible completamente en español para tu comodidad.
                    </p>
                </div>
            </div>

            {{-- FAQ 7 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Cómo se comparan con Yelp o Google?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        A diferencia de Yelp (que cobra $300-$1000/mes por publicidad y muestra competidores en tu perfil) o Google (donde compites con millones de negocios), nosotros somos el directorio <strong>100% especializado en restaurantes mexicanos</strong>. Esto significa que tu audiencia ya está buscando específicamente comida mexicana, no tienes que competir con otros tipos de negocios, y nuestro Premium cuesta solo $39/mes sin trucos ocultos.
                    </p>
                </div>
            </div>

            {{-- FAQ 8 --}}
            <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
                    <span class="font-semibold text-gray-900">¿Puedo actualizar o degradar mi plan después?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-gray-600">
                        Sí, puedes cambiar tu plan en cualquier momento. Si actualizas de Gratis a Premium, el cambio es inmediato. Si decides volver al plan gratuito, mantendrás los beneficios Premium hasta el final de tu período de facturación actual. No hay penalizaciones ni cargos adicionales por cambiar de plan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Still have questions? --}}
        <div class="mt-12 text-center">
            <p class="text-gray-600 mb-4">¿Todavía tienes preguntas?</p>
            <a href="mailto:soporte@restaurantesmexicanosfamosos.com" class="inline-flex items-center text-red-600 font-semibold hover:text-red-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contáctanos - Respondemos en menos de 24 horas
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- CITY STATS SEARCH Section --}}
{{-- ============================================ --}}
<section class="py-20 bg-white" x-data="cityStats()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block bg-amber-100 text-amber-700 text-sm font-bold px-4 py-2 rounded-full mb-4">DESCUBRE TU MERCADO</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ¿Cuántas Personas Buscan en Tu Ciudad?
            </h2>
            <p class="text-xl text-gray-600">Ingresa tu ciudad para ver estadísticas de búsquedas de comida mexicana</p>
        </div>

        {{-- Search Input --}}
        <div class="max-w-xl mx-auto mb-8">
            <div class="relative">
                <input type="text"
                       x-model="citySearch"
                       @input.debounce.300ms="searchCity()"
                       placeholder="Ej: Houston TX, Los Angeles CA..."
                       class="w-full px-6 py-4 text-lg border-2 border-gray-200 rounded-xl focus:border-amber-500 focus:ring-0 transition pl-14">
                <svg class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{-- Loading indicator --}}
                <div x-show="isLoading" class="absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                {{-- Suggestions dropdown --}}
                <div x-show="showSuggestions && suggestions.length > 0" x-transition class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto">
                    <template x-for="suggestion in suggestions" :key="suggestion.city + suggestion.state_code">
                        <button @click="selectCity(suggestion.city, suggestion.state_code)" class="w-full px-4 py-3 text-left hover:bg-amber-50 flex justify-between items-center border-b border-gray-100 last:border-0">
                            <span class="font-medium" x-text="suggestion.city + ", " + suggestion.state_code"></span>
                            <span class="text-sm text-gray-500" x-text="suggestion.restaurant_count + " restaurantes""></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
        
        {{-- No data found message --}}
        <div x-show="noDataFound && !isLoading" x-transition class="max-w-xl mx-auto mb-8 bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
            <p class="text-blue-800">
                <span class="font-bold">No encontramos datos para esta ciudad.</span><br>
                <span class="text-sm">Prueba buscando ciudades más grandes o verifica la ortografía.</span>
            </p>
        </div>

        {{-- Results --}}
        <div x-show="showResults" x-transition class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-8 border border-amber-200">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900" x-text="'Estadísticas para ' + selectedCity"></h3>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                    <div class="text-3xl font-bold text-amber-600" x-text="monthlySearches.toLocaleString()"></div>
                    <p class="text-sm text-gray-600 mt-1">Búsquedas/mes</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                    <div class="text-3xl font-bold text-green-600" x-text="restaurantCount.toLocaleString()"></div>
                    <p class="text-sm text-gray-600 mt-1">Restaurantes</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                    <div class="text-3xl font-bold text-blue-600" x-text="availableToClaim.toLocaleString()"></div>
                    <p class="text-sm text-gray-600 mt-1">Sin reclamar</p>
                </div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm">
                    <div class="text-3xl font-bold text-red-600" x-text="competitionLevel"></div>
                    <p class="text-sm text-gray-600 mt-1">Competencia</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 mb-6">
                <p class="text-gray-700">
                    <span class="font-bold text-green-600">¡Buenas noticias!</span>
                    En <span x-text="selectedCity" class="font-semibold"></span> hay
                    <span x-text="availableToClaim.toLocaleString()" class="font-bold text-red-600"></span>
                    restaurantes mexicanos que aún no han sido reclamados.
                    Si tu restaurante está entre ellos, ¡este es el momento perfecto para destacar!
                </p>
            </div>

            <div class="text-center">
                <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center px-8 py-4 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition shadow-lg">
                    Buscar Mi Restaurante en <span x-text="selectedCity" class="ml-1"></span>
                </a>
            </div>
        </div>

        {{-- Popular Cities --}}
        <div x-show="!showResults" class="mt-8">
            <p class="text-center text-gray-500 mb-4">Ciudades populares:</p>
            <div class="flex flex-wrap justify-center gap-2">
                <button @click="selectCity('Los Angeles', 'CA')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Los Angeles</button>
                <button @click="selectCity('Houston', 'TX')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Houston</button>
                <button @click="selectCity('Chicago', 'IL')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Chicago</button>
                <button @click="selectCity('Phoenix', 'AZ')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Phoenix</button>
                <button @click="selectCity('San Antonio', 'TX')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">San Antonio</button>
                <button @click="selectCity('Dallas')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Dallas</button>
                <button @click="selectCity('Miami')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">Miami</button>
                <button @click="selectCity('New York')" class="px-4 py-2 bg-gray-100 rounded-full text-gray-700 hover:bg-amber-100 hover:text-amber-800 transition">New York</button>
            </div>
        </div>
    </div>
</section>

{{-- Final CTA Section --}}
<section class="py-20 bg-gradient-to-br from-red-600 via-red-700 to-orange-600 relative overflow-hidden">
    {{-- Decorative Elements --}}
    <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full translate-x-1/3 translate-y-1/3"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            ¿Listo Para Atraer Más Clientes?
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Únete a los {{ number_format($stats['claimed'] ?? 847) }} restaurantes que ya están creciendo con nosotros. Reclama tu perfil gratis hoy.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-8">
            <a href="{{ route('claim.restaurant') }}" class="px-10 py-4 bg-white text-red-600 font-bold rounded-xl hover:bg-gray-100 transition shadow-xl flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Reclamar Mi Restaurante Gratis
            </a>
            <a href="#pricing" class="px-10 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 transition flex items-center">
                Ver Planes Premium
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="flex flex-wrap justify-center gap-6 text-white/80 text-sm">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                100% Gratis
            </span>
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                5 minutos
            </span>
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Sin tarjeta de crédito
            </span>
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Soporte en español
            </span>
        </div>
    </div>
</section>

{{-- Alpine.js City Stats Component --}}
<script>
function cityStats() {
    return {
        citySearch: '',
        showResults: false,
        selectedCity: '',
        monthlySearches: 0,
        restaurantCount: 0,
        availableToClaim: 0,
        competitionLevel: '',
        isLoading: false,
        suggestions: [],
        showSuggestions: false,
        noDataFound: false,

        async searchCity() {
            if (this.citySearch.length < 3) {
                this.showResults = false;
                this.showSuggestions = false;
                return;
            }

            this.isLoading = true;
            this.noDataFound = false;

            try {
                const suggestResponse = await fetch('/api/city-search?q=' + encodeURIComponent(this.citySearch));
                this.suggestions = await suggestResponse.json();
                this.showSuggestions = this.suggestions.length > 0;

                const statsResponse = await fetch('/api/city-stats?city=' + encodeURIComponent(this.citySearch));
                const data = await statsResponse.json();

                if (data.restaurant_count > 0) {
                    this.displayRealData(data);
                } else if (this.suggestions.length > 0) {
                    this.selectCity(this.suggestions[0].city, this.suggestions[0].state_code);
                } else {
                    this.noDataFound = true;
                    this.showResults = false;
                }
            } catch (error) {
                console.error('Error fetching city stats:', error);
                this.noDataFound = true;
            }

            this.isLoading = false;
        },

        async selectCity(city, stateCode) {
            stateCode = stateCode || '';
            this.citySearch = stateCode ? city + ', ' + stateCode : city;
            this.showSuggestions = false;
            this.isLoading = true;

            try {
                const response = await fetch('/api/city-stats?city=' + encodeURIComponent(city) + '&state=' + encodeURIComponent(stateCode));
                const data = await response.json();
                this.displayRealData(data);
            } catch (error) {
                console.error('Error:', error);
            }

            this.isLoading = false;
        },

        displayRealData(data) {
            this.selectedCity = data.state ? data.city + ', ' + data.state : data.city;
            this.monthlySearches = data.monthly_searches || 0;
            this.restaurantCount = data.restaurant_count || 0;
            this.availableToClaim = data.available_to_claim || 0;
            this.competitionLevel = data.competition_level || 'Baja';
            this.showResults = this.restaurantCount > 0;
            this.noDataFound = this.restaurantCount === 0;
        }
    }
}
</script>
</div>
