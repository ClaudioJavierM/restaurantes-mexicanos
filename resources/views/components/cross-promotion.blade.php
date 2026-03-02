@props(['currentSite' => null])

@php
    // Cargar configuración
    $config = config('cross_promotion');
    $sites = collect($config['sites']);
    $settings = $config['settings'];

    // Filtrar sitios
    $filteredSites = $sites->filter(function ($site, $key) use ($currentSite, $settings) {
        // Excluir sitio actual si está configurado
        if ($settings['exclude_current'] && $currentSite && $key === $currentSite) {
            return false;
        }

        // Solo mostrar activos si está configurado
        if (!$settings['show_inactive'] && !$site['active']) {
            return false;
        }

        return true;
    });

    // Ordenar aleatoriamente si está configurado
    if ($settings['shuffle']) {
        $filteredSites = $filteredSites->shuffle();
    }

    // Limitar cantidad de sitios a mostrar
    $displaySites = $filteredSites->take($settings['max_items']);

    // No mostrar si no hay sitios disponibles
    if ($displaySites->isEmpty()) {
        return;
    }
@endphp

{{-- Sección de Promoción Cruzada --}}
<section class="bg-gradient-to-br from-slate-50 via-orange-50 to-emerald-50 border-t-4 border-gradient-to-r from-emerald-500 via-white to-red-600 py-16 mt-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Título con estilo mexicano --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center mb-4">
                <div class="h-1 w-12 bg-gradient-to-r from-emerald-500 to-transparent rounded-full"></div>
                <svg class="mx-4 h-8 w-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <div class="h-1 w-12 bg-gradient-to-l from-red-500 to-transparent rounded-full"></div>
            </div>

            <h2 class="text-3xl md:text-4xl font-display font-bold mb-3">
                <span class="bg-gradient-to-r from-emerald-700 via-red-600 to-orange-600 bg-clip-text text-transparent">
                    {{ app()->getLocale() === 'en' ? 'Discover Our Family of Businesses' : 'Descubre Nuestra Familia de Negocios' }}
                </span>
            </h2>

            <p class="text-gray-700 text-lg max-w-2xl mx-auto">
                {{ app()->getLocale() === 'en'
                    ? 'Everything you need for your Mexican restaurant or business in one place'
                    : 'Todo lo que necesitas para tu restaurante o negocio mexicano en un solo lugar' }}
            </p>
        </div>

        {{-- Grid de Sitios --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($displaySites as $key => $site)
                <a href="{{ $site['url'] }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="group relative overflow-hidden rounded-2xl border-2 border-gray-200 bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:border-transparent"
                   style="--site-color: {{ $site['color'] }}">

                    {{-- Efecto de hover con color de marca --}}
                    <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity duration-300"
                         style="background: linear-gradient(135deg, {{ $site['color'] }}, transparent)"></div>

                    {{-- Logo --}}
                    <div class="relative mb-6 flex items-center justify-center h-20">
                        <img src="{{ $site['logo'] }}"
                             alt="{{ $site['name'] }}"
                             class="h-full w-auto object-contain transition-transform duration-300 group-hover:scale-110"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                        {{-- Fallback si no hay logo --}}
                        <div class="hidden items-center justify-center h-20 w-20 rounded-xl text-2xl font-bold text-white"
                             style="background: {{ $site['color'] }}">
                            {{ substr($site['name'], 0, 2) }}
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <h3 class="mb-3 text-xl font-bold text-gray-900 text-center transition-colors duration-300"
                        style="color: var(--site-color)">
                        {{ $site['name'] }}
                    </h3>

                    {{-- Descripción --}}
                    <p class="mb-6 text-sm text-gray-600 text-center leading-relaxed min-h-[3rem]">
                        {{ $site['description'] }}
                    </p>

                    {{-- CTA Button --}}
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-md transition-all duration-300 group-hover:gap-4 group-hover:shadow-xl"
                              style="background: linear-gradient(135deg, {{ $site['color'] }}, {{ $site['color'] }}dd)">
                            {{ $site['cta'] }}
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </span>
                    </div>

                    {{-- Badge "Nuevo" para sitios específicos (opcional) --}}
                    @if($key === 'decorarmex' || $key === 'mftrailers')
                        <div class="absolute top-4 right-4 bg-gradient-to-r from-red-500 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg transform rotate-12 animate-pulse">
                            {{ app()->getLocale() === 'en' ? 'NEW!' : '¡NUEVO!' }}
                        </div>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Footer de la sección --}}
        <div class="mt-12 text-center">
            <p class="text-gray-600 text-sm">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    {{ app()->getLocale() === 'en'
                        ? 'Proudly serving the Mexican community in USA since 2020'
                        : 'Orgullosamente sirviendo a la comunidad mexicana en USA desde 2020' }}
                </span>
            </p>
        </div>
    </div>
</section>
