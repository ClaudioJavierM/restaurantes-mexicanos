@extends('layouts.app')

@section('title')
Restaurantes Mexicanos Cerca de Mí | FAMER
@endsection

@section('meta_description')Encuentra los mejores restaurantes mexicanos auténticos cerca de ti. Usa tu ubicación para descubrir birria, tamales, pozole y más platillos tradicionales mexicanos.@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">
    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; justify-content:center; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">Cerca de Mí</li>
                </ol>
            </nav>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                Restaurantes Mexicanos<br>
                <span style="color:#D4AF37;">Cerca de Mí</span>
            </h1>
            <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2.5rem;">
                Descubre los mejores restaurantes mexicanos auténticos en tu área. Birria, tamales, pozole, tacos y mucho más.
            </p>

            <!-- Geolocation Button -->
            <div id="geo-section">
                <button onclick="findNearMe()" id="geo-btn"
                    style="background:#D4AF37; color:#0B0B0B; padding:1rem 2.5rem; border:none; border-radius:9999px; font-size:1.125rem; font-weight:700; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:0.75rem; transition:opacity 0.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Usar Mi Ubicación
                </button>
                <p id="geo-status" style="color:#9CA3AF; font-size:0.875rem; margin-top:1rem; min-height:1.25rem;"></p>
            </div>
        </div>
    </div>

    <!-- Dynamic results (shown after geolocation) -->
    <div id="nearby-results" style="display:none;" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;" id="nearby-title">
            Restaurantes Cerca de Ti
        </h2>
        <div id="nearby-grid" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem;">
            <!-- Filled by JS -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Top Cities Grid -->
        <section style="margin-bottom:4rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">
                Ciudades con Más Restaurantes Mexicanos
            </h2>
            <p style="color:#9CA3AF; margin-bottom:2rem;">Busca directamente por ciudad o deja que detectemos tu ubicación.</p>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem;">
                @foreach($topCities as $city)
                @php
                    $citySlug = \Illuminate\Support\Str::slug($city->city);
                    $stateCode = strtolower($city->state_code);
                @endphp
                <a href="/mejores/{{ $stateCode }}/{{ $citySlug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="font-weight:600; color:#F5F5F5; margin-bottom:0.25rem; font-size:0.9375rem;">{{ $city->city }}</div>
                    <div style="color:#9CA3AF; font-size:0.8125rem; margin-bottom:0.5rem;">{{ $city->state_name }}</div>
                    <div style="color:#D4AF37; font-size:0.8125rem; font-weight:600;">{{ $city->restaurant_count }} restaurantes</div>
                </a>
                @endforeach
            </div>
        </section>

        <!-- Featured Restaurants -->
        <section style="margin-bottom:4rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">
                Restaurantes Mejor Calificados
            </h2>
            <p style="color:#9CA3AF; margin-bottom:2rem;">Los favoritos de nuestra comunidad en toda la red FAMER.</p>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem;">
                @foreach($featuredRestaurants as $restaurant)
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    @if($restaurant->cover_image)
                    <div style="height:160px; overflow:hidden;">
                        <img src="{{ $restaurant->cover_image }}" alt="{{ $restaurant->name }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    @else
                    <div style="height:100px; background:#2A2A2A; display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:2rem;">🌮</span>
                    </div>
                    @endif
                    <div style="padding:1rem;">
                        <h3 style="font-weight:600; color:#F5F5F5; margin-bottom:0.25rem; font-size:0.9375rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $restaurant->name }}
                        </h3>
                        <p style="color:#9CA3AF; font-size:0.8125rem; margin-bottom:0.5rem;">
                            {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? '' }}@endif
                        </p>
                        @if($restaurant->average_rating)
                        <span style="color:#D4AF37; font-size:0.875rem;">★ {{ number_format($restaurant->average_rating, 1) }}</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </section>

        <!-- SEO Content -->
        <section style="padding:3rem 0; border-top:1px solid #2A2A2A;">
            <div style="max-width:800px; margin:0 auto;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                    La Mejor Guía de Restaurantes Mexicanos Auténticos
                </h2>
                <div style="color:#9CA3AF; line-height:1.8; font-size:1rem;">
                    <p style="margin-bottom:1rem;">
                        FAMER es el directorio más completo de restaurantes mexicanos auténticos en Estados Unidos y México. Con más de 25,000 restaurantes listados, encontrarás desde pequeñas taquerías familiares hasta restaurantes de alta cocina mexicana.
                    </p>
                    <p style="margin-bottom:1rem;">
                        Nuestra misión es conectar a los amantes de la cocina mexicana con los mejores restaurantes, chefs y experiencias culinarias. Cada restaurante en FAMER ha sido verificado y cuenta con reseñas reales de nuestra comunidad.
                    </p>
                    <p>
                        Busca por platillo (<a href="/birria" style="color:#D4AF37;">birria</a>, <a href="/tamales" style="color:#D4AF37;">tamales</a>, <a href="/pozole" style="color:#D4AF37;">pozole</a>), por ciudad o usa tu ubicación para encontrar el restaurante mexicano perfecto cerca de ti.
                    </p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function findNearMe() {
    const btn = document.getElementById('geo-btn');
    const status = document.getElementById('geo-status');

    if (!navigator.geolocation) {
        status.textContent = 'La geolocalización no está disponible en tu navegador.';
        return;
    }

    btn.disabled = true;
    btn.style.opacity = '0.7';
    status.textContent = 'Detectando tu ubicación...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            status.textContent = 'Buscando restaurantes cerca de ti...';
            searchNearby(lat, lng);
        },
        function(error) {
            btn.disabled = false;
            btn.style.opacity = '1';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    status.textContent = 'Permiso de ubicación denegado. Busca por ciudad arriba.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    status.textContent = 'Ubicación no disponible. Busca por ciudad arriba.';
                    break;
                default:
                    status.textContent = 'No se pudo obtener tu ubicación. Busca por ciudad arriba.';
            }
        },
        { timeout: 10000, enableHighAccuracy: false }
    );
}

