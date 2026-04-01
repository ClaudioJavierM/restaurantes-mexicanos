@extends('layouts.app')

@section('title', 'Restaurantes Mexicanos por Estado | Guia Completa USA')
@section('meta_description', 'Encuentra los mejores restaurantes mexicanos en cada estado de USA. Directorio completo con ratings, resenas y fotos de Yelp y Google.')

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Mexican Restaurants by State | Complete USA Guide">
<meta property="og:description" content="Find the best Mexican restaurants in every state. {{ number_format($totalRestaurants) }} restaurants across {{ $totalStates }} states and {{ number_format($totalCities) }} cities.">
<meta property="og:image" content="{{ asset('images/branding/og-states-guide.jpg') }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Mexican Restaurants by State | USA Guide">
<meta name="twitter:description" content="{{ number_format($totalRestaurants) }} Mexican restaurants in {{ $totalStates }} states. Find authentic Mexican food near you.">
@endpush

@section('content')
<div style="background:#0B0B0B; min-height:100vh; color:#F5F5F5;">

    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex;justify-content:center;flex-wrap:wrap;gap:0.5rem;align-items:center;font-size:0.875rem;color:#9CA3AF;list-style:none;padding:0;margin:0;">
                    <li><a href="/" style="color:#D4AF37;text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">Guía</li>
                </ol>
            </nav>
            <h1 style="font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:700;color:#F5F5F5;margin-bottom:1rem;line-height:1.2;">
                Guía de Restaurantes Mexicanos<br>
                <span style="color:#D4AF37;">por Estado</span>
            </h1>
            <p style="color:#9CA3AF;font-size:1.125rem;max-width:600px;margin:0 auto 2.5rem;">
                Explora {{ number_format($totalRestaurants) }} restaurantes en {{ $totalStates }} estados. Encuentra auténtica comida mexicana en tu ciudad.
            </p>

            <!-- Stats -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;max-width:700px;margin:0 auto;">
                @foreach([
                    [$totalRestaurants, 'Restaurantes'],
                    [$totalStates, 'Estados'],
                    [$totalCities, 'Ciudades'],
                    [$claimedCount, 'Verificados'],
                ] as [$val, $label])
                <div style="background:#1A1A1A;border:1px solid #2A2A2A;border-radius:12px;padding:1.25rem 1rem;text-align:center;">
                    <div style="font-size:1.75rem;font-weight:800;color:#D4AF37;">{{ number_format($val) }}</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:0.25rem;">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Top States -->
        <section style="margin-bottom:3rem;">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#F5F5F5;margin-bottom:1.5rem;">
                Estados con Más Restaurantes Mexicanos
            </h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
                @foreach($topStates as $state)
                <a href="{{ route('city-guides.state', $state->code) }}"
                   style="display:block;background:#1A1A1A;border:1px solid #2A2A2A;border-radius:12px;padding:1.25rem;text-decoration:none;transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                        <div>
                            <div style="font-weight:700;color:#F5F5F5;font-size:1.1rem;">{{ $state->name }}</div>
                            <div style="color:#6B7280;font-size:0.8rem;">{{ $state->code }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:1.5rem;font-weight:800;color:#D4AF37;">{{ number_format($state->restaurants_count) }}</div>
                            <div style="font-size:0.75rem;color:#6B7280;">restaurantes</div>
                        </div>
                    </div>
                    @if($state->top_cities)
                    <div style="border-top:1px solid #2A2A2A;padding-top:0.75rem;">
                        <span style="font-size:0.8rem;color:#6B7280;">Top ciudades: </span>
                        <span style="font-size:0.8rem;color:#9CA3AF;">{{ implode(', ', array_slice($state->top_cities, 0, 3)) }}</span>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        </section>

        <!-- All States A-Z -->
        <section style="margin-bottom:3rem;">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#F5F5F5;margin-bottom:1.5rem;">
                Todos los Estados (A-Z)
            </h2>
            <div style="background:#1A1A1A;border:1px solid #2A2A2A;border-radius:12px;padding:1.5rem;">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.5rem;">
                    @foreach($allStates as $state)
                    <a href="{{ route('city-guides.state', $state->code) }}"
                       style="display:flex;align-items:center;justify-content:space-between;padding:0.625rem 0.75rem;border-radius:8px;text-decoration:none;transition:background 0.15s;"
                       onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                        <span style="color:#E5E7EB;font-size:0.875rem;">{{ $state->name }}</span>
                        <span style="font-size:0.75rem;color:#D4AF37;font-weight:600;">{{ number_format($state->restaurants_count) }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section style="background:linear-gradient(135deg,#1A1A1A 0%,#2A2A2A 100%);border:1px solid #D4AF37;border-radius:16px;padding:2.5rem;text-align:center;margin-bottom:3rem;">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.75rem;font-weight:700;color:#F5F5F5;margin-bottom:0.75rem;">¿Eres Dueño de un Restaurante?</h2>
            <p style="color:#9CA3AF;margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto;">
                Reclama tu perfil GRATIS y toma control de tu información. Obtén la insignia de verificado.
            </p>
            <a href="{{ route('claim.restaurant') }}"
               style="display:inline-block;background:#D4AF37;color:#0B0B0B;font-weight:700;padding:0.875rem 2rem;border-radius:9999px;text-decoration:none;font-size:1rem;"
               onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                Reclamar Mi Restaurante Gratis
            </a>
        </section>

        <!-- SEO text -->
        <section style="color:#6B7280;font-size:0.9rem;line-height:1.7;max-width:700px;">
            <p>Bienvenido a la guía más completa de restaurantes mexicanos en Estados Unidos. Nuestro directorio combina datos de Google para ofrecerte información actualizada sobre miles de restaurantes en todo el país.</p>
            <p style="margin-top:0.75rem;">Desde taquerías auténticas en California y Texas hasta restaurantes gourmet en Nueva York. Cada perfil incluye ratings, fotos y reseñas verificadas.</p>
        </section>
    </div>
</div>

@push('scripts')
<!-- BreadcrumbList Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Inicio",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Guía por Ciudad"
        }
    ]
}
</script>

<!-- ItemList Schema for States -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Mexican Restaurants by State in USA",
    "description": "Complete directory of {{ number_format($totalRestaurants) }} Mexican restaurants across {{ $totalStates }} states in the United States",
    "numberOfItems": {{ $totalStates }},
    "itemListElement": [
        @foreach($topStates->take(10) as $index => $state)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "State",
                "name": "{{ $state->name }}",
                "url": "{{ route('city-guides.state', $state->code) }}",
                "containedInPlace": {
                    "@@type": "Country",
                    "name": "United States"
                }
            }
        }{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>

<!-- WebPage Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Mexican Restaurants by State | Complete USA Guide",
    "description": "Find the best Mexican restaurants in every state across the USA. {{ number_format($totalRestaurants) }} restaurants in {{ $totalStates }} states and {{ number_format($totalCities) }} cities.",
    "url": "{{ url()->current() }}",
    "inLanguage": "{{ app()->getLocale() }}",
    "isPartOf": {
        "@@type": "WebSite",
        "name": "{{ __('app.site_name') }}",
        "url": "{{ url('/') }}"
    }
}
</script>
@endpush
@endsection
