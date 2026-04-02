@extends('layouts.app')

@section('title', $isEn ? 'How FAMER Works — Grow Your Mexican Restaurant' : 'Cómo Funciona FAMER — Haz Crecer Tu Restaurante Mexicano')
@section('meta_description', $isEn
    ? 'See exactly how FAMER helps Mexican restaurant owners get discovered, collect reviews, manage menus, and grow — all in one platform.'
    : 'Descubre cómo FAMER ayuda a dueños de restaurantes mexicanos a ser encontrados, obtener reseñas, gestionar su menú y crecer — todo en una plataforma.')

@section('content')
<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                        --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.25); padding:5rem 1.5rem 4rem;">
        <div style="max-width:900px; margin:0 auto; text-align:center;">
            <div style="display:inline-block; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); border-radius:999px; padding:0.35rem 1.1rem; font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.1em; text-transform:uppercase; margin-bottom:1.5rem;">
                {{ $isEn ? 'For Restaurant Owners' : 'Para Dueños de Restaurantes' }}
            </div>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2.2rem,5vw,3.5rem); font-weight:800; line-height:1.15; margin:0 0 1.25rem;">
                {{ $isEn ? 'Everything your restaurant needs.' : 'Todo lo que tu restaurante necesita.' }}<br>
                <span style="color:#D4AF37;">{{ $isEn ? 'In one place.' : 'En un solo lugar.' }}</span>
            </h1>
            <p style="font-size:1.125rem; color:#CCCCCC; max-width:680px; margin:0 auto 2.5rem; line-height:1.7;">
                {{ $isEn
                    ? 'FAMER is the only directory built exclusively for Mexican restaurants. We help you get discovered, collect reviews, manage your presence, and grow — without paying commissions.'
                    : 'FAMER es el único directorio construido exclusivamente para restaurantes mexicanos. Te ayudamos a ser encontrado, obtener reseñas, gestionar tu presencia y crecer — sin pagar comisiones.' }}
            </p>
            <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('claim.restaurant') }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:background 0.2s;"
                   onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                    {{ $isEn ? '🚀 Claim My Restaurant — Free' : '🚀 Reclamar Mi Restaurante — Gratis' }}
                </a>
                <a href="#proceso"
                   style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.875rem 2rem; border:1px solid rgba(212,175,55,0.4); color:#D4AF37; font-weight:600; font-size:1rem; border-radius:0.75rem; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='rgba(212,175,55,0.4)'">
                    {{ $isEn ? 'See How It Works ↓' : 'Ver Cómo Funciona ↓' }}
                </a>
            </div>
        </div>

        {{-- Stats bar --}}
        <div style="max-width:700px; margin:3.5rem auto 0; display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; text-align:center;">
            @foreach([
                [$totalRestaurants > 0 ? number_format($totalRestaurants).'+ restaurantes' : '26,000+ restaurantes', '26,000+ restaurants', '🍽️'],
                [number_format($totalStates).' estados cubiertos', number_format($totalStates).' states covered', '🗺️'],
                [number_format($totalCities).'+ ciudades', number_format($totalCities).'+ cities', '📍'],
            ] as [$labelEs, $labelEn, $icon])
            <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:0.75rem; padding:1rem 0.75rem;">
                <div style="font-size:1.5rem; margin-bottom:0.25rem;">{{ $icon }}</div>
                <div style="font-size:0.875rem; color:#D4AF37; font-weight:600;">{{ $isEn ? $labelEn : $labelEs }}</div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PROBLEMA — Por qué necesitas FAMER                         --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#0B0B0B;">
        <div style="max-width:960px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ $isEn ? 'The problem' : 'El problema' }}
                </p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 1rem;">
                    {{ $isEn ? 'Other platforms charge you to reach your own customers.' : 'Las otras plataformas te cobran por llegar a tus propios clientes.' }}
                </h2>
                <p style="color:#CCCCCC; font-size:1.0625rem; max-width:640px; margin:0 auto; line-height:1.7;">
                    {{ $isEn
                        ? 'DoorDash takes 15-30% per order. Yelp charges $300+/month. OpenTable takes $1.50 per reservation. And none of them are built specifically for Mexican cuisine.'
                        : 'DoorDash cobra 15-30% por pedido. Yelp cobra $300+/mes. OpenTable cobra $1.50 por reservación. Y ninguno fue construido específicamente para la cocina mexicana.' }}
                </p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.25rem;">
                @foreach([
                    ['❌ DoorDash / UberEats', '15–30% de comisión por cada pedido. En $10,000 de ventas mensuales, pagas hasta $3,000 en comisiones.', '15–30% commission per order. On $10K monthly sales, you pay up to $3,000 in fees.'],
                    ['❌ Yelp Ads', '$300–500/mes sin garantía de resultados. Pagas por clics, no por clientes reales.', '$300–500/month with no result guarantees. You pay for clicks, not real customers.'],
                    ['❌ OpenTable', '$1.50 por comensal referido + cuota mensual. Tus clientes fieles te cuestan dinero extra.', '$1.50 per referred diner + monthly fee. Your loyal customers cost you extra money.'],
                    ['❌ Google alone', 'Te muestra, pero no gestiona reseñas, menú digital, ni automatiza nada. Solo presencia básica.', 'Shows you, but doesn\'t manage reviews, digital menus, or automate anything. Basic presence only.'],
                ] as [$platform, $descEs, $descEn])
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:1.5rem;">
                    <p style="font-weight:700; color:#F5F5F5; margin:0 0 0.5rem; font-size:0.9375rem;">{{ $platform }}</p>
                    <p style="color:#9CA3AF; font-size:0.875rem; margin:0; line-height:1.6;">{{ $isEn ? $descEn : $descEs }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PROCESO — 5 pasos                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section id="proceso" style="padding:5rem 1.5rem; background:#111111;">
        <div style="max-width:900px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3.5rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ $isEn ? 'How it works' : 'Cómo funciona' }}
                </p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0;">
                    {{ $isEn ? 'From zero to growing in 4 simple steps.' : 'De cero a crecer en 4 pasos simples.' }}
                </h2>
            </div>

            @php
            $steps = [
                [
                    'icon'    => '🔍',
                    'num'     => '01',
                    'title_es'=> 'Encuentra y reclama tu restaurante',
                    'title_en'=> 'Find and claim your restaurant',
                    'desc_es' => 'Tu restaurante ya existe en FAMER. Búscalo por nombre o dirección, haz clic en "Reclamar" y verifica que eres el dueño por teléfono o email. El proceso toma menos de 24 horas.',
                    'desc_en' => 'Your restaurant already exists in FAMER. Search by name or address, click "Claim", and verify you\'re the owner by phone or email. The process takes less than 24 hours.',
                    'detail_es'=> ['Sin costo — el plan básico es gratis para siempre', 'Verificación por llamada o email en < 24 horas', 'Acceso inmediato al panel de dueño', '26,000+ restaurantes ya en el directorio'],
                    'detail_en'=> ['Free — the basic plan is free forever', 'Verification by call or email in < 24 hours', 'Immediate access to owner dashboard', '26,000+ restaurants already in the directory'],
                    'plan'    => 'Gratis / Free',
                ],
                [
                    'icon'    => '✏️',
                    'num'     => '02',
                    'title_es'=> 'Completa y optimiza tu perfil',
                    'title_en'=> 'Complete and optimize your profile',
                    'desc_es' => 'Agrega fotos, menú digital con código QR, horarios, descripción y todo lo que los clientes necesitan para elegirte. FAMER Score mide qué tan completo está tu perfil y te dice exactamente qué mejorar.',
                    'desc_en' => 'Add photos, digital menu with QR code, hours, description, and everything customers need to choose you. FAMER Score measures how complete your profile is and tells you exactly what to improve.',
                    'detail_es'=> ['FAMER Score 0–100: mide tu visibilidad online', 'Fotos de alta calidad — importadas de Google/Yelp', 'Menú digital con categorías y precios', 'Código QR descargable para tu mesa o mostrador', 'Horarios con excepciones por día festivo'],
                    'detail_en'=> ['FAMER Score 0–100: measures your online visibility', 'High-quality photos — imported from Google/Yelp', 'Digital menu with categories and prices', 'Downloadable QR code for your table or counter', 'Hours with holiday exceptions'],
                    'plan'    => 'Premium $29/mo',
                ],
                [
                    'icon'    => '⭐',
                    'num'     => '03',
                    'title_es'=> 'Automatiza la obtención de reseñas',
                    'title_en'=> 'Automate review collection',
                    'desc_es' => 'FAMER envía SMS automáticos a tus clientes 1–4 horas después de su visita, invitándolos a dejar una reseña. Sin que tú hagas nada. Cada reseña nueva mejora tu posición en los rankings.',
                    'desc_en' => 'FAMER automatically sends SMS to your customers 1–4 hours after their visit, inviting them to leave a review. Without you doing anything. Each new review improves your ranking position.',
                    'detail_es'=> ['SMS automáticos post-visita (sin intervención manual)', 'Deduplicación: máximo 1 mensaje cada 7 días por cliente', 'Reseñas en FAMER + redirección a Google', 'Más reseñas = mejor posición en rankings de ciudad', 'Panel de gestión de reseñas — responder en 1 clic'],
                    'detail_en'=> ['Automatic post-visit SMS (no manual intervention)', 'Deduplication: max 1 message per customer every 7 days', 'FAMER reviews + redirect to Google', 'More reviews = better city ranking position', 'Review management panel — respond in 1 click'],
                    'plan'    => 'Premium $29/mo',
                ],
                [
                    'icon'    => '📈',
                    'num'     => '04',
                    'title_es'=> 'Aparece en rankings y genera tráfico',
                    'title_en'=> 'Appear in rankings and generate traffic',
                    'desc_es' => 'FAMER calcula rankings semanales: Top 10 por ciudad, Top 10 por estado y Top 100 nacional. Aparecer en el Top 10 de tu ciudad te pone frente a miles de personas buscando exactamente lo que ofreces.',
                    'desc_en' => 'FAMER calculates weekly rankings: Top 10 by city, Top 10 by state, and Top 100 national. Appearing in your city\'s Top 10 puts you in front of thousands of people searching for exactly what you offer.',
                    'detail_es'=> ['Rankings semanales: ciudad, estado y nacional', 'SEO en 3 dominios: .com.mx (MX) + .com (US ES) + .com (US EN)', 'Landing pages de ciudad y estado ya posicionándose', 'Badge "Top 10 en [ciudad]" en tu perfil', 'Hreflang en 3 idiomas para máxima visibilidad'],
                    'detail_en'=> ['Weekly rankings: city, state and national', 'SEO across 3 domains: .com.mx (MX) + .com (US ES) + .com (EN)', 'City and state landing pages already ranking', '"Top 10 in [city]" badge on your profile', 'Hreflang in 3 languages for maximum visibility'],
                    'plan'    => 'Elite $79/mo',
                ],
            ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:0;">
                @foreach($steps as $i => $step)
                <div style="display:grid; grid-template-columns:auto 1fr; gap:0; align-items:stretch; {{ $loop->last ? '' : 'margin-bottom:0;' }}">
                    {{-- Timeline --}}
                    <div style="display:flex; flex-direction:column; align-items:center; padding:0 2rem 0 0.5rem;">
                        <div style="width:52px; height:52px; border-radius:50%; background:#D4AF37; color:#0B0B0B; display:flex; align-items:center; justify-content:center; font-size:1.25rem; font-weight:900; flex-shrink:0; z-index:1;">
                            {{ $step['num'] }}
                        </div>
                        @if(!$loop->last)
                        <div style="width:2px; background:rgba(212,175,55,0.2); flex:1; margin:0.25rem 0;"></div>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div style="padding-bottom:{{ $loop->last ? '0' : '3rem' }};">
                        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.75rem 2rem; margin-bottom:0;">
                            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                                <span style="font-size:1.5rem;">{{ $step['icon'] }}</span>
                                <h3 style="font-family:'Playfair Display',serif; font-size:1.3125rem; font-weight:700; margin:0; color:#F5F5F5;">
                                    {{ $isEn ? $step['title_en'] : $step['title_es'] }}
                                </h3>
                                <span style="margin-left:auto; font-size:0.7rem; font-weight:700; padding:0.2rem 0.6rem; border-radius:999px; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.3); color:#D4AF37; white-space:nowrap;">
                                    {{ $step['plan'] }}
                                </span>
                            </div>
                            <p style="color:#CCCCCC; font-size:0.9375rem; line-height:1.7; margin:0 0 1.25rem;">
                                {{ $isEn ? $step['desc_en'] : $step['desc_es'] }}
                            </p>
                            <ul style="list-style:none; padding:0; margin:0; display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:0.5rem;">
                                @foreach($isEn ? $step['detail_en'] : $step['detail_es'] as $item)
                                <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.8125rem; color:#D4AF37;">
                                    <span style="margin-top:0.1rem; flex-shrink:0;">✓</span>
                                    <span style="color:#CCCCCC;">{{ $item }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FUNCIONES ÚNICAS — Lo que nadie más tiene                  --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#0B0B0B;">
        <div style="max-width:1024px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3.5rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ $isEn ? 'Only on FAMER' : 'Solo en FAMER' }}
                </p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 1rem;">
                    {{ $isEn ? 'Features built specifically for Mexican restaurants.' : 'Funciones construidas específicamente para restaurantes mexicanos.' }}
                </h2>
                <p style="color:#CCCCCC; font-size:1rem; max-width:600px; margin:0 auto;">
                    {{ $isEn
                        ? 'No generic tools. Everything in FAMER was designed with Mexican cuisine and culture in mind.'
                        : 'Sin herramientas genéricas. Todo en FAMER fue diseñado pensando en la cocina y cultura mexicana.' }}
                </p>
            </div>

            @php
            $features = [
                [
                    'icon'   => '🏆',
                    'title_es'=> 'Rankings Semanales',
                    'title_en'=> 'Weekly Rankings',
                    'desc_es' => 'Top 10 por ciudad, Top 10 por estado y Top 100 nacional. Calculados cada semana con base en calificaciones, reseñas y completitud del perfil. Un incentivo real para mejorar constantemente.',
                    'desc_en' => 'Top 10 by city, Top 10 by state and Top 100 national. Calculated weekly based on ratings, reviews and profile completeness. A real incentive to constantly improve.',
                    'unique'  => true,
                ],
                [
                    'icon'   => '📊',
                    'title_es'=> 'FAMER Score™',
                    'title_en'=> 'FAMER Score™',
                    'desc_es' => 'Un score de 0 a 100 que mide qué tan completo y visible estás online. Te dice exactamente qué mejorar: fotos, horarios, menú, reseñas. Es tu GPS para crecer.',
                    'desc_en' => 'A 0–100 score measuring how complete and visible you are online. Tells you exactly what to improve: photos, hours, menu, reviews. It\'s your GPS for growth.',
                    'unique'  => true,
                ],
                [
                    'icon'   => '💬',
                    'title_es'=> 'SMS Automáticos Post-Visita',
                    'title_en'=> 'Automatic Post-Visit SMS',
                    'desc_es' => 'FAMER envía mensajes de texto a tus clientes 1–4 horas después de su visita pidiendo una reseña. Completamente automatizado. Sin costo extra por mensaje.',
                    'desc_en' => 'FAMER sends text messages to your customers 1–4 hours after their visit asking for a review. Fully automated. No extra cost per message.',
                    'unique'  => true,
                ],
                [
                    'icon'   => '🌐',
                    'title_es'=> 'Presencia en 3 Dominios',
                    'title_en'=> 'Presence Across 3 Domains',
                    'desc_es' => 'Tu restaurante aparece en .com.mx (México), .com (USA en español) y famousmexicanrestaurants.com (USA en inglés). Tres audiencias, una sola configuración.',
                    'desc_en' => 'Your restaurant appears on .com.mx (Mexico), .com (US in Spanish) and famousmexicanrestaurants.com (US in English). Three audiences, one setup.',
                    'unique'  => true,
                ],
                [
                    'icon'   => '📋',
                    'title_es'=> 'Menú Digital + QR Code',
                    'title_en'=> 'Digital Menu + QR Code',
                    'desc_es' => 'Sube tu menú con fotos, precios y categorías. Genera un código QR para imprimir. Los clientes lo escanean desde su mesa. Actualizable en segundos.',
                    'desc_en' => 'Upload your menu with photos, prices and categories. Generate a QR code to print. Customers scan it from their table. Updatable in seconds.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '🛡️',
                    'title_es'=> 'Badge "Verificado por FAMER"',
                    'title_en'=> '"Verified by FAMER" Badge',
                    'desc_es' => 'Un distintivo visual en tu perfil que indica que el dueño verificó y gestiona activamente la información. Genera confianza y diferencia tu perfil de los no reclamados.',
                    'desc_en' => 'A visual badge on your profile showing the owner verified and actively manages the information. Builds trust and differentiates your profile from unclaimed ones.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '📍',
                    'title_es'=> 'Landing Pages de Ciudad y Estado',
                    'title_en'=> 'City and State Landing Pages',
                    'desc_es' => 'Cada ciudad y estado tiene su propia página SEO en FAMER. "Mejores restaurantes mexicanos en Dallas TX" ya posicionándose en Google — y tu restaurante puede aparecer ahí.',
                    'desc_en' => 'Every city and state has its own SEO page on FAMER. "Best Mexican restaurants in Dallas TX" already ranking on Google — and your restaurant can appear there.',
                    'unique'  => true,
                ],
                [
                    'icon'   => '📧',
                    'title_es'=> 'Email Marketing para Dueños',
                    'title_en'=> 'Email Marketing for Owners',
                    'desc_es' => 'Crea campañas de email para tus clientes frecuentes. Promociones, eventos especiales, nuevos platillos. Sin necesidad de herramientas externas ni conocimientos técnicos.',
                    'desc_en' => 'Create email campaigns for your frequent customers. Promotions, special events, new dishes. No external tools or technical knowledge required.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '🎯',
                    'title_es'=> 'Panel de Analytics',
                    'title_en'=> 'Analytics Dashboard',
                    'desc_es' => 'Ve cuántas personas vieron tu perfil, hicieron clic para llamar, buscaron direcciones o visitaron tu menú. Datos reales para tomar decisiones reales.',
                    'desc_en' => 'See how many people viewed your profile, clicked to call, looked up directions, or visited your menu. Real data to make real decisions.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '🔔',
                    'title_es'=> 'Gestión de Reseñas en 1 Clic',
                    'title_en'=> '1-Click Review Management',
                    'desc_es' => 'Recibe notificaciones de nuevas reseñas y responde directamente desde tu panel FAMER. Nunca más pierdas una reseña negativa sin responder.',
                    'desc_en' => 'Receive new review notifications and respond directly from your FAMER dashboard. Never miss a negative review without a response again.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '🖥️',
                    'title_es'=> 'Website Propio (Elite)',
                    'title_en'=> 'Your Own Website (Elite)',
                    'desc_es' => 'Con Elite obtienes un website completo para tu restaurante, conectado a tu perfil FAMER. Incluye menú, fotos, reservaciones y formulario de contacto.',
                    'desc_en' => 'With Elite you get a full website for your restaurant, connected to your FAMER profile. Includes menu, photos, reservations and contact form.',
                    'unique'  => false,
                ],
                [
                    'icon'   => '🏅',
                    'title_es'=> 'FAMER Awards',
                    'title_en'=> 'FAMER Awards',
                    'desc_es' => 'Reconocimientos anuales al mejor restaurante por ciudad, estado y nivel nacional. Un award FAMER en tu perfil y en tu restaurante es marketing gratuito de alto valor.',
                    'desc_en' => 'Annual recognition for the best restaurant by city, state and national level. A FAMER award on your profile and restaurant is high-value free marketing.',
                    'unique'  => true,
                ],
            ];
            @endphp

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:1.25rem;">
                @foreach($features as $feature)
                <div style="background:#1A1A1A; border:1px solid {{ $feature['unique'] ? 'rgba(212,175,55,0.35)' : '#2A2A2A' }}; border-radius:14px; padding:1.5rem; position:relative; transition:border-color 0.2s;"
                     onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='{{ $feature['unique'] ? 'rgba(212,175,55,0.35)' : '#2A2A2A' }}'">
                    @if($feature['unique'])
                    <div style="position:absolute; top:-0.65rem; right:1rem; background:#D4AF37; color:#0B0B0B; font-size:0.65rem; font-weight:800; padding:0.15rem 0.6rem; border-radius:999px; letter-spacing:0.05em; text-transform:uppercase;">
                        {{ $isEn ? 'Exclusive' : 'Exclusivo' }}
                    </div>
                    @endif
                    <div style="font-size:2rem; margin-bottom:0.75rem;">{{ $feature['icon'] }}</div>
                    <h3 style="font-size:1rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">
                        {{ $isEn ? $feature['title_en'] : $feature['title_es'] }}
                    </h3>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0; line-height:1.65;">
                        {{ $isEn ? $feature['desc_en'] : $feature['desc_es'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- COMPARACIÓN vs competencia                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#111111;">
        <div style="max-width:900px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ $isEn ? 'The comparison' : 'La comparación' }}
                </p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0;">
                    {{ $isEn ? 'FAMER vs the rest' : 'FAMER vs los demás' }}
                </h2>
            </div>

            <div style="overflow-x:auto; -webkit-overflow-scrolling:touch;">
                <table style="width:100%; border-collapse:collapse; min-width:600px;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:0.875rem 1rem; font-size:0.8125rem; color:#9CA3AF; font-weight:500; border-bottom:1px solid #2A2A2A;">
                                {{ $isEn ? 'Feature' : 'Función' }}
                            </th>
                            @foreach(['FAMER', 'Yelp', 'DoorDash', 'OpenTable', 'Google'] as $col)
                            <th style="text-align:center; padding:0.875rem 0.75rem; font-size:0.8125rem; font-weight:700; border-bottom:1px solid #2A2A2A; {{ $col === 'FAMER' ? 'color:#D4AF37; background:rgba(212,175,55,0.05);' : 'color:#9CA3AF;' }}">
                                {{ $col }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $rows = $isEn ? [
                            ['Listing in directory',        '✅','✅','✅','✅','✅'],
                            ['Verified reviews',            '✅','✅','❌','✅','✅'],
                            ['Digital menu + QR',           '✅','❌','✅','❌','❌'],
                            ['Automatic review requests',   '✅','❌','❌','❌','❌'],
                            ['Weekly city/state rankings',  '✅','❌','❌','❌','❌'],
                            ['FAMER Score™',                '✅','❌','❌','❌','❌'],
                            ['3 domains (ES/EN/MX)',        '✅','❌','❌','❌','❌'],
                            ['Email & SMS marketing',       '✅','❌','❌','❌','❌'],
                            ['No per-order commission',     '✅','✅','❌','✅','✅'],
                            ['Built for Mexican cuisine',   '✅','❌','❌','❌','❌'],
                            ['Monthly cost',                'Free / $29 / $79','$300+','15–30%','$249+','Free'],
                        ] : [
                            ['Listado en directorio',        '✅','✅','✅','✅','✅'],
                            ['Reseñas verificadas',          '✅','✅','❌','✅','✅'],
                            ['Menú digital + QR',            '✅','❌','✅','❌','❌'],
                            ['Solicitudes automáticas de reseña','✅','❌','❌','❌','❌'],
                            ['Rankings ciudad/estado semanales','✅','❌','❌','❌','❌'],
                            ['FAMER Score™',                 '✅','❌','❌','❌','❌'],
                            ['3 dominios (ES/EN/MX)',        '✅','❌','❌','❌','❌'],
                            ['Email y SMS marketing',        '✅','❌','❌','❌','❌'],
                            ['Sin comisión por pedido',      '✅','✅','❌','✅','✅'],
                            ['Construido para cocina mexicana','✅','❌','❌','❌','❌'],
                            ['Costo mensual',                'Gratis / $29 / $79','$300+','15–30%','$249+','Gratis'],
                        ];
                        @endphp
                        @foreach($rows as $ri => $row)
                        <tr style="border-bottom:1px solid #1E1E1E; {{ $loop->last ? 'border-bottom:2px solid rgba(212,175,55,0.2);' : '' }}">
                            <td style="padding:0.75rem 1rem; font-size:0.875rem; color:#CCCCCC; {{ $loop->last ? 'font-weight:600; color:#F5F5F5;' : '' }}">
                                {{ $row[0] }}
                            </td>
                            @foreach(array_slice($row, 1) as $ci => $val)
                            <td style="text-align:center; padding:0.75rem 0.75rem; font-size:{{ in_array($val, ['✅','❌']) ? '1rem' : '0.8rem' }}; {{ $ci === 0 ? 'background:rgba(212,175,55,0.04); font-weight:700; color:'.($loop->last ? '#D4AF37' : ($val === '✅' ? '#4ADE80' : '#F87171')).';' : 'color:'.($val === '✅' ? '#4ADE80' : ($val === '❌' ? '#F87171' : '#9CA3AF')).';' }}">
                                {{ $val }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PRECIOS                                                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#0B0B0B;">
        <div style="max-width:900px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3rem;">
                <p style="font-size:0.75rem; font-weight:600; color:#D4AF37; letter-spacing:0.12em; text-transform:uppercase; margin-bottom:0.75rem;">
                    {{ $isEn ? 'Pricing' : 'Precios' }}
                </p>
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 0.75rem;">
                    {{ $isEn ? 'Start free. Grow when you\'re ready.' : 'Empieza gratis. Crece cuando estés listo.' }}
                </h2>
                <p style="color:#9CA3AF; font-size:0.9375rem;">
                    {{ $isEn ? 'No contracts. Cancel anytime.' : 'Sin contratos. Cancela cuando quieras.' }}
                </p>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1.25rem; align-items:start;">

                {{-- FREE --}}
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:2rem;">
                    <p style="font-size:0.75rem; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 0.5rem;">
                        {{ $isEn ? 'Basic' : 'Básico' }}
                    </p>
                    <div style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:800; color:#F5F5F5; margin:0 0 0.25rem;">
                        {{ $isEn ? 'Free' : 'Gratis' }}
                        <span style="font-size:0.875rem; font-weight:400; color:#9CA3AF; font-family:Poppins,sans-serif;">
                            {{ $isEn ? 'forever' : 'para siempre' }}
                        </span>
                    </div>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.5rem;">
                        {{ $isEn ? 'Claim and manage your basic profile.' : 'Reclama y gestiona tu perfil básico.' }}
                    </p>
                    <ul style="list-style:none; padding:0; margin:0 0 1.75rem; display:flex; flex-direction:column; gap:0.6rem;">
                        @foreach($isEn
                            ? ['Directory listing','Basic info (name, address, phone)','Google Maps integration','Claim & verify ownership','Edit basic information','Customer reviews','Hours and contact','Up to 5 photos']
                            : ['Listado en directorio','Info básica (nombre, dirección, teléfono)','Integración con Google Maps','Verificar propiedad','Editar información básica','Reseñas de clientes','Horarios y contacto','Hasta 5 fotos']
                        as $item)
                        <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#CCCCCC;">
                            <span style="color:#D4AF37; flex-shrink:0;">✓</span> {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('claim.restaurant') }}" style="display:block; text-align:center; padding:0.75rem; border:1px solid rgba(255,255,255,0.15); border-radius:0.75rem; color:#F5F5F5; font-weight:600; font-size:0.9375rem; text-decoration:none; transition:background 0.2s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                        {{ $isEn ? 'Start free →' : 'Empezar gratis →' }}
                    </a>
                </div>

                {{-- PREMIUM --}}
                <div style="background:#1A1A1A; border:2px solid #D4AF37; border-radius:16px; padding:2rem; position:relative; box-shadow:0 0 40px rgba(212,175,55,0.1);">
                    <div style="position:absolute; top:-0.75rem; left:50%; transform:translateX(-50%); background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:800; padding:0.2rem 0.9rem; border-radius:999px; text-transform:uppercase; letter-spacing:0.08em; white-space:nowrap;">
                        {{ $isEn ? '⭐ Most Popular' : '⭐ Más Popular' }}
                    </div>
                    <p style="font-size:0.75rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.1em; margin:0.5rem 0 0.5rem;">Premium</p>
                    <div style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:800; color:#D4AF37; margin:0 0 0.25rem;">
                        $29<span style="font-size:0.875rem; font-weight:400; color:#9CA3AF; font-family:Poppins,sans-serif;">/{{ $isEn ? 'mo' : 'mes' }}</span>
                    </div>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.5rem;">
                        {{ $isEn ? 'Everything in Basic, plus:' : 'Todo en Básico, más:' }}
                    </p>
                    <ul style="list-style:none; padding:0; margin:0 0 1.75rem; display:flex; flex-direction:column; gap:0.6rem;">
                        @foreach($isEn
                            ? ['Verified "Premium" badge','Digital menu + QR code','Automatic review request SMS','FAMER Score & recommendations','Unlimited photos','Profile analytics','Highlighted in rankings','Review management panel']
                            : ['Badge Verificado "Premium"','Menú digital + código QR','SMS automáticos de solicitud de reseña','FAMER Score y recomendaciones','Fotos ilimitadas','Analytics del perfil','Destacado en rankings','Panel de gestión de reseñas']
                        as $item)
                        <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#CCCCCC;">
                            <span style="color:#D4AF37; flex-shrink:0;">✓</span> {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('claim.restaurant') }}?plan=premium" style="display:block; text-align:center; padding:0.75rem; background:#D4AF37; border-radius:0.75rem; color:#0B0B0B; font-weight:700; font-size:0.9375rem; text-decoration:none; transition:background 0.2s;"
                       onmouseover="this.style.background='#B8962E'" onmouseout="this.style.background='#D4AF37'">
                        {{ $isEn ? 'Start Premium →' : 'Empezar Premium →' }}
                    </a>
                </div>

                {{-- ELITE --}}
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:2rem;">
                    <p style="font-size:0.75rem; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.1em; margin:0 0 0.5rem;">Elite</p>
                    <div style="font-family:'Playfair Display',serif; font-size:2.25rem; font-weight:800; color:#F5F5F5; margin:0 0 0.25rem;">
                        $79<span style="font-size:0.875rem; font-weight:400; color:#9CA3AF; font-family:Poppins,sans-serif;">/{{ $isEn ? 'mo' : 'mes' }}</span>
                    </div>
                    <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.5rem;">
                        {{ $isEn ? 'Everything in Premium, plus:' : 'Todo en Premium, más:' }}
                    </p>
                    <ul style="list-style:none; padding:0; margin:0 0 1.75rem; display:flex; flex-direction:column; gap:0.6rem;">
                        @foreach($isEn
                            ? ['#1 position in city rankings','Advanced analytics + reports','Email marketing campaigns','Your own restaurant website','FAMER Awards eligibility','Priority support','Reservations system']
                            : ['Posición #1 en rankings de ciudad','Analytics avanzados + reportes','Campañas de email marketing','Website propio del restaurante','Elegible para FAMER Awards','Soporte prioritario','Sistema de reservaciones']
                        as $item)
                        <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#CCCCCC;">
                            <span style="color:#D4AF37; flex-shrink:0;">✓</span> {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('claim.restaurant') }}?plan=elite" style="display:block; text-align:center; padding:0.75rem; border:1px solid rgba(255,255,255,0.15); border-radius:0.75rem; color:#F5F5F5; font-weight:600; font-size:0.9375rem; text-decoration:none; transition:background 0.2s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                        {{ $isEn ? 'Start Elite →' : 'Empezar Elite →' }}
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- FAQ                                                         --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:#111111;">
        <div style="max-width:740px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:3rem;">
                <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.25rem); font-weight:700; margin:0;">
                    {{ $isEn ? 'Frequently Asked Questions' : 'Preguntas Frecuentes' }}
                </h2>
            </div>

            @php
            $faqs = $isEn ? [
                ['q' => 'My restaurant isn\'t on FAMER yet. Can I still sign up?', 'a' => 'Absolutely. You can suggest your restaurant and we\'ll add it within 24–48 hours. Then you claim it and start managing it for free.'],
                ['q' => 'Do I need technical knowledge to manage my profile?', 'a' => 'None. Everything is designed for restaurant owners, not developers. If you can use WhatsApp, you can use FAMER.'],
                ['q' => 'How exactly does the automatic review SMS work?', 'a' => 'When a customer visits your restaurant, FAMER sends them a text message 1–4 hours after their visit inviting them to leave a review. You provide us the customer\'s number at the time of service (via our app or integration). We take care of the rest.'],
                ['q' => 'Does FAMER replace my Google Business Profile?', 'a' => 'No — FAMER complements Google. You keep your Google profile active, and FAMER adds an additional channel with more features specific to Mexican cuisine. More visibility = more customers.'],
                ['q' => 'Can I cancel my subscription at any time?', 'a' => 'Yes. No contracts, no penalties. You can cancel from your panel and the plan reverts to free at the end of the billing period.'],
            ] : [
                ['q' => 'Mi restaurante no está en FAMER todavía. ¿Puedo registrarme?', 'a' => 'Claro que sí. Puedes sugerir tu restaurante y lo agregamos en 24–48 horas. Luego lo reclamas y empiezas a gestionarlo gratis.'],
                ['q' => '¿Necesito conocimientos técnicos para gestionar mi perfil?', 'a' => 'Ninguno. Todo está diseñado para dueños de restaurantes, no para desarrolladores. Si sabes usar WhatsApp, sabes usar FAMER.'],
                ['q' => '¿Cómo funciona exactamente el SMS automático de reseñas?', 'a' => 'Cuando un cliente visita tu restaurante, FAMER le envía un mensaje de texto 1–4 horas después de su visita invitándolo a dejar una reseña. Tú nos proporcionas el número del cliente al momento del servicio (vía nuestra app o integración). Nosotros nos encargamos del resto.'],
                ['q' => '¿FAMER reemplaza mi Google Business Profile?', 'a' => 'No — FAMER complementa a Google. Mantienes tu perfil de Google activo, y FAMER agrega un canal adicional con más funciones específicas para la cocina mexicana. Más visibilidad = más clientes.'],
                ['q' => '¿Puedo cancelar mi suscripción en cualquier momento?', 'a' => 'Sí. Sin contratos, sin penalizaciones. Puedes cancelar desde tu panel y el plan regresa a gratuito al final del período de facturación.'],
            ];
            @endphp

            <div style="display:flex; flex-direction:column; gap:0.75rem;" x-data="{open:null}">
                @foreach($faqs as $fi => $faq)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden;">
                    <button onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'; this.querySelector('span:last-child').textContent=this.nextElementSibling.style.display==='block'?'−':'+';"
                            style="width:100%; text-align:left; padding:1.125rem 1.25rem; background:transparent; border:none; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                        <span style="font-size:0.9375rem; font-weight:600; color:#F5F5F5;">{{ $faq['q'] }}</span>
                        <span style="color:#D4AF37; font-size:1.25rem; font-weight:700; flex-shrink:0; line-height:1;">+</span>
                    </button>
                    <div style="display:none; padding:0 1.25rem 1.25rem; font-size:0.875rem; color:#9CA3AF; line-height:1.7;">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- CTA FINAL                                                   --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <section style="padding:5rem 1.5rem; background:linear-gradient(135deg,#1A1A1A 0%,#0B0B0B 50%,#1A1A1A 100%); border-top:1px solid rgba(212,175,55,0.2); text-align:center;">
        <div style="max-width:680px; margin:0 auto;">
            <div style="font-size:3rem; margin-bottom:1rem;">🌮</div>
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,4vw,2.5rem); font-weight:700; margin:0 0 1.25rem;">
                {{ $isEn ? 'Your restaurant deserves to be found.' : 'Tu restaurante merece ser encontrado.' }}
            </h2>
            <p style="color:#CCCCCC; font-size:1.0625rem; line-height:1.7; margin:0 0 2.5rem;">
                {{ $isEn
                    ? 'Join 26,000+ Mexican restaurants already on FAMER. Start free — no credit card required.'
                    : 'Únete a 26,000+ restaurantes mexicanos que ya están en FAMER. Empieza gratis — sin tarjeta de crédito.' }}
            </p>
            <a href="{{ route('claim.restaurant') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:1rem 2.5rem; background:#D4AF37; color:#0B0B0B; font-weight:800; font-size:1.0625rem; border-radius:0.875rem; text-decoration:none; transition:background 0.2s, transform 0.15s; box-shadow:0 4px 24px rgba(212,175,55,0.3);"
               onmouseover="this.style.background='#B8962E';this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='#D4AF37';this.style.transform='translateY(0)'">
                {{ $isEn ? '🚀 Claim My Restaurant Now' : '🚀 Reclamar Mi Restaurante Ahora' }}
            </a>
            <p style="margin-top:1rem; font-size:0.8125rem; color:#6B7280;">
                {{ $isEn ? 'Already claimed? ' : '¿Ya lo reclamaste? ' }}
                <a href="/owner" style="color:#D4AF37; text-decoration:none;">{{ $isEn ? 'Go to your dashboard →' : 'Ir a tu panel →' }}</a>
            </p>
        </div>
    </section>

</div>

{{-- Schema.org --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "{{ $isEn ? 'How FAMER Works' : 'Cómo Funciona FAMER' }}",
    "description": "{{ $isEn ? 'How FAMER helps Mexican restaurant owners grow their business online.' : 'Cómo FAMER ayuda a los dueños de restaurantes mexicanos a crecer su negocio en línea.' }}",
    "url": "{{ request()->url() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "FAMER",
        "url": "https://restaurantesmexicanosfamosos.com.mx"
    }
}
</script>
@endsection
