@extends('layouts.app')

@section('title')
Restaurantes {{ $category->name }} | FAMER
@endsection

@section('meta_description')Los mejores restaurantes de {{ $category->name }} auténtica mexicana. Directorio FAMER con {{ $restaurants->total() }} restaurantes verificados.@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">
    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF; list-style:none; padding:0; margin:0;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/restaurantes" style="color:#D4AF37; text-decoration:none;">Restaurantes</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ $category->name }}</li>
                </ol>
            </nav>
            <div style="text-align:center;">
                <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                    Restaurantes de {{ $category->name }}
                </h1>
                <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2rem;">
                    Los mejores restaurantes mexicanos especializados en {{ $category->name }}. {{ $restaurants->total() }} lugares verificados en FAMER.
                </p>
                <span style="background:#1A1A1A; border:1px solid #D4AF37; color:#D4AF37; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600;">
                    {{ number_format($restaurants->total()) }} Restaurantes
                </span>
            </div>
        </div>
    </div>

    <!-- Restaurant Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; margin-bottom:3rem;">
            @forelse($restaurants as $restaurant)
            <a href="/restaurante/{{ $restaurant->slug }}"
               style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                @if($restaurant->image)
                <div style="height:160px; overflow:hidden;">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                </div>
                @else
                <div style="height:80px; background:#2A2A2A; display:flex; align-items:center; justify-content:center;">
                    <span style="font-size:1.75rem;">🌮</span>
                </div>
                @endif
                <div style="padding:1rem;">
                    <h2 style="font-size:0.9375rem; font-weight:600; color:#F5F5F5; margin:0 0 0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $restaurant->name }}
                    </h2>
                    <p style="color:#9CA3AF; font-size:0.8125rem; margin:0 0 0.5rem;">
                        {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? '' }}@endif
                    </p>
                    @if($restaurant->average_rating)
                    <span style="color:#D4AF37; font-size:0.875rem;">★ {{ number_format($restaurant->average_rating, 1) }}</span>
                    @if($restaurant->total_reviews)
                    <span style="color:#6B7280; font-size:0.75rem; margin-left:0.5rem;">({{ number_format($restaurant->total_reviews) }})</span>
                    @endif
                    @endif
                </div>
            </a>
            @empty
            <div style="grid-column:1/-1; text-align:center; padding:4rem 0; color:#9CA3AF;">
                No se encontraron restaurantes en esta categoría.
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($restaurants->hasPages())
        <div style="display:flex; justify-content:center; gap:0.5rem; flex-wrap:wrap;">
            @if($restaurants->onFirstPage())
            <span style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#6B7280; font-size:0.875rem;">← Anterior</span>
            @else
            <a href="{{ $restaurants->previousPageUrl() }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#D4AF37; text-decoration:none; font-size:0.875rem;">← Anterior</a>
            @endif

            @foreach($restaurants->getUrlRange(max(1,$restaurants->currentPage()-2), min($restaurants->lastPage(),$restaurants->currentPage()+2)) as $page => $url)
            @if($page == $restaurants->currentPage())
            <span style="padding:0.5rem 1rem; background:#D4AF37; border-radius:8px; color:#0B0B0B; font-weight:700; font-size:0.875rem;">{{ $page }}</span>
            @else
            <a href="{{ $url }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#9CA3AF; text-decoration:none; font-size:0.875rem;">{{ $page }}</a>
            @endif
            @endforeach

            @if($restaurants->hasMorePages())
            <a href="{{ $restaurants->nextPageUrl() }}" style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#D4AF37; text-decoration:none; font-size:0.875rem;">Siguiente →</a>
            @else
            <span style="padding:0.5rem 1rem; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:8px; color:#6B7280; font-size:0.875rem;">Siguiente →</span>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Restaurantes de {{ addslashes($category->name) }}",
    "description": "Los mejores restaurantes mexicanos de {{ addslashes($category->name) }} en FAMER",
    "numberOfItems": {{ $restaurants->total() }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $i => $restaurant)
        {
            "@type": "ListItem",
            "position": {{ $i + 1 }},
            "item": {
                "@type": "Restaurant",
                "name": "{{ addslashes($restaurant->name) }}",
                "url": "{{ url('/restaurante/' . $restaurant->slug) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endpush
