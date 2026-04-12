@extends('layouts.app')

@section('title', 'Completa tu pago — FAMER')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap');

    .famer-payment-page {
        background: #0B0B0B;
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
        color: #F5F5F5;
    }

    .playfair { font-family: 'Playfair Display', serif; }

    /* Header */
    .payment-header {
        background: #0B0B0B;
        border-bottom: 1px solid rgba(212, 175, 55, 0.15);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .payment-header img {
        height: 38px;
        width: auto;
    }

    .back-link {
        color: #D4AF37;
        font-size: 0.85rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: opacity 0.2s;
    }

    .back-link:hover { opacity: 0.75; }

    /* Layout */
    .payment-layout {
        max-width: 1100px;
        margin: 0 auto;
        padding: 3rem 1.5rem 4rem;
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 2.5rem;
        align-items: start;
    }

    @media (max-width: 768px) {
        .payment-layout {
            grid-template-columns: 1fr;
            padding: 1.5rem 1rem 3rem;
        }
    }

    /* Order summary card */
    .order-card {
        background: #111111;
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 16px;
        padding: 2rem;
        position: sticky;
        top: 1.5rem;
    }

    .order-card .section-label {
        font-size: 0.7rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #D4AF37;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .order-card .restaurant-name {
        font-size: 1.15rem;
        font-weight: 600;
        color: #F5F5F5;
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .order-card .restaurant-meta {
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 1.75rem;
    }

    .plan-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(212, 175, 55, 0.1);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 999px;
        padding: 0.3rem 0.85rem;
        font-size: 0.8rem;
        color: #D4AF37;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .price-block {
        background: #1A1A1A;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .price-main {
        font-size: 2.2rem;
        font-weight: 700;
        color: #D4AF37;
        font-family: 'Playfair Display', serif;
        line-height: 1;
    }

    .price-period {
        font-size: 0.85rem;
        color: #888;
        margin-top: 0.2rem;
    }

    .price-renewal {
        font-size: 0.78rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.75rem;
    }

    .feature-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        font-size: 0.85rem;
        color: #ccc;
        padding: 0.45rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }

    .feature-list li:last-child { border-bottom: none; }

    .feature-list li .check {
        color: #D4AF37;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* Guarantee badge */
    .guarantee-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: rgba(31, 61, 43, 0.4);
        border: 1px solid rgba(31, 61, 43, 0.8);
        border-radius: 10px;
        padding: 0.85rem 1rem;
    }

    .guarantee-badge .icon {
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .guarantee-badge .text strong {
        display: block;
        font-size: 0.85rem;
        color: #6fcf97;
        font-weight: 600;
    }

    .guarantee-badge .text span {
        font-size: 0.75rem;
        color: #888;
    }

    /* Payment column */
    .checkout-column {
        background: #111111;
        border: 1px solid rgba(212, 175, 55, 0.15);
        border-radius: 16px;
        padding: 2rem;
    }

    .checkout-column .section-label {
        font-size: 0.7rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #D4AF37;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Loading spinner */
    .checkout-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        gap: 1rem;
        color: #666;
        font-size: 0.85rem;
    }

    .spinner {
        width: 36px;
        height: 36px;
        border: 3px solid rgba(212, 175, 55, 0.15);
        border-top-color: #D4AF37;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* PaymentElement wrapper */
    #payment-element {
        display: none; /* shown once Stripe fires 'ready' */
        margin-bottom: 1.5rem;
    }

    /* Error message */
    #payment-error {
        display: none;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-radius: 8px;
        color: #fca5a5;
        font-size: 0.85rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1.25rem;
    }

    /* Pay button */
    .pay-btn {
        width: 100%;
        background: #D4AF37;
        color: #0B0B0B;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        padding: 0.9rem 1.5rem;
        cursor: pointer;
        transition: background 0.2s, opacity 0.2s;
        letter-spacing: 0.02em;
    }

    .pay-btn:hover:not(:disabled) { background: #c9a730; }

    .pay-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .pay-btn-hidden {
        display: none; /* hidden until PaymentElement is ready */
    }

    /* Fine print */
    .payment-fine-print {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        color: #555;
        margin-top: 1rem;
    }

    .payment-fine-print svg {
        color: #D4AF37;
        opacity: 0.6;
        flex-shrink: 0;
    }

    /* Security badges row */
    .security-row {
        max-width: 1100px;
        margin: 0 auto;
        padding: 1.5rem 1.5rem 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .security-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.78rem;
        color: #555;
    }

    .security-item svg {
        color: #D4AF37;
        opacity: 0.6;
    }
</style>

<div class="famer-payment-page">

    {{-- Header --}}
    <div class="payment-header">
        <a href="/">
            <img src="/images/branding/famer55.png" alt="FAMER — Famous Mexican Restaurants">
        </a>
        <a href="{{ route('claim.restaurant', ['restaurant' => $restaurant->slug]) }}" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
            Volver a seleccionar plan
        </a>
    </div>

    {{-- Two-column layout --}}
    <div class="payment-layout">

        {{-- LEFT: Order Summary --}}
        <div class="order-card">
            <div class="section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Resumen de tu pedido
            </div>

            <div class="restaurant-name">{{ $restaurant->name }}</div>
            <div class="restaurant-meta">
                {{ $restaurant->city }}@if($restaurant->state_name), {{ $restaurant->state_name }}@endif
            </div>

            <div class="plan-badge">
                @if($plan === 'elite')
                    ⭐
                @else
                    ✦
                @endif
                Plan {{ $planDetails['name'] }}
            </div>

            <div class="price-block">
                @if($isTrial)
                    <div class="price-main" style="color:#D4AF37;">30 días GRATIS</div>
                    <div class="price-period" style="color:#aaa; margin-top:0.3rem;">No se realiza ningún cargo hoy</div>
                    <div class="price-renewal" style="color:#666; margin-top:0.4rem;">A partir del día 31: $79/mes — cancela cuando quieras</div>
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-top:0.85rem; font-size:0.82rem; color:#4ADE80;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        Ingresa tu tarjeta para activar el período de prueba
                    </div>
                @else
                    <div class="price-main">{{ $planDetails['price'] }}<span style="font-size:1rem;color:#888;font-family:'Poppins',sans-serif;font-weight:400"> USD</span></div>
                    <div class="price-period">{{ $planDetails['period'] }}</div>
                    <div class="price-renewal">{{ $planDetails['renewal'] }}</div>
                @endif
            </div>

            <ul class="feature-list">
                @foreach($planDetails['features'] as $feature)
                <li>
                    <svg class="check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>

            <div class="guarantee-badge">
                <div class="icon">🛡️</div>
                <div class="text">
                    <strong>Garantía 30 días</strong>
                    <span>Si no estás satisfecho, te devolvemos tu dinero sin preguntas.</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: Stripe PaymentElement --}}
        <div class="checkout-column">
            @if($isTrial)
            <div style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); border-radius:0.5rem; padding:0.75rem 1rem; margin-bottom:1rem; text-align:center;">
                <p style="color:#4ADE80; font-weight:600; margin:0; font-size:0.9rem;">No se realizará ningún cargo durante 30 días</p>
                <p style="color:#6B7280; font-size:0.8rem; margin:0.25rem 0 0;">Solo guardamos tu tarjeta para activar el plan al finalizar el período de prueba</p>
            </div>
            @endif
            <div class="section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
                Información de pago
            </div>

            <form id="payment-form">

                {{-- Loading spinner (hidden once Stripe fires 'ready') --}}
                <div id="payment-loading" class="checkout-loading">
                    <div class="spinner"></div>
                    <span>Cargando pasarela de pago segura…</span>
                </div>

                {{-- Stripe PaymentElement mounts here --}}
                <div id="payment-element"></div>

                {{-- Error messages --}}
                <div id="payment-error"></div>

                {{-- Submit button (hidden until ready) --}}
                <button id="pay-button" type="submit" class="pay-btn pay-btn-hidden" disabled>
                    @if($isTrial)
                        Activar 30 días gratis →
                    @else
                        Pagar ${{ $planDetails['price'] }} ahora →
                    @endif
                </button>

            </form>

            <div class="payment-fine-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="11" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Pago seguro procesado por Stripe
            </div>
        </div>

    </div>

    {{-- Security badges row --}}
    <div class="security-row">
        <div class="security-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Conexión cifrada SSL 256-bit
        </div>
        <div class="security-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
            Pagos procesados por Stripe
        </div>
        <div class="security-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Tus datos nunca se almacenan en nuestros servidores
        </div>
        <div class="security-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Cancela cuando quieras
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const stripe = Stripe('{{ $stripePublicKey }}');
    const clientSecret = '{{ $clientSecret }}';
    const returnUrl = '{{ $returnUrl }}';

    // Initialize Elements with dark night theme
    const elements = stripe.elements({
        clientSecret,
        appearance: {
            theme: 'night',
            variables: {
                colorPrimary: '#D4AF37',
                colorBackground: '#1A1A1A',
                colorText: '#F5F5F5',
                colorTextSecondary: '#999999',
                colorDanger: '#EF4444',
                fontFamily: 'Poppins, sans-serif',
                borderRadius: '8px',
                spacingUnit: '4px',
                colorIconTab: '#9CA3AF',
                colorIconTabSelected: '#F5F5F5',
                colorIconTabHover: '#D4AF37',
            },
            rules: {
                '.Input': {
                    border: '1px solid #2A2A2A',
                    boxShadow: 'none',
                },
                '.Input:focus': {
                    border: '1px solid rgba(212,175,55,0.5)',
                    boxShadow: '0 0 0 2px rgba(212,175,55,0.1)',
                },
                '.Label': {
                    color: '#999',
                    fontSize: '0.8rem',
                    letterSpacing: '0.03em',
                },
                '.Tab': {
                    border: '1px solid #2A2A2A',
                    color: '#999',
                },
                '.Tab--selected': {
                    border: '1px solid rgba(212,175,55,0.4)',
                    color: '#D4AF37',
                    backgroundColor: '#1A1A1A',
                },
                '.Tab:hover': {
                    color: '#D4AF37',
                },
            }
        }
    });

    // Create and mount PaymentElement
    const paymentElement = elements.create('payment', {
        layout: 'tabs',
        defaultValues: {
            billingDetails: {
                name: '{{ addslashes(auth()->user()?->name ?? "") }}',
                email: '{{ addslashes(auth()->user()?->email ?? "") }}',
            }
        }
    });

    paymentElement.mount('#payment-element');

    // Show form once Stripe is ready
    paymentElement.on('ready', () => {
        document.getElementById('payment-loading').style.display = 'none';
        document.getElementById('payment-element').style.display = 'block';

        const payBtn = document.getElementById('pay-button');
        payBtn.classList.remove('pay-btn-hidden');
        payBtn.disabled = false;
    });

    // Handle submit
    const form = document.getElementById('payment-form');
    const payButton = document.getElementById('pay-button');
    const errorDiv = document.getElementById('payment-error');
    const isTrial = {{ $isTrial ? 'true' : 'false' }};

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        payButton.disabled = true;
        payButton.textContent = isTrial ? 'Activando prueba...' : 'Procesando pago...';
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';

        let result;
        if (isTrial) {
            // SetupIntent — just save the card, no charge today
            result = await stripe.confirmSetup({
                elements,
                confirmParams: {
                    return_url: returnUrl,
                }
            });
        } else {
            // PaymentIntent — charge the card now
            result = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: returnUrl,
                }
            });
        }

        const { error } = result;
        if (error) {
            errorDiv.textContent = error.message;
            errorDiv.style.display = 'block';
            payButton.disabled = false;
            payButton.textContent = isTrial ? 'Activar 30 días gratis →' : 'Pagar ahora';
        }
        // On success Stripe redirects to return_url automatically — no further action needed here
    });
});
</script>
@endpush
