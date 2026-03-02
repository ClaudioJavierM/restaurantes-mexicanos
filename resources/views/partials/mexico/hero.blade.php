{{-- Hero Section for Mexico --}}
<div class="relative overflow-hidden">
    {{-- Dark gradient background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-red-900 to-green-900"></div>
    <div class="absolute inset-0 bg-black/30"></div>
    
    {{-- Mexican flag accent bars --}}
    <div class="absolute top-0 left-0 w-2 h-full bg-green-600"></div>
    <div class="absolute top-0 right-0 w-2 h-full bg-red-600"></div>
    
    {{-- Pattern overlay --}}
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center px-6 py-3 rounded-full bg-yellow-500/20 backdrop-blur-sm border border-yellow-400/50 text-yellow-300 mb-8">
                <svg class="w-6 h-6 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-bold text-lg">Los Mejores Restaurantes de Mexico</span>
            </div>

            {{-- Main Title --}}
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-black text-white mb-6 leading-tight drop-shadow-lg">
                Descubre los<br>
                <span class="text-yellow-400">
                    100 Restaurantes
                </span><br>
                <span class="text-3xl md:text-5xl text-white">que Debes Visitar en Mexico</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-4xl mx-auto font-medium leading-relaxed">
                Reunimos los <strong class="text-white">mejores restaurantes de Mexico</strong> basandonos en las resenas de 
                <span class="text-yellow-400 font-bold">Google</span>, 
                <span class="text-red-400 font-bold">Yelp</span>, 
                <span class="text-green-400 font-bold">TripAdvisor</span> y mas.
                <br class="hidden md:block">
                Encuentra los <strong class="text-white">Top 10</strong> de cada ciudad y estado.
            </p>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-8 mb-10">
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">{{ number_format($stats['total_restaurants'] ?? 0) }}</div>
                    <div class="text-gray-300 text-sm font-medium">Restaurantes Verificados</div>
                </div>
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">{{ $stats['total_states'] ?? 0 }}</div>
                    <div class="text-gray-300 text-sm font-medium">Estados</div>
                </div>
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">5+</div>
                    <div class="text-gray-300 text-sm font-medium">Fuentes de Resenas</div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/restaurantes" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-900 font-bold text-lg rounded-xl hover:bg-yellow-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explorar Restaurantes
                </a>
                <a href="#top-100" class="inline-flex items-center justify-center px-8 py-4 bg-yellow-500 text-gray-900 font-bold text-lg rounded-xl hover:bg-yellow-400 transition-all shadow-lg">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Ver Top 100
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Platform Logos --}}
<div class="bg-gray-100 py-8 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-center text-gray-500 text-sm mb-4">Resenas verificadas de las mejores plataformas</p>
        <div class="flex flex-wrap justify-center items-center gap-8">
            <span class="text-2xl font-bold text-gray-700">Google</span>
            <span class="text-2xl font-bold text-red-600">Yelp</span>
            <span class="text-2xl font-bold text-green-600">TripAdvisor</span>
            <span class="text-2xl font-bold text-blue-600">Facebook</span>
            <span class="text-2xl font-bold text-purple-600">Foursquare</span>
            <span class="text-2xl font-bold text-gray-800">Apple Maps</span>
            <span class="text-2xl font-bold text-green-500">Uber Eats</span>
            <span class="text-2xl font-bold text-orange-600">OpenTable</span>
        </div>
    </div>
</div>
