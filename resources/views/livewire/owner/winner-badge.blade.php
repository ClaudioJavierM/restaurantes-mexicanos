<div style="background:#111111; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1.5rem;">
        <div style="width:40px; height:40px; background:linear-gradient(135deg,#D4AF37,#F0C040); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6L12 2z" fill="#0B0B0B"/>
            </svg>
        </div>
        <div>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.125rem; font-weight:700; color:#F5F5F5; margin:0;">Badges Ganados</h2>
            <p style="font-size:0.75rem; color:#888; margin:0;">Descarga y comparte en redes sociales</p>
        </div>
    </div>

    @if($this->badges->isEmpty())
        {{-- Empty state --}}
        <div style="text-align:center; padding:2rem 1rem; border:1px dashed #2A2A2A; border-radius:10px;">
            <div style="font-size:2.5rem; margin-bottom:0.75rem;">🏆</div>
            <p style="color:#888; font-size:0.875rem; margin:0 0 0.5rem;">Aún no tienes badges ganados.</p>
            <p style="color:#666; font-size:0.8rem; margin:0;">¡Sigue acumulando votos para ganar el #1 de tu ciudad!</p>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:1.5rem;">
            @foreach($this->badges as $badge)
            @php
                $scopeLabel = $this->getScopeLabel($badge);
                $monthName  = $this->getMonthName($badge->month);
                $restaurant = $this->restaurant;
                $restName   = $restaurant?->name ?? 'Mi Restaurante';
            @endphp

            <div style="background:#0D0D0D; border:1px solid #2A2A2A; border-radius:10px; overflow:hidden;">

                {{-- SVG Badge Preview --}}
                <div style="padding:1rem; display:flex; justify-content:center; background:#0B0B0B;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300"
                         style="border-radius:8px; max-width:100%; height:auto;">
                        <defs>
                            <linearGradient id="goldGrad{{ $loop->index }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:1"/>
                                <stop offset="100%" style="stop-color:#F0C040;stop-opacity:1"/>
                            </linearGradient>
                            <linearGradient id="bgGrad{{ $loop->index }}" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" style="stop-color:#1A1A1A;stop-opacity:1"/>
                                <stop offset="100%" style="stop-color:#0B0B0B;stop-opacity:1"/>
                            </linearGradient>
                        </defs>

                        {{-- Background --}}
                        <rect width="300" height="300" fill="url(#bgGrad{{ $loop->index }})" rx="12"/>

                        {{-- Gold border --}}
                        <rect x="8" y="8" width="284" height="284" fill="none"
                              stroke="url(#goldGrad{{ $loop->index }})" stroke-width="2" rx="10"/>

                        {{-- Inner border --}}
                        <rect x="14" y="14" width="272" height="272" fill="none"
                              stroke="#2A2A2A" stroke-width="1" rx="8"/>

                        {{-- Star icon --}}
                        <polygon points="150,42 157,64 180,64 162,77 169,99 150,86 131,99 138,77 120,64 143,64"
                                 fill="url(#goldGrad{{ $loop->index }})"/>

                        {{-- FAMER text --}}
                        <text x="150" y="120" text-anchor="middle"
                              font-family="'Playfair Display', Georgia, serif"
                              font-size="28" font-weight="700" letter-spacing="6"
                              fill="url(#goldGrad{{ $loop->index }})">FAMER</text>

                        {{-- Separator line --}}
                        <line x1="60" y1="132" x2="240" y2="132" stroke="#D4AF37" stroke-width="0.75" opacity="0.6"/>

                        {{-- VOTADO #1 --}}
                        <text x="150" y="162" text-anchor="middle"
                              font-family="'Playfair Display', Georgia, serif"
                              font-size="22" font-weight="700" letter-spacing="2"
                              fill="#FFFFFF">VOTADO #1</text>

                        {{-- Scope (City/State/Nacional) --}}
                        <text x="150" y="187" text-anchor="middle"
                              font-family="Arial, Helvetica, sans-serif"
                              font-size="13" font-weight="600" letter-spacing="3"
                              fill="#D4AF37">EN {{ $scopeLabel }}</text>

                        {{-- Month + Year --}}
                        <text x="150" y="212" text-anchor="middle"
                              font-family="Arial, Helvetica, sans-serif"
                              font-size="12" letter-spacing="1"
                              fill="#AAAAAA">{{ strtoupper($monthName) }} {{ $badge->year }}</text>

                        {{-- Separator line --}}
                        <line x1="80" y1="232" x2="220" y2="232" stroke="#2A2A2A" stroke-width="1"/>

                        {{-- Restaurant name --}}
                        <text x="150" y="252" text-anchor="middle"
                              font-family="'Playfair Display', Georgia, serif"
                              font-size="10" font-weight="600"
                              fill="#888888">{{ mb_strtoupper(mb_substr($restName, 0, 34)) }}</text>

                        {{-- URL --}}
                        <text x="150" y="272" text-anchor="middle"
                              font-family="Arial, Helvetica, sans-serif"
                              font-size="7.5" letter-spacing="0.5"
                              fill="#555555">restaurantesmexicanosfamosos.com.mx</text>
                    </svg>
                </div>

                {{-- Badge info & Download --}}
                <div style="padding:0.875rem 1rem; display:flex; align-items:center; justify-content:space-between; gap:0.75rem; border-top:1px solid #1E1E1E;">
                    <div>
                        <p style="font-size:0.8rem; font-weight:600; color:#D4AF37; margin:0;">
                            #1 en {{ $scopeLabel }}
                        </p>
                        <p style="font-size:0.75rem; color:#666; margin:0.125rem 0 0;">
                            {{ $monthName }} {{ $badge->year }}
                        </p>
                    </div>
                    <button
                        onclick="window.open('/owner/badge/download?restaurant_id={{ $this->restaurantId }}&year={{ $badge->year }}&month={{ $badge->month }}&scope={{ $badge->ranking_type }}&scope_value={{ urlencode($badge->ranking_scope ?? '') }}', '_blank')"
                        style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 0.875rem;
                               background:linear-gradient(135deg,#D4AF37,#F0C040); color:#0B0B0B;
                               border:none; border-radius:6px; font-size:0.75rem; font-weight:700;
                               cursor:pointer; text-decoration:none; white-space:nowrap; flex-shrink:0;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Descargar SVG
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Social share tip --}}
        <div style="margin-top:1.25rem; padding:0.875rem; background:#0D0D0D; border-radius:8px; border:1px solid #1E1E1E; display:flex; align-items:flex-start; gap:0.625rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <p style="font-size:0.75rem; color:#888; margin:0; line-height:1.5;">
                Comparte este badge en Instagram, Facebook o imprímelo en tu menú para destacar tu logro.
                Usa el hashtag <strong style="color:#D4AF37;">#FAMERAwards</strong> para más visibilidad.
            </p>
        </div>
    @endif
</div>
