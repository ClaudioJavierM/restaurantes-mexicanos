<div style="min-height:100vh;background:#0B0B0B;display:flex;align-items:center;justify-content:center;padding:24px;">

    {{-- ═══════════════════════════════════════════════════════════
         COMPLETION STATE
    ══════════════════════════════════════════════════════════════ --}}
    @if($completed)
    <div style="max-width:560px;width:100%;text-align:center;position:relative;overflow:hidden;">

        {{-- CSS confetti --}}
        <style>
            @keyframes confettiFall {
                0%   { transform: translateY(-40px) rotate(0deg); opacity:1; }
                100% { transform: translateY(110vh) rotate(720deg); opacity:0; }
            }
            .confetti-piece {
                position:fixed;
                width:10px;
                height:10px;
                top:-20px;
                animation: confettiFall linear infinite;
                border-radius:2px;
                pointer-events:none;
                z-index:9999;
            }
        </style>
        @php
            $confettiColors = ['#D4AF37','#F5F5F5','#1F3D2B','#8B1E1E','#D4AF37','#F0C040'];
            $positions = [5,12,18,25,33,40,47,55,62,70,78,85,92];
            $delays    = [0,0.3,0.7,1.1,0.5,1.5,0.2,0.9,0.4,1.2,0.6,1.8,0.1];
            $durations = [2.5,3,2.8,3.2,2.6,3.5,2.3,3.1,2.7,3.3,2.4,2.9,3.4];
        @endphp
        @foreach($positions as $i => $left)
        <div class="confetti-piece" style="
            left:{{ $left }}%;
            background:{{ $confettiColors[$i % count($confettiColors)] }};
            animation-delay:{{ $delays[$i] }}s;
            animation-duration:{{ $durations[$i] }}s;
            width:{{ ($i % 2 === 0) ? 8 : 12 }}px;
            height:{{ ($i % 2 === 0) ? 12 : 8 }}px;
        "></div>
        @endforeach

        <div style="background:#111111;border:1px solid #2A2A2A;border-radius:20px;padding:52px 44px;">
            <div style="font-size:72px;margin-bottom:20px;">&#127881;</div>
            <h1 style="font-family:'Playfair Display',Georgia,serif;color:#D4AF37;font-size:30px;font-weight:700;margin:0 0 14px;line-height:1.2;">
                ¡Ya estás en el mapa!
            </h1>
            <p style="color:#A0A0A0;font-size:16px;margin:0 0 10px;line-height:1.6;">
                <strong style="color:#F5F5F5;">{{ $restaurant->name }}</strong> ya es parte de FAMER.
            </p>
            <p style="color:#666;font-size:14px;margin:0 0 36px;line-height:1.6;">
                Miles de personas buscan restaurantes mexicanos cada día. Tu perfil está listo para aparecer en los resultados.
            </p>

            <div style="display:flex;gap:12px;flex-direction:column;">
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block;background:linear-gradient(135deg,#B8941F,#D4AF37);color:#0B0B0B;font-family:'Poppins',sans-serif;font-weight:700;font-size:15px;padding:15px 24px;border-radius:10px;text-decoration:none;text-align:center;letter-spacing:0.02em;">
                    Ver mi perfil público &#8599;
                </a>
                <a href="{{ filament()->getPanel('owner')->getUrl() }}"
                   style="display:block;background:#1A1A1A;color:#F5F5F5;font-family:'Poppins',sans-serif;font-weight:600;font-size:15px;padding:15px 24px;border-radius:10px;text-decoration:none;text-align:center;border:1px solid #3A3A3A;">
                    Ir al panel de control
                </a>
            </div>
        </div>
    </div>

    @else
    {{-- ═══════════════════════════════════════════════════════════
         WIZARD CARD
    ══════════════════════════════════════════════════════════════ --}}
    <div style="max-width:620px;width:100%;">

        {{-- Header branding --}}
        <div style="text-align:center;margin-bottom:32px;">
            <img src="/images/branding/famer55.png" alt="FAMER" style="height:44px;margin-bottom:10px;" onerror="this.style.display='none'">
            <p style="color:#555;font-family:'Poppins',sans-serif;font-size:13px;margin:0;letter-spacing:0.04em;text-transform:uppercase;">Configuración inicial</p>
        </div>

        {{-- Progress tracker --}}
        <div style="background:#111111;border-radius:14px;padding:24px 28px;margin-bottom:20px;border:1px solid #222222;">

            {{-- Step dots + connectors --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:18px;">
                @for($i = 1; $i <= $totalSteps; $i++)
                <div style="display:flex;align-items:center;">
                    <div style="
                        width:38px;height:38px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;
                        font-family:'Poppins',sans-serif;font-size:13px;font-weight:700;
                        transition:all 0.3s;
                        {{ $i < $currentStep
                            ? 'background:#D4AF37;color:#0B0B0B;'
                            : ($i === $currentStep
                                ? 'background:#D4AF37;color:#0B0B0B;box-shadow:0 0 0 4px rgba(212,175,55,0.2);'
                                : 'background:#1A1A1A;color:#555;border:2px solid #2A2A2A;') }}
                    ">
                        @if($i < $currentStep)
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="#0B0B0B" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    @if($i < $totalSteps)
                    <div style="width:56px;height:2px;{{ $i < $currentStep ? 'background:linear-gradient(90deg,#D4AF37,#B8941F);' : 'background:#222222;' }}"></div>
                    @endif
                </div>
                @endfor
            </div>

            {{-- Gold progress bar --}}
            <div style="background:#1A1A1A;border-radius:99px;height:4px;overflow:hidden;margin-bottom:8px;">
                <div style="background:linear-gradient(90deg,#8B6914,#B8941F,#D4AF37,#F0C040);height:100%;border-radius:99px;width:{{ $progressPercent }}%;transition:width 0.5s cubic-bezier(0.4,0,0.2,1);"></div>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="color:#444;font-family:'Poppins',sans-serif;font-size:11px;letter-spacing:0.03em;">PASO {{ $currentStep }} DE {{ $totalSteps }}</span>
                <span style="color:#D4AF37;font-family:'Poppins',sans-serif;font-size:11px;font-weight:700;letter-spacing:0.03em;">{{ number_format($progressPercent, 0) }}% COMPLETADO</span>
            </div>
        </div>

        {{-- Step card --}}
        <div style="background:#111111;border:1px solid #222222;border-radius:16px;padding:40px 44px;">

            {{-- ───────────────────────────────────────────────────
                 STEP 1 — Welcome
            ─────────────────────────────────────────────────────── --}}
            @if($currentStep === 1)

            {{-- Icon badge --}}
            <div style="width:64px;height:64px;background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                <span style="font-size:32px;">&#127869;</span>
            </div>

            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#F5F5F5;font-size:26px;font-weight:700;margin:0 0 6px;line-height:1.2;">
                ¡Bienvenido a FAMER,<br>
                <span style="color:#D4AF37;">{{ $restaurant->name }}</span>!
            </h2>
            <p style="color:#888;font-family:'Poppins',sans-serif;font-size:14px;margin:0 0 28px;line-height:1.7;">
                FAMER conecta a miles de personas que buscan restaurantes mexicanos auténticos contigo. En los próximos minutos configuraremos tu perfil para que empieces a atraer clientes desde hoy.
            </p>

            {{-- Benefit cards --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px;">
                <div style="background:#0B0B0B;border:1px solid #222222;border-radius:12px;padding:16px;">
                    <div style="font-size:24px;margin-bottom:8px;">&#128200;</div>
                    <p style="color:#D4AF37;font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin:0 0 2px;">3x</p>
                    <p style="color:#888;font-family:'Poppins',sans-serif;font-size:12px;margin:0;line-height:1.4;">más visitas con perfil completo</p>
                </div>
                <div style="background:#0B0B0B;border:1px solid #222222;border-radius:12px;padding:16px;">
                    <div style="font-size:24px;margin-bottom:8px;">&#11088;</div>
                    <p style="color:#D4AF37;font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin:0 0 2px;">70%</p>
                    <p style="color:#888;font-family:'Poppins',sans-serif;font-size:12px;margin:0;line-height:1.4;">más confianza con reseñas</p>
                </div>
            </div>

            @elseif($currentStep === 2)
            {{-- ───────────────────────────────────────────────────
                 STEP 2 — Complete your profile
            ─────────────────────────────────────────────────────── --}}

            <div style="width:64px;height:64px;background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                <span style="font-size:32px;">&#128196;</span>
            </div>

            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#F5F5F5;font-size:24px;font-weight:700;margin:0 0 6px;line-height:1.2;">
                Completa tu perfil
            </h2>
            <p style="color:#888;font-family:'Poppins',sans-serif;font-size:14px;margin:0 0 20px;line-height:1.7;">
                Un perfil completo genera hasta <strong style="color:#D4AF37;">3 veces más visitas</strong>. Agrega tu descripción, horarios, teléfono y sitio web para que los clientes encuentren todo lo que necesitan saber de ti.
            </p>

            {{-- What to fill in --}}
            <div style="background:#0B0B0B;border:1px solid #222222;border-radius:12px;padding:20px;margin-bottom:24px;">
                <p style="color:#C0C0C0;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;margin:0 0 14px;text-transform:uppercase;letter-spacing:0.04em;">Qué completar</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['&#128214;','Descripción de tu restaurante'],
                        ['&#128336;','Horarios de apertura'],
                        ['&#128241;','Teléfono de contacto'],
                        ['&#127760;','Sitio web o link de pedidos'],
                    ] as [$icon, $label])
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:18px;width:24px;text-align:center;">{{ $icon }}</span>
                        <span style="color:#888;font-family:'Poppins',sans-serif;font-size:13px;">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ filament()->getPanel('owner')->getUrl() }}/my-restaurant-resources"
               target="_blank"
               style="display:block;background:rgba(212,175,55,0.08);border:1px solid rgba(212,175,55,0.3);color:#D4AF37;font-family:'Poppins',sans-serif;font-weight:600;font-size:14px;padding:14px 20px;border-radius:10px;text-decoration:none;text-align:center;margin-bottom:4px;">
                Editar mi perfil &#8599;
            </a>
            <p style="color:#444;font-family:'Poppins',sans-serif;font-size:12px;text-align:center;margin:10px 0 0;">Abre en una nueva pestaña — regresa aquí cuando termines</p>

            @elseif($currentStep === 3)
            {{-- ───────────────────────────────────────────────────
                 STEP 3 — Upload photos
            ─────────────────────────────────────────────────────── --}}

            <div style="width:64px;height:64px;background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                <span style="font-size:32px;">&#128247;</span>
            </div>

            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#F5F5F5;font-size:24px;font-weight:700;margin:0 0 6px;line-height:1.2;">
                Sube tus mejores fotos
            </h2>
            <p style="color:#888;font-family:'Poppins',sans-serif;font-size:14px;margin:0 0 20px;line-height:1.7;">
                Los restaurantes con fotos atractivas reciben <strong style="color:#D4AF37;">3x más clics</strong>. Sube fotos de tu local, tus platillos y el ambiente para enamorar a los clientes antes de que lleguen.
            </p>

            {{-- Photo tips --}}
            <div style="background:#0B0B0B;border:1px solid #222222;border-radius:12px;padding:20px;margin-bottom:24px;">
                <p style="color:#C0C0C0;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;margin:0 0 14px;text-transform:uppercase;letter-spacing:0.04em;">Fotos que más convierten</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['&#127858;','Platillos estrella bien iluminados'],
                        ['&#127968;','Fachada y entrada del restaurante'],
                        ['&#127869;','El ambiente y la decoración interior'],
                        ['&#129470;','El equipo y chef en acción'],
                    ] as [$icon, $label])
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:18px;width:24px;text-align:center;">{{ $icon }}</span>
                        <span style="color:#888;font-family:'Poppins',sans-serif;font-size:13px;">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ filament()->getPanel('owner')->getUrl() }}/my-photos-resources"
               target="_blank"
               style="display:block;background:rgba(212,175,55,0.08);border:1px solid rgba(212,175,55,0.3);color:#D4AF37;font-family:'Poppins',sans-serif;font-weight:600;font-size:14px;padding:14px 20px;border-radius:10px;text-decoration:none;text-align:center;margin-bottom:4px;">
                Subir fotos &#8599;
            </a>
            <p style="color:#444;font-family:'Poppins',sans-serif;font-size:12px;text-align:center;margin:10px 0 0;">Abre en una nueva pestaña — regresa aquí cuando termines</p>

            @elseif($currentStep === 4)
            {{-- ───────────────────────────────────────────────────
                 STEP 4 — Add your menu
            ─────────────────────────────────────────────────────── --}}

            <div style="width:64px;height:64px;background:rgba(212,175,55,0.1);border:1px solid rgba(212,175,55,0.25);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                <span style="font-size:32px;">&#128214;</span>
            </div>

            <h2 style="font-family:'Playfair Display',Georgia,serif;color:#F5F5F5;font-size:24px;font-weight:700;margin:0 0 6px;line-height:1.2;">
                Agrega tu menú digital
            </h2>
            <p style="color:#888;font-family:'Poppins',sans-serif;font-size:14px;margin:0 0 20px;line-height:1.7;">
                Un menú en línea elimina la barrera de "¿qué tienen?" antes de visitar. Los restaurantes con menú digital reciben <strong style="color:#D4AF37;">2x más reservaciones</strong> y consultas por WhatsApp.
            </p>

            {{-- Menu benefits --}}
            <div style="background:#0B0B0B;border:1px solid #222222;border-radius:12px;padding:20px;margin-bottom:24px;">
                <p style="color:#C0C0C0;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;margin:0 0 14px;text-transform:uppercase;letter-spacing:0.04em;">Qué puedes agregar</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['&#127859;','Categorías (Entradas, Platos fuertes, Bebidas)'],
                        ['&#128176;','Nombres, descripciones y precios'],
                        ['&#11088;','Marca tus platillos estrella'],
                        ['&#128247;','Foto por platillo (opcional)'],
                    ] as [$icon, $label])
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:18px;width:24px;text-align:center;">{{ $icon }}</span>
                        <span style="color:#888;font-family:'Poppins',sans-serif;font-size:13px;">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ filament()->getPanel('owner')->getUrl() }}/my-menu-resources"
               target="_blank"
               style="display:block;background:rgba(212,175,55,0.08);border:1px solid rgba(212,175,55,0.3);color:#D4AF37;font-family:'Poppins',sans-serif;font-weight:600;font-size:14px;padding:14px 20px;border-radius:10px;text-decoration:none;text-align:center;margin-bottom:4px;">
                Crear mi menú &#8599;
            </a>
            <p style="color:#444;font-family:'Poppins',sans-serif;font-size:12px;text-align:center;margin:10px 0 0;">Abre en una nueva pestaña — regresa aquí cuando termines</p>

            @elseif($currentStep === 5)
            {{-- ───────────────────────────────────────────────────
                 STEP 5 — All done!
            ─────────────────────────────────────────────────────── --}}

            <div style="text-align:center;">
                <div style="font-size:64px;margin-bottom:20px;">&#127775;</div>
                <h2 style="font-family:'Playfair Display',Georgia,serif;color:#F5F5F5;font-size:26px;font-weight:700;margin:0 0 14px;line-height:1.2;">
                    ¡Todo listo, <span style="color:#D4AF37;">{{ $restaurant->name }}</span>!
                </h2>
                <p style="color:#888;font-family:'Poppins',sans-serif;font-size:14px;margin:0 0 32px;line-height:1.7;max-width:420px;margin-left:auto;margin-right:auto;">
                    Tu restaurante ya está configurado y visible en FAMER. Desde tu panel de control podrás seguir mejorando tu perfil, ver estadísticas y gestionar reseñas.
                </p>
            </div>

            {{-- Summary cards --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:28px;">
                @foreach([
                    ['&#128196;','Perfil','/my-restaurant-resources'],
                    ['&#128247;','Fotos','/my-photos-resources'],
                    ['&#128214;','Menú','/my-menu-resources'],
                    ['&#11088;','Reseñas','/my-reviews-resources'],
                ] as [$icon, $label, $path])
                <a href="{{ filament()->getPanel('owner')->getUrl() }}{{ $path }}"
                   style="display:flex;align-items:center;gap:10px;background:#0B0B0B;border:1px solid #222222;border-radius:10px;padding:14px 16px;text-decoration:none;transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#222222'">
                    <span style="font-size:20px;">{{ $icon }}</span>
                    <span style="color:#C0C0C0;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;">{{ $label }}</span>
                    <span style="color:#555;font-size:11px;margin-left:auto;">&#8594;</span>
                </a>
                @endforeach
            </div>

            {{-- Complete button --}}
            <button wire:click="completeOnboarding" type="button"
                style="width:100%;background:linear-gradient(135deg,#B8941F,#D4AF37);border:none;color:#0B0B0B;font-family:'Poppins',sans-serif;font-weight:700;font-size:16px;padding:16px 24px;border-radius:12px;cursor:pointer;box-shadow:0 4px 24px rgba(212,175,55,0.35);letter-spacing:0.02em;"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60">
                <span wire:loading.remove wire:target="completeOnboarding">Ir a mi panel de control &#127881;</span>
                <span wire:loading wire:target="completeOnboarding">Preparando tu panel...</span>
            </button>

            @endif

            {{-- ───────────────────────────────────────────────────
                 Navigation buttons (steps 1–4 only)
            ─────────────────────────────────────────────────────── --}}
            @if($currentStep < $totalSteps)
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:36px;padding-top:24px;border-top:1px solid #1E1E1E;">

                {{-- Back --}}
                @if($currentStep > 1)
                <button wire:click="prevStep" type="button"
                    style="background:#1A1A1A;border:1px solid #2A2A2A;color:#888;font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;padding:11px 20px;border-radius:10px;cursor:pointer;"
                    wire:loading.attr="disabled">
                    &#8592; Anterior
                </button>
                @else
                <div></div>
                @endif

                {{-- Skip link --}}
                <button wire:click="skipStep" type="button"
                    style="background:none;border:none;color:#444;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;text-decoration:underline;text-underline-offset:3px;padding:0;"
                    wire:loading.attr="disabled">
                    Saltar por ahora
                </button>

                {{-- Continue --}}
                <button wire:click="skipStep" type="button"
                    style="background:linear-gradient(135deg,#B8941F,#D4AF37);border:none;color:#0B0B0B;font-family:'Poppins',sans-serif;font-weight:700;font-size:14px;padding:12px 24px;border-radius:10px;cursor:pointer;box-shadow:0 4px 16px rgba(212,175,55,0.25);"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="skipStep">
                        @if($currentStep === 1)
                            Empezar &#8594;
                        @else
                            Ya lo hice, continuar &#8594;
                        @endif
                    </span>
                    <span wire:loading wire:target="skipStep">...</span>
                </button>
            </div>
            @endif

        </div>{{-- end step card --}}
    </div>{{-- end wizard --}}
    @endif

</div>
