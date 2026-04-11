{{-- Usage: <livewire:owner.upgrade-banner :restaurant="$restaurant" /> --}}
@if($this->isVisible)
<div class="rounded-2xl p-6 mb-6"
     style="background:linear-gradient(135deg,#1A1A1A 0%,#1F3D2B 100%); border:1px solid #D4AF37;">
    <div class="flex items-start justify-between gap-4">

        <div class="flex-1">
            <!-- Headline -->
            <div class="flex items-center gap-3 mb-3">
                <span class="text-2xl">🚀</span>
                <h3 class="font-bold text-lg leading-tight"
                    style="color:#F5F5F5;">
                    Desbloquea más visibilidad con Premium
                </h3>
            </div>

            <!-- Beneficios -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-4">
                <!-- Beneficio 1 -->
                <div class="flex items-start gap-2">
                    <span style="color:#D4AF37; flex-shrink:0; font-size:14px; margin-top:1px;">✓</span>
                    <p class="text-sm leading-snug" style="color:#D1D5DB; margin:0;">
                        <strong style="color:#F5F5F5;">Posición destacada</strong><br>
                        Aparece primero en tu ciudad y categoría
                    </p>
                </div>
                <!-- Beneficio 2 -->
                <div class="flex items-start gap-2">
                    <span style="color:#D4AF37; flex-shrink:0; font-size:14px; margin-top:1px;">✓</span>
                    <p class="text-sm leading-snug" style="color:#D1D5DB; margin:0;">
                        <strong style="color:#F5F5F5;">Analytics avanzados</strong><br>
                        Vistas, clics, fuente de tráfico y más
                    </p>
                </div>
                <!-- Beneficio 3 -->
                <div class="flex items-start gap-2">
                    <span style="color:#D4AF37; flex-shrink:0; font-size:14px; margin-top:1px;">✓</span>
                    <p class="text-sm leading-snug" style="color:#D1D5DB; margin:0;">
                        <strong style="color:#F5F5F5;">Cupones y fidelización</strong><br>
                        Atrae y retén clientes con recompensas
                    </p>
                </div>
            </div>

            <!-- Precio + CTA -->
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-sm" style="color:#9CA3AF;">
                    <strong style="color:#D4AF37;">$9.99</strong> primer mes &nbsp;·&nbsp; después $39/mes
                </span>
                <a href="/claim/upgrade?plan=premium&restaurant={{ $restaurant->id }}"
                   class="px-6 py-2 rounded-lg font-semibold text-sm transition-all hover:opacity-90"
                   style="background:#D4AF37; color:#0B0B0B; text-decoration:none; display:inline-block; white-space:nowrap;">
                    Actualizar ahora &nbsp;→
                </a>
            </div>
        </div>

        <!-- Botón cerrar -->
        <button wire:click="dismiss"
                type="button"
                title="Cerrar"
                class="flex-shrink-0 transition-opacity hover:opacity-70"
                style="color:#6B7280; background:none; border:none; cursor:pointer; padding:2px;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

    </div>
</div>
@endif
