@extends('layouts.app')

@section('title')
{{ $data['title'] }} | FAMER
@endsection

@section('meta_description'){{ $data['description'] }}@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">
    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; justify-content:center; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF; list-style:none; padding:0; margin:0;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/{{ $data['slug'] }}" style="color:#D4AF37; text-decoration:none;">{{ $data['name'] }}</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">Cerca de Mí</li>
                </ol>
            </nav>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                {{ $data['hero_text'] }}
            </h1>
            <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2.5rem;">
                {{ $data['description'] }}
            </p>

            <!-- Geo button -->
            <button onclick="findNearMe('{{ $dish }}')" id="geo-btn"
                style="background:#D4AF37; color:#0B0B0B; padding:1rem 2.5rem; border:none; border-radius:9999px; font-size:1.125rem; font-weight:700; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:0.75rem;"
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

    <!-- Dynamic results -->
    <div id="nearby-results" style="display:none;" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 id="nearby-title" style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
            {{ $data['name'] }} Cerca de Ti
        </h2>
        <div id="nearby-grid" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1rem;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Cities -->
        @if($topCities->isNotEmpty())
        <section style="margin-bottom:3rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                Ciudades con {{ $data['name'] }} Auténtica
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:0.875rem;">
                @foreach($topCities as $city)
                @php $citySlug = \Illuminate\Support\Str::slug($city->city); $stateCode = strtolower($city->state_code); @endphp
                <a href="/mejores/{{ $stateCode }}/{{ $citySlug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:10px; padding:1rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="font-weight:600; color:#F5F5F5; font-size:0.875rem; margin-bottom:0.25rem;">{{ $city->city }}</div>
                    <div style="color:#9CA3AF; font-size:0.75rem; margin-bottom:0.25rem;">{{ $city->state_name }}</div>
                    <div style="color:#D4AF37; font-size:0.75rem; font-weight:600;">{{ $city->restaurant_count }} lugares</div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Top Restaurants -->
        <section>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                Los Mejores Lugares para {{ $data['name'] }}
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem;">
                @foreach($topRestaurants as $restaurant)
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    @if($restaurant->image)
                    <div style="height:140px; overflow:hidden;">
                        <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    @else
                    <div style="height:80px; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:1.75rem;">🌮</div>
                    @endif
                    <div style="padding:0.875rem;">
                        <h3 style="font-weight:600; color:#F5F5F5; font-size:0.9rem; margin:0 0 0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $restaurant->name }}</h3>
                        <p style="color:#9CA3AF; font-size:0.8rem; margin:0 0 0.375rem;">
                            {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? '' }}@endif
                        </p>
                        @if($restaurant->average_rating)
                        <span style="color:#D4AF37; font-size:0.8125rem;">★ {{ number_format($restaurant->average_rating, 1) }}</span>
                        @if($restaurant->total_reviews)
                        <span style="color:#6B7280; font-size:0.75rem; margin-left:0.375rem;">({{ number_format($restaurant->total_reviews) }})</span>
                        @endif
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function findNearMe(dish) {
    const btn = document.getElementById('geo-btn');
    const status = document.getElementById('geo-status');
    if (!navigator.geolocation) { status.textContent = 'Geolocalización no disponible.'; return; }
    btn.disabled = true; btn.style.opacity = '0.7';
    status.textContent = 'Detectando tu ubicación...';
    navigator.geolocation.getCurrentPosition(
        pos => {
            status.textContent = 'Buscando ' + dish + ' cerca de ti...';
            fetch(`/api/v1/restaurants/nearby?lat=${pos.coords.latitude}&lng=${pos.coords.longitude}&limit=12`)
                .then(r => r.json())
                .then(data => {
                    const items = data.data || data.restaurants || [];
                    if (items.length > 0) {
                        document.getElementById('nearby-grid').innerHTML = items.map(r => `
                            <a href="/restaurante/${r.slug}" style="display:block;background:#1A1A1A;border:1px solid #2A2A2A;border-radius:12px;padding:1rem;text-decoration:none;" onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                                <h3 style="font-weight:600;color:#F5F5F5;font-size:0.9rem;margin:0 0 0.25rem;">${r.name}</h3>
                                <p style="color:#9CA3AF;font-size:0.8rem;margin:0;">${r.city||''}</p>
                                ${r.rating ? `<span style="color:#D4AF37;font-size:0.8rem;">★ ${parseFloat(r.rating).toFixed(1)}</span>` : ''}
                            </a>
                        `).join('');
                        document.getElementById('nearby-results').style.display = 'block';
                        document.getElementById('nearby-results').scrollIntoView({behavior:'smooth'});
                        status.textContent = '';
                    } else { status.textContent = 'No encontramos resultados cercanos. Busca por ciudad abajo.'; btn.disabled=false; btn.style.opacity='1'; }
                }).catch(() => { status.textContent = 'Error al buscar. Usa los enlaces de ciudad.'; btn.disabled=false; btn.style.opacity='1'; });
        },
        err => { btn.disabled=false; btn.style.opacity='1'; status.textContent = 'No se pudo obtener tu ubicación. Busca por ciudad abajo.'; }
    );
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ addslashes($data['title']) }}",
    "description": "{{ addslashes($data['description']) }}",
    "url": "{{ url('/' . $data['slug'] . '-cerca-de-mi') }}"
}
</script>
@endpush
