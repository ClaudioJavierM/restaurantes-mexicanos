<div
    wire:key="newsletter-signup"
    style="
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1a0a00 0%, #2d1100 50%, #1a0a00 100%);
        border: 1px solid rgba(212, 160, 23, 0.3);
        border-radius: 16px;
        padding: 2.5rem 2rem;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        max-width: 680px;
        margin: 0 auto;
    "
>

    {{-- Decorative top accent line --}}
    <div style="
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #d4a017, #e8b820, #d4a017, transparent);
    "></div>

    {{-- ── SUCCESS STATE ─────────────────────────────────────────────── --}}
    @if ($success)
        <div
            style="text-align: center; padding: 1rem 0;"
            x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 50)"
        >
            {{-- Confetti burst (pure CSS animated dots) --}}
            <div style="position: relative; height: 60px; margin-bottom: 0.5rem;" aria-hidden="true">
                @foreach ([
                    ['#d4a017', '-40px', '-20px', '0.1s', '8px'],
                    ['#e8b820', '-10px', '-50px', '0.15s', '6px'],
                    ['#ff6b35', '30px',  '-35px', '0.2s',  '9px'],
                    ['#22c55e', '55px',  '-15px', '0.1s',  '7px'],
                    ['#d4a017', '-60px', '-10px', '0.25s', '5px'],
                    ['#e8b820', '70px',  '-45px', '0.05s', '8px'],
                    ['#ff6b35', '10px',  '-55px', '0.3s',  '6px'],
                    ['#22c55e', '-25px', '-30px', '0.18s', '9px'],
                ] as [$color, $tx, $ty, $delay, $size])
                    <div
                        x-show="show"
                        x-transition:enter="transition"
                        x-transition:enter-start="opacity: 0; transform: translate(0,0) scale(0)"
                        x-transition:enter-end="opacity: 1; transform: translate({{ $tx }}, {{ $ty }}) scale(1)"
                        style="
                            position: absolute;
                            top: 50%; left: 50%;
                            width: {{ $size }};
                            height: {{ $size }};
                            background: {{ $color }};
                            border-radius: 50%;
                            transition: all 0.6s cubic-bezier(0.22,1,0.36,1) {{ $delay }};
                        "
                    ></div>
                @endforeach
            </div>

            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✓</div>

            @if ($duplicate)
                <h3 style="color: #d4a017; font-size: 1.3rem; font-weight: 700; margin: 0 0 0.5rem;">
                    ¡Ya estás suscrito!
                </h3>
                <p style="color: #a07820; font-size: 0.95rem; margin: 0;">
                    Tu correo ya está en nuestra lista. ¡Gracias por ser parte de FAMER!
                </p>
            @else
                <h3 style="color: #d4a017; font-size: 1.3rem; font-weight: 700; margin: 0 0 0.5rem;">
                    ¡Listo! Revisa tu inbox
                </h3>
                <p style="color: #a07820; font-size: 0.95rem; margin: 0;">
                    Te enviamos una confirmación a <strong style="color: #e8b820;">{{ $email }}</strong>.
                    El mejor contenido mexicano llega pronto.
                </p>
            @endif
        </div>

    {{-- ── SIGNUP FORM ────────────────────────────────────────────────── --}}
    @else

        {{-- Heading --}}
        <div style="text-align: center; margin-bottom: 1.75rem;">
            <div style="font-size: 2rem; margin-bottom: 0.25rem;" aria-hidden="true">🌮</div>
            <h2 style="
                color: #d4a017;
                font-size: 1.35rem;
                font-weight: 800;
                margin: 0 0 0.4rem;
                line-height: 1.3;
                letter-spacing: -0.02em;
            ">
                El mejor contenido mexicano, en tu inbox
            </h2>
            <p style="color: #9a7a30; font-size: 0.9rem; margin: 0; line-height: 1.5;">
                Recibe el Top 10 de tu ciudad, ofertas exclusivas y noticias&nbsp;FAMER cada semana
            </p>
        </div>

        {{-- Error message --}}
        @if ($errorMsg)
            <div style="
                background: rgba(220, 38, 38, 0.15);
                border: 1px solid rgba(220, 38, 38, 0.4);
                border-radius: 8px;
                padding: 0.75rem 1rem;
                color: #fca5a5;
                font-size: 0.875rem;
                margin-bottom: 1rem;
            ">
                {{ $errorMsg }}
            </div>
        @endif

        <form wire:submit="subscribe" novalidate>

            {{-- Email + Name row (side-by-side on desktop) --}}
            <div style="
                display: flex;
                gap: 0.75rem;
                margin-bottom: 0.75rem;
                flex-wrap: wrap;
            ">
                {{-- Email --}}
                <div style="flex: 2; min-width: 180px;">
                    <label
                        for="nl-email"
                        style="display: block; color: #9a7a30; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; letter-spacing: 0.05em; text-transform: uppercase;"
                    >
                        Correo electrónico *
                    </label>
                    <input
                        id="nl-email"
                        type="email"
                        wire:model="email"
                        placeholder="tu@correo.com"
                        autocomplete="email"
                        style="
                            width: 100%;
                            box-sizing: border-box;
                            background: rgba(255,255,255,0.06);
                            border: 1px solid {{ $errors->has('email') ? 'rgba(220,38,38,0.6)' : 'rgba(212,160,23,0.25)' }};
                            border-radius: 8px;
                            padding: 0.65rem 0.875rem;
                            color: #f5e6c0;
                            font-size: 0.925rem;
                            outline: none;
                            transition: border-color 0.2s;
                        "
                        onfocus="this.style.borderColor='rgba(212,160,23,0.7)'"
                        onblur="this.style.borderColor='{{ $errors->has('email') ? 'rgba(220,38,38,0.6)' : 'rgba(212,160,23,0.25)' }}'"
                    />
                    @error('email')
                        <p style="color: #fca5a5; font-size: 0.75rem; margin: 0.3rem 0 0;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div style="flex: 1.5; min-width: 140px;">
                    <label
                        for="nl-name"
                        style="display: block; color: #9a7a30; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; letter-spacing: 0.05em; text-transform: uppercase;"
                    >
                        Nombre
                    </label>
                    <input
                        id="nl-name"
                        type="text"
                        wire:model="name"
                        placeholder="Tu nombre"
                        autocomplete="name"
                        style="
                            width: 100%;
                            box-sizing: border-box;
                            background: rgba(255,255,255,0.06);
                            border: 1px solid rgba(212,160,23,0.25);
                            border-radius: 8px;
                            padding: 0.65rem 0.875rem;
                            color: #f5e6c0;
                            font-size: 0.925rem;
                            outline: none;
                        "
                        onfocus="this.style.borderColor='rgba(212,160,23,0.7)'"
                        onblur="this.style.borderColor='rgba(212,160,23,0.25)'"
                    />
                </div>
            </div>

            {{-- City (optional) --}}
            <div style="margin-bottom: 1.25rem;">
                <label
                    for="nl-city"
                    style="display: block; color: #9a7a30; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.3rem; letter-spacing: 0.05em; text-transform: uppercase;"
                >
                    Ciudad <span style="color: #6b5520; font-weight: 400;">(opcional — para personalizar tu Top 10)</span>
                </label>
                <input
                    id="nl-city"
                    type="text"
                    wire:model="city"
                    placeholder="Ej: Guadalajara, CDMX, Monterrey…"
                    style="
                        width: 100%;
                        box-sizing: border-box;
                        background: rgba(255,255,255,0.06);
                        border: 1px solid rgba(212,160,23,0.25);
                        border-radius: 8px;
                        padding: 0.65rem 0.875rem;
                        color: #f5e6c0;
                        font-size: 0.925rem;
                        outline: none;
                    "
                    onfocus="this.style.borderColor='rgba(212,160,23,0.7)'"
                    onblur="this.style.borderColor='rgba(212,160,23,0.25)'"
                />
            </div>

            {{-- Submit button --}}
            <button
                type="submit"
                wire:loading.attr="disabled"
                style="
                    width: 100%;
                    background: linear-gradient(135deg, #d4a017 0%, #e8b820 50%, #d4a017 100%);
                    color: #1a0a00;
                    border: none;
                    border-radius: 8px;
                    padding: 0.8rem 1.5rem;
                    font-size: 1rem;
                    font-weight: 800;
                    letter-spacing: 0.02em;
                    cursor: pointer;
                    transition: opacity 0.2s, transform 0.1s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                "
                onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'"
                onmousedown="this.style.transform='translateY(0)'"
            >
                <span wire:loading.remove>
                    🎯 Suscribirme Gratis
                </span>
                <span wire:loading style="display: flex; align-items: center; gap: 0.4rem;">
                    <svg style="width:18px;height:18px;animation:spin 1s linear infinite" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="60" stroke-dashoffset="20" stroke-linecap="round"/>
                    </svg>
                    Suscribiendo…
                </span>
            </button>

            {{-- Privacy note --}}
            <p style="
                text-align: center;
                color: #5a4010;
                font-size: 0.75rem;
                margin: 0.75rem 0 0;
                line-height: 1.4;
            ">
                🔒 Sin spam. Cancela cuando quieras. No compartimos tu correo.
            </p>

        </form>
    @endif

    <style>
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        /* Make inputs transparent on autofill (Chrome) */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 100px #2d1100 inset !important;
            -webkit-text-fill-color: #f5e6c0 !important;
        }
        @media (max-width: 500px) {
            #nl-email, [id^="nl-"] {
                font-size: 16px !important; /* prevent iOS zoom */
            }
        }
    </style>
</div>
