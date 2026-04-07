@extends('layouts.app')

@section('title')
Mejor {{ $dishData['name'] }} en {{ $cityName }}, {{ $stateName }} | FAMER
@endsection

@section('meta_description')Encuentra los mejores restaurantes de {{ $dishData['name'] }} auténtico en {{ $cityName }}, {{ $stateName }}. Lista verificada por FAMER.@endsection

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">
    <!-- Hero -->
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF; list-style:none; padding:0; margin:0;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/{{ $dish }}" style="color:#D4AF37; text-decoration:none;">{{ $dishData['name'] }}</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/{{ $dish }}-en-{{ $stateCode }}" style="color:#D4AF37; text-decoration:none;">{{ $stateName }}</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ $cityName }}</li>
                </ol>
            </nav>
            <div style="text-align:center;">
                <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                    {{ $dishData['name'] }} en {{ $cityName }}
                </h1>
                <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2rem;">
                    Los mejores restaurantes de {{ $dishData['name'] }} auténtico en {{ $cityName }}, {{ $stateName }}. {{ $restaurants->count() }} lugares verificados.
                </p>
                <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap;">
                    <span style="background:#1A1A1A; border:1px solid #D4AF37; color:#D4AF37; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600;">
                        {{ $restaurants->count() }} Restaurantes
                    </span>
                    <a href="/{{ $dish }}-en-{{ $stateCode }}"
                       style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; text-decoration:none;"
                       onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                        Ver todos en {{ $stateName }}
                    </a>
                    <a href="/{{ $dish }}"
                       style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; text-decoration:none;"
                       onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                        Ver todos los estados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Restaurant list -->
            <div class="lg:col-span-2">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem; padding-bottom:0.75rem; border-bottom:1px solid #2A2A2A;">
                    Mejores {{ $dishData['name'] }} en {{ $cityName }}
                </h2>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    @forelse($restaurants as $index => $restaurant)
                    <a href="/restaurante/{{ $restaurant->slug }}"
                       style="display:flex; gap:1rem; align-items:center; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1rem; text-decoration:none; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                        <div style="flex-shrink:0; width:2.5rem; height:2.5rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.875rem;
                            @if($index===0) background:#D4AF37;color:#0B0B0B;
                            @elseif($index===1) background:#C0C0C0;color:#0B0B0B;
                            @elseif($index===2) background:#CD7F32;color:#0B0B0B;
                            @else background:#2A2A2A;color:#9CA3AF;
                            @endif">
                            #{{ $index+1 }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $restaurant->name }}</h3>
                            <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 0.25rem;">{{ $restaurant->city }}, {{ $stateName }}</p>
                            @if($restaurant->average_rating)
                            <span style="color:#D4AF37; font-size:0.8125rem;">&#9733; {{ number_format($restaurant->average_rating,1) }}</span>
                            @if($restaurant->total_reviews)
                            <span style="color:#6B7280; font-size:0.75rem; margin-left:0.375rem;">({{ number_format($restaurant->total_reviews) }})</span>
                            @endif
                            @endif
                        </div>
                    </a>
                    @empty
                    <p style="color:#9CA3AF; text-align:center; padding:3rem 0;">No encontramos restaurantes en {{ $cityName }} aún.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem; position:sticky; top:1rem;">
                    <h3 style="font-size:1rem; font-weight:600; color:#F5F5F5; margin-bottom:1rem;">{{ $dishData['name'] }} en {{ $stateName }}</h3>
                    <a href="/{{ $dish }}-en-{{ $stateCode }}"
                       style="display:block; background:#0B0B0B; border:1px solid #D4AF37; border-radius:8px; padding:0.75rem 1rem; color:#D4AF37; text-decoration:none; font-size:0.875rem; font-weight:600; margin-bottom:1rem; text-align:center;"
                       onmouseover="this.style.background='#D4AF37';this.style.color='#0B0B0B'" onmouseout="this.style.background='#0B0B0B';this.style.color='#D4AF37'">
                        Ver todos en {{ $stateName }}
                    </a>
                    <p style="font-size:0.75rem; font-weight:600; color:#6B7280; text-transform:uppercase; letter-spacing:0.075em; margin-bottom:0.75rem;">{{ $dishData['name'] }} en otros estados</p>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        @foreach(['tx'=>'Texas','ca'=>'California','il'=>'Illinois','az'=>'Arizona','fl'=>'Florida','co'=>'Colorado','nv'=>'Nevada','nm'=>'New Mexico'] as $code => $name)
                        @if($code !== $stateCode)
                        <a href="/{{ $dish }}-en-{{ $code }}"
                           style="display:block; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:8px; padding:0.625rem 1rem; color:#D4AF37; text-decoration:none; font-size:0.875rem; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                            &rarr; {{ $dishData['name'] }} en {{ $name }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Back links -->
        <div style="margin-top:3rem; padding-top:2rem; border-top:1px solid #2A2A2A; display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
            <a href="/{{ $dish }}-en-{{ $stateCode }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; background:#1A1A1A; border:1px solid #2A2A2A; color:#D4AF37; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; font-weight:600; text-decoration:none;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                &larr; {{ $dishData['name'] }} en {{ $stateName }}
            </a>
            <a href="/{{ $dish }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; text-decoration:none;"
               onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                &larr; Todos los restaurantes de {{ $dishData['name'] }}
            </a>
            @if(in_array($dish, ['birria','tamales','pozole','carnitas','barbacoa','mole','carne-asada']))
            <a href="/{{ $dish }}-cerca-de-mi"
               style="display:inline-flex; align-items:center; gap:0.5rem; background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.5rem 1.25rem; border-radius:9999px; font-size:0.875rem; text-decoration:none;"
               onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                &#128205; {{ $dishData['name'] }} cerca de mí
            </a>
            @endif
        </div>

        <!-- También en: other states for same dish -->
        @php
            $allStates = ['tx'=>'Texas','ca'=>'California','il'=>'Illinois','az'=>'Arizona','fl'=>'Florida','co'=>'Colorado','nv'=>'Nevada','nm'=>'N. México','ny'=>'N. York','ga'=>'Georgia','wa'=>'Washington','nc'=>'N. Carolina','or'=>'Oregón','ut'=>'Utah','tn'=>'Tennessee'];
        @endphp
        <div style="margin-top:2rem; padding-top:2rem; border-top:1px solid #2A2A2A;">
            <p style="font-size:0.8125rem; font-weight:600; color:#6B7280; text-transform:uppercase; letter-spacing:0.075em; margin-bottom:0.875rem;">
                {{ $dishData['name'] }} también en...
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                @foreach($allStates as $code => $name)
                @if($code !== $stateCode)
                <a href="/{{ $dish }}-en-{{ $code }}"
                   style="display:inline-block; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.3125rem 0.875rem; border-radius:9999px; font-size:0.8125rem; text-decoration:none; background:#1A1A1A;"
                   onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                    {{ $name }}
                </a>
                @endif
                @endforeach
            </div>
        </div>

        <!-- SEO text -->
        <div style="margin-top:4rem; padding-top:3rem; border-top:1px solid #2A2A2A;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1rem;">
                {{ $dishData['name'] }} Auténtico en {{ $cityName }}, {{ $stateName }}
            </h2>
            <p style="color:#9CA3AF; line-height:1.8; max-width:800px;">
                {{ $cityName }} cuenta con una vibrante comunidad mexicana que mantiene vivas las tradiciones culinarias de México. Los restaurantes de {{ $dishData['name'] }} en {{ $cityName }} ofrecen recetas auténticas preparadas por chefs con raíces mexicanas. Usa FAMER para encontrar el mejor lugar de {{ $dishData['name'] }} en {{ $cityName }}, {{ $stateName }}.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ addslashes($dishData['name']) }} en {{ addslashes($cityName) }}, {{ addslashes($stateName) }}",
    "description": "Los mejores restaurantes de {{ addslashes($dishData['name']) }} en {{ addslashes($cityName) }}, {{ addslashes($stateName) }}",
    "numberOfItems": {{ $restaurants->count() }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $i => $r)
        {
            "@@type": "ListItem",
            "position": {{ $i+1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ addslashes($r->name) }}",
                "url": "{{ url('/restaurante/'.$r->slug) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
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
            "name": "{{ addslashes($dishData['name']) }}",
            "item": "{{ url('/'.$dish) }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ addslashes($dishData['name']) }} en {{ addslashes($stateName) }}",
            "item": "{{ url('/'.$dish.'-en-'.$stateCode) }}"
        },
        {
            "@@type": "ListItem",
            "position": 4,
            "name": "{{ addslashes($dishData['name']) }} en {{ addslashes($cityName) }}",
            "item": "{{ url('/'.$dish.'-en-'.$citySlug.'-'.$stateCode) }}"
        }
    ]
}
</script>
@endpush
