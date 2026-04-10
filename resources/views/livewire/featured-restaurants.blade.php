<div>
@if($featured->isNotEmpty())
<div style="margin-bottom:2rem;">
    {{-- Section header --}}
    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem;">
        <span style="font-family:'Poppins',sans-serif; font-size:0.7rem; font-weight:700; color:#0B0B0B; background:#D4AF37; padding:0.2rem 0.6rem; border-radius:4px; text-transform:uppercase; letter-spacing:0.08em;">Destacado</span>
        <div style="flex:1; height:1px; background:rgba(212,175,55,0.2);"></div>
    </div>

    {{-- Cards grid --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1rem;">
        @foreach($featured as $restaurant)
        <a href="/restaurante/{{ $restaurant->slug }}"
           wire:click="trackClick({{ $restaurant->id }})"
           style="display:block; text-decoration:none; background:#1A1A1A; border:1px solid rgba(212,175,55,0.35); border-radius:16px; overflow:hidden; position:relative; transition:border-color 0.2s, transform 0.2s;"
           onmouseover="this.style.borderColor='#D4AF37'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='rgba(212,175,55,0.35)'; this.style.transform='none'">

            {{-- Gold "Destacado" ribbon top-right --}}
            <div style="position:absolute; top:10px; right:10px; background:#D4AF37; color:#0B0B0B; font-family:'Poppins',sans-serif; font-size:0.65rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:4px; text-transform:uppercase; letter-spacing:0.06em; z-index:1;">
                ⭐ Destacado
            </div>

            {{-- Restaurant image --}}
            @if($restaurant->image)
            <div style="height:160px; overflow:hidden;">
                <img src="{{ $restaurant->image }}"
                     alt="{{ $restaurant->name }}"
                     loading="lazy"
                     style="width:100%; height:100%; object-fit:cover;">
            </div>
            @else
            <div style="height:100px; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:2rem;">🌮</div>
            @endif

            {{-- Card body --}}
            <div style="padding:1rem 1.25rem;">
                <div style="font-family:'Playfair Display',serif; font-size:1rem; font-weight:700; color:#F5F5F5; margin-bottom:0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $restaurant->name }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF;">
                    {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code }}@endif
                </div>
                @if(isset($restaurant->average_rating) && $restaurant->average_rating > 0)
                <div style="margin-top:0.5rem; font-size:0.8rem; color:#D4AF37;">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($restaurant->average_rating) ? '★' : '☆' }}
                    @endfor
                    <span style="color:#6B7280; font-size:0.75rem; margin-left:0.25rem;">
                        {{ number_format($restaurant->average_rating, 1) }}
                    </span>
                </div>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
</div>
