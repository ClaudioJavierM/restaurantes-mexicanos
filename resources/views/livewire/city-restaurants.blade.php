@php
  $isEn    = app()->getLocale() === 'en';
  $heading = $isEn
    ? "Mexican Restaurants in {$cityName}, {$stateCode}"
    : "Restaurantes Mexicanos en {$cityName}, {$stateCode}";
@endphp

@push('head')
<meta name="description" content="{{ $metaDesc }}">
<link rel="canonical" href="{{ url()->current() }}">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "{{ $heading }}",
  "description": "{{ $metaDesc }}",
  "numberOfItems": {{ $total }},
  "itemListElement": [
    @foreach($restaurants as $i => $r)
    {
      "@type": "ListItem",
      "position": {{ ($restaurants->currentPage() - 1) * 24 + $i + 1 }},
      "item": {
        "@type": "Restaurant",
        "name": "{{ addslashes($r->name) }}",
        "url": "{{ url('/restaurante/' . $r->slug) }}",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "{{ $r->city }}",
          "addressRegion": "{{ $r->state?->code }}"
        }
        @if($r->average_rating > 0)
        ,"aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "{{ $r->average_rating }}",
          "reviewCount": "{{ $r->total_reviews ?: 1 }}"
        }
        @endif
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

<div style="background:#0B0B0B; min-height:100vh; font-family:'Poppins',sans-serif; color:#E5E5E5;">

    {{-- BREADCRUMB --}}
    <nav style="background:#111111; border-bottom:1px solid #2A2A2A; padding:12px 0;">
        <div style="max-width:1200px; margin:0 auto; padding:0 24px; display:flex; align-items:center; gap:8px; font-size:13px; color:#888;">
            <a href="/" style="color:#888; text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'">Home</a>
            <span style="color:#444;">›</span>
            <a href="/restaurantes" style="color:#888; text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'">Restaurantes</a>
            <span style="color:#444;">›</span>
            <a href="/restaurantes/{{ strtolower($stateCode) }}" style="color:#888; text-decoration:none; transition:color .2s;"
               onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'">{{ $stateName }}</a>
            <span style="color:#444;">›</span>
            <span style="color:#D4AF37;">{{ $cityName }}</span>
        </div>
    </nav>

    {{-- HERO --}}
    <section style="background:#1A1A1A; border-bottom:1px solid #2A2A2A; padding:56px 24px 48px;">
        <div style="max-width:1200px; margin:0 auto; text-align:center;">
            <p style="font-size:13px; letter-spacing:3px; text-transform:uppercase; color:#D4AF37; margin-bottom:16px;">
                {{ $isEn ? 'City Guide' : 'Guía de la Ciudad' }}
            </p>
            <h1 style="font-family:'Playfair Display',Georgia,serif; font-size:clamp(28px,5vw,52px); font-weight:700; color:#D4AF37; margin:0 0 16px; line-height:1.15;">
                {{ $heading }}
            </h1>
            <p style="font-size:16px; color:#AAAAAA; max-width:640px; margin:0 auto 24px; line-height:1.7;">
                @if($isEn)
                    Discover the <strong style="color:#E5E5E5;">{{ $total }} authentic Mexican restaurants</strong>
                    in {{ $cityName }}, {{ $stateCode }}. Verified reviews, photos, menus and more.
                @else
                    Descubre los <strong style="color:#E5E5E5;">{{ $total }} mejores restaurantes mexicanos</strong>
                    en {{ $cityName }}, {{ $stateCode }}. Reseñas verificadas, fotos, menús y mucho más.
                @endif
            </p>
            {{-- Stats badges --}}
            <div style="display:flex; justify-content:center; gap:32px; flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:32px; color:#D4AF37; font-weight:700;">{{ $total }}</div>
                    <div style="font-size:12px; color:#888; text-transform:uppercase; letter-spacing:1px;">
                        {{ $isEn ? 'Restaurants' : 'Restaurantes' }}
                    </div>
                </div>
                <div style="width:1px; background:#2A2A2A;"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:32px; color:#D4AF37; font-weight:700;">★</div>
                    <div style="font-size:12px; color:#888; text-transform:uppercase; letter-spacing:1px;">
                        {{ $isEn ? 'Verified Reviews' : 'Reseñas Verificadas' }}
                    </div>
                </div>
                <div style="width:1px; background:#2A2A2A;"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:32px; color:#D4AF37; font-weight:700;">🌮</div>
                    <div style="font-size:12px; color:#888; text-transform:uppercase; letter-spacing:1px;">
                        {{ $isEn ? 'Authentic Mexican' : 'Auténtica Cocina Mexicana' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SORT BAR --}}
    <div style="background:#111111; border-bottom:1px solid #2A2A2A; padding:16px 24px; position:sticky; top:0; z-index:50;">
        <div style="max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <span style="font-size:13px; color:#666; margin-right:8px;">
                {{ $isEn ? 'Sort by:' : 'Ordenar por:' }}
            </span>
            <button wire:click="$set('sortBy', 'rating')"
                style="padding:8px 20px; border-radius:24px; border:1px solid {{ $sortBy === 'rating' ? '#D4AF37' : '#2A2A2A' }};
                       background:{{ $sortBy === 'rating' ? '#D4AF37' : 'transparent' }};
                       color:{{ $sortBy === 'rating' ? '#0B0B0B' : '#AAAAAA' }};
                       font-size:13px; font-weight:{{ $sortBy === 'rating' ? '600' : '400' }};
                       cursor:pointer; transition:all .2s; font-family:'Poppins',sans-serif;"
                onmouseover="if('{{ $sortBy }}' !== 'rating') { this.style.borderColor='#D4AF37'; this.style.color='#D4AF37'; }"
                onmouseout="if('{{ $sortBy }}' !== 'rating') { this.style.borderColor='#2A2A2A'; this.style.color='#AAAAAA'; }">
                ★ {{ $isEn ? 'Best Rated' : 'Mejor calificados' }}
            </button>
            <button wire:click="$set('sortBy', 'reviews')"
                style="padding:8px 20px; border-radius:24px; border:1px solid {{ $sortBy === 'reviews' ? '#D4AF37' : '#2A2A2A' }};
                       background:{{ $sortBy === 'reviews' ? '#D4AF37' : 'transparent' }};
                       color:{{ $sortBy === 'reviews' ? '#0B0B0B' : '#AAAAAA' }};
                       font-size:13px; font-weight:{{ $sortBy === 'reviews' ? '600' : '400' }};
                       cursor:pointer; transition:all .2s; font-family:'Poppins',sans-serif;"
                onmouseover="if('{{ $sortBy }}' !== 'reviews') { this.style.borderColor='#D4AF37'; this.style.color='#D4AF37'; }"
                onmouseout="if('{{ $sortBy }}' !== 'reviews') { this.style.borderColor='#2A2A2A'; this.style.color='#AAAAAA'; }">
                💬 {{ $isEn ? 'Most Reviews' : 'Más reseñas' }}
            </button>
            <button wire:click="$set('sortBy', 'name')"
                style="padding:8px 20px; border-radius:24px; border:1px solid {{ $sortBy === 'name' ? '#D4AF37' : '#2A2A2A' }};
                       background:{{ $sortBy === 'name' ? '#D4AF37' : 'transparent' }};
                       color:{{ $sortBy === 'name' ? '#0B0B0B' : '#AAAAAA' }};
                       font-size:13px; font-weight:{{ $sortBy === 'name' ? '600' : '400' }};
                       cursor:pointer; transition:all .2s; font-family:'Poppins',sans-serif;"
                onmouseover="if('{{ $sortBy }}' !== 'name') { this.style.borderColor='#D4AF37'; this.style.color='#D4AF37'; }"
                onmouseout="if('{{ $sortBy }}' !== 'name') { this.style.borderColor='#2A2A2A'; this.style.color='#AAAAAA'; }">
                🔤 A-Z
            </button>
            <span style="margin-left:auto; font-size:13px; color:#555;">
                {{ $restaurants->firstItem() }}–{{ $restaurants->lastItem() }}
                {{ $isEn ? 'of' : 'de' }} {{ $total }}
            </span>
        </div>
    </div>

    {{-- RESTAURANT GRID --}}
    <div style="max-width:1200px; margin:0 auto; padding:40px 24px;">

        @if($restaurants->isEmpty())
            <div style="text-align:center; padding:80px 24px;">
                <div style="font-size:64px; margin-bottom:24px;">🌮</div>
                <h2 style="font-family:'Playfair Display',serif; color:#D4AF37; margin-bottom:12px;">
                    {{ $isEn ? 'No restaurants found' : 'No se encontraron restaurantes' }}
                </h2>
                <p style="color:#666; margin-bottom:32px;">
                    {{ $isEn
                        ? "We couldn't find Mexican restaurants in {$cityName}, {$stateCode} yet."
                        : "Aún no tenemos restaurantes mexicanos registrados en {$cityName}, {$stateCode}." }}
                </p>
                <a href="/restaurantes" style="display:inline-block; padding:14px 32px; background:#D4AF37; color:#0B0B0B;
                   border-radius:8px; text-decoration:none; font-weight:600; font-size:14px;">
                    {{ $isEn ? 'Browse All Cities' : 'Ver todas las ciudades' }}
                </a>
            </div>
        @else
            <div wire:loading.style="opacity:0.5; pointer-events:none;"
                 style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px; transition:opacity .3s;">

                @foreach($restaurants as $restaurant)
                <article style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; overflow:hidden;
                                transition:transform .2s, border-color .2s, box-shadow .2s;"
                         onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#D4AF37'; this.style.boxShadow='0 12px 40px rgba(212,175,55,0.15)';"
                         onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#2A2A2A'; this.style.boxShadow='none';">

                    {{-- Image --}}
                    <a href="/restaurante/{{ $restaurant->slug }}" style="display:block; text-decoration:none;">
                        @if($restaurant->image)
                            <img src="{{ $restaurant->image }}"
                                 alt="{{ $restaurant->name }} - restaurante mexicano en {{ $restaurant->city }}"
                                 loading="lazy"
                                 style="width:100%; height:180px; object-fit:cover; display:block;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="display:none; width:100%; height:180px; background:#222; align-items:center;
                                        justify-content:center; font-size:48px;">🌮</div>
                        @else
                            <div style="width:100%; height:180px; background:#222; display:flex; align-items:center;
                                        justify-content:center; font-size:48px;">🌮</div>
                        @endif
                    </a>

                    {{-- Card body --}}
                    <div style="padding:20px;">
                        {{-- Name --}}
                        <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:17px; font-weight:700;
                                   color:#FFFFFF; margin:0 0 8px; line-height:1.3;">
                            <a href="/restaurante/{{ $restaurant->slug }}"
                               style="color:inherit; text-decoration:none;"
                               onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#FFFFFF'">
                                {{ $restaurant->name }}
                            </a>
                        </h2>

                        {{-- Address --}}
                        @if($restaurant->address)
                        <p style="font-size:13px; color:#777; margin:0 0 12px; display:flex; align-items:flex-start; gap:6px; line-height:1.4;">
                            <span style="color:#D4AF37; flex-shrink:0;">📍</span>
                            {{ Str::limit($restaurant->address, 60) }}
                        </p>
                        @else
                        <p style="font-size:13px; color:#777; margin:0 0 12px;">
                            {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code }}@endif
                        </p>
                        @endif

                        {{-- Rating + Price --}}
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; gap:6px;">
                                @if($restaurant->average_rating > 0)
                                    <span style="color:#D4AF37; font-size:15px;">★</span>
                                    <span style="color:#E5E5E5; font-size:14px; font-weight:600;">
                                        {{ number_format($restaurant->average_rating, 1) }}
                                    </span>
                                    @if($restaurant->total_reviews > 0)
                                    <span style="color:#555; font-size:12px;">
                                        ({{ $restaurant->total_reviews }}
                                        {{ $isEn ? ($restaurant->total_reviews === 1 ? 'review' : 'reviews') : 'reseñas' }})
                                    </span>
                                    @endif
                                @else
                                    <span style="color:#444; font-size:13px;">{{ $isEn ? 'No reviews yet' : 'Sin reseñas aún' }}</span>
                                @endif
                            </div>
                            @if($restaurant->price_range)
                            <span style="background:#2A2A2A; color:#D4AF37; border:1px solid #3A3A2A;
                                         padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                {{ $restaurant->price_range }}
                            </span>
                            @endif
                        </div>

                        {{-- CTA --}}
                        <a href="/restaurante/{{ $restaurant->slug }}"
                           style="display:flex; align-items:center; gap:6px; color:#D4AF37; font-size:13px;
                                  font-weight:600; text-decoration:none; transition:gap .2s;"
                           onmouseover="this.querySelector('span').style.marginLeft='4px'"
                           onmouseout="this.querySelector('span').style.marginLeft='0'">
                            {{ $isEn ? 'View restaurant' : 'Ver restaurante' }}
                            <span style="transition:margin .2s;">→</span>
                        </a>
                    </div>
                </article>
                @endforeach

            </div>

            {{-- Responsive CSS via style tag --}}
            <style>
                @media (max-width: 900px) {
                    [data-city-grid] { grid-template-columns: repeat(2, 1fr) !important; }
                }
                @media (max-width: 600px) {
                    [data-city-grid] { grid-template-columns: 1fr !important; }
                }
            </style>

            {{-- PAGINATION --}}
            <div style="margin-top:48px; display:flex; justify-content:center;">
                {{ $restaurants->links() }}
            </div>

        @endif
    </div>

    {{-- SEO TEXT BLOCK --}}
    <section style="background:#111111; border-top:1px solid #2A2A2A; padding:64px 24px;">
        <div style="max-width:800px; margin:0 auto;">

            <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:28px; color:#D4AF37;
                       margin:0 0 20px; font-weight:700;">
                @if($isEn)
                    Mexican Food Culture in {{ $cityName }}, {{ $stateCode }}
                @else
                    La Cocina Mexicana en {{ $cityName }}, {{ $stateCode }}
                @endif
            </h2>
            <p style="font-size:16px; color:#AAAAAA; line-height:1.8; margin:0 0 24px;">
                @if($isEn)
                    {{ $cityName }} is home to a vibrant Mexican restaurant scene, offering everything from
                    traditional taquerias and family-style cantinas to upscale contemporary Mexican cuisine.
                    Whether you're craving authentic street tacos, slow-cooked mole, fresh ceviche, or
                    handmade tamales, the Mexican restaurants in {{ $cityName }} bring the rich flavors of
                    Mexico directly to {{ $stateName }}.
                @else
                    {{ $cityName }} cuenta con una vibrante escena de restaurantes mexicanos, desde tradicionales
                    taquerías y cantinas familiares hasta alta cocina mexicana contemporánea. Ya sea que busques
                    auténticos tacos callejeros, mole de olla, ceviche fresco o tamales artesanales, los
                    restaurantes mexicanos en {{ $cityName }} traen los sabores más auténticos de México
                    directamente a {{ $stateName }}.
                @endif
            </p>

            <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:24px; color:#D4AF37;
                       margin:0 0 16px; font-weight:700;">
                @if($isEn)
                    How to Choose the Best Mexican Restaurant in {{ $cityName }}
                @else
                    Cómo Elegir el Mejor Restaurante Mexicano en {{ $cityName }}
                @endif
            </h2>
            <p style="font-size:16px; color:#AAAAAA; line-height:1.8; margin:0 0 24px;">
                @if($isEn)
                    With {{ $total }} Mexican restaurants listed in {{ $cityName }}, {{ $stateCode }},
                    finding the perfect spot can feel overwhelming. Our verified ratings and reviews
                    help you filter by quality, price range, and cuisine style. Look for restaurants
                    with consistent high ratings (4.0+) and a healthy number of reviews to ensure
                    an authentic dining experience. FAMER's community of food lovers has personally
                    visited and reviewed each location.
                @else
                    Con {{ $total }} restaurantes mexicanos listados en {{ $cityName }}, {{ $stateCode }},
                    encontrar el lugar perfecto puede parecer abrumador. Nuestras calificaciones y
                    reseñas verificadas te ayudan a filtrar por calidad, rango de precios y estilo
                    culinario. Busca restaurantes con calificaciones altas (4.0+) y un buen número de
                    reseñas para garantizar una experiencia gastronómica auténtica. La comunidad de
                    amantes de la comida de FAMER ha visitado y reseñado personalmente cada establecimiento.
                @endif
            </p>

            <h2 style="font-family:'Playfair Display',Georgia,serif; font-size:24px; color:#D4AF37;
                       margin:0 0 16px; font-weight:700;">
                @if($isEn)
                    Popular Mexican Dishes to Try in {{ $cityName }}
                @else
                    Platillos Mexicanos Populares en {{ $cityName }}
                @endif
            </h2>
            <p style="font-size:16px; color:#AAAAAA; line-height:1.8; margin:0;">
                @if($isEn)
                    Mexican cuisine in {{ $cityName }} spans the full spectrum of regional Mexican cooking.
                    You'll find Oaxacan moles with deep complex flavors, Veracruz-style seafood, Jalisco's
                    famous birria, and the iconic Mexico City-style tacos. Many restaurants in
                    {{ $cityName }} also feature an extensive selection of margaritas, mezcal, and
                    aguas frescas to complement your meal. Use FAMER to explore the best of Mexican
                    culinary traditions right here in {{ $cityName }}, {{ $stateName }}.
                @else
                    La gastronomía mexicana en {{ $cityName }} abarca toda la amplitud de la cocina
                    regional de México. Encontrarás moles oaxaqueños con sabores profundos y complejos,
                    mariscos al estilo veracruzano, birria famosa de Jalisco y los icónicos tacos
                    estilo Ciudad de México. Muchos restaurantes en {{ $cityName }} también ofrecen una
                    amplia selección de margaritas, mezcal y aguas frescas para acompañar tu comida.
                    Usa FAMER para explorar lo mejor de la tradición culinaria mexicana aquí mismo
                    en {{ $cityName }}, {{ $stateName }}.
                @endif
            </p>

        </div>
    </section>

    {{-- RELATED CITIES CTA --}}
    <section style="background:#0B0B0B; border-top:1px solid #1A1A1A; padding:48px 24px;">
        <div style="max-width:800px; margin:0 auto; text-align:center;">
            <p style="font-size:13px; letter-spacing:3px; text-transform:uppercase; color:#555; margin-bottom:12px;">
                {{ $isEn ? 'Keep Exploring' : 'Seguir Explorando' }}
            </p>
            <h3 style="font-family:'Playfair Display',Georgia,serif; font-size:24px; color:#E5E5E5;
                       margin:0 0 12px; font-weight:700;">
                @if($isEn)
                    Find Mexican Restaurants in Other Cities
                @else
                    Explorar Otras Ciudades
                @endif
            </h3>
            <p style="font-size:14px; color:#666; margin:0 0 32px;">
                @if($isEn)
                    Browse our directory of Mexican restaurants across 82 states in the US and Mexico.
                @else
                    Explora nuestro directorio de restaurantes mexicanos en 82 estados de Estados Unidos y México.
                @endif
            </p>
            <a href="/restaurantes"
               style="display:inline-flex; align-items:center; gap:10px; padding:14px 36px;
                      background:transparent; border:2px solid #D4AF37; color:#D4AF37;
                      border-radius:8px; text-decoration:none; font-size:14px; font-weight:600;
                      font-family:'Poppins',sans-serif; transition:all .2s;"
               onmouseover="this.style.background='#D4AF37'; this.style.color='#0B0B0B';"
               onmouseout="this.style.background='transparent'; this.style.color='#D4AF37';">
                {{ $isEn ? 'Browse All Cities' : 'Ver Todas las Ciudades' }} →
            </a>
        </div>
    </section>

</div>
