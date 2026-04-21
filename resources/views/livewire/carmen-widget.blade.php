<div>
    {{-- ============================================================
         CARMEN WIDGET — Global Support Chat
         Floating bubble + slide-up modal, all inline styles
         ============================================================ --}}

    <style>
        /* Bubble pulse */
        @keyframes carmen-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212,175,55,0.5); }
            50%       { box-shadow: 0 0 0 12px rgba(212,175,55,0); }
        }
        @keyframes carmen-slide-up {
            from { opacity: 0; transform: translateY(20px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0)   scale(1); }
        }
        @keyframes carmen-check {
            0%   { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes carmen-spin {
            to { transform: rotate(360deg); }
        }

        /* Floating bubble */
        .carmen-bubble {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #D4AF37 0%, #B08A1E 100%);
            color: #0B0B0B;
            border-radius: 50px;
            padding: 14px 20px 14px 16px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 20px rgba(212,175,55,0.4), 0 2px 8px rgba(0,0,0,0.4);
            border: none;
            outline: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            user-select: none;
        }
        .carmen-bubble:hover {
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 28px rgba(212,175,55,0.55), 0 4px 12px rgba(0,0,0,0.4);
        }
        .carmen-bubble.pulse {
            animation: carmen-pulse 2s ease-in-out infinite;
        }
        .carmen-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            background: #EF4444;
            color: #fff;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #0B0B0B;
        }

        /* Modal */
        .carmen-modal {
            position: fixed;
            bottom: 100px;
            right: 24px;
            z-index: 9998;
            width: 380px;
            max-width: calc(100vw - 32px);
            max-height: 580px;
            background: #111111;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(212,175,55,0.15);
            display: flex;
            flex-direction: column;
            animation: carmen-slide-up 0.3s cubic-bezier(0.34,1.56,0.64,1);
            font-family: 'Poppins', sans-serif;
        }

        /* Header */
        .carmen-header {
            background: #1A1A1A;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(212,175,55,0.15);
            flex-shrink: 0;
        }
        .carmen-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid rgba(212,175,55,0.4);
        }
        .carmen-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .carmen-status-dot {
            width: 8px;
            height: 8px;
            background: #22C55E;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            box-shadow: 0 0 6px rgba(34,197,94,0.6);
        }
        .carmen-close-btn {
            margin-left: auto;
            background: none;
            border: none;
            color: #6B7280;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s, background 0.2s;
        }
        .carmen-close-btn:hover { color: #F5F5F5; background: rgba(255,255,255,0.06); }

        /* Body */
        .carmen-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
            scrollbar-width: thin;
            scrollbar-color: #2A2A2A #111111;
        }

        /* Pills */
        .carmen-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        .carmen-pill {
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            color: #9CA3AF;
            border-radius: 50px;
            padding: 7px 13px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            font-family: 'Poppins', sans-serif;
        }
        .carmen-pill:hover { border-color: #D4AF37; color: #D4AF37; }
        .carmen-pill.active {
            background: rgba(212,175,55,0.12);
            border-color: #D4AF37;
            color: #D4AF37;
            font-weight: 600;
        }

        /* Form fields */
        .carmen-field {
            margin-bottom: 12px;
        }
        .carmen-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #9CA3AF;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .carmen-input {
            width: 100%;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 10px;
            padding: 10px 14px;
            color: #F5F5F5;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: border-color 0.15s;
            box-sizing: border-box;
        }
        .carmen-input:focus { border-color: #D4AF37; }
        .carmen-input.error { border-color: #EF4444; }
        .carmen-input::placeholder { color: #4B5563; }
        .carmen-error-msg {
            color: #EF4444;
            font-size: 11px;
            margin-top: 4px;
        }

        /* Restaurant tag */
        .carmen-restaurant-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(212,175,55,0.08);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            color: #D4AF37;
            font-weight: 600;
            margin-bottom: 14px;
            width: 100%;
        }

        /* Submit button */
        .carmen-submit {
            width: 100%;
            background: linear-gradient(135deg, #D4AF37 0%, #B08A1E 100%);
            color: #0B0B0B;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: opacity 0.2s, transform 0.15s;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .carmen-submit:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
        .carmen-submit:disabled { opacity: 0.6; cursor: not-allowed; }
        .carmen-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(11,11,11,0.3);
            border-top-color: #0B0B0B;
            border-radius: 50%;
            animation: carmen-spin 0.6s linear infinite;
        }

        /* Success */
        .carmen-success {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px 0 16px;
        }
        .carmen-check-wrap {
            width: 72px;
            height: 72px;
            background: rgba(212,175,55,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            border: 2px solid rgba(212,175,55,0.3);
        }
        .carmen-check-svg circle {
            stroke: #D4AF37;
            stroke-width: 2;
            fill: none;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: carmen-check 0.5s ease forwards 0.1s;
        }
        .carmen-check-svg polyline {
            stroke: #D4AF37;
            stroke-width: 2.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: carmen-check 0.4s ease forwards 0.4s;
        }
        .carmen-ticket-number {
            background: rgba(212,175,55,0.1);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 10px;
            padding: 10px 20px;
            color: #D4AF37;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin: 12px 0;
        }
        .carmen-close-success {
            width: 100%;
            margin-top: 16px;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            color: #F5F5F5;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background 0.15s;
        }
        .carmen-close-success:hover { background: #2A2A2A; }

        /* Mobile: full-width bottom sheet */
        @media (max-width: 480px) {
            .carmen-bubble {
                bottom: 16px;
                right: 16px;
                padding: 12px 16px 12px 14px;
                font-size: 13px;
            }
            .carmen-modal {
                bottom: 0;
                right: 0;
                left: 0;
                width: 100%;
                max-width: 100%;
                max-height: 88vh;
                border-radius: 20px 20px 0 0;
            }
        }
    </style>

    {{-- ── Floating Bubble ── --}}
    <button
        class="carmen-bubble {{ !$isOpen ? 'pulse' : '' }}"
        wire:click="open"
        aria-label="Abrir soporte Carmen"
        style="position:relative;"
    >
        {{-- Red badge attention getter (shown only when closed) --}}
        @if (!$isOpen)
            <span class="carmen-badge">1</span>
        @endif

        {{-- Chat icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>Soporte</span>
    </button>

    {{-- ── Modal ── --}}
    @if ($isOpen)
        <div class="carmen-modal" role="dialog" aria-modal="true" aria-label="Chat de soporte Carmen">

            {{-- Header --}}
            <div class="carmen-header">
                <div class="carmen-avatar">
                    <img src="/images/carmen-avatar.jpg" alt="Carmen">
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:15px; font-weight:700; color:#F5F5F5; line-height:1.2;">Carmen</div>
                    <div style="font-size:12px; color:#6B7280; margin-top:2px;">
                        <span class="carmen-status-dot"></span>
                        Soporte FAMER &middot; Responde en minutos
                    </div>
                </div>
                <button class="carmen-close-btn" wire:click="close" aria-label="Cerrar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="carmen-body">

                {{-- STEP: form --}}
                @if ($step === 'form')

                    <p style="font-size:14px; color:#D1D5DB; margin:0 0 18px; line-height:1.5;">
                        Hola, soy Carmen 👋 ¿En qué puedo ayudarte hoy?
                    </p>

                    {{-- General error --}}
                    @if (!empty($formErrors['general']))
                        <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); border-radius:10px; padding:10px 14px; font-size:13px; color:#EF4444; margin-bottom:14px;">
                            {{ $formErrors['general'] }}
                        </div>
                    @endif

                    {{-- Issue type pills --}}
                    <div style="margin-bottom:6px;">
                        <div class="carmen-label">¿Cuál es el problema?</div>
                    </div>
                    <div class="carmen-pills">
                        <button type="button"
                            class="carmen-pill {{ $issueType === 'verification' ? 'active' : '' }}"
                            wire:click="$set('issueType', 'verification')">
                            🔐 No reconozco el email/teléfono
                        </button>
                        <button type="button"
                            class="carmen-pill {{ $issueType === 'claim' ? 'active' : '' }}"
                            wire:click="$set('issueType', 'claim')">
                            📋 Problema al reclamar
                        </button>
                        <button type="button"
                            class="carmen-pill {{ $issueType === 'subscription' ? 'active' : '' }}"
                            wire:click="$set('issueType', 'subscription')">
                            💳 Suscripción o pago
                        </button>
                        <button type="button"
                            class="carmen-pill {{ $issueType === 'data_error' ? 'active' : '' }}"
                            wire:click="$set('issueType', 'data_error')">
                            ✏️ Datos incorrectos en mi perfil
                        </button>
                        <button type="button"
                            class="carmen-pill {{ $issueType === 'other' ? 'active' : '' }}"
                            wire:click="$set('issueType', 'other')">
                            💬 Otro
                        </button>
                    </div>

                    {{-- Restaurant tag (if pre-filled) --}}
                    @if ($restaurantName)
                        <div class="carmen-restaurant-tag">
                            🍽️ Restaurante: <span style="font-weight:700;">{{ $restaurantName }}</span>
                        </div>
                    @endif

                    {{-- Nombre --}}
                    <div class="carmen-field">
                        <label class="carmen-label">Nombre <span style="color:#EF4444;">*</span></label>
                        <input
                            type="text"
                            class="carmen-input {{ !empty($formErrors['name']) ? 'error' : '' }}"
                            wire:model="name"
                            placeholder="Tu nombre completo"
                            autocomplete="name"
                        >
                        @if (!empty($formErrors['name']))
                            <div class="carmen-error-msg">{{ $formErrors['name'] }}</div>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="carmen-field">
                        <label class="carmen-label">Email <span style="color:#EF4444;">*</span></label>
                        <input
                            type="email"
                            class="carmen-input {{ !empty($formErrors['email']) ? 'error' : '' }}"
                            wire:model="email"
                            placeholder="tu@email.com"
                            autocomplete="email"
                        >
                        @if (!empty($formErrors['email']))
                            <div class="carmen-error-msg">{{ $formErrors['email'] }}</div>
                        @endif
                    </div>

                    {{-- Teléfono (optional) --}}
                    <div class="carmen-field">
                        <label class="carmen-label">Teléfono <span style="color:#4B5563; font-weight:400; text-transform:none; letter-spacing:0;">(opcional)</span></label>
                        <input
                            type="tel"
                            class="carmen-input"
                            wire:model="phone"
                            placeholder="+1 (555) 000-0000"
                            autocomplete="tel"
                        >
                    </div>

                    {{-- Mensaje --}}
                    <div class="carmen-field">
                        <label class="carmen-label">Cuéntame más <span style="color:#EF4444;">*</span></label>
                        <textarea
                            class="carmen-input {{ !empty($formErrors['message']) ? 'error' : '' }}"
                            wire:model="message"
                            placeholder="Describe tu situación con el mayor detalle posible..."
                            rows="4"
                            style="resize:vertical; min-height:80px;"
                        ></textarea>
                        @if (!empty($formErrors['message']))
                            <div class="carmen-error-msg">{{ $formErrors['message'] }}</div>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button
                        type="button"
                        class="carmen-submit"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                    >
                        <span wire:loading.remove wire:target="submit">Enviar mensaje →</span>
                        <span wire:loading wire:target="submit" style="display:flex;align-items:center;gap:8px;">
                            <span class="carmen-spinner"></span> Enviando...
                        </span>
                    </button>

                {{-- STEP: success --}}
                @elseif ($step === 'success')

                    <div class="carmen-success">
                        {{-- Animated checkmark --}}
                        <div class="carmen-check-wrap">
                            <svg class="carmen-check-svg" width="36" height="36" viewBox="0 0 52 52">
                                <circle cx="26" cy="26" r="24" />
                                <polyline points="14,27 22,35 38,17" />
                            </svg>
                        </div>

                        <div style="font-size:18px; font-weight:700; color:#F5F5F5; margin-bottom:6px;">
                            ¡Listo! Tu ticket fue creado 🎉
                        </div>

                        <div class="carmen-ticket-number">
                            # {{ $ticketNumber }}
                        </div>

                        <p style="font-size:13px; color:#9CA3AF; line-height:1.6; margin:0;">
                            Carmen te contactará pronto al email que proporcionaste.<br>
                            <span style="color:#6B7280; font-size:12px;">Guarda tu número de ticket como referencia.</span>
                        </p>

                        <button type="button" class="carmen-close-success" wire:click="close">
                            Cerrar
                        </button>
                    </div>

                @endif

            </div>{{-- /carmen-body --}}
        </div>{{-- /carmen-modal --}}
    @endif

</div>
