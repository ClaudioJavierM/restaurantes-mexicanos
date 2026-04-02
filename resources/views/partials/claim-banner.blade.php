@if(!$restaurant->is_claimed)
@php $isEn = str_contains(request()->getHost(), 'famousmexicanrestaurants.com'); @endphp
<div style="background: linear-gradient(135deg, #1A1A1A 0%, #0F2A1A 100%); border: 1px solid #D4AF37; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 1.5rem;">

    {{-- Header row --}}
    <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1rem;">
        <span style="font-size: 1.5rem; line-height: 1;">🏪</span>
        <p style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #F5F5F5; line-height: 1.3;">
            @if($isEn)
                Are you the owner of <span style="color: #D4AF37;">{{ $restaurant->name }}</span>?
            @else
                ¿Eres el dueño de <span style="color: #D4AF37;">{{ $restaurant->name }}</span>?
            @endif
        </p>
    </div>

    {{-- Subtitle --}}
    <p style="margin: 0 0 1rem 0; color: #9CA3AF; font-size: 0.9rem;">
        @if($isEn)
            Claim your business for FREE and get:
        @else
            Reclama tu negocio GRATIS y obtén:
        @endif
    </p>

    {{-- Benefits 2×2 grid --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 1.5rem; margin-bottom: 1.25rem;">
        @if($isEn)
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Reply to customer reviews</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Add photos, menu &amp; hours</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Accept online reservations</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">View visits &amp; click analytics</span>
            </div>
        @else
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Responde a reseñas de clientes</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Agrega fotos, menú y horarios</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Recibe reservaciones en línea</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                <span style="color: #D4AF37; font-weight: 700; flex-shrink: 0;">✓</span>
                <span style="color: #9CA3AF; font-size: 0.875rem;">Estadísticas de visitas y clics</span>
            </div>
        @endif
    </div>

    {{-- CTA Button --}}
    <a href="/claim?restaurant={{ $restaurant->slug }}&utm_source=detail&utm_medium=banner&utm_campaign=claim"
       style="display: block; background: #D4AF37; color: #0B0B0B; font-weight: 700; padding: 0.875rem 2rem; border-radius: 8px; width: 100%; font-size: 1rem; border: none; cursor: pointer; text-align: center; text-decoration: none; line-height: 1.4; box-sizing: border-box;">
        @if($isEn)
            Claim This Restaurant — It's Free
        @else
            Reclamar Este Restaurante — Es Gratis
        @endif
    </a>

    {{-- Trust signals below button --}}
    <p style="margin: 0.5rem 0 0 0; color: #6B7280; font-size: 0.775rem; text-align: center;">
        @if($isEn)
            No credit card required · Verified within 24h · 100% free
        @else
            Sin tarjeta de crédito · Verificación en 24h · 100% gratis
        @endif
    </p>

    {{-- Social proof --}}
    <p style="color: #6B7280; font-size: 0.8rem; text-align: center; margin: 0.75rem 0 0 0;">
        @if($isEn)
            Over 3,800 restaurants have already claimed their profile on FAMER
        @else
            Más de 3,800 restaurantes ya reclamaron su perfil en FAMER
        @endif
    </p>

</div>
@endif
