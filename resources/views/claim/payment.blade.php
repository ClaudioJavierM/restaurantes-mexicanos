@extends('layouts.app')

@section('title', 'Completa tu pago — FAMER')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap');

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

    /* Stripe embed column */
    .checkout-column {
        background: #111111;
        border: 1px solid rgba(212, 175, 55, 0.15);
        border-radius: 16px;
        padding: 2rem;
        min-height: 480px;
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
        min-height: 300px;
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

    /* Security badges */
    .security-row {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.5rem 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding-top: 1.5rem;
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

    /* Stripe embed overrides for dark theme */
    #checkout {
        /* Stripe will inject an iframe — let it take full width */
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
                <div class="price-main">{{ $planDetails['price'] }}<span style="font-size:1rem;color:#888;font-family:'Poppins',sans-serif;font-weight:400"> USD</span></div>
                <div class="price-period">{{ $planDetails['period'] }}</div>
                <div class="price-renewal">{{ $planDetails['renewal'] }}</div>
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

        {{-- RIGHT: Stripe Embedded Checkout --}}
        <div class="checkout-column">
            <div class="section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/></svg>
                Información de pago
            </div>

            {{-- Loading state (hidden once Stripe mounts) --}}
            <div id="checkout-loading" class="checkout-loading">
                <div class="spinner"></div>
                <span>Cargando pasarela de pago segura…</span>
            </div>

            {{-- Stripe mounts its full UI here --}}
            <div id="checkout"></div>
        </div>

    </div>

    {{-- Security row --}}
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
    (function () {
        const stripe = Stripe('{{ $stripePublicKey }}');
        const clientSecret = '{{ $clientSecret }}';

        async function initialize() {
            try {
                const checkout = await stripe.initEmbeddedCheckout({ clientSecret });
                // Hide loading spinner, mount checkout
                document.getElementById('checkout-loading').style.display = 'none';
                checkout.mount('#checkout');
            } catch (err) {
                console.error('Stripe Embedded Checkout error:', err);
                document.getElementById('checkout-loading').innerHTML =
                    '<p style="color:#e57373;font-size:0.9rem;text-align:center;">Error al cargar el formulario de pago.<br><a href="{{ route("claim.restaurant", ["restaurant" => $restaurant->slug]) }}" style="color:#D4AF37;">← Volver e intentar de nuevo</a></p>';
            }
        }

        initialize();
    })();
</script>
@endpush
