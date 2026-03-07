<div>
    {{-- ============================================ --}}
    {{-- 1. NAVBAR FLOTANTE --}}
    {{-- ============================================ --}}
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         x-data="{ scrolled: false }"
         x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 80)"
         :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-lg' : 'bg-transparent'">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                {{-- Logo / Name --}}
                <a href="#top" class="flex items-center gap-3">
                    @if($branding && $branding->logo_url)
                        <img src="{{ $branding->logo_url }}" alt="{{ $restaurant->name }}" class="h-10 w-10 rounded-lg object-cover">
                    @endif
                    <span class="font-display font-bold text-lg truncate max-w-[200px]"
                          :class="scrolled ? 'text-gray-900' : 'text-white'">
                        {{ $restaurant->name }}
                    </span>
                </a>

                {{-- Nav Links (Desktop) --}}
                <div class="hidden md:flex items-center gap-6">
                    @foreach([
                        ['#sobre', 'Nosotros'],
                        ['#menu', 'Menú'],
                        ['#galeria', 'Fotos'],
                        ['#resenas', 'Reseñas'],
                        ['#ubicacion', 'Ubicación'],
                        ['#contacto', 'Contacto'],
                    ] as [$href, $label])
                        <a href="{{ $href }}"
                           class="text-sm font-medium transition-colors"
                           :class="scrolled ? 'text-gray-600 hover:text-gray-900' : 'text-white/80 hover:text-white'">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- CTA Button --}}
                <div class="flex items-center gap-3">
                    @if($restaurant->phone)
                        <a href="tel:{{ $restaurant->phone }}"
                           class="brand-bg text-white px-5 py-2 rounded-full text-sm font-semibold hover:opacity-90 transition shadow-lg">
                            Llamar
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Mobile Nav (Horizontal scroll) --}}
        <div class="md:hidden overflow-x-auto hide-scrollbar border-t"
             :class="scrolled ? 'border-gray-200' : 'border-white/20'"
             x-show="scrolled" x-transition>
            <div class="flex gap-1 px-4 py-2 min-w-max">
                @foreach([
                    ['#sobre', 'Nosotros'],
                    ['#menu', 'Menú'],
                    ['#galeria', 'Fotos'],
                    ['#resenas', 'Reseñas'],
                    ['#ubicacion', 'Ubicación'],
                    ['#contacto', 'Contacto'],
                ] as [$href, $label])
                    <a href="{{ $href }}" class="text-xs font-medium text-gray-600 hover:text-gray-900 px-3 py-1 rounded-full hover:bg-gray-100 transition whitespace-nowrap">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    {{-- ============================================ --}}
    {{-- 2. HERO --}}
    {{-- ============================================ --}}
    <section id="top" class="relative min-h-[85vh] flex items-end hero-bg"
             style="background-image: url('{{ $restaurant->image ?? '/images/restaurant-placeholder.jpg' }}');">
        {{-- Overlay oscuro --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20"></div>

        <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 pb-12 pt-32">
            {{-- Category badge --}}
            @if($restaurant->category)
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-4"
                      style="background-color: var(--brand-primary); color: white;">
                    {{ $restaurant->category->name }}
                </span>
            @endif

            {{-- Restaurant Name --}}
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight">
                {{ $restaurant->name }}
            </h1>

            {{-- Rating + Location --}}
            <div class="flex flex-wrap items-center gap-4 text-white/90 mb-6">
                @if($restaurant->average_rating)
                    <div class="flex items-center gap-1.5">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($restaurant->average_rating) ? 'text-yellow-400' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="font-semibold text-lg">{{ number_format($restaurant->average_rating, 1) }}</span>
                        @if($restaurant->total_reviews)
                            <span class="text-white/60">({{ $restaurant->total_reviews }} reseñas)</span>
                        @endif
                    </div>
                @endif

                @if($restaurant->city)
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                    </span>
                @endif

                @if($restaurant->price_range)
                    <span class="font-semibold">{{ $restaurant->price_range }}</span>
                @endif
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap gap-3">
                @if($menuCategories->count())
                    <a href="#menu" class="brand-bg text-white px-8 py-3 rounded-full font-semibold hover:opacity-90 transition shadow-lg text-lg">
                        Ver Menú
                    </a>
                @endif
                @if($restaurant->accepts_reservations)
                    <a href="#contacto" class="bg-white/20 backdrop-blur text-white px-8 py-3 rounded-full font-semibold hover:bg-white/30 transition border border-white/30 text-lg">
                        Reservar Mesa
                    </a>
                @endif
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="bg-white/20 backdrop-blur text-white px-8 py-3 rounded-full font-semibold hover:bg-white/30 transition border border-white/30 text-lg md:hidden">
                        Llamar
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- 3. SOBRE NOSOTROS --}}
    {{-- ============================================ --}}
    <section id="sobre" class="py-16 sm:py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                {{-- Left: About text --}}
                <div>
                    <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Sobre Nosotros</h2>
                    <div class="w-16 h-1 rounded-full mb-6" style="background-color: var(--brand-primary);"></div>

                    @if($restaurant->description)
                        <div class="text-gray-600 text-lg leading-relaxed space-y-4">
                            {!! nl2br(e($restaurant->description)) !!}
                        </div>
                    @else
                        <p class="text-gray-500 text-lg">Bienvenido a {{ $restaurant->name }}. Disfruta de la mejor comida mexicana auténtica.</p>
                    @endif

                    {{-- Authenticity Badges --}}
                    @if($restaurant->chef_certified || $restaurant->traditional_recipes || $restaurant->imported_ingredients)
                        <div class="flex flex-wrap gap-3 mt-8">
                            @if($restaurant->chef_certified)
                                <div class="flex items-center gap-2 bg-amber-50 px-4 py-2 rounded-full border border-amber-200">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    <span class="text-sm font-medium text-amber-800">Chef Certificado</span>
                                </div>
                            @endif
                            @if($restaurant->traditional_recipes)
                                <div class="flex items-center gap-2 bg-green-50 px-4 py-2 rounded-full border border-green-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    <span class="text-sm font-medium text-green-800">Recetas Tradicionales</span>
                                </div>
                            @endif
                            @if($restaurant->imported_ingredients)
                                <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-full border border-blue-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                    <span class="text-sm font-medium text-blue-800">Ingredientes Importados</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Right: Specialties --}}
                <div>
                    @if(count($this->specialties))
                        <h3 class="font-display text-2xl font-bold text-gray-900 mb-6">Nuestras Especialidades</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($this->specialties as $specialty)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: var(--brand-primary);"></div>
                                    <span class="text-gray-700 font-medium text-sm">{{ $specialty }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Quick Info --}}
                    <div class="mt-8 space-y-4">
                        @if($restaurant->price_range)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Rango de precios</p>
                                    <p class="font-semibold text-gray-900">{{ $restaurant->price_range }}</p>
                                </div>
                            </div>
                        @endif
                        @if($restaurant->category)
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Categoría</p>
                                    <p class="font-semibold text-gray-900">{{ $restaurant->category->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- 4. MENÚ --}}
    {{-- ============================================ --}}
    @if($menuCategories->count())
        <section id="menu" class="py-16 sm:py-20 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-12">
                    <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Nuestro Menú</h2>
                    <div class="w-16 h-1 rounded-full mx-auto mb-4" style="background-color: var(--brand-primary);"></div>
                    <p class="text-gray-500 text-lg">Descubre nuestros platillos favoritos</p>
                </div>

                <div class="space-y-10">
                    @foreach($menuCategories as $category)
                        @if($category->items->count())
                            <div>
                                {{-- Category Name --}}
                                <h3 class="font-display text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                                    <span class="w-8 h-0.5 rounded-full" style="background-color: var(--brand-primary);"></span>
                                    {{ $category->name }}
                                </h3>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    @foreach($category->items as $item)
                                        <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow flex gap-4">
                                            @if($item->image_url)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                                     class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <h4 class="font-semibold text-gray-900">{{ $item->name }}</h4>
                                                    <span class="font-bold whitespace-nowrap brand-text">
                                                        ${{ number_format($item->price, 2) }}
                                                    </span>
                                                </div>
                                                @if($item->description)
                                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $item->description }}</p>
                                                @endif
                                                @if($item->is_popular)
                                                    <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                        Popular
                                                    </span>
                                                @endif
                                                @if($item->dietary_tags && is_array($item->dietary_tags))
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach($item->dietary_tags as $tag)
                                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">{{ $tag }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- 5. GALERÍA DE FOTOS --}}
    {{-- ============================================ --}}
    @if(count($photos))
        <section id="galeria" class="py-16 sm:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-12">
                    <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Galería</h2>
                    <div class="w-16 h-1 rounded-full mx-auto mb-4" style="background-color: var(--brand-primary);"></div>
                    <p class="text-gray-500 text-lg">Un vistazo a nuestra experiencia</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($photos as $index => $photo)
                        <div class="relative group cursor-pointer overflow-hidden rounded-xl aspect-square {{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}"
                             onclick="openLightbox('{{ $photo }}')">
                            <img src="{{ $photo }}" alt="{{ $restaurant->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                                <svg class="w-10 h-10 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- 6. RESEÑAS --}}
    {{-- ============================================ --}}
    @if($reviews->count())
        <section id="resenas" class="py-16 sm:py-20 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center mb-12">
                    <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Lo Que Dicen Nuestros Clientes</h2>
                    <div class="w-16 h-1 rounded-full mx-auto mb-4" style="background-color: var(--brand-primary);"></div>

                    @if($restaurant->average_rating)
                        <div class="flex items-center justify-center gap-3 mt-6">
                            <span class="text-5xl font-bold text-gray-900">{{ number_format($restaurant->average_rating, 1) }}</span>
                            <div>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-6 h-6 {{ $i <= round($restaurant->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $restaurant->total_reviews ?? $reviews->count() }} reseñas</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reviews as $review)
                        <div class="bg-white rounded-2xl p-6 shadow-sm">
                            {{-- Stars --}}
                            <div class="flex mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            {{-- Comment --}}
                            @if($review->title)
                                <h4 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h4>
                            @endif
                            <p class="text-gray-600 text-sm leading-relaxed line-clamp-4">{{ $review->comment }}</p>

                            {{-- Author --}}
                            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-gray-600">
                                        {{ strtoupper(substr($review->user?->name ?? 'A', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $review->user?->name ?? 'Cliente' }}</p>
                                    <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Link to full reviews --}}
                <div class="text-center mt-10">
                    <a href="{{ route('restaurants.show', $restaurant->slug) }}#reviews"
                       class="inline-flex items-center gap-2 brand-text font-semibold hover:underline">
                        Ver todas las reseñas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- 7. HORARIOS Y UBICACIÓN --}}
    {{-- ============================================ --}}
    <section id="ubicacion" class="py-16 sm:py-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Horarios y Ubicación</h2>
                <div class="w-16 h-1 rounded-full mx-auto" style="background-color: var(--brand-primary);"></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                {{-- Hours --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="font-display text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Horario de Atención
                    </h3>

                    @if(count($this->openingHours))
                        <div class="space-y-3">
                            @foreach($this->openingHours as $day => $hours)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <span class="font-medium text-gray-700">{{ $day }}</span>
                                    <span class="text-gray-600">{{ $hours }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Horarios no disponibles. Llámanos para confirmar.</p>
                    @endif
                </div>

                {{-- Map + Address --}}
                <div class="space-y-4">
                    {{-- Map --}}
                    @if($restaurant->latitude && $restaurant->longitude)
                        <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 aspect-video">
                            <iframe
                                width="100%" height="100%" style="border:0"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}&z=15&output=embed"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @endif

                    {{-- Address Card --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: var(--brand-primary); opacity: 0.1;">
                                <svg class="w-6 h-6 brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Dirección</h4>
                                <p class="text-gray-600">{{ $restaurant->address }}</p>
                                <p class="text-gray-600">{{ $restaurant->city }}, {{ $restaurant->state?->name }} {{ $restaurant->zip_code }}</p>
                                @if($restaurant->google_maps_url)
                                    <a href="{{ $restaurant->google_maps_url }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1 mt-3 text-sm font-medium brand-text hover:underline">
                                        Abrir en Google Maps
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- 8. CONTACTO / CTA --}}
    {{-- ============================================ --}}
    <section id="contacto" class="py-16 sm:py-20 bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl sm:text-4xl font-bold mb-2">Visítanos Hoy</h2>
                <div class="w-16 h-1 rounded-full mx-auto mb-4" style="background-color: var(--brand-primary);"></div>
                <p class="text-gray-400 text-lg">Estamos listos para atenderte</p>
            </div>

            {{-- Contact Grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                {{-- Phone --}}
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:bg-white/20 transition group">
                        <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: var(--brand-primary);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <h4 class="font-semibold mb-1">Llámanos</h4>
                        <p class="text-gray-400 text-sm">{{ $restaurant->phone }}</p>
                    </a>
                @endif

                {{-- Email --}}
                @if($restaurant->email)
                    <a href="mailto:{{ $restaurant->email }}" class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:bg-white/20 transition group">
                        <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: var(--brand-primary);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-semibold mb-1">Escríbenos</h4>
                        <p class="text-gray-400 text-sm">{{ $restaurant->email }}</p>
                    </a>
                @endif

                {{-- Directions --}}
                @if($restaurant->latitude && $restaurant->longitude)
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" rel="noopener"
                       class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:bg-white/20 transition group">
                        <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: var(--brand-primary);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <h4 class="font-semibold mb-1">Cómo Llegar</h4>
                        <p class="text-gray-400 text-sm">Ver indicaciones</p>
                    </a>
                @endif

                {{-- Order Online --}}
                @if($restaurant->order_url || $restaurant->doordash_url || $restaurant->ubereats_url || $restaurant->grubhub_url)
                    <a href="{{ $restaurant->order_url ?? $restaurant->doordash_url ?? $restaurant->ubereats_url ?? $restaurant->grubhub_url }}" target="_blank" rel="noopener"
                       class="bg-white/10 backdrop-blur rounded-2xl p-6 text-center hover:bg-white/20 transition group">
                        <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: var(--brand-primary);">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h4 class="font-semibold mb-1">Ordenar en Línea</h4>
                        <p class="text-gray-400 text-sm">Pide para llevar o delivery</p>
                    </a>
                @endif
            </div>

            {{-- Social Links --}}
            @if($branding && ($branding->facebook_url || $branding->instagram_url || $branding->tiktok_url || $branding->twitter_url))
                <div class="flex items-center justify-center gap-4">
                    @if($branding->facebook_url)
                        <a href="{{ $branding->facebook_url }}" target="_blank" rel="noopener" class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($branding->instagram_url)
                        <a href="{{ $branding->instagram_url }}" target="_blank" rel="noopener" class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    @endif
                    @if($branding->tiktok_url)
                        <a href="{{ $branding->tiktok_url }}" target="_blank" rel="noopener" class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    @endif
                    @if($branding->twitter_url)
                        <a href="{{ $branding->twitter_url }}" target="_blank" rel="noopener" class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- 9. FOOTER --}}
    {{-- ============================================ --}}
    <footer class="bg-gray-950 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($branding && $branding->logo_url)
                        <img src="{{ $branding->logo_url }}" alt="{{ $restaurant->name }}" class="h-8 w-8 rounded-lg object-cover">
                    @endif
                    <span class="font-display font-bold text-white">{{ $restaurant->name }}</span>
                </div>

                <div class="text-center sm:text-right text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} {{ $restaurant->name }}. Todos los derechos reservados.</p>
                    @if($restaurant->address)
                        <p class="mt-1">{{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state?->code }}</p>
                    @endif
                </div>
            </div>
        </div>
    </footer>
</div>
