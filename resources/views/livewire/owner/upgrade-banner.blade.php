{{-- Usage: <livewire:owner.upgrade-banner :restaurant="$restaurant" /> --}}
@if($this->isVisible)
@php $competitorCount = $this->premiumCompetitorCount; @endphp
<div class="rounded-2xl p-5 mb-6"
     style="background:linear-gradient(135deg,rgba(212,175,55,0.1) 0%,rgba(212,175,55,0.03) 100%);border:1px solid rgba(212,175,55,0.3);">
    <div class="flex items-start justify-between gap-4">

        <div class="flex-1">
            <!-- Headline con dato contextual -->
            <div class="flex items-center gap-3 mb-3">
                <div style="width:2.5rem;height:2.5rem;background:rgba(212,175,55,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;">
                    &#9889;
                </div>
                <div>
                    <h3 class="font-bold text-base leading-tight" style="color:#F5F5F5;margin:0;">
                        @if($competitorCount > 0)
                            {{ $competitorCount }} restaurante{{ $competitorCount !== 1 ? 's' : '' }} premium te superan en b&uacute;squedas en tu estado
                        @else
                            S&eacute; el primero en Premium en tu estado &mdash; destaca sobre la competencia
                        @endif
                    </h3>
                    <p class="text-sm" style="color:#9CA3AF;margin:0.2rem 0 0;">
                        Con Premium apareces en el Top 3 &mdash; <strong style="color:#D4AF37;">$9.99</strong> primer mes, despu&eacute;s $39/mes
                    </p>
                </div>
            </div>

            <!-- Beneficios en línea -->
            <div class="flex flex-wrap gap-x-5 gap-y-1 mb-4" style="padding-left:3.5rem;">
                <span class="text-sm" style="color:#D1D5DB;">
                    <span style="color:#D4AF37;">&#10003;</span>&nbsp;Top 3 en b&uacute;squedas locales
                </span>
                <span class="text-sm" style="color:#D1D5DB;">
                    <span style="color:#D4AF37;">&#10003;</span>&nbsp;Badge Destacado
                </span>
                <span class="text-sm" style="color:#D1D5DB;">
                    <span style="color:#D4AF37;">&#10003;</span>&nbsp;Men&uacute; Digital + QR
                </span>
                <span class="text-sm" style="color:#D1D5DB;">
                    <span style="color:#D4AF37;">&#10003;</span>&nbsp;Analytics avanzados
                </span>
            </div>

            <!-- CTA -->
            <div style="padding-left:3.5rem;">
                <a href="/claim/upgrade?plan=premium&restaurant={{ $restaurant->id }}"
                   class="inline-block px-5 py-2 rounded-lg font-semibold text-sm transition-all hover:opacity-90"
                   style="background:#D4AF37;color:#0B0B0B;text-decoration:none;white-space:nowrap;">
                    Mejorar a Premium &nbsp;&rarr;
                </a>
            </div>
        </div>

        <!-- Botón cerrar -->
        <button wire:click="dismiss"
                type="button"
                title="Cerrar"
                class="flex-shrink-0 transition-opacity hover:opacity-70"
                style="color:#6B7280;background:none;border:none;cursor:pointer;padding:2px;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>
</div>
@endif
