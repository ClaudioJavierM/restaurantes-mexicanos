@extends('layouts.app')

@section('title')
{{ $isEn ? 'Mexican Restaurants by State | FAMER' : 'Restaurantes Mexicanos por Estado | FAMER' }}
@endsection

@section('meta_description'){{ $isEn
    ? 'Browse authentic Mexican restaurants by state across the US and Mexico. Find birria, tacos, tamales and more near you.'
    : 'Explora los mejores restaurantes mexicanos por estado en EUA y México. Birria, tacos, tamales y más.' }}@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">

    {{-- ─── HERO ─────────────────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; justify-content:center; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ $isEn ? 'States' : 'Estados' }}</li>
                </ol>
            </nav>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                {{ $isEn ? 'Mexican Restaurants' : 'Restaurantes Mexicanos' }}<br>
                <span style="color:#D4AF37;">{{ $isEn ? 'by State' : 'por Estado' }}</span>
            </h1>
            <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto;">
                {{ $isEn
                    ? 'Find authentic Mexican food across every state in the US and Mexico.'
                    : 'Encuentra comida mexicana auténtica en cada estado de EUA y México.' }}
            </p>
        </div>
    </div>

    {{-- ─── STATE GRIDS ────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        @php
            $usStates  = $grouped->get('US', collect());
            $mxStates  = $grouped->get('MX', collect());
        @endphp

        {{-- ── United States ── --}}
        @if($usStates->count() > 0)
        <section style="margin-bottom:4rem;">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.75rem;">
                <span style="font-size:1.75rem;">🇺🇸</span>
                <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin:0;">
                    {{ $isEn ? 'United States' : 'Estados Unidos' }}
                </h2>
                <span style="color:#9CA3AF; font-size:0.9375rem;">({{ $usStates->count() }} {{ $isEn ? 'states' : 'estados' }})</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:0.875rem;">
                @foreach($usStates as $state)
                @php
                    $stateSlug = \Illuminate\Support\Str::slug($state->name);
                @endphp
                <a href="/restaurantes-mexicanos-en-{{ $stateSlug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-weight:600; color:#F5F5F5; font-size:0.9375rem;">{{ $state->name }}</span>
                        <span style="background:#2A2A2A; color:#9CA3AF; font-size:0.75rem; padding:0.2rem 0.5rem; border-radius:4px; font-weight:600;">
                            {{ $state->code }}
                        </span>
                    </div>
                    <div style="color:#D4AF37; font-size:0.8125rem; font-weight:600;">
                        {{ number_format($state->restaurants_count) }} {{ $isEn ? 'restaurants' : 'restaurantes' }}
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ── México ── --}}
        @if($mxStates->count() > 0)
        <section style="margin-bottom:4rem;">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.75rem;">
                <span style="font-size:1.75rem;">🇲🇽</span>
                <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin:0;">
                    México
                </h2>
                <span style="color:#9CA3AF; font-size:0.9375rem;">({{ $mxStates->count() }} {{ $isEn ? 'states' : 'estados' }})</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:0.875rem;">
                @foreach($mxStates as $state)
                @php
                    $stateSlug = \Illuminate\Support\Str::slug($state->name);
                @endphp
                <a href="/restaurantes-mexicanos-en-{{ $stateSlug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                        <span style="font-weight:600; color:#F5F5F5; font-size:0.9375rem;">{{ $state->name }}</span>
                        <span style="background:#2A2A2A; color:#9CA3AF; font-size:0.75rem; padding:0.2rem 0.5rem; border-radius:4px; font-weight:600;">
                            {{ $state->code }}
                        </span>
                    </div>
                    <div style="color:#D4AF37; font-size:0.8125rem; font-weight:600;">
                        {{ number_format($state->restaurants_count) }} {{ $isEn ? 'restaurants' : 'restaurantes' }}
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- SEO footer block --}}
        <section style="padding:2.5rem 0; border-top:1px solid #2A2A2A;">
            <div style="max-width:800px; margin:0 auto; text-align:center;">
                <p style="color:#6B7280; font-size:0.9375rem; line-height:1.7;">
                    @if($isEn)
                        FAMER is the largest directory of authentic Mexican restaurants in the US and Mexico.
                        Choose your state above to discover the best tacos, birria, tamales, pozole and more near you.
                    @else
                        FAMER es el directorio más grande de restaurantes mexicanos auténticos en EUA y México.
                        Selecciona tu estado para descubrir los mejores tacos, birria, tamales, pozole y más.
                    @endif
                </p>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "FAMER",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ $isEn ? 'States' : 'Estados' }}",
            "item": "{{ url('/estados') }}"
        }
    ]
}
</script>
@endpush
