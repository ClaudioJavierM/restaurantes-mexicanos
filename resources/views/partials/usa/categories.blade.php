{{-- Categories Section - Mexican Food Styles --}}
<section class="py-20 lg:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-5 py-1.5 bg-[#D4AF37]/10 text-[#D4AF37] rounded-full text-xs font-semibold tracking-[0.2em] uppercase border border-[#D4AF37]/20 mb-6">
                Explore by Cuisine
            </span>
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-5 tracking-tight">
                Styles of Mexican Cuisine
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed">
                Descubre la variedad de la cocina mexicana. Desde tacos callejeros hasta mariscos frescos.
            </p>
        </div>

        {{-- Main Categories Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-10">
            @php
                $categoryIcons = [
                    'tacos' => ['icon' => '🌮', 'color' => 'from-yellow-400 to-orange-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'mariscos' => ['icon' => '🦐', 'color' => 'from-blue-400 to-cyan-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'burritos' => ['icon' => '🌯', 'color' => 'from-orange-400 to-red-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'birria' => ['icon' => '🍖', 'color' => 'from-red-500 to-red-700', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'carnitas' => ['icon' => '🐷', 'color' => 'from-pink-400 to-rose-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'barbacoa' => ['icon' => '🥩', 'color' => 'from-amber-500 to-orange-600', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'tortas' => ['icon' => '🥪', 'color' => 'from-yellow-500 to-amber-600', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'tamales' => ['icon' => '🫔', 'color' => 'from-green-500 to-emerald-600', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'pozole' => ['icon' => '🍲', 'color' => 'from-red-400 to-pink-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'menudo' => ['icon' => '🥣', 'color' => 'from-orange-500 to-red-600', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'antojitos' => ['icon' => '🫓', 'color' => 'from-amber-400 to-yellow-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'panaderia' => ['icon' => '🥐', 'color' => 'from-amber-300 to-orange-400', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'paleteria' => ['icon' => '🍦', 'color' => 'from-pink-300 to-purple-400', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'tortilleria' => ['icon' => '🫓', 'color' => 'from-yellow-400 to-amber-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'food-truck' => ['icon' => '🚚', 'color' => 'from-gray-500 to-gray-700', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'cocina-casera' => ['icon' => '🏠', 'color' => 'from-green-400 to-teal-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'comida-regional' => ['icon' => '🇲🇽', 'color' => 'from-green-500 to-red-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                    'mexican-restaurant' => ['icon' => '🍽️', 'color' => 'from-red-500 to-green-500', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'],
                ];
                $defaultIcon = ['icon' => '🍽️', 'color' => 'from-gray-400 to-gray-600', 'bg' => 'bg-[#2A2A2A] hover:bg-[#2A2A2A]'];
            @endphp

            @foreach($categories->take(12) as $category)
                @php
                    $catData = $categoryIcons[$category->slug] ?? $defaultIcon;
                @endphp
                <a href="/restaurantes?category={{ $category->slug }}"
                   class="group relative {{ $catData['bg'] }} rounded-xl p-5 text-center transition-all duration-300 border border-white/5 hover:border-[#D4AF37]/30 hover:-translate-y-1">
                    {{-- Icon Circle --}}
                    <div class="relative mx-auto w-14 h-14 mb-3">
                        <div class="absolute inset-0 bg-gradient-to-br {{ $catData['color'] }} rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
                        <div class="relative flex items-center justify-center w-full h-full text-3xl">
                            {{ $catData['icon'] }}
                        </div>
                    </div>

                    {{-- Category Name --}}
                    <h3 class="font-medium text-white text-sm group-hover:text-[#D4AF37] transition-colors duration-300 leading-tight">
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
        <div class="mt-10 pt-10 border-t border-white/5">
            <h3 class="text-center text-base font-semibold text-gray-400 mb-6 tracking-wide">Busquedas Populares</h3>
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
                       class="inline-flex items-center px-4 py-2 bg-[#2A2A2A] border border-white/10 rounded-full text-sm font-medium text-gray-300 hover:border-[#D4AF37]/40 hover:text-[#D4AF37] transition-all duration-300 group">
                        <span class="mr-2 group-hover:scale-110 transition-transform duration-300">{{ $tag['icon'] }}</span>
                        {{ $tag['name'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- View All Link --}}
        <div class="text-center mt-12">
            <a href="/restaurantes" class="inline-flex items-center px-7 py-3.5 border border-[#D4AF37]/30 text-[#D4AF37] font-medium rounded-xl hover:bg-[#D4AF37]/10 transition-all duration-300 text-sm tracking-wide">
                Ver Todas las Categorias
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
