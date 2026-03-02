<div class="flex flex-col min-h-screen bg-gray-50" style="background-color: var(--pwa-background);">
    {{-- Header --}}
    <header class="pwa-header text-white sticky top-0 z-40 safe-top">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($branding && $branding->logo_url)
                        <img src="{{ $branding->logo_url }}" alt="{{ $restaurant->name }}" class="h-10 w-10 rounded-lg object-cover bg-white/10">
                    @endif
                    <div>
                        <h1 class="font-bold text-lg leading-tight">{{ $branding->app_name ?? $restaurant->name }}</h1>
                        @if($restaurant->city)
                            <p class="text-white/80 text-xs">{{ $restaurant->city }}, {{ $restaurant->state?->code }}</p>
                        @endif
                    </div>
                </div>

                {{-- Call Button --}}
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="p-2 bg-white/20 rounded-full hover:bg-white/30 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex border-t border-white/20">
            <button wire:click="setTab('menu')"
                    class="flex-1 py-3 text-center text-sm font-medium transition {{ $activeTab === 'menu' ? 'bg-white/20 border-b-2 border-white' : 'text-white/70 hover:text-white' }}">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Menú
            </button>
            <button wire:click="setTab('info')"
                    class="flex-1 py-3 text-center text-sm font-medium transition {{ $activeTab === 'info' ? 'bg-white/20 border-b-2 border-white' : 'text-white/70 hover:text-white' }}">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Info
            </button>
            <button wire:click="setTab('contact')"
                    class="flex-1 py-3 text-center text-sm font-medium transition {{ $activeTab === 'contact' ? 'bg-white/20 border-b-2 border-white' : 'text-white/70 hover:text-white' }}">
                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Contacto
            </button>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-1 overflow-y-auto hide-scrollbar pb-6">
        {{-- Menu Tab --}}
        @if($activeTab === 'menu')
            <div class="p-4 space-y-6">
                @forelse($categories as $category)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        {{-- Category Header --}}
                        <div class="px-4 py-3 border-b border-gray-100" style="background: linear-gradient(135deg, var(--pwa-primary) 0%, var(--pwa-secondary) 100%);">
                            <h2 class="font-semibold text-white">{{ $category->name }}</h2>
                            @if($category->description)
                                <p class="text-sm text-white/80">{{ $category->description }}</p>
                            @endif
                        </div>

                        {{-- Items --}}
                        <div class="divide-y divide-gray-100">
                            @foreach($category->items as $item)
                                <div class="p-4 flex gap-4">
                                    {{-- Item Image --}}
                                    @if($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                             class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                                    @endif

                                    {{-- Item Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <h3 class="font-medium text-gray-900">{{ $item->name }}</h3>
                                                @if($item->description)
                                                    <p class="text-sm text-gray-500 line-clamp-2">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            <span class="font-semibold whitespace-nowrap" style="color: var(--pwa-primary);">
                                                ${{ number_format($item->price, 2) }}
                                            </span>
                                        </div>

                                        {{-- Tags --}}
                                        @if($item->is_popular || $item->is_spicy || $item->is_vegetarian)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @if($item->is_popular)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        ⭐ Popular
                                                    </span>
                                                @endif
                                                @if($item->is_spicy)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        🌶️ Picante
                                                    </span>
                                                @endif
                                                @if($item->is_vegetarian)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        🥬 Vegetariano
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Menú no disponible</h3>
                        <p class="mt-1 text-gray-500">Este restaurante aún no ha publicado su menú digital.</p>
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="inline-block mt-4 px-6 py-2 rounded-lg text-white font-medium"
                           style="background-color: var(--pwa-primary);">
                            Ver en FAMER
                        </a>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Info Tab --}}
        @if($activeTab === 'info')
            <div class="p-4 space-y-4">
                {{-- Restaurant Image --}}
                @if($restaurant->image_url)
                    <div class="rounded-2xl overflow-hidden shadow-sm">
                        <img src="{{ $restaurant->image_url }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                    </div>
                @endif

                {{-- Rating Card (weighted average) --}}
                @php $pwaWeightedRating = $restaurant->getWeightedRating(); @endphp
                @if($pwaWeightedRating > 0)
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($pwaWeightedRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-2xl font-bold" style="color: var(--pwa-primary);">{{ number_format($pwaWeightedRating, 1) }}</span>
                            </div>
                            <span class="text-gray-500 text-sm">{{ $restaurant->getCombinedReviewCount() }} reseñas</span>
                        </div>
                    </div>
                @endif

                {{-- About --}}
                @if($restaurant->description)
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Acerca de</h3>
                        <p class="text-gray-600 text-sm">{{ $restaurant->description }}</p>
                    </div>
                @endif

                {{-- Categories/Cuisine --}}
                @if($restaurant->categories && $restaurant->categories->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Especialidades</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($restaurant->categories as $category)
                                <span class="px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color: var(--pwa-primary); color: white;">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Hours --}}
                @if($restaurant->hours && is_array($restaurant->hours))
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Horario</h3>
                        <div class="space-y-2 text-sm">
                            @foreach($restaurant->hours as $day => $hours)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">{{ $day }}</span>
                                    <span class="text-gray-900 font-medium">{{ $hours }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- View Full Profile --}}
                <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                   class="block w-full py-3 text-center rounded-2xl font-medium text-white shadow-sm"
                   style="background-color: var(--pwa-primary);">
                    Ver Perfil Completo en FAMER
                </a>
            </div>
        @endif

        {{-- Contact Tab --}}
        @if($activeTab === 'contact')
            <div class="p-4 space-y-4">
                {{-- Address Card --}}
                @if($restaurant->address)
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-full" style="background-color: var(--pwa-primary);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">Dirección</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $restaurant->address }}</p>
                                <p class="text-gray-500 text-sm">{{ $restaurant->city }}, {{ $restaurant->state?->name }} {{ $restaurant->zip_code }}</p>
                                <a href="https://maps.google.com/?q={{ urlencode($restaurant->address . ', ' . $restaurant->city . ', ' . ($restaurant->state?->name ?? '')) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 mt-2 text-sm font-medium"
                                   style="color: var(--pwa-primary);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Abrir en Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Phone Card --}}
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="block bg-white rounded-2xl shadow-sm p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-full" style="background-color: var(--pwa-primary);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Teléfono</h3>
                                <p class="text-sm" style="color: var(--pwa-primary);">{{ $restaurant->phone }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endif

                {{-- Website Card --}}
                @if($restaurant->website)
                    <a href="{{ $restaurant->website }}" target="_blank" class="block bg-white rounded-2xl shadow-sm p-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-full" style="background-color: var(--pwa-primary);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Sitio Web</h3>
                                <p class="text-sm text-gray-500">{{ parse_url($restaurant->website, PHP_URL_HOST) }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </div>
                    </a>
                @endif

                {{-- Social Links --}}
                @if($restaurant->facebook_url || $restaurant->instagram_url)
                    <div class="bg-white rounded-2xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Redes Sociales</h3>
                        <div class="flex gap-3">
                            @if($restaurant->facebook_url)
                                <a href="{{ $restaurant->facebook_url }}" target="_blank"
                                   class="flex-1 py-3 bg-blue-600 text-white rounded-xl text-center font-medium flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path>
                                    </svg>
                                    Facebook
                                </a>
                            @endif
                            @if($restaurant->instagram_url)
                                <a href="{{ $restaurant->instagram_url }}" target="_blank"
                                   class="flex-1 py-3 bg-gradient-to-r from-purple-600 via-pink-500 to-orange-400 text-white rounded-xl text-center font-medium flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"></path>
                                    </svg>
                                    Instagram
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </main>

    {{-- Fixed Bottom Bar - View Full Profile --}}
    <div class="fixed bottom-0 left-0 right-0 p-3 bg-white border-t border-gray-200 safe-bottom">
        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
           class="block w-full py-3 text-center rounded-xl font-semibold text-white shadow-lg"
           style="background: linear-gradient(135deg, var(--pwa-primary), var(--pwa-secondary));">
            Ver Perfil Completo
        </a>
    </div>
</div>
