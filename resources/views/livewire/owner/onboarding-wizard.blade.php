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

        <div style="background:#1A1A1A;border:1px solid #2A2A2A;border-radius:20px;padding:48px 40px;">
            <div style="font-size:72px;margin-bottom:16px;">&#127881;</div>
            <h1 style="color:#D4AF37;font-size:28px;font-weight:700;margin:0 0 12px;">
                ¡Tu restaurante está listo en FAMER!
            </h1>
            <p style="color:#A0A0A0;font-size:16px;margin:0 0 32px;line-height:1.6;">
                Has completado la configuración de <strong style="color:#F5F5F5;">{{ $restaurant->name }}</strong>.
                Tu perfil ya está visible para miles de clientes.
            </p>

            @if($inviteSent)
            <div style="background:#1F3D2B;border:1px solid #2A5C3F;border-radius:10px;padding:14px 20px;margin-bottom:24px;">
                <p style="color:#4ADE80;font-size:14px;margin:0;">
                    &#10003; Invitación enviada correctamente
                </p>
            </div>
            @endif

            <div style="display:flex;gap:12px;flex-direction:column;">
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block;background:#D4AF37;color:#0B0B0B;font-weight:700;font-size:15px;padding:14px 24px;border-radius:10px;text-decoration:none;text-align:center;">
                    Ver mi perfil público
                </a>
                <a href="{{ filament()->getPanel('owner')->getUrl() }}"
                   style="display:block;background:#2A2A2A;color:#F5F5F5;font-weight:600;font-size:15px;padding:14px 24px;border-radius:10px;text-decoration:none;text-align:center;border:1px solid #3A3A3A;">
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
            <img src="/images/branding/famer55.png" alt="FAMER" style="height:40px;margin-bottom:12px;" onerror="this.style.display='none'">
            <p style="color:#666;font-size:13px;margin:0;">Configuración inicial de tu restaurante</p>
        </div>

        {{-- Progress bar --}}
        <div style="background:#1A1A1A;border-radius:12px;padding:24px 28px;margin-bottom:24px;border:1px solid #2A2A2A;">

            {{-- Step dots --}}
            <div style="display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:20px;">
                @for($i = 1; $i <= $totalSteps; $i++)
                <div style="display:flex;align-items:center;">
                    {{-- Dot --}}
                    <div style="
                        width:36px;height:36px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;
                        font-size:14px;font-weight:700;
                        {{ $i < $currentStep
                            ? 'background:#D4AF37;color:#0B0B0B;'
                            : ($i === $currentStep
                                ? 'background:#D4AF37;color:#0B0B0B;box-shadow:0 0 0 3px rgba(212,175,55,0.3);'
                                : 'background:#2A2A2A;color:#666;border:2px solid #3A3A3A;') }}
                    ">
                        @if($i < $currentStep)
                            &#10003;
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    {{-- Connector --}}
                    @if($i < $totalSteps)
                    <div style="
                        width:60px;height:2px;
                        {{ $i < $currentStep ? 'background:#D4AF37;' : 'background:#2A2A2A;' }}
                    "></div>
                    @endif
                </div>
                @endfor
            </div>

            {{-- Gold progress bar --}}
            <div style="background:#2A2A2A;border-radius:99px;height:4px;overflow:hidden;">
                <div style="background:linear-gradient(90deg,#B8941F,#D4AF37,#F0C040);height:100%;border-radius:99px;width:{{ $progressPercent }}%;transition:width 0.4s ease;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:8px;">
                <span style="color:#666;font-size:12px;">Paso {{ $currentStep }} de {{ $totalSteps }}</span>
                <span style="color:#D4AF37;font-size:12px;font-weight:600;">{{ number_format($progressPercent, 0) }}% completado</span>
            </div>
        </div>

        {{-- Step card --}}
        <div style="background:#1A1A1A;border:1px solid #2A2A2A;border-radius:16px;padding:36px 40px;">

            {{-- ───────────────────────────────────────────────────
                 STEP 1 — Cover Photo
            ─────────────────────────────────────────────────────── --}}
            @if($currentStep === 1)
            <h2 style="color:#F5F5F5;font-size:22px;font-weight:700;margin:0 0 6px;">Agrega tu foto principal</h2>
            <p style="color:#888;font-size:14px;margin:0 0 28px;line-height:1.5;">
                Los perfiles con foto reciben <strong style="color:#D4AF37;">5x más visitas</strong>. Elige la mejor imagen de tu restaurante.
            </p>

            @error('coverPhoto')
            <div style="background:#3D1515;border:1px solid #8B1E1E;border-radius:8px;padding:10px 16px;margin-bottom:16px;">
                <p style="color:#F87171;font-size:13px;margin:0;">{{ $message }}</p>
            </div>
            @enderror

            {{-- Preview --}}
            @if($coverPhoto)
            <div style="margin-bottom:20px;border-radius:12px;overflow:hidden;height:200px;background:#0B0B0B;">
                <img src="{{ $coverPhoto->temporaryUrl() }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            @elseif($existingCoverUrl)
            <div style="margin-bottom:20px;border-radius:12px;overflow:hidden;height:200px;background:#0B0B0B;position:relative;">
                <img src="{{ $existingCoverUrl }}" style="width:100%;height:100%;object-fit:cover;">
                <div style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.7);color:#D4AF37;font-size:11px;padding:4px 10px;border-radius:99px;">
                    Foto actual
                </div>
            </div>
            @endif

            {{-- Drop zone --}}
            <label for="cover-upload" style="
                display:block;border:2px dashed {{ $coverPhoto ? '#D4AF37' : '#3A3A3A' }};
                border-radius:12px;padding:40px 20px;text-align:center;cursor:pointer;
                background:{{ $coverPhoto ? 'rgba(212,175,55,0.05)' : '#0B0B0B' }};
                transition:border-color 0.2s,background 0.2s;
            "
            onmouseover="this.style.borderColor='#D4AF37';this.style.background='rgba(212,175,55,0.05)'"
            onmouseout="this.style.borderColor='{{ $coverPhoto ? '#D4AF37' : '#3A3A3A' }}';this.style.background='{{ $coverPhoto ? 'rgba(212,175,55,0.05)' : '#0B0B0B' }}'">
                <div style="font-size:40px;margin-bottom:12px;">&#128247;</div>
                <p style="color:#F5F5F5;font-weight:600;margin:0 0 4px;font-size:15px;">
                    {{ $coverPhoto ? 'Cambiar imagen' : 'Arrastra tu foto aquí' }}
                </p>
                <p style="color:#666;font-size:13px;margin:0;">JPG, PNG o WEBP — máximo 5 MB</p>
                <input id="cover-upload" type="file" wire:model="coverPhoto" accept="image/*" style="display:none;">
            </label>

            <div wire:loading wire:target="coverPhoto">
                <p style="color:#D4AF37;font-size:13px;text-align:center;margin-top:12px;">Cargando imagen...</p>
            </div>

            @elseif($currentStep === 2)
            {{-- ───────────────────────────────────────────────────
                 STEP 2 — Hours
            ─────────────────────────────────────────────────────── --}}
            <h2 style="color:#F5F5F5;font-size:22px;font-weight:700;margin:0 0 6px;">¿Cuáles son tus horarios?</h2>
            <p style="color:#888;font-size:14px;margin:0 0 28px;line-height:1.5;">
                Indica tus horarios de apertura para que los clientes sepan cuándo visitarte.
            </p>

            @php
                $dayLabels = [
                    'monday'    => 'Lun',
                    'tuesday'   => 'Mar',
                    'wednesday' => 'Mié',
                    'thursday'  => 'Jue',
                    'friday'    => 'Vie',
                    'saturday'  => 'Sáb',
                    'sunday'    => 'Dom',
                ];
            @endphp

            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($dayLabels as $day => $label)
                <div style="
                    display:flex;align-items:center;gap:12px;
                    background:#0B0B0B;border:1px solid #2A2A2A;border-radius:10px;
                    padding:12px 16px;
                ">
                    {{-- Day label --}}
                    <div style="width:36px;color:#888;font-size:13px;font-weight:600;flex-shrink:0;">{{ $label }}</div>

                    {{-- Closed toggle --}}
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;flex-shrink:0;">
                        <div style="position:relative;width:40px;height:22px;">
                            <input type="checkbox" wire:model="hours.{{ $day }}.closed" style="opacity:0;position:absolute;width:100%;height:100%;margin:0;cursor:pointer;z-index:1;">
                            <div style="
                                width:40px;height:22px;border-radius:11px;
                                background:{{ ($hours[$day]['closed'] ?? false) ? '#8B1E1E' : '#2A2A2A' }};
                                transition:background 0.2s;position:relative;
                            ">
                                <div style="
                                    position:absolute;top:3px;
                                    left:{{ ($hours[$day]['closed'] ?? false) ? '21px' : '3px' }};
                                    width:16px;height:16px;border-radius:50%;background:#F5F5F5;
                                    transition:left 0.2s;
                                "></div>
                            </div>
                        </div>
                        <span style="color:#666;font-size:12px;white-space:nowrap;">
                            {{ ($hours[$day]['closed'] ?? false) ? 'Cerrado' : 'Abierto' }}
                        </span>
                    </label>

                    {{-- Time inputs --}}
                    @if(!($hours[$day]['closed'] ?? false))
                    <div style="display:flex;align-items:center;gap:8px;flex:1;">
                        <input type="time" wire:model="hours.{{ $day }}.open"
                            style="background:#1A1A1A;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:6px 10px;font-size:14px;width:100%;max-width:110px;"
                            value="{{ $hours[$day]['open'] ?? '09:00' }}">
                        <span style="color:#666;font-size:13px;">—</span>
                        <input type="time" wire:model="hours.{{ $day }}.close"
                            style="background:#1A1A1A;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:6px 10px;font-size:14px;width:100%;max-width:110px;"
                            value="{{ $hours[$day]['close'] ?? '21:00' }}">
                    </div>
                    @else
                    <div style="flex:1;color:#555;font-size:13px;font-style:italic;">Cerrado todo el día</div>
                    @endif
                </div>
                @endforeach
            </div>

            @elseif($currentStep === 3)
            {{-- ───────────────────────────────────────────────────
                 STEP 3 — Menu Items
            ─────────────────────────────────────────────────────── --}}
            <h2 style="color:#F5F5F5;font-size:22px;font-weight:700;margin:0 0 6px;">Agrega tus platillos estrella</h2>
            <p style="color:#888;font-size:14px;margin:0 0 28px;line-height:1.5;">
                ¿Cuáles son los platillos que más orgullan a tu restaurante? Puedes agregar hasta 5.
            </p>

            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($menuItems as $idx => $item)
                <div style="background:#0B0B0B;border:1px solid #2A2A2A;border-radius:12px;padding:16px;position:relative;">
                    {{-- Remove button --}}
                    @if(count($menuItems) > 1)
                    <button wire:click="removeMenuItem({{ $idx }})" type="button" style="
                        position:absolute;top:12px;right:12px;
                        background:none;border:none;color:#555;cursor:pointer;font-size:18px;line-height:1;
                    " title="Eliminar platillo">&#215;</button>
                    @endif

                    <div style="display:flex;gap:12px;margin-bottom:10px;">
                        {{-- Name --}}
                        <div style="flex:2;">
                            <label style="display:block;color:#888;font-size:12px;margin-bottom:4px;">Nombre del platillo *</label>
                            <input type="text" wire:model="menuItems.{{ $idx }}.name"
                                placeholder="Ej: Tacos de Birria"
                                style="width:100%;background:#1A1A1A;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                        </div>
                        {{-- Price --}}
                        <div style="flex:1;">
                            <label style="display:block;color:#888;font-size:12px;margin-bottom:4px;">Precio</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#666;font-size:14px;">$</span>
                                <input type="number" wire:model="menuItems.{{ $idx }}.price"
                                    placeholder="12.99" min="0" step="0.01"
                                    style="width:100%;background:#1A1A1A;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 12px 10px 24px;font-size:14px;box-sizing:border-box;">
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label style="display:block;color:#888;font-size:12px;margin-bottom:4px;">Descripción breve (opcional)</label>
                        <input type="text" wire:model="menuItems.{{ $idx }}.description"
                            placeholder="Ej: Con caldo de res, cilantro y cebolla"
                            style="width:100%;background:#1A1A1A;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                    </div>
                </div>
                @endforeach
            </div>

            @if(count($menuItems) < 5)
            <button wire:click="addMenuItem" type="button" style="
                display:flex;align-items:center;gap:8px;
                margin-top:14px;background:none;border:2px dashed #3A3A3A;border-radius:10px;
                color:#D4AF37;font-size:14px;font-weight:600;padding:12px 20px;cursor:pointer;
                width:100%;justify-content:center;transition:border-color 0.2s;
            "
            onmouseover="this.style.borderColor='#D4AF37'"
            onmouseout="this.style.borderColor='#3A3A3A'">
                <span style="font-size:20px;line-height:1;">+</span> Agregar platillo
            </button>
            @endif

            @elseif($currentStep === 4)
            {{-- ───────────────────────────────────────────────────
                 STEP 4 — WhatsApp & Contact
            ─────────────────────────────────────────────────────── --}}
            <h2 style="color:#F5F5F5;font-size:22px;font-weight:700;margin:0 0 6px;">Conecta tu WhatsApp y pedidos</h2>
            <p style="color:#888;font-size:14px;margin:0 0 24px;line-height:1.5;">
                Facilita a los clientes contactarte directamente.
            </p>

            {{-- WhatsApp tip --}}
            <div style="background:rgba(37,211,102,0.08);border:1px solid rgba(37,211,102,0.25);border-radius:10px;padding:12px 16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
                <span style="font-size:24px;">&#128241;</span>
                <p style="color:#4ADE80;font-size:13px;margin:0;line-height:1.4;">
                    <strong>Los clientes con WhatsApp reciben 3x más consultas.</strong> Agrega tu número para que te contacten fácilmente.
                </p>
            </div>

            {{-- WhatsApp field --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;color:#C0C0C0;font-size:13px;font-weight:600;margin-bottom:8px;">Número de WhatsApp</label>
                <div style="display:flex;gap:8px;">
                    <select wire:model="countryCode" style="
                        background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;
                        color:#F5F5F5;padding:10px 12px;font-size:14px;flex-shrink:0;
                    ">
                        <option value="+52">&#127474;&#127485; +52 MX</option>
                        <option value="+1">&#127482;&#127480; +1 US</option>
                        <option value="+1">&#127464;&#127462; +1 CA</option>
                        <option value="+34">&#127466;&#127480; +34 ES</option>
                        <option value="+57">&#127464;&#127476; +57 CO</option>
                        <option value="+54">&#127462;&#127479; +54 AR</option>
                    </select>
                    <input type="tel" wire:model="whatsappPhone"
                        placeholder="55 1234 5678"
                        style="flex:1;background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 14px;font-size:14px;">
                </div>
            </div>

            {{-- Website --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;color:#C0C0C0;font-size:13px;font-weight:600;margin-bottom:8px;">Sitio web (opcional)</label>
                <input type="url" wire:model="websiteUrl"
                    placeholder="https://turestaurante.com"
                    style="width:100%;background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 14px;font-size:14px;box-sizing:border-box;">
            </div>

            {{-- Order URL --}}
            <div>
                <label style="display:block;color:#C0C0C0;font-size:13px;font-weight:600;margin-bottom:8px;">URL de pedidos en línea (opcional)</label>
                <input type="url" wire:model="orderUrl"
                    placeholder="https://pedidos.turestaurante.com"
                    style="width:100%;background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 14px;font-size:14px;box-sizing:border-box;">
                <p style="color:#555;font-size:12px;margin:6px 0 0;">Para UberEats, Rappi, o tu tienda en línea</p>
            </div>

            @elseif($currentStep === 5)
            {{-- ───────────────────────────────────────────────────
                 STEP 5 — Invite first review
            ─────────────────────────────────────────────────────── --}}
            <h2 style="color:#F5F5F5;font-size:22px;font-weight:700;margin:0 0 6px;">Invita tu primera reseña</h2>
            <p style="color:#888;font-size:14px;margin:0 0 24px;line-height:1.5;">
                Tu perfil está casi listo. Invita a un cliente de confianza a dejar la primera reseña y aumenta tu credibilidad.
            </p>

            {{-- Stars preview --}}
            <div style="text-align:center;margin-bottom:28px;">
                <div style="font-size:32px;letter-spacing:4px;color:#D4AF37;">★★★★★</div>
                <p style="color:#666;font-size:13px;margin:8px 0 0;">Los restaurantes con reseñas reciben <strong style="color:#D4AF37;">70% más visitas</strong></p>
            </div>

            {{-- Email invite --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;color:#C0C0C0;font-size:13px;font-weight:600;margin-bottom:8px;">
                    &#128140; Invitar por correo electrónico
                </label>
                <input type="email" wire:model="inviteEmail"
                    placeholder="cliente@ejemplo.com"
                    style="width:100%;background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 14px;font-size:14px;box-sizing:border-box;">
            </div>

            {{-- SMS invite --}}
            <div style="margin-bottom:28px;">
                <label style="display:block;color:#C0C0C0;font-size:13px;font-weight:600;margin-bottom:8px;">
                    &#128241; Invitar por SMS (opcional)
                </label>
                <input type="tel" wire:model="invitePhone"
                    placeholder="+52 55 1234 5678"
                    style="width:100%;background:#0B0B0B;border:1px solid #3A3A3A;border-radius:8px;color:#F5F5F5;padding:10px 14px;font-size:14px;box-sizing:border-box;">
            </div>

            {{-- Divider --}}
            <div style="height:1px;background:#2A2A2A;margin-bottom:20px;"></div>

            {{-- Skip link --}}
            <div style="text-align:center;">
                <button wire:click="completeOnboarding" type="button" style="
                    background:none;border:none;color:#555;font-size:13px;cursor:pointer;
                    text-decoration:underline;text-underline-offset:2px;
                " wire:loading.attr="disabled">
                    Terminar sin invitar
                </button>
            </div>
            @endif

            {{-- ───────────────────────────────────────────────────
                 Navigation buttons
            ─────────────────────────────────────────────────────── --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:32px;padding-top:24px;border-top:1px solid #2A2A2A;">

                {{-- Back --}}
                @if($currentStep > 1)
                <button wire:click="prevStep" type="button" style="
                    background:#2A2A2A;border:1px solid #3A3A3A;color:#C0C0C0;
                    padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;
                " wire:loading.attr="disabled">
                    &#8592; Anterior
                </button>
                @else
                <div></div>
                @endif

                {{-- Skip link (steps 1-4) --}}
                @if($currentStep < $totalSteps)
                <button wire:click="skipStep" type="button" style="
                    background:none;border:none;color:#555;font-size:13px;cursor:pointer;
                    text-decoration:underline;text-underline-offset:2px;
                ">
                    Saltar por ahora
                </button>
                @else
                <div></div>
                @endif

                {{-- Next / Send invite --}}
                @if($currentStep < $totalSteps)
                <button wire:click="nextStep" type="button" style="
                    background:linear-gradient(135deg,#B8941F,#D4AF37);
                    border:none;color:#0B0B0B;font-weight:700;font-size:15px;
                    padding:12px 28px;border-radius:10px;cursor:pointer;
                    box-shadow:0 4px 20px rgba(212,175,55,0.3);
                " wire:loading.attr="disabled"
                   wire:loading.class="opacity-50">
                    <span wire:loading.remove wire:target="nextStep">Continuar &#8594;</span>
                    <span wire:loading wire:target="nextStep">Guardando...</span>
                </button>
                @else
                <button wire:click="saveStep5" type="button" style="
                    background:linear-gradient(135deg,#B8941F,#D4AF37);
                    border:none;color:#0B0B0B;font-weight:700;font-size:15px;
                    padding:12px 28px;border-radius:10px;cursor:pointer;
                    box-shadow:0 4px 20px rgba(212,175,55,0.3);
                " wire:loading.attr="disabled"
                   wire:loading.class="opacity-50">
                    <span wire:loading.remove wire:target="saveStep5">Enviar invitación &#10003;</span>
                    <span wire:loading wire:target="saveStep5">Enviando...</span>
                </button>
                @endif
            </div>

        </div>{{-- end step card --}}
    </div>{{-- end wizard --}}
    @endif

</div>
