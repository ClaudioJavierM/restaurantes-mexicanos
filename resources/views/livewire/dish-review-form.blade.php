<div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">

    @if($submitted)
        {{-- Success State --}}
        <div style="text-align:center; padding:2rem 1rem;">
            <div style="font-size:2.5rem; margin-bottom:0.75rem;">✅</div>
            <p style="color:#4ADE80; font-family:'Poppins',sans-serif; font-size:1rem; font-weight:500; margin:0;">
                ¡Gracias! Tu reseña será publicada tras revisión.
            </p>
        </div>
    @else
        {{-- Form --}}
        <div style="margin-bottom:1.25rem;">
            <span style="color:#D4AF37; font-family:'Poppins',sans-serif; font-size:0.75rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase;">
                Califica este platillo
            </span>
        </div>

        {{-- Dish Name --}}
        @if(!$menuItemId)
            <div style="margin-bottom:1rem;">
                <input
                    wire:model="dishName"
                    type="text"
                    placeholder="¿Qué platillo probaste?"
                    style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                    onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                    onblur="this.style.borderColor='#2A2A2A'"
                />
                @error('dishName')
                    <span style="color:#F87171; font-size:0.8rem; font-family:'Poppins',sans-serif; margin-top:0.25rem; display:block;">{{ $message }}</span>
                @enderror
            </div>
        @else
            <div style="margin-bottom:1rem;">
                <span style="color:#D4AF37; font-family:'Poppins',sans-serif; font-size:0.9375rem; font-weight:500;">
                    {{ $dishName }}
                </span>
            </div>
        @endif

        {{-- Star Rating --}}
        <div style="margin-bottom:1rem;">
            <div x-data="{hover:0}" style="display:flex; gap:4px; font-size:1.75rem; cursor:pointer;">
                @for($i = 1; $i <= 5; $i++)
                <span
                    @click="$wire.setRating({{ $i }})"
                    @mouseover="hover={{ $i }}"
                    @mouseleave="hover=0"
                    :style="(hover >= {{ $i }} || $wire.rating >= {{ $i }}) ? 'color:#D4AF37' : 'color:#3A3A3A'"
                    style="transition:color 0.15s ease; user-select:none;"
                >★</span>
                @endfor
            </div>
            @error('rating')
                <span style="color:#F87171; font-size:0.8rem; font-family:'Poppins',sans-serif; margin-top:0.25rem; display:block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- Comment --}}
        <div style="margin-bottom:1rem;">
            <textarea
                wire:model="comment"
                placeholder="Cuéntanos tu experiencia con este platillo... (mínimo 10 caracteres)"
                rows="4"
                style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif; resize:vertical;"
                onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                onblur="this.style.borderColor='#2A2A2A'"
            ></textarea>
            @error('comment')
                <span style="color:#F87171; font-size:0.8rem; font-family:'Poppins',sans-serif; margin-top:0.25rem; display:block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- Guest Fields --}}
        @guest
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                <div>
                    <input
                        wire:model="reviewerName"
                        type="text"
                        placeholder="Tu nombre"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'"
                    />
                    @error('reviewerName')
                        <span style="color:#F87171; font-size:0.8rem; font-family:'Poppins',sans-serif; margin-top:0.25rem; display:block;">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <input
                        wire:model="reviewerEmail"
                        type="email"
                        placeholder="Tu correo"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'"
                    />
                    @error('reviewerEmail')
                        <span style="color:#F87171; font-size:0.8rem; font-family:'Poppins',sans-serif; margin-top:0.25rem; display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endguest

        {{-- Submit Button --}}
        <button
            wire:click="submit"
            wire:loading.attr="disabled"
            style="background:#D4AF37; color:#0B0B0B; border:none; border-radius:0.625rem; padding:0.75rem 1.5rem; font-size:0.9375rem; font-weight:600; font-family:'Poppins',sans-serif; cursor:pointer; width:100%; transition:opacity 0.2s ease;"
            onmouseover="this.style.opacity='0.85'"
            onmouseout="this.style.opacity='1'"
        >
            <span wire:loading.remove>Publicar reseña</span>
            <span wire:loading style="display:none;">Publicando...</span>
        </button>
    @endif

</div>