function searchNearby(lat, lng) {
    // Use our search API to find nearby restaurants
    fetch(`/api/v1/restaurants/nearby?lat=${lat}&lng=${lng}&limit=12`)
        .then(r => r.json())
        .then(data => {
            const restaurants = data.data || data.restaurants || [];
            const status = document.getElementById('geo-status');
            const resultsDiv = document.getElementById('nearby-results');
            const grid = document.getElementById('nearby-grid');
            const title = document.getElementById('nearby-title');

            if (restaurants.length > 0) {
                title.textContent = `${restaurants.length} Restaurantes Cerca de Ti`;
                grid.innerHTML = restaurants.map(r => `
                    <a href="/restaurante/${r.slug}" style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; text-decoration:none;"
                       onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                        <h3 style="font-weight:600; color:#F5F5F5; margin-bottom:0.25rem; font-size:0.9375rem;">${r.name}</h3>
                        <p style="color:#9CA3AF; font-size:0.8125rem; margin-bottom:0.5rem;">${r.city || ''}</p>
                        ${r.rating ? `<span style="color:#D4AF37; font-size:0.875rem;">★ ${parseFloat(r.rating).toFixed(1)}</span>` : ''}
                        ${r.distance ? `<span style="color:#6B7280; font-size:0.8125rem; margin-left:0.5rem;">${r.distance.toFixed(1)} mi</span>` : ''}
                    </a>
                `).join('');
                resultsDiv.style.display = 'block';
                resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                status.textContent = '';
            } else {
                status.textContent = 'No encontramos restaurantes en tu área inmediata. Busca por ciudad arriba.';
                document.getElementById('geo-btn').disabled = false;
                document.getElementById('geo-btn').style.opacity = '1';
            }
        })
        .catch(() => {
            const status = document.getElementById('geo-status');
            status.textContent = 'Error al buscar. Usa los enlaces de ciudad abajo.';
            document.getElementById('geo-btn').disabled = false;
            document.getElementById('geo-btn').style.opacity = '1';
        });
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Restaurantes Mexicanos Cerca de Mí",
    "description": "Encuentra los mejores restaurantes mexicanos auténticos cerca de ti.",
    "url": "{{ url('/restaurantes-mexicanos-cerca-de-mi') }}"
}
</script>
@endpush
