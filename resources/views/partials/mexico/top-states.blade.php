{{-- Top Restaurants by State Section --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-bold mb-4">
                POR ESTADO
            </span>
            <h2 class="text-3xl md:text-4xl font-display font-black text-gray-900 mb-4">
                Top 10 por Estado
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Descubre los mejores restaurantes en cada estado de México, clasificados por sus reseñas y popularidad.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($states->take(8) as $state)
                <a href="/restaurantes?state={{ $state->id }}" 
                   class="group relative bg-gradient-to-br from-green-600 to-red-600 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition-all"></div>
                    <div class="relative p-6 text-center">
                        <div class="text-4xl mb-2">🇲🇽</div>
                        <h3 class="text-white font-bold text-lg">{{ $state->name }}</h3>
                        <p class="text-white/80 text-sm mt-1">
                            {{ $state->restaurants_count ?? 0 }} restaurantes
                        </p>
                        <div class="mt-3 inline-flex items-center text-yellow-300 text-sm font-medium">
                            Ver Top 10
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="/estados" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all">
                Ver todos los estados
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
