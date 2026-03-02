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
<div class="bg-gradient-to-br from-emerald-600 via-green-600 to-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">
            Restaurantes Mexicanos por Estado
        </h1>
        <p class="text-lg opacity-90 max-w-3xl">
            Explora nuestra guia completa de restaurantes mexicanos en Estados Unidos.
            Selecciona un estado para ver las mejores ciudades y restaurantes.
        </p>

        <!-- Global Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($totalRestaurants) }}</p>
                <p class="text-sm opacity-80">Restaurantes</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ $totalStates }}</p>
                <p class="text-sm opacity-80">Estados</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($totalCities) }}</p>
                <p class="text-sm opacity-80">Ciudades</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($claimedCount) }}</p>
                <p class="text-sm opacity-80">Verificados</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <!-- Top States by Restaurant Count -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Estados con Mas Restaurantes Mexicanos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topStates as $state)
            <a href="{{ route('city-guides.state', $state->code) }}"
               class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition">
                            {{ $state->name }}
                        </h3>
                        <p class="text-gray-500 text-sm">{{ $state->code }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($state->restaurants_count) }}</p>
                        <p class="text-xs text-gray-500">restaurantes</p>
                    </div>
                </div>
                @if($state->top_cities)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Top ciudades:</span>
                        {{ implode(', ', array_slice($state->top_cities, 0, 3)) }}
                    </p>
                </div>
                @endif
            </a>
            @endforeach
        </div>
    </section>

    <!-- All States A-Z -->
    <section>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Todos los Estados (A-Z)</h2>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($allStates as $state)
                <a href="{{ route('city-guides.state', $state->code) }}"
                   class="flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition group">
                    <span class="text-gray-700 group-hover:text-emerald-600 transition">{{ $state->name }}</span>
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded group-hover:bg-emerald-100 group-hover:text-emerald-700 transition">
                        {{ $state->restaurants_count }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="mt-12 bg-gradient-to-r from-emerald-600 to-green-600 rounded-lg p-8 text-white text-center">
        <h2 class="text-2xl font-bold mb-4">Eres Dueno de un Restaurante?</h2>
        <p class="text-lg opacity-90 mb-6 max-w-2xl mx-auto">
            Reclama tu perfil GRATIS y toma control de tu informacion.
            Obtiene la insignia de verificado y destaca sobre la competencia.
        </p>
        <a href="{{ route('claim.restaurant') }}"
           class="inline-block bg-white text-emerald-600 font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
            Reclamar Mi Restaurante
        </a>
    </section>

    <!-- SEO Content -->
    <section class="mt-12 bg-gray-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Guia de Restaurantes Mexicanos en Estados Unidos
        </h2>
        <div class="prose max-w-none text-gray-600">
            <p>
                Bienvenido a la guia mas completa de restaurantes mexicanos en Estados Unidos.
                Nuestro directorio combina datos de Yelp y Google para ofrecerte informacion
                actualizada sobre miles de restaurantes en todo el pais.
            </p>
            <p class="mt-4">
                Desde taquerias autenticas en California y Texas hasta restaurantes gourmet
                en Nueva York, encontraras opciones para todos los gustos y presupuestos.
                Cada perfil incluye ratings, fotos, horarios y resenas verificadas.
            </p>
            <p class="mt-4">
                Los restaurantes verificados en nuestra plataforma han reclamado su perfil
                y mantienen su informacion actualizada, garantizando datos precisos para
                los clientes que buscan la mejor comida mexicana cerca de ellos.
            </p>
        </div>
    </section>
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
