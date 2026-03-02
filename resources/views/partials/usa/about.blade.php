{{-- About Section for USA --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm font-bold mb-4">
                    SOBRE FAMER AWARDS
                </span>
                <h2 class="text-3xl md:text-4xl font-display font-black text-gray-900 mb-6">
                    {{ __('app.how_we_select') }}
                </h2>
                <div class="space-y-4 text-gray-600">
                    <p>
                        {!! __('app.about_famer_description') !!}
                    </p>
                    <p>
                        Nuestro algoritmo considera:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold mr-3">✓</span>
                            <span><strong>Calificacion promedio</strong> en Google, Yelp, TripAdvisor y Facebook</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold mr-3">✓</span>
                            <span><strong>Numero total de resenas</strong> para medir la popularidad real</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold mr-3">✓</span>
                            <span><strong>Consistencia</strong> en la calidad a traves del tiempo</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold mr-3">✓</span>
                            <span><strong>Verificacion</strong> de que el restaurante esta activo y operando</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6">
                    <a href="/famer-awards" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl hover:shadow-lg transition-all">
                        Ver FAMER Awards {{ now()->year - 1 }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6 text-white text-center transform hover:scale-105 transition-transform">
                    <div class="text-4xl font-black">10K+</div>
                    <div class="text-green-100 text-sm">{{ __('app.verified_restaurants') }}</div>
                </div>
                <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-xl p-6 text-white text-center transform hover:scale-105 transition-transform">
                    <div class="text-4xl font-black">50</div>
                    <div class="text-red-100 text-sm">{{ __('app.states_covered') }}</div>
                </div>
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl p-6 text-white text-center transform hover:scale-105 transition-transform">
                    <div class="text-4xl font-black">5+</div>
                    <div class="text-yellow-100 text-sm">Fuentes de Datos</div>
                </div>
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6 text-white text-center transform hover:scale-105 transition-transform">
                    <div class="text-4xl font-black">2M+</div>
                    <div class="text-purple-100 text-sm">Resenas Analizadas</div>
                </div>
            </div>
        </div>
    </div>
</section>
