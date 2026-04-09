<x-filament-panels::page>

    {{-- ── Page header ─────────────────────────────────────────────────── --}}
    <div style="margin-bottom:2rem;">
        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.5rem;">
            <div style="width:40px; height:40px; background:linear-gradient(135deg,#D4AF37,#B8960C); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">⭐</div>
            <div>
                <h1 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin:0;">Destacar mi Restaurante</h1>
                <p style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin:0;">Aumenta tu visibilidad y atrae más clientes</p>
            </div>
        </div>
        <div style="height:1px; background:rgba(212,175,55,0.2); margin-top:1rem;"></div>
    </div>

    @php
        $placement = $this->activePlacement;
        $stats     = $this->stats;
        $restaurant = $this->getRestaurant();
    @endphp

    @if($placement)
    {{-- ── Active placement card ────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,rgba(212,175,55,0.12),rgba(212,175,55,0.04)); border:1px solid rgba(212,175,55,0.5); border-radius:16px; padding:1.75rem; margin-bottom:2rem;">
        <div style="display:flex; align-items:center; gap:0.6rem; margin-bottom:1rem;">
            <span style="display:inline-block; width:10px; height:10px; background:#22C55E; border-radius:50%; box-shadow:0 0 6px #22C55E;"></span>
            <span style="font-family:'Poppins',sans-serif; font-size:0.85rem; font-weight:600; color:#22C55E;">Activo</span>
        </div>
        <p style="font-family:'Poppins',sans-serif; font-size:1rem; color:#F5F5F5; margin:0 0 0.5rem;">
            Tu restaurante está destacado hasta
            <strong style="color:#D4AF37;">{{ $placement->ends_at->format('d/m/Y') }}</strong>
        </p>
        <p style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF; margin:0 0 1.5rem;">
            Plan:
            @if($placement->placement_type === 'city') 🏙️ Ciudad — {{ $placement->scope }}
            @elseif($placement->placement_type === 'state') 🗺️ Estado — {{ $placement->scope }}
            @else 🌎 Nacional
            @endif
        </p>

        {{-- Stats row --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;">
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1rem; text-align:center;">
                <div style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#D4AF37;">{{ number_format($stats['impressions']) }}</div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.75rem; color:#9CA3AF; margin-top:0.25rem;">Impresiones</div>
            </div>
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1rem; text-align:center;">
                <div style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#D4AF37;">{{ number_format($stats['clicks']) }}</div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.75rem; color:#9CA3AF; margin-top:0.25rem;">Clics</div>
            </div>
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1rem; text-align:center;">
                <div style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#D4AF37;">{{ $stats['ctr'] }}%</div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.75rem; color:#9CA3AF; margin-top:0.25rem;">CTR</div>
            </div>
        </div>
    </div>

    @else
    {{-- ── No active placement — pricing cards ─────────────────────────── --}}
    <div style="margin-bottom:2rem;">
        <p style="font-family:'Poppins',sans-serif; font-size:0.9rem; color:#9CA3AF; margin-bottom:1.5rem;">
            Elige el alcance que mejor se adapte a tu negocio:
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:1.25rem;">

            {{-- Ciudad --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div style="font-size:2rem; text-align:center;">🏙️</div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#F5F5F5; margin-bottom:0.25rem;">Ciudad</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:2rem; font-weight:700; color:#D4AF37; line-height:1;">$49<span style="font-size:0.85rem; color:#9CA3AF;">/mes</span></div>
                </div>
                <ul style="list-style:none; padding:0; margin:0; font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF; display:flex; flex-direction:column; gap:0.4rem;">
                    <li>✅ Destacado en búsquedas de tu ciudad</li>
                    <li>✅ Estadísticas de impresiones y clics</li>
                    <li>✅ Badge "Destacado" en tu tarjeta</li>
                </ul>
                <button wire:click="requestPlan('city')"
                        style="background:linear-gradient(135deg,#D4AF37,#B8960C); color:#0B0B0B; font-family:'Poppins',sans-serif; font-size:0.85rem; font-weight:700; border:none; border-radius:8px; padding:0.65rem 1rem; cursor:pointer; width:100%; transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'"
                        onmouseout="this.style.opacity='1'">
                    Solicitar
                </button>
            </div>

            {{-- Estado --}}
            <div style="background:#1A1A1A; border:2px solid rgba(212,175,55,0.5); border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:1rem; position:relative;">
                <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:#D4AF37; color:#0B0B0B; font-family:'Poppins',sans-serif; font-size:0.65rem; font-weight:700; padding:0.2rem 0.75rem; border-radius:20px; text-transform:uppercase; letter-spacing:0.08em; white-space:nowrap;">Más Popular</div>
                <div style="font-size:2rem; text-align:center;">🗺️</div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#F5F5F5; margin-bottom:0.25rem;">Estado</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:2rem; font-weight:700; color:#D4AF37; line-height:1;">$99<span style="font-size:0.85rem; color:#9CA3AF;">/mes</span></div>
                </div>
                <ul style="list-style:none; padding:0; margin:0; font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF; display:flex; flex-direction:column; gap:0.4rem;">
                    <li>✅ Destacado en todo el estado</li>
                    <li>✅ Mayor alcance geográfico</li>
                    <li>✅ Estadísticas detalladas</li>
                    <li>✅ Badge "Destacado" en tu tarjeta</li>
                </ul>
                <button wire:click="requestPlan('state')"
                        style="background:linear-gradient(135deg,#D4AF37,#B8960C); color:#0B0B0B; font-family:'Poppins',sans-serif; font-size:0.85rem; font-weight:700; border:none; border-radius:8px; padding:0.65rem 1rem; cursor:pointer; width:100%; transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'"
                        onmouseout="this.style.opacity='1'">
                    Solicitar
                </button>
            </div>

            {{-- Nacional --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
                <div style="font-size:2rem; text-align:center;">🌎</div>
                <div style="text-align:center;">
                    <div style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#F5F5F5; margin-bottom:0.25rem;">Nacional</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:2rem; font-weight:700; color:#D4AF37; line-height:1;">$199<span style="font-size:0.85rem; color:#9CA3AF;">/mes</span></div>
                </div>
                <ul style="list-style:none; padding:0; margin:0; font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF; display:flex; flex-direction:column; gap:0.4rem;">
                    <li>✅ Destacado en búsquedas nacionales</li>
                    <li>✅ Máxima visibilidad en toda la red</li>
                    <li>✅ Estadísticas premium</li>
                    <li>✅ Badge "Destacado" en tu tarjeta</li>
                </ul>
                <button wire:click="requestPlan('national')"
                        style="background:linear-gradient(135deg,#D4AF37,#B8960C); color:#0B0B0B; font-family:'Poppins',sans-serif; font-size:0.85rem; font-weight:700; border:none; border-radius:8px; padding:0.65rem 1rem; cursor:pointer; width:100%; transition:opacity 0.2s;"
                        onmouseover="this.style.opacity='0.85'"
                        onmouseout="this.style.opacity='1'">
                    Solicitar
                </button>
            </div>

        </div>
    </div>
    @endif

    {{-- ── Why feature your restaurant? ───────────────────────────────────── --}}
    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.75rem;">
        <h2 style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#D4AF37; margin:0 0 1.25rem;">¿Por qué destacarte?</h2>
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; align-items:flex-start; gap:0.75rem;">
                <span style="font-size:1.25rem; flex-shrink:0;">👁️</span>
                <div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5; margin-bottom:0.2rem;">Mayor visibilidad</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF;">Tu restaurante aparece primero en los resultados de búsqueda, por encima de los listados orgánicos.</div>
                </div>
            </div>
            <div style="display:flex; align-items:flex-start; gap:0.75rem;">
                <span style="font-size:1.25rem; flex-shrink:0;">📈</span>
                <div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5; margin-bottom:0.2rem;">Más clientes potenciales</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF;">Los restaurantes destacados reciben en promedio 3x más visitas al perfil que los no destacados.</div>
                </div>
            </div>
            <div style="display:flex; align-items:flex-start; gap:0.75rem;">
                <span style="font-size:1.25rem; flex-shrink:0;">⭐</span>
                <div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5; margin-bottom:0.2rem;">Credibilidad y confianza</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#9CA3AF;">El badge dorado "Destacado" transmite profesionalismo y genera confianza en nuevos comensales.</div>
                </div>
            </div>
        </div>
    </div>

</x-filament-panels::page>
