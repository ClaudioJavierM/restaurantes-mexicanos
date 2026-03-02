{{-- MF Group Companies Footer Section --}}
{{-- Cross-linking component for SEO backlinks between MF Group sites --}}
{{-- Works on both light and dark backgrounds --}}
<div class="pt-6 pb-4">
    <div class="mx-auto max-w-6xl px-4">
        <p class="text-center text-xs font-medium text-gray-500 mb-3">Empresas MF Group</p>
        <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-xs text-gray-400">
            @php
                $currentDomain = request()->getHost();
                $sites = [
                    // E-commerce Laravel Sites
                    ['url' => 'https://mf-imports.com', 'name' => 'MF Imports', 'anchor' => 'Muebles y Decoración para Restaurantes Mexicanos'],
                    ['url' => 'https://mueblesmexicanos.com', 'name' => 'Muebles Mexicanos', 'anchor' => 'Sillas, Mesas y Booths para Restaurantes'],
                    ['url' => 'https://tododetonala.com', 'name' => 'Todo de Tonalá', 'anchor' => 'Arte Mexicano Auténtico de Tonalá'],
                    ['url' => 'https://restaurantesmexicanosfamosos.com.mx', 'name' => 'Restaurantes Mexicanos', 'anchor' => 'Los Mejores Restaurantes Mexicanos en USA'],
                    ['url' => 'https://refrimexpaleteria.com', 'name' => 'Refrimex', 'anchor' => 'Equipos para Paleterías y Heladerías'],
                    ['url' => 'https://tormexpro.com', 'name' => 'Tormex', 'anchor' => 'Tortilladoras Mexicanas en USA'],
                    ['url' => 'https://mftrailers.com', 'name' => 'MF Trailers', 'anchor' => 'Food Trucks y Trailers Gastronómicos'],
                    ['url' => 'https://libertyrsc.com', 'name' => 'Liberty RSC', 'anchor' => 'Roofing y Construcción en Dallas Fort Worth'],
                    // WordPress Sites
                    ['url' => 'https://www.soremex.com', 'name' => 'Soremex', 'anchor' => 'Soluciones para Restaurantes Mexicanos'],
                    ['url' => 'https://www.mexartcraft.com', 'name' => 'MexArtCraft', 'anchor' => 'Authentic Mexican Art and Crafts'],
                    ['url' => 'https://decorarmex.com', 'name' => 'Decorarmex', 'anchor' => 'Decoración y Novedades para Restaurantes'],
                ];
            @endphp
            @foreach($sites as $index => $site)
                @php
                    $siteDomain = str_replace(['https://', 'http://', 'www.'], '', $site['url']);
                    $isCurrent = str_contains($currentDomain, $siteDomain) || str_contains($siteDomain, $currentDomain);
                @endphp
                @if(!$isCurrent)
                    <a href="{{ $site['url'] }}"
                       target="_blank"
                       rel="noopener"
                       class="hover:opacity-75 transition-opacity"
                       title="{{ $site['anchor'] }}">
                        {{ $site['name'] }}
                    </a>
                    @if($index < count($sites) - 1)
                        <span class="opacity-50 hidden sm:inline">|</span>
                    @endif
                @endif
            @endforeach
        </div>
    </div>
</div>
