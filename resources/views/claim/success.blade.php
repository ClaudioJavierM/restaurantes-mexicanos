@extends('layouts.app')

@section('title', '¡Restaurante Reclamado! — FAMER')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap');

    .famer-success-page {
        background: #0B0B0B;
        min-height: 100vh;
        padding: 4rem 1rem;
        font-family: 'Poppins', sans-serif;
    }

    .playfair { font-family: 'Playfair Display', serif; }

    /* Animated checkmark */
    @keyframes checkPop {
        0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
        60%  { transform: scale(1.15) rotate(3deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }
    @keyframes ringPulse {
        0%   { box-shadow: 0 0 0 0 rgba(212,175,55,0.45); }
        70%  { box-shadow: 0 0 0 20px rgba(212,175,55,0); }
        100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
    }

    .check-wrapper {
        animation: checkPop 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    .check-ring {
        animation: ringPulse 1.5s ease-out 0.6s infinite;
    }

    /* Coupon */
    .coupon-code {
        font-family: 'Courier New', monospace;
        letter-spacing: 0.2em;
    }

    /* Plan cards */
    .plan-card {
        background: #1A1A1A;
        border: 1px solid #2A2A2A;
        border-radius: 1rem;
        padding: 1.75rem;
        transition: border-color 0.2s, transform 0.2s;
    }
    .plan-card:hover { transform: translateY(-2px); }
    .plan-card.featured {
        border-color: #D4AF37;
        position: relative;
    }
    .plan-card.featured::before {
        content: 'Recomendado';
        position: absolute;
        top: -0.75rem;
        left: 50%;
        transform: translateX(-50%);
        background: #D4AF37;
        color: #0B0B0B;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.2rem 0.8rem;
        border-radius: 9999px;
    }

    /* Buttons */
    .btn-gold-solid {
        background: #D4AF37;
        color: #0B0B0B;
        font-weight: 700;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        display: block;
        text-align: center;
        transition: background 0.2s, transform 0.15s;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-gold-solid:hover { background: #C4A027; transform: translateY(-1px); }

    .btn-gold-outline {
        border: 1.5px solid #D4AF37;
        color: #D4AF37;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        display: block;
        text-align: center;
        transition: background 0.2s, transform 0.15s;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-gold-outline:hover { background: rgba(212,175,55,0.08); transform: translateY(-1px); }

    .btn-dark {
        background: #2A2A2A;
        color: #F5F5F5;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        display: block;
        text-align: center;
        transition: background 0.2s, transform 0.15s;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-dark:hover { background: #333; transform: translateY(-1px); }

    /* Steps */
    .step-circle {
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background: rgba(212,175,55,0.15);
        border: 1.5px solid #D4AF37;
        color: #D4AF37;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Copy button */
    .copy-btn {
        background: rgba(212,175,55,0.12);
        border: 1px solid rgba(212,175,55,0.3);
        color: #D4AF37;
        border-radius: 0.375rem;
        padding: 0.35rem 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
        white-space: nowrap;
    }
    .copy-btn:hover { background: rgba(212,175,55,0.2); }
</style>

<div class="famer-success-page">
    <div style="max-width: 56rem; margin: 0 auto;">

        {{-- =========================================================
             Section 1 — Celebration
        ========================================================= --}}
        <div style="text-align: center; margin-bottom: 3rem;">

            {{-- Animated checkmark --}}
            <div style="display: flex; justify-content: center; margin-bottom: 1.75rem;">
                <div class="check-wrapper check-ring" style="
                    width: 6rem; height: 6rem;
                    background: rgba(34,197,94,0.12);
                    border: 2px solid rgba(34,197,94,0.4);
                    border-radius: 9999px;
                    display: flex; align-items: center; justify-content: center;
                ">
                    <svg style="width: 3rem; height: 3rem; color: #22c55e;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <h1 class="playfair" style="font-size: 3rem; font-weight: 700; color: #D4AF37; margin-bottom: 0.75rem; line-height: 1.15;">
                ¡Felicidades!
            </h1>
            <p style="font-size: 1.2rem; color: #A0A0A0; max-width: 38rem; margin: 0 auto;">
                <span style="color: #F5F5F5; font-weight: 600;">{{ $restaurant->name ?? 'Tu restaurante' }}</span>
                ya está reclamado en FAMER
            </p>
        </div>

        {{-- =========================================================
             Section 2 — Coupon Box (only for free / null plan)
        ========================================================= --}}
        @if(empty($plan) || $plan === 'free')
        <div x-data="{ copied: false }" style="
            border: 1.5px solid #D4AF37;
            background: rgba(212,175,55,0.04);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2.5rem;
            text-align: center;
        ">
            <p style="font-size: 1rem; color: #D4AF37; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.06em;">
                🎁 Oferta exclusiva — Solo por 24 horas
            </p>
            <p style="color: #A0A0A0; font-size: 0.9rem; margin-bottom: 1.25rem;">
                30% de descuento en tu primer mes Premium
            </p>

            <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <span class="coupon-code" style="
                    font-size: 2rem;
                    font-weight: 700;
                    color: #D4AF37;
                    background: #1A1A1A;
                    border: 1px dashed rgba(212,175,55,0.5);
                    padding: 0.5rem 1.5rem;
                    border-radius: 0.5rem;
                    letter-spacing: 0.25em;
                ">FAMER30</span>

                <button
                    @click="
                        navigator.clipboard.writeText('FAMER30');
                        copied = true;
                        setTimeout(() => copied = false, 2500);
                    "
                    class="copy-btn"
                    x-text="copied ? '✓ Copiado' : 'Copiar'"
                ></button>
            </div>

            <p style="color: #6B6B6B; font-size: 0.8rem;">
                Vence: {{ now()->addHours(24)->format('d \d\e M Y, H:i') }} hrs
            </p>
        </div>
        @endif

        {{-- =========================================================
             Section 3 — Plan Cards
        ========================================================= --}}
        @if(empty($plan) || $plan === 'free')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr)); gap: 1.25rem; margin-bottom: 3rem;">

            {{-- Free (current) --}}
            <div class="plan-card">
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6B6B6B;">Tu plan actual</span>
                    <h3 class="playfair" style="font-size: 1.4rem; color: #F5F5F5; margin: 0.25rem 0 0.1rem;">Plan Gratuito</h3>
                    <p style="font-size: 1.6rem; font-weight: 700; color: #A0A0A0;">$0<span style="font-size: 0.85rem; font-weight: 400;">/mes</span></p>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; color: #6B6B6B; font-size: 0.85rem; space-y: 0.5rem;">
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Perfil básico</li>
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Aparece en búsquedas</li>
                    <li style="padding: 0.3rem 0;">✓ &nbsp;1 foto</li>
                </ul>
                <a href="/owner" class="btn-gold-outline">Ir a mi Dashboard →</a>
            </div>

            {{-- Premium (highlighted) --}}
            <div class="plan-card featured">
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #D4AF37;">Más popular</span>
                    <h3 class="playfair" style="font-size: 1.4rem; color: #F5F5F5; margin: 0.25rem 0 0.1rem;">Premium</h3>
                    <p style="font-size: 1.6rem; font-weight: 700; color: #D4AF37;">
                        $9.99<span style="font-size: 0.85rem; font-weight: 400; color: #A0A0A0;"> primer mes</span>
                    </p>
                    <p style="font-size: 0.8rem; color: #6B6B6B; margin-top: -0.1rem;">Después $39/mes</p>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; color: #A0A0A0; font-size: 0.85rem;">
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A; color: #D4AF37;">✦ &nbsp;Fotos ilimitadas</li>
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A; color: #D4AF37;">✦ &nbsp;Badge verificado</li>
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Estadísticas avanzadas</li>
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Posición destacada</li>
                    <li style="padding: 0.3rem 0;">✓ &nbsp;Soporte prioritario</li>
                </ul>
                <a href="/claim/upgrade?plan=premium&coupon=FAMER30" class="btn-gold-solid">Activar con FAMER30 →</a>
            </div>

            {{-- Elite --}}
            <div class="plan-card">
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #8B6914;">Elite</span>
                    <h3 class="playfair" style="font-size: 1.4rem; color: #F5F5F5; margin: 0.25rem 0 0.1rem;">Elite</h3>
                    <p style="font-size: 1.6rem; font-weight: 700; color: #A0A0A0;">$79<span style="font-size: 0.85rem; font-weight: 400;">/mes</span></p>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.25rem; color: #6B6B6B; font-size: 0.85rem;">
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Todo lo de Premium</li>
                    <li style="padding: 0.3rem 0; border-bottom: 1px solid #2A2A2A;">✓ &nbsp;Acceso a API</li>
                    <li style="padding: 0.3rem 0;">✓ &nbsp;Destacado en homepage</li>
                </ul>
                <a href="/claim/upgrade?plan=elite" class="btn-dark">Ver Elite →</a>
            </div>

        </div>

        @else
        {{-- Already on paid plan --}}
        <div style="
            background: rgba(34,197,94,0.06);
            border: 1.5px solid rgba(34,197,94,0.3);
            border-radius: 1rem;
            padding: 1.75rem;
            margin-bottom: 3rem;
            text-align: center;
        ">
            <span style="
                display: inline-flex; align-items: center; gap: 0.4rem;
                background: rgba(34,197,94,0.15);
                color: #22c55e;
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                padding: 0.3rem 0.9rem;
                border-radius: 9999px;
                margin-bottom: 0.75rem;
            ">
                <svg style="width: 1rem; height: 1rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Activo
            </span>
            <h3 class="playfair" style="font-size: 1.6rem; color: #F5F5F5; margin-bottom: 0.5rem;">
                ¡Ya tienes {{ ucfirst($plan ?? 'Premium') }}!
            </h3>
            <ul style="list-style: none; padding: 0; margin: 1rem auto 0; max-width: 22rem; color: #A0A0A0; font-size: 0.9rem; text-align: left; display: inline-block;">
                @if(in_array($plan, ['premium', 'elite']))
                <li style="padding: 0.3rem 0;">✦ &nbsp;Fotos ilimitadas desbloqueadas</li>
                <li style="padding: 0.3rem 0;">✦ &nbsp;Badge verificado activo</li>
                <li style="padding: 0.3rem 0;">✦ &nbsp;Estadísticas avanzadas</li>
                <li style="padding: 0.3rem 0;">✦ &nbsp;Posición destacada en búsquedas</li>
                @endif
                @if($plan === 'elite')
                <li style="padding: 0.3rem 0;">✦ &nbsp;Acceso a API REST</li>
                <li style="padding: 0.3rem 0;">✦ &nbsp;Destacado en homepage</li>
                @endif
            </ul>
        </div>
        @endif

        {{-- =========================================================
             Section 4 — Next Steps
        ========================================================= --}}
        <div style="
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 1rem;
            padding: 2rem;
        ">
            <h2 class="playfair" style="font-size: 1.5rem; color: #F5F5F5; margin-bottom: 1.5rem; text-align: center;">
                Tus próximos pasos
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr)); gap: 1rem; margin-bottom: 2rem;">

                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.6rem;">
                    <div class="step-circle">1</div>
                    <div>
                        <p style="color: #F5F5F5; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.2rem;">Agrega fotos</p>
                        <p style="color: #6B6B6B; font-size: 0.78rem;">Muestra tu restaurante</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.6rem;">
                    <div class="step-circle">2</div>
                    <div>
                        <p style="color: #F5F5F5; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.2rem;">Define horarios</p>
                        <p style="color: #6B6B6B; font-size: 0.78rem;">¿Cuándo estás abierto?</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.6rem;">
                    <div class="step-circle">3</div>
                    <div>
                        <p style="color: #F5F5F5; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.2rem;">Sube tu menú</p>
                        <p style="color: #6B6B6B; font-size: 0.78rem;">Platos y precios</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 0.6rem;">
                    <div class="step-circle">4</div>
                    <div>
                        <p style="color: #F5F5F5; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.2rem;">Responde reseñas</p>
                        <p style="color: #6B6B6B; font-size: 0.78rem;">Conecta con clientes</p>
                    </div>
                </div>

            </div>

            <div style="text-align: center;">
                <a href="/owner" class="btn-gold-solid" style="display: inline-block; padding: 1rem 2.5rem; font-size: 1rem;">
                    Ir a mi Dashboard de Propietario →
                </a>
                <p style="color: #4B4B4B; font-size: 0.78rem; margin-top: 1rem;">
                    También te enviamos un correo con tus datos de acceso y las instrucciones
                </p>
            </div>
        </div>

        {{-- Support --}}
        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #1A1A1A;">
            <p style="color: #4B4B4B; font-size: 0.82rem;">
                ¿Necesitas ayuda? &nbsp;
                <a href="mailto:support@restaurantesmexicanosfamosos.com" style="color: #D4AF37; text-decoration: none; font-weight: 500;">
                    Contacta a soporte
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
