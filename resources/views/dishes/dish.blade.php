@extends('layouts.app')

@section('title')
{{ $data['title'] }} | FAMER
@endsection

@section('meta_description'){{ $data['description'] }}@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">
    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/restaurantes" style="color:#D4AF37; text-decoration:none;">Restaurantes</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ $data['name'] }}</li>
                </ol>
            </nav>

            <!-- Title -->
            <div style="text-align:center;">
                <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                    {{ $data['hero_text'] }}
                </h1>
                <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2rem;">
                    {{ $data['description'] }}
                </p>
                <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                    <span style="background:#1A1A1A; border:1px solid #D4AF37; color:#D4AF37; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600;">
                        {{ $restaurants->count() }} Restaurantes
                    </span>
                    <span style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem;">
                        Auténtica Cocina Mexicana
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Restaurant List -->
            <div class="lg:col-span-2">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2A2A2A;">
                    Los Mejores Restaurantes de {{ $data['name'] }}
                </h2>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    @forelse($restaurants as $index => $restaurant)
                    <a href="/restaurante/{{ $restaurant->slug }}" style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; text-decoration:none; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                        <div style="display:flex; gap:1rem; align-items:flex-start;">
                            <!-- Rank Badge -->
                            <div style="flex-shrink:0; width:2.5rem; height:2.5rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.875rem;
                                @if($index === 0) background:#D4AF37; color:#0B0B0B;
                                @elseif($index === 1) background:#C0C0C0; color:#0B0B0B;
                                @elseif($index === 2) background:#CD7F32; color:#0B0B0B;
                                @else background:#2A2A2A; color:#9CA3AF; border:1px solid #374151;
                                @endif">
                                #{{ $index + 1 }}
                            </div>
                            <!-- Info -->
                            <div style="flex:1; min-width:0;">
                                <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $restaurant->name }}
                                </h3>
                                <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 0.5rem;">
                                    {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? $restaurant->state->name }}@endif
                                </p>
                                @if($restaurant->rating)
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <span style="color:#D4AF37; font-size:0.875rem;">★ {{ number_format($restaurant->rating, 1) }}</span>
                                    @if($restaurant->review_count)
                                    <span style="color:#6B7280; font-size:0.75rem;">({{ number_format($restaurant->review_count) }} reseñas)</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <p style="color:#9CA3AF; text-align:center; padding:3rem 0;">
                        No se encontraron restaurantes para este platillo aún.
                    </p>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- About this dish -->
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; position:sticky; top:1rem;">
                    <h3 style="font-family:'Playfair Display',serif; font-size:1.125rem; font-weight:700; color:#F5F5F5; margin-bottom:1rem;">
                        Sobre la {{ $data['name'] }}
                    </h3>
                    <p style="color:#9CA3AF; font-size:0.875rem; line-height:1.7; margin-bottom:1.5rem;">
                        {{ $data['body'] }}
                    </p>
                    <div style="border-top:1px solid #2A2A2A; padding-top:1rem;">
                        <p style="color:#6B7280; font-size:0.75rem; text-align:center;">
                            Directorio de restaurantes mexicanos auténticos
                        </p>
                    </div>
                </div>

                <!-- Other dishes -->
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">
                    <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin-bottom:1rem;">Otros Platillos</h3>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        @foreach([['birria','Birria'],['tamales','Tamales'],['pozole','Pozole']] as [$slug,$label])
                        @if($slug !== $dish)
                        <a href="/{{ $slug }}" style="display:block; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:8px; padding:0.75rem 1rem; color:#D4AF37; text-decoration:none; font-size:0.875rem; font-weight:500; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                            → {{ $label }}
                        </a>
                        @endif
                        @endforeach
                        <a href="/restaurantes" style="display:block; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:8px; padding:0.75rem 1rem; color:#9CA3AF; text-decoration:none; font-size:0.875rem; font-weight:500; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                            → Todos los Restaurantes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Text Section -->
        <div style="margin-top:4rem; padding-top:3rem; border-top:1px solid #2A2A2A;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                ¿Dónde comer {{ $data['name'] }} auténtica?
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.5rem;">
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">
                    <h3 style="color:#D4AF37; font-size:1rem; font-weight:600; margin-bottom:0.75rem;">Texas</h3>
                    <p style="color:#9CA3AF; font-size:0.875rem; line-height:1.6;">Houston, San Antonio, Dallas y Austin tienen una gran concentración de restaurantes mexicanos auténticos que sirven {{ $data['name'] }}.</p>
                </div>
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">
                    <h3 style="color:#D4AF37; font-size:1rem; font-weight:600; margin-bottom:0.75rem;">California</h3>
                    <p style="color:#9CA3AF; font-size:0.875rem; line-height:1.6;">Los Ángeles, San Francisco y San Diego cuentan con una vibrante escena de cocina mexicana tradicional con excelentes opciones de {{ $data['name'] }}.</p>
                </div>
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">
                    <h3 style="color:#D4AF37; font-size:1rem; font-weight:600; margin-bottom:0.75rem;">Illinois</h3>
                    <p style="color:#9CA3AF; font-size:0.875rem; line-height:1.6;">Chicago tiene una de las comunidades mexicanas más grandes del Midwest, con restaurantes que ofrecen auténtica {{ $data['name'] }} y platillos tradicionales.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ addslashes($data['title']) }}",
    "description": "{{ addslashes($data['description']) }}",
    "numberOfItems": {{ $restaurants->count() }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $index => $restaurant)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ addslashes($restaurant->name) }}",
                "url": "{{ url('/restaurante/' . $restaurant->slug) }}",
                "address": {
                    "@@type": "PostalAddress",
                    "addressLocality": "{{ addslashes($restaurant->city ?? '') }}"
                    @if($restaurant->state),
                    "addressRegion": "{{ addslashes($restaurant->state->code ?? $restaurant->state->name ?? '') }}"
                    @endif
                }
                @if($restaurant->rating),
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ $restaurant->rating }}",
                    "reviewCount": "{{ $restaurant->review_count ?? 0 }}"
                }
                @endif
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endpush
