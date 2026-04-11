<div>
@if($deals->isEmpty())
<div></div>
@else
<div style="margin-bottom:1.5rem;">

    {{-- Section header --}}
    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
        <span style="font-size:1.25rem; line-height:1;">🎁</span>
        <h2 style="font-size:1rem; font-weight:700; color:#F5F5F5; text-transform:uppercase; letter-spacing:0.05em; margin:0;">
            Ofertas Activas
        </h2>
        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.6875rem; font-weight:700; padding:0.125rem 0.5rem; border-radius:9999px; line-height:1.5;">
            {{ $deals->count() }}
        </span>
    </div>

    <div style="display:flex; flex-direction:column; gap:0.875rem;">
        @foreach($deals as $deal)
            @php
                $hoursLeft   = now()->diffInHours($deal->ends_at, false);
                $isUrgent    = $hoursLeft >= 0 && $hoursLeft <= 48;
                $isRevealed  = $showCode && $revealedDealId === $deal->id;

                $typeLabels = [
                    'percentage' => 'Descuento %',
                    'fixed'      => 'Descuento fijo',
                    'free_item'  => 'Artículo gratis',
                    'bogo'       => '2x1',
                ];
                $typeLabel = $typeLabels[$deal->discount_type] ?? $deal->discount_type;

                $applicableLabels = [
                    'all'       => 'Comer aquí, para llevar, delivery',
                    'dine_in'   => 'Solo comer aquí',
                    'takeout'   => 'Solo para llevar',
                    'delivery'  => 'Solo delivery',
                ];
                $applicableLabel = $applicableLabels[$deal->applicable_for] ?? $deal->applicable_for;
            @endphp

            <div style="background:#1A1A1A; border:1px solid #D4AF37; border-radius:0.875rem; padding:1.25rem; position:relative; overflow:hidden;">

                {{-- Gold accent stripe --}}
                <div style="position:absolute; top:0; left:0; width:4px; height:100%; background:#D4AF37; border-radius:4px 0 0 4px;"></div>

                {{-- Urgent badge --}}
                @if($isUrgent)
                <div style="position:absolute; top:0.875rem; right:0.875rem;">
                    <span style="background:#8B1E1E; color:#FCA5A5; font-size:0.6875rem; font-weight:700; padding:0.25rem 0.625rem; border-radius:9999px; letter-spacing:0.03em; display:inline-flex; align-items:center; gap:0.25rem;">
                        <span style="width:6px; height:6px; background:#FCA5A5; border-radius:50%; display:inline-block; animation:pulse 1.5s infinite;"></span>
                        ¡Termina pronto!
                    </span>
                </div>
                @endif

                <div style="padding-left:0.75rem;">

                    {{-- Title row --}}
                    <div style="display:flex; align-items:flex-start; gap:0.75rem; flex-wrap:wrap; padding-right:{{ $isUrgent ? '8rem' : '0' }};">
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:1rem; font-weight:700; color:#F5F5F5; margin:0 0 0.375rem;">{{ $deal->title }}</p>
                            @if($deal->description)
                            <p style="font-size:0.8125rem; color:#9CA3AF; margin:0 0 0.5rem; line-height:1.5;">{{ $deal->description }}</p>
                            @endif
                        </div>

                        {{-- Discount value bubble --}}
                        <div style="flex-shrink:0; text-align:center; background:#0B0B0B; border:1px solid #D4AF37; border-radius:0.625rem; padding:0.5rem 0.875rem; min-width:5rem;">
                            <p style="font-size:1.25rem; font-weight:800; color:#D4AF37; margin:0; line-height:1.2;">
                                @if($deal->discount_type === 'percentage')
                                    {{ number_format($deal->discount_value, 0) }}%
                                @elseif($deal->discount_type === 'fixed')
                                    ${{ number_format($deal->discount_value, 2) }}
                                @elseif($deal->discount_type === 'bogo')
                                    2×1
                                @else
                                    FREE
                                @endif
                            </p>
                            <p style="font-size:0.625rem; color:#9CA3AF; margin:0; text-transform:uppercase; letter-spacing:0.04em;">{{ $typeLabel }}</p>
                        </div>
                    </div>

                    {{-- Meta row: applicable + validity --}}
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.875rem;">
                        <span style="font-size:0.75rem; color:#9CA3AF; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:9999px; padding:0.2rem 0.625rem;">
                            {{ $applicableLabel }}
                        </span>
                        <span style="font-size:0.75rem; color:#9CA3AF; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:9999px; padding:0.2rem 0.625rem; display:inline-flex; align-items:center; gap:0.25rem;">
                            <svg style="width:12px; height:12px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Válido hasta: {{ $deal->ends_at->format('d M Y, H:i') }}
                        </span>
                        @if($deal->max_redemptions)
                        <span style="font-size:0.75rem; color:#9CA3AF; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:9999px; padding:0.2rem 0.625rem;">
                            {{ $deal->current_redemptions }}/{{ $deal->max_redemptions }} usados
                        </span>
                        @endif
                    </div>

                    {{-- Promo code section --}}
                    @if($deal->code)
                    <div style="background:#0B0B0B; border:1px dashed #2A2A2A; border-radius:0.625rem; padding:0.75rem 1rem; display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
                        <div>
                            <p style="font-size:0.6875rem; color:#9CA3AF; margin:0 0 0.25rem; text-transform:uppercase; letter-spacing:0.04em;">Código de oferta</p>
                            @if($isRevealed)
                            <p style="font-size:1rem; font-weight:800; color:#D4AF37; font-family:monospace; letter-spacing:0.1em; margin:0; user-select:all;">{{ $deal->code }}</p>
                            @else
                            <p style="font-size:1rem; font-weight:800; color:#D4AF37; font-family:monospace; letter-spacing:0.1em; margin:0; filter:blur(5px); user-select:none;">{{ str_repeat('●', strlen($deal->code)) }}</p>
                            @endif
                        </div>
                        @if(!$isRevealed)
                        <button
                            wire:click="revealCode({{ $deal->id }})"
                            wire:loading.attr="disabled"
                            style="background:#D4AF37; color:#0B0B0B; font-size:0.8125rem; font-weight:700; padding:0.5rem 1rem; border-radius:0.5rem; border:none; cursor:pointer; white-space:nowrap; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            <span wire:loading.remove wire:target="revealCode({{ $deal->id }})">Ver código</span>
                            <span wire:loading wire:target="revealCode({{ $deal->id }})">Cargando...</span>
                        </button>
                        @else
                        <button
                            onclick="navigator.clipboard.writeText('{{ $deal->code }}').then(() => { this.textContent = '¡Copiado!'; setTimeout(() => this.textContent = 'Copiar', 1500); })"
                            style="background:#2A2A2A; color:#D4AF37; font-size:0.8125rem; font-weight:700; padding:0.5rem 1rem; border-radius:0.5rem; border:1px solid #D4AF37; cursor:pointer; white-space:nowrap; transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            Copiar
                        </button>
                        @endif
                    </div>
                    @else
                    <div style="background:#0B0B0B; border:1px dashed #2A2A2A; border-radius:0.625rem; padding:0.75rem 1rem; display:flex; align-items:center; gap:0.625rem;">
                        <svg style="width:18px; height:18px; color:#D4AF37; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
                        </svg>
                        <p style="font-size:0.8125rem; font-weight:600; color:#F5F5F5; margin:0;">
                            Menciona <span style="color:#D4AF37; font-family:monospace; letter-spacing:0.05em;">FAMER</span> al pedir para aplicar esta oferta
                        </p>
                    </div>
                    @endif

                </div>
            </div>
        @endforeach
    </div>

</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}
</style>
@endif
</div>
