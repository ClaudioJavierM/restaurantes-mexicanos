<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.2); padding:4rem 1rem 3rem; text-align:center; position:relative; overflow:hidden;">
        <div style="position:absolute; inset:0; background:radial-gradient(ellipse 60% 50% at 50% 40%, rgba(212,175,55,0.07) 0%, transparent 70%); pointer-events:none;"></div>
        <div style="position:relative; z-index:1;">
            <div style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); border-radius:999px; padding:0.4rem 1rem; margin-bottom:1.5rem; font-size:0.8rem; font-weight:700; color:#D4AF37; letter-spacing:0.1em; text-transform:uppercase;">
                🏆 FAMER Awards 2026
            </div>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:800; color:#F5F5F5; margin:0 0 0.75rem;">
                Vota por tu Restaurante Favorito
            </h1>
            <p style="color:#9CA3AF; font-size:1.0625rem; margin:0;">
                {{ now()->format('F Y') }} — Tu voto decide a los mejores mexicanos de USA
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div style="background:#111111; border-bottom:1px solid rgba(255,255,255,0.06); padding:1.5rem 1rem;">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- State --}}
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Estado</label>
                    <select wire:model.live="stateCode"
                            style="width:100%; background:#1A1A1A; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.5rem; padding:0.625rem 0.75rem; font-size:0.9rem;"
                            onchange="">
                        <option value="">Todos los estados</option>
                        @foreach($this->states as $state)
                            <option value="{{ $state->code }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Ciudad</label>
                    <select wire:model.live="city"
                            style="width:100%; background:#1A1A1A; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.5rem; padding:0.625rem 0.75rem; font-size:0.9rem; {{ !$stateCode ? 'opacity:0.4;' : '' }}"
                            @if(!$stateCode) disabled @endif>
                        <option value="">Todas las ciudades</option>
                        @foreach($this->cities as $cityName)
                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="md:col-span-2">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Buscar</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Nombre del restaurante..."
                           style="width:100%; background:#1A1A1A; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.5rem; padding:0.625rem 0.75rem; font-size:0.9rem; box-sizing:border-box;"
                           onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
                </div>
            </div>
        </div>
    </div>

    {{-- Error --}}
    @if($voteError)
    <div class="max-w-7xl mx-auto px-4 mt-6">
        <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:0.75rem; padding:1rem 1.25rem; display:flex; align-items:center; gap:0.75rem;">
            <span>⚠️</span>
            <span style="color:#FCA5A5;">{{ $voteError }}</span>
        </div>
    </div>
    @endif

    {{-- Restaurants Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->restaurants as $restaurant)
            <div style="background:#1A1A1A; border:1px solid {{ $votedRestaurantId === $restaurant->id ? 'rgba(74,222,128,0.5)' : 'rgba(212,175,55,0.12)' }}; border-radius:1rem; overflow:hidden; transition:transform 0.2s, border-color 0.2s;"
                 onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">

                {{-- Image --}}
                <a href="{{ route('restaurants.show', $restaurant->slug) }}" style="display:block; height:180px; background:#111; position:relative; overflow:hidden;">
                    @if($restaurant->image)
                        <img src="{{ str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image) }}"
                             alt="{{ $restaurant->name }}"
                             style="width:100%; height:100%; object-fit:cover;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display:none; align-items:center; justify-content:center; height:100%; position:absolute; inset:0; background:#1A1A1A; font-size:3.5rem;">🌮</div>
                    @elseif($restaurant->hasMedia('images'))
                        <img src="{{ $restaurant->getFirstMediaUrl('images') }}"
                             alt="{{ $restaurant->name }}"
                             style="width:100%; height:100%; object-fit:cover;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display:none; align-items:center; justify-content:center; height:100%; position:absolute; inset:0; background:#1A1A1A; font-size:3.5rem;">🌮</div>
                    @elseif($restaurant->logo)
                        <img src="{{ str_starts_with($restaurant->logo, 'http') ? $restaurant->logo : asset('storage/' . $restaurant->logo) }}"
                             alt="{{ $restaurant->name }}"
                             style="width:100%; height:100%; object-fit:contain; padding:1rem;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display:none; align-items:center; justify-content:center; height:100%; position:absolute; inset:0; background:#1A1A1A; font-size:3.5rem;">🌮</div>
                    @else
                        <div style="display:flex; align-items:center; justify-content:center; height:100%; font-size:3.5rem; background:#111;">🌮</div>
                    @endif

                    {{-- Votes badge --}}
                    <div style="position:absolute; top:0.75rem; right:0.75rem; background:#D4AF37; color:#0B0B0B; padding:0.25rem 0.75rem; border-radius:999px; font-size:0.75rem; font-weight:700;">
                        {{ $restaurant->monthly_votes ?? 0 }} votos
                    </div>
                </a>

                {{-- Info --}}
                <div style="padding:1.25rem;">
                    <a href="{{ route('restaurants.show', $restaurant->slug) }}" style="text-decoration:none;">
                        <h3 style="font-weight:700; font-size:1.0625rem; color:#F5F5F5; margin:0 0 0.25rem; transition:color 0.2s;"
                            onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#F5F5F5'">
                            {{ $restaurant->name }}
                        </h3>
                    </a>
                    <p style="color:#9CA3AF; font-size:0.875rem; margin:0 0 0.875rem;">
                        {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                    </p>

                    {{-- Ratings --}}
                    <div style="display:flex; gap:1rem; margin-bottom:1rem; font-size:0.8125rem;">
                        @if($restaurant->google_rating)
                        <div style="display:flex; align-items:center; gap:0.25rem; color:#F5F5F5;">
                            <span>⭐</span>
                            <span style="font-weight:600;">{{ number_format($restaurant->google_rating, 1) }}</span>
                            <span style="color:#6B7280;">Google</span>
                        </div>
                        @endif
                        @if($restaurant->yelp_rating)
                        <div style="display:flex; align-items:center; gap:0.25rem; color:#F5F5F5;">
                            <span>🔴</span>
                            <span style="font-weight:600;">{{ number_format($restaurant->yelp_rating, 1) }}</span>
                            <span style="color:#6B7280;">Yelp</span>
                        </div>
                        @endif
                    </div>

                    {{-- Vote Button --}}
                    @if($votedRestaurantId === $restaurant->id)
                        <button disabled style="width:100%; background:rgba(74,222,128,0.15); color:#4ADE80; border:1px solid rgba(74,222,128,0.4); padding:0.75rem; border-radius:0.625rem; font-weight:700; font-size:0.9375rem; cursor:default;">
                            ✓ ¡Votaste por este!
                        </button>
                    @else
                        <button wire:click="vote({{ $restaurant->id }})"
                                wire:loading.attr="disabled"
                                style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.75rem; border-radius:0.625rem; font-weight:700; font-size:0.9375rem; cursor:pointer; transition:background 0.2s;"
                                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
                            <span wire:loading.remove wire:target="vote({{ $restaurant->id }})">🗳️ Votar</span>
                            <span wire:loading wire:target="vote({{ $restaurant->id }})">Votando...</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <span style="font-size:3.5rem;">🔍</span>
                <p style="color:#9CA3AF; margin-top:1rem;">No se encontraron restaurantes con esos filtros</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Thank You Modal --}}
    @if($showThankYou)
    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.75); display:flex; align-items:center; justify-content:center; z-index:50;" wire:click.self="closeThankYou">
        <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.25); border-radius:1.25rem; max-width:420px; width:calc(100% - 2rem); padding:2.5rem; text-align:center;"
             class="animate-bounce-in">
            <div style="font-size:3.5rem; margin-bottom:1rem;">🎉</div>
            <h3 style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">
                ¡Gracias por tu voto!
            </h3>
            <p style="color:#9CA3AF; margin:0 0 1.75rem; line-height:1.6;">
                Tu opinión es importante para seleccionar a los mejores restaurantes mexicanos.
            </p>

            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <button wire:click="closeThankYou"
                        style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem; border-radius:0.75rem; font-weight:700; cursor:pointer; transition:background 0.2s;"
                        onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
                    Seguir Votando
                </button>
                <a href="{{ route('famer.awards') }}"
                   style="display:block; width:100%; background:transparent; border:1px solid #2A2A2A; color:#9CA3AF; padding:0.875rem; border-radius:0.75rem; font-weight:600; text-decoration:none; transition:border-color 0.2s; box-sizing:border-box;"
                   onmouseover="this.style.borderColor='rgba(212,175,55,0.3)';this.style.color='#F5F5F5'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#9CA3AF'">
                    Ver FAMER Awards
                </a>
            </div>

            {{-- Share --}}
            <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.06);">
                <p style="color:#6B7280; font-size:0.8125rem; margin:0 0 0.75rem;">Comparte con tus amigos</p>
                <div style="display:flex; justify-content:center; gap:0.75rem;">
                    <a href="https://twitter.com/intent/tweet?text=Acabo%20de%20votar%20por%20mi%20restaurante%20mexicano%20favorito%20en%20FAMER%20Awards%202026&url={{ urlencode(url()->current()) }}" target="_blank"
                       style="width:2.5rem; height:2.5rem; background:#1A1A2E; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-weight:900; font-size:0.875rem; border:1px solid #2A2A2A;">𝕏</a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                       style="width:2.5rem; height:2.5rem; background:#1877F2; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-weight:900; font-size:0.875rem;">f</a>
                    <a href="whatsapp://send?text=Vota%20por%20tu%20restaurante%20mexicano%20favorito%20{{ urlencode(url()->current()) }}"
                       style="width:2.5rem; height:2.5rem; background:#22C55E; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:0.875rem;">📱</a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<style>
@keyframes bounce-in {
    0% { transform: scale(0.85); opacity: 0; }
    55% { transform: scale(1.03); }
    100% { transform: scale(1); opacity: 1; }
}
.animate-bounce-in { animation: bounce-in 0.3s ease-out; }
</style>
