@if($similar->isNotEmpty())
<div style="background:#0B0B0B; padding:2.5rem 0 3rem; border-top:1px solid #2A2A2A;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
            Restaurantes Similares en <span style="color:#D4AF37;">{{ $similar->first()?->city ?? 'la zona' }}</span>
        </h2>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1rem;">
            @foreach($similar as $restaurant)
                @php
                    $photo = !empty($restaurant->photos) ? $restaurant->photos[0] : null;
                    $photoUrl = $photo
                        ? (str_starts_with($photo, 'http') ? $photo : \Illuminate\Support\Facades\Storage::url($photo))
                        : null;
                    $rating = $restaurant->google_rating ?? $restaurant->average_rating ?? 0;
                    $ratingFloor = (int) floor($rating);
                    $stars = str_repeat('★', min($ratingFloor, 5)) . str_repeat('☆', max(0, 5 - $ratingFloor));
                @endphp
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s ease;"
                   onmouseover="this.style.borderColor='#D4AF37'"
                   onmouseout="this.style.borderColor='#2A2A2A'">

                    {{-- Photo --}}
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}"
                             alt="{{ $restaurant->name }}"
                             style="width:100%; height:160px; object-fit:cover; display:block;"
                             loading="lazy">
                    @else
                        <div style="width:100%; height:160px; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:2.5rem;">
                            🍽️
                        </div>
                    @endif

                    {{-- Content --}}
                    <div style="padding:1rem;">
                        <p style="font-weight:700; color:#F5F5F5; font-size:0.9375rem; margin:0 0 0.25rem; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $restaurant->name }}
                        </p>

                        <p style="font-size:0.8rem; color:#9CA3AF; margin:0 0 0.5rem;">
                            {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? $restaurant->state->name }}@endif
                        </p>

                        @if($rating > 0)
                            <p style="font-size:0.8125rem; color:#D4AF37; margin:0 0 0.75rem; letter-spacing:0.05em;">
                                {{ $stars }}
                                <span style="color:#9CA3AF; font-size:0.75rem; margin-left:0.25rem;">{{ number_format($rating, 1) }}</span>
                            </p>
                        @else
                            <div style="margin-bottom:0.75rem;"></div>
                        @endif

                        <span style="color:#D4AF37; font-size:0.8125rem; font-weight:600;">
                            Ver Restaurante →
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
