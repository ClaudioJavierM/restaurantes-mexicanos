{{-- Categories Section - Mexican Food Styles --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-2 bg-red-600 text-white rounded-full text-sm font-bold mb-4">
                EXPLORA POR CATEGORIA
            </span>
            <h2 class="text-3xl md:text-4xl font-display font-black text-gray-900 mb-4">
                Estilos de Comida Mexicana
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Descubre la variedad de la cocina mexicana. Desde tacos callejeros hasta mariscos frescos.
            </p>
        </div>

        {{-- Main Categories Grid - Yelp Style --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            @php
                $categoryIcons = [
                    'tacos' => ['icon' => '🌮', 'color' => 'from-yellow-400 to-orange-500', 'bg' => 'bg-yellow-50 hover:bg-yellow-100'],
                    'mariscos' => ['icon' => '🦐', 'color' => 'from-blue-400 to-cyan-500', 'bg' => 'bg-blue-50 hover:bg-blue-100'],
                    'burritos' => ['icon' => '🌯', 'color' => 'from-orange-400 to-red-500', 'bg' => 'bg-orange-50 hover:bg-orange-100'],
                    'birria' => ['icon' => '🍖', 'color' => 'from-red-500 to-red-700', 'bg' => 'bg-red-50 hover:bg-red-100'],
                    'carnitas' => ['icon' => '🐷', 'color' => 'from-pink-400 to-rose-500', 'bg' => 'bg-pink-50 hover:bg-pink-100'],
                    'barbacoa' => ['icon' => '🥩', 'color' => 'from-amber-500 to-orange-600', 'bg' => 'bg-amber-50 hover:bg-amber-100'],
                    'tortas' => ['icon' => '🥪', 'color' => 'from-yellow-500 to-amber-600', 'bg' => 'bg-yellow-50 hover:bg-yellow-100'],
                    'tamales' => ['icon' => '🫔', 'color' => 'from-green-500 to-emerald-600', 'bg' => 'bg-green-50 hover:bg-green-100'],
                    'pozole' => ['icon' => '🍲', 'color' => 'from-red-400 to-pink-500', 'bg' => 'bg-red-50 hover:bg-red-100'],
                    'menudo' => ['icon' => '🥣', 'color' => 'from-orange-500 to-red-600', 'bg' => 'bg-orange-50 hover:bg-orange-100'],
                    'antojitos' => ['icon' => '🫓', 'color' => 'from-amber-400 to-yellow-500', 'bg' => 'bg-amber-50 hover:bg-amber-100'],
                    'panaderia' => ['icon' => '🥐', 'color' => 'from-amber-300 to-orange-400', 'bg' => 'bg-amber-50 hover:bg-amber-100'],
                    'paleteria' => ['icon' => '🍦', 'color' => 'from-pink-300 to-purple-400', 'bg' => 'bg-pink-50 hover:bg-pink-100'],
                    'tortilleria' => ['icon' => '🫓', 'color' => 'from-yellow-400 to-amber-500', 'bg' => 'bg-yellow-50 hover:bg-yellow-100'],
                    'food-truck' => ['icon' => '🚚', 'color' => 'from-gray-500 to-gray-700', 'bg' => 'bg-gray-50 hover:bg-gray-100'],
                    'cocina-casera' => ['icon' => '🏠', 'color' => 'from-green-400 to-teal-500', 'bg' => 'bg-green-50 hover:bg-green-100'],
                    'comida-regional' => ['icon' => '🇲🇽', 'color' => 'from-green-500 to-red-500', 'bg' => 'bg-green-50 hover:bg-green-100'],
                    'mexican-restaurant' => ['icon' => '🍽️', 'color' => 'from-red-500 to-green-500', 'bg' => 'bg-red-50 hover:bg-red-100'],
                ];
                $defaultIcon = ['icon' => '🍽️', 'color' => 'from-gray-400 to-gray-600', 'bg' => 'bg-gray-50 hover:bg-gray-100'];
            @endphp

            @foreach($categories->take(12) as $category)
                @php
                    $catData = $categoryIcons[$category->slug] ?? $defaultIcon;
                @endphp
                <a href="/restaurantes?category={{ $category->slug }}"
                   class="group relative {{ $catData['bg'] }} rounded-2xl p-6 text-center transition-all duration-300 border-2 border-transparent hover:border-gray-200 hover:shadow-xl transform hover:-translate-y-1">
                    {{-- Icon Circle --}}
                    <div class="relative mx-auto w-16 h-16 mb-4">
                        <div class="absolute inset-0 bg-gradient-to-br {{ $catData['color'] }} rounded-full opacity-20 group-hover:opacity-30 transition-opacity"></div>
                        <div class="relative flex items-center justify-center w-full h-full text-4xl">
                            {{ $catData['icon'] }}
                        </div>
                    </div>

                    {{-- Category Name --}}
                    <h3 class="font-bold text-gray-900 text-sm group-hover:text-gray-700 transition-colors leading-tight">
                        {{ $category->name }}
                    </h3>

                    {{-- Restaurant Count --}}
                    @if($category->restaurants_count > 0)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ number_format($category->restaurants_count) }} lugares
                    </p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Special Tags Row --}}
        <div class="mt-8 pt-8 border-t border-gray-200">
            <h3 class="text-center text-lg font-bold text-gray-700 mb-6">Busquedas Populares</h3>
            <div class="flex flex-wrap justify-center gap-3">
                @php
                    $popularTags = [
                        ['name' => 'Tacos de Birria', 'query' => 'birria+tacos', 'icon' => '🌮'],
                        ['name' => 'Mariscos Frescos', 'query' => 'mariscos+frescos', 'icon' => '🦞'],
                        ['name' => 'Tacos al Pastor', 'query' => 'al+pastor', 'icon' => '🍖'],
                        ['name' => 'Comida Oaxaquena', 'query' => 'oaxaquena', 'icon' => '🫔'],
                        ['name' => 'Comida Jaliscience', 'query' => 'jalisco', 'icon' => '🥃'],
                        ['name' => 'Micheladas', 'query' => 'micheladas', 'icon' => '🍺'],
                        ['name' => 'Desayunos Mexicanos', 'query' => 'desayunos', 'icon' => '🍳'],
                        ['name' => 'Tacos de Asada', 'query' => 'carne+asada', 'icon' => '🥩'],
                        ['name' => 'Elotes y Esquites', 'query' => 'elotes', 'icon' => '🌽'],
                        ['name' => 'Aguas Frescas', 'query' => 'aguas+frescas', 'icon' => '🥤'],
                        ['name' => 'Comida Yucateca', 'query' => 'yucateca', 'icon' => '🍋'],
                        ['name' => 'Mole', 'query' => 'mole', 'icon' => '🍫'],
                    ];
                @endphp

                @foreach($popularTags as $tag)
                    <a href="/restaurantes?search={{ $tag['query'] }}"
                       class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-200 rounded-full text-sm font-medium text-gray-700 hover:border-red-500 hover:text-red-600 hover:bg-red-50 transition-all group">
                        <span class="mr-2 group-hover:scale-110 transition-transform">{{ $tag['icon'] }}</span>
                        {{ $tag['name'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- View All Link --}}
        <div class="text-center mt-10">
            <a href="/restaurantes" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-lg hover:shadow-xl">
                Ver Todas las Categorias
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
