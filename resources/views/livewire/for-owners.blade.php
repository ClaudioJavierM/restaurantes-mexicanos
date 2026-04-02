<div class="min-h-screen" style="background-color: #0B0B0B;">

{{-- ============================================ --}}
{{-- 1. HERO SECTION --}}
{{-- ============================================ --}}
<section class="relative overflow-hidden" style="background-color: #0B0B0B; min-height: 70vh;">
    {{-- Restaurant background photo --}}
    @php
        $heroRestaurant = \App\Models\Restaurant::approved()
            ->whereNotNull('image')->where('image','!=','')
            ->orderByDesc('total_reviews')->first(['image','name']);
        $heroBg = $heroRestaurant ? \Illuminate\Support\Facades\Storage::url($heroRestaurant->image) : null;
    @endphp
    @if($heroBg)
    <div class="absolute inset-0" style="background-image:url('{{ $heroBg }}'); background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
    @endif
    {{-- Dark overlay so text is readable --}}
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(11,11,11,0.92) 0%, rgba(11,11,11,0.75) 50%, rgba(11,11,11,0.88) 100%);"></div>
    {{-- Subtle gold glow --}}
    <div class="absolute inset-0" style="background: radial-gradient(ellipse 60% 50% at 50% 60%, rgba(212,175,55,0.06) 0%, transparent 70%);"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 md:pt-32 md:pb-24 relative z-10">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold leading-tight mb-6" style="font-family: 'Playfair Display', Georgia, serif; color: #F5F5F5;">
                {{ app()->getLocale() === 'en' ? 'Your Customers Are Already Searching for You' : 'Tus Clientes Ya Te Estan Buscando' }}
            </h1>

            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-10" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en'
                    ? 'Make sure they find your restaurant first on the leading platform for Mexican dining in the U.S.'
                    : 'Asegurate de que encuentren tu restaurante primero en la plataforma lider de comida mexicana en EE.UU.' }}
            </p>

            {{-- CTAs --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center px-8 py-4 text-lg font-bold rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg" style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 4px 24px rgba(212,175,55,0.25);">
                    {{ app()->getLocale() === 'en' ? 'Claim Your Restaurant' : 'Reclama Tu Restaurante' }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="{{ app()->getLocale() === 'en' ? '/how-famer-works' : '/como-funciona-famer' }}" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-xl transition-all duration-300 hover:bg-white/5" style="border: 1px solid #D4AF37; color: #D4AF37;">
                    {{ app()->getLocale() === 'en' ? 'See How It Works' : 'Cómo Funciona' }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            {{-- Stats Bar --}}
            <div class="grid grid-cols-3 gap-6 md:gap-12 max-w-3xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_restaurants'] ?? 13000) }}+</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_views'] ?? 150000) }}+</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Monthly Views' : 'Visitas/Mes' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">50</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'States' : 'Estados' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-24" style="background: linear-gradient(to top, #1A1A1A, transparent);"></div>
</section>

{{-- ============================================ --}}
{{-- 2. PROBLEM → SOLUTION --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-20">
            {{-- Pain Points --}}
            <div>
                <h3 class="text-sm font-bold tracking-widest uppercase mb-8" style="color: #8B1E1E;">
                    {{ app()->getLocale() === 'en' ? 'The Problem' : 'El Problema' }}
                </h3>
                <div class="space-y-6">
                    @php
                        $problems = app()->getLocale() === 'en'
                            ? ['Low visibility online', 'Too much competition', 'Dependence on third-party apps', 'Lack of direct traffic']
                            : ['Baja visibilidad en internet', 'Demasiada competencia', 'Dependencia de apps de terceros', 'Falta de trafico directo'];
                    @endphp
                    @foreach($problems as $problem)
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mt-0.5" style="background-color: rgba(139,30,30,0.15);">
                            <svg class="w-4 h-4" style="color: #8B1E1E;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <p class="text-lg" style="color: #CCCCCC;">{{ $problem }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Solutions --}}
            <div>
                <h3 class="text-sm font-bold tracking-widest uppercase mb-8" style="color: #D4AF37;">
                    {{ app()->getLocale() === 'en' ? 'The Solution' : 'La Solucion' }}
                </h3>
                <div class="space-y-6">
                    @php
                        $solutions = app()->getLocale() === 'en'
                            ? ['Get discovered by local customers', 'Rank in top city lists', 'Showcase your brand properly', 'Drive direct traffic']
                            : ['Se descubierto por clientes locales', 'Aparece en los rankings de tu ciudad', 'Muestra tu marca como merece', 'Genera trafico directo'];
                    @endphp
                    @foreach($solutions as $solution)
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mt-0.5" style="background-color: rgba(212,175,55,0.15);">
                            <svg class="w-4 h-4" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-lg" style="color: #F5F5F5;">{{ $solution }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 3. BENEFITS GRID --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'Grow Your Restaurant with FAMER' : 'Haz Crecer Tu Restaurante con FAMER' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'Everything you need to attract more customers' : 'Todo lo que necesitas para atraer mas clientes' }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- Card 1: Get Discovered --}}
            <div class="rounded-2xl p-8 transition-all duration-300 hover:-translate-y-1" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Get Discovered' : 'Se Encontrado' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Appear in search results and city rankings when people search for Mexican restaurants near them.' : 'Aparece en resultados de busqueda y rankings cuando buscan restaurantes mexicanos cerca.' }}</p>
            </div>

            {{-- Card 2: Increase Visibility --}}
            <div class="rounded-2xl p-8 transition-all duration-300 hover:-translate-y-1" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Increase Visibility' : 'Aumenta Tu Visibilidad' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Featured placements and verified badges that make your restaurant stand out from the rest.' : 'Ubicaciones destacadas e insignias verificadas que hacen que tu restaurante destaque.' }}</p>
            </div>

            {{-- Card 3: Showcase Your Brand --}}
            <div class="rounded-2xl p-8 transition-all duration-300 hover:-translate-y-1" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Showcase Your Brand' : 'Muestra Tu Marca' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Upload menus, photos, and promotions. Give customers a reason to choose you.' : 'Sube menus, fotos y promociones. Dale a los clientes una razon para elegirte.' }}</p>
            </div>

            {{-- Card 4: Convert Visitors --}}
            <div class="rounded-2xl p-8 transition-all duration-300 hover:-translate-y-1" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Convert Visitors into Customers' : 'Convierte Visitantes en Clientes' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Direct calls, directions, and reservations. Turn online traffic into real foot traffic.' : 'Llamadas directas, direcciones y reservaciones. Convierte trafico online en clientes reales.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 3.3 COMPARISON TABLE --}}
{{-- ============================================ --}}
<section class="py-20" style="background-color: #1A1A1A;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <div class="inline-block text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-full mb-4" style="background:rgba(212,175,55,0.12); color:#D4AF37;">
                {{ app()->getLocale() === 'en' ? 'Stop Paying for 4 Platforms' : 'Deja de Pagar 4 Plataformas' }}
            </div>
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color:#F5F5F5; font-family:'Playfair Display',Georgia,serif;">
                {{ app()->getLocale() === 'en' ? 'Everything in One Place' : 'Todo en Un Solo Lugar' }}
            </h2>
            <p class="text-lg max-w-2xl mx-auto" style="color:#CCCCCC;">
                {{ app()->getLocale() === 'en'
                    ? 'Yelp + Owner.com + OpenTable + DoorDash = over $800/month. FAMER gives you all of that for $39/month, specialized in Mexican restaurants.'
                    : 'Yelp + Owner.com + OpenTable + DoorDash = más de $800/mes. FAMER te da todo eso por $39/mes, especializado en restaurantes mexicanos.' }}
            </p>
        </div>

        {{-- Mobile: cost summary cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-10 md:hidden">
            @foreach([['Yelp', '$300-500/mo'], ['Owner.com', '$199/mo'], ['OpenTable', '$249/mo'], ['DoorDash', '15-30%']] as [$name, $price])
            <div class="rounded-xl p-4 text-center" style="background:#2A2A2A; border:1px solid #3A3A3A;">
                <div class="text-sm font-semibold mb-1" style="color:#9CA3AF;">{{ $name }}</div>
                <div class="text-base font-bold" style="color:#8B1E1E;">{{ $price }}</div>
            </div>
            @endforeach
        </div>

        {{-- Desktop comparison table --}}
        <div class="overflow-x-auto rounded-2xl hidden md:block" style="border:1px solid #2A2A2A;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#0B0B0B;">
                        <th style="padding:1rem 1.25rem; text-align:left; color:#9CA3AF; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; width:30%;">
                            {{ app()->getLocale() === 'en' ? 'Feature' : 'Característica' }}
                        </th>
                        <th style="padding:1rem; text-align:center; color:#9CA3AF; font-size:0.8rem; font-weight:600;">Yelp<br><span style="color:#8B1E1E; font-size:0.75rem;">$300-500/mo</span></th>
                        <th style="padding:1rem; text-align:center; color:#9CA3AF; font-size:0.8rem; font-weight:600;">Owner.com<br><span style="color:#8B1E1E; font-size:0.75rem;">$199/mo</span></th>
                        <th style="padding:1rem; text-align:center; color:#9CA3AF; font-size:0.8rem; font-weight:600;">OpenTable<br><span style="color:#8B1E1E; font-size:0.75rem;">$249/mo</span></th>
                        <th style="padding:1rem; text-align:center; color:#9CA3AF; font-size:0.8rem; font-weight:600;">DoorDash<br><span style="color:#8B1E1E; font-size:0.75rem;">15-30% comisión</span></th>
                        <th style="padding:1rem 1.25rem; text-align:center; font-size:0.9rem; font-weight:700; border-radius:0 12px 0 0;" style="background:rgba(212,175,55,0.1);">
                            <span style="color:#D4AF37;">FAMER</span><br>
                            <span style="color:#D4AF37; font-size:0.8rem;">$39/mo</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rows = [
                        ['Directorio + SEO orgánico',    true,  false, false, false, true],
                        ['Perfil con fotos',              true,  true,  true,  true,  true],
                        ['Reseñas verificadas',           true,  false, true,  true,  true],
                        ['Menú digital + QR',             false, true,  false, true,  true],
                        ['Reservaciones',                 false, false, true,  false, true],
                        ['Pedidos online (sin comisión)', false, true,  false, false, true],
                        ['Programa de Lealtad',           false, true,  false, false, true],
                        ['Email & SMS Marketing',         false, true,  false, false, true],
                        ['Cupones y Promociones',         '$$$', true,  false, true,  true],
                        ['Analytics de perfil',           true,  true,  true,  true,  true],
                        ['AI Chatbot español/inglés',     false, false, false, false, true],
                        ['Flash Deals',                   false, false, false, true,  true],
                        ['Gestión de equipo',             false, false, true,  true,  true],
                        ['Sitio web propio (PWA)',         false, true,  false, false, true],
                        ['FAMER Score y Awards',          false, false, false, false, true],
                        ['Especializado en mexicano',     false, false, false, false, true],
                    ];
                    if (app()->getLocale() === 'en') {
                        $rows = [
                            ['Directory + Organic SEO',      true,  false, false, false, true],
                            ['Profile with photos',          true,  true,  true,  true,  true],
                            ['Verified reviews',             true,  false, true,  true,  true],
                            ['Digital menu + QR code',       false, true,  false, true,  true],
                            ['Reservations',                 false, false, true,  false, true],
                            ['Online orders (no commission)',false, true,  false, false, true],
                            ['Loyalty Program',              false, true,  false, false, true],
                            ['Email & SMS Marketing',        false, true,  false, false, true],
                            ['Coupons & Promotions',         '$$$', true,  false, true,  true],
                            ['Profile analytics',            true,  true,  true,  true,  true],
                            ['AI Chatbot (ES/EN)',           false, false, false, false, true],
                            ['Flash Deals',                  false, false, false, true,  true],
                            ['Team management',              false, false, true,  true,  true],
                            ['Own website (PWA)',            false, true,  false, false, true],
                            ['FAMER Score & Awards',         false, false, false, false, true],
                            ['Specialized in Mexican food',  false, false, false, false, true],
                        ];
                    }
                    @endphp

                    @foreach($rows as $i => $row)
                    <tr style="border-top:1px solid #2A2A2A; {{ $i % 2 === 0 ? 'background:#1A1A1A;' : 'background:#161616;' }}">
                        <td style="padding:0.875rem 1.25rem; color:#E5E7EB; font-size:0.875rem;">{{ $row[0] }}</td>
                        @foreach([1,2,3,4] as $col)
                        <td style="padding:0.875rem; text-align:center;">
                            @if($row[$col] === true)
                                <span style="color:#6B7280; font-size:1rem;">✓</span>
                            @elseif($row[$col] === false)
                                <span style="color:#3A3A3A; font-size:1rem;">—</span>
                            @else
                                <span style="color:#8B1E1E; font-size:0.75rem; font-weight:600;">{{ $row[$col] }}</span>
                            @endif
                        </td>
                        @endforeach
                        {{-- FAMER column --}}
                        <td style="padding:0.875rem 1.25rem; text-align:center; background:rgba(212,175,55,0.04);">
                            @if($row[5] === true)
                                <span style="color:#D4AF37; font-size:1.1rem; font-weight:700;">✓</span>
                            @else
                                <span style="color:#3A3A3A; font-size:1rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    {{-- Total row --}}
                    <tr style="border-top:2px solid #2A2A2A; background:#0B0B0B;">
                        <td style="padding:1.25rem; font-weight:700; color:#F5F5F5; font-size:0.9rem;">
                            {{ app()->getLocale() === 'en' ? 'Monthly Cost' : 'Costo Mensual' }}
                        </td>
                        <td style="padding:1.25rem; text-align:center; font-weight:700; color:#8B1E1E;">$400+</td>
                        <td style="padding:1.25rem; text-align:center; font-weight:700; color:#8B1E1E;">$199</td>
                        <td style="padding:1.25rem; text-align:center; font-weight:700; color:#8B1E1E;">$249</td>
                        <td style="padding:1.25rem; text-align:center; font-weight:700; color:#8B1E1E;">{{ app()->getLocale() === 'en' ? '30% per order' : '30% por pedido' }}</td>
                        <td style="padding:1.25rem; text-align:center; background:rgba(212,175,55,0.08); border-radius:0 0 12px 0;">
                            <div style="font-size:1.5rem; font-weight:900; color:#D4AF37;">$39<span style="font-size:0.8rem; font-weight:400;">/mo</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Bottom CTA --}}
        <div class="text-center mt-10">
            <a href="{{ route('claim.restaurant') }}"
               class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105"
               style="background:#D4AF37; color:#0B0B0B; box-shadow:0 4px 20px rgba(212,175,55,0.3);">
                {{ app()->getLocale() === 'en' ? 'Start Free Today' : 'Empieza Gratis Hoy' }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <p class="mt-3 text-sm" style="color:#6B7280;">
                {{ app()->getLocale() === 'en' ? 'No credit card required. Free forever.' : 'Sin tarjeta de crédito. Gratis para siempre.' }}
            </p>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 3.5 FAMER SCORE BANNER --}}
{{-- ============================================ --}}
<section class="py-16" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden" style="background: linear-gradient(135deg, #1A1A1A 0%, #1F3D2B 50%, #1A1A1A 100%); border: 1px solid rgba(212,175,55,0.3);">
            {{-- Gold glow accent --}}
            <div class="absolute inset-0" style="background: radial-gradient(ellipse 60% 80% at 80% 50%, rgba(212,175,55,0.07) 0%, transparent 70%);"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center gap-8 p-10 md:p-14">
                {{-- Score badge visual --}}
                <div class="flex-shrink-0 flex items-center justify-center w-28 h-28 rounded-full" style="background: rgba(212,175,55,0.12); border: 2px solid rgba(212,175,55,0.4);">
                    <div class="text-center">
                        <div class="text-3xl font-black leading-none" style="color: #D4AF37;">87</div>
                        <div class="text-xs font-bold mt-1" style="color: #D4AF37;">SCORE</div>
                    </div>
                </div>

                {{-- Text --}}
                <div class="flex-1 text-center md:text-left">
                    <div class="text-xs font-bold tracking-widest uppercase mb-2" style="color: #D4AF37;">
                        {{ app()->getLocale() === 'en' ? 'Free Tool' : 'Herramienta Gratuita' }}
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-3" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                        {{ app()->getLocale() === 'en' ? 'Check Your FAMER Score' : '¿Cuánto vale tu restaurante en línea?' }}
                    </h2>
                    <p style="color: #CCCCCC; max-width: 520px;">
                        {{ app()->getLocale() === 'en'
                            ? 'Get a free analysis of your restaurant\'s online presence — ratings, photos, completeness, and visibility. See exactly what to fix.'
                            : 'Obtén un análisis gratis de la presencia online de tu restaurante — calificaciones, fotos, completitud y visibilidad. Ve exactamente qué mejorar.' }}
                    </p>
                </div>

                {{-- CTA --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('famer.grader') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 hover:scale-105"
                       style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 4px 20px rgba(212,175,55,0.3);">
                        {{ app()->getLocale() === 'en' ? 'Get My Score' : 'Ver Mi Score' }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 4. HOW IT WORKS --}}
{{-- ============================================ --}}
<section id="how-it-works" class="py-20 md:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'How It Works' : 'Como Funciona' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'Three simple steps to start growing' : 'Tres pasos simples para empezar a crecer' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            {{-- Step 1 --}}
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: #D4AF37;">
                    <span class="text-2xl font-bold" style="color: #0B0B0B;">1</span>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5" style="background-color: #2A2A2A;">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Claim Your Restaurant' : 'Reclama Tu Restaurante' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Search for your restaurant and verify that you are the owner.' : 'Busca tu restaurante y verifica que eres el dueno.' }}</p>
            </div>

            {{-- Step 2 --}}
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: #D4AF37;">
                    <span class="text-2xl font-bold" style="color: #0B0B0B;">2</span>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5" style="background-color: #2A2A2A;">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Optimize Your Profile' : 'Optimiza Tu Perfil' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Add photos, menus, hours, and everything customers need to find you.' : 'Agrega fotos, menus, horarios y todo lo que tus clientes necesitan.' }}</p>
            </div>

            {{-- Step 3 --}}
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6" style="background-color: #D4AF37;">
                    <span class="text-2xl font-bold" style="color: #0B0B0B;">3</span>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mx-auto mb-5" style="background-color: #2A2A2A;">
                    <svg class="w-7 h-7" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Start Getting Customers' : 'Empieza a Recibir Clientes' }}</h3>
                <p style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Your visibility increases automatically. More views, more calls, more visits.' : 'Tu visibilidad aumenta automaticamente. Mas vistas, mas llamadas, mas visitas.' }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 5. SOCIAL PROOF / STATS --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'The Numbers Speak for Themselves' : 'Los Numeros Hablan por Si Solos' }}
            </h2>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            <div class="text-center rounded-2xl p-6" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div class="text-2xl md:text-3xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_views'] ?? 447283) }}</div>
                <p class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Total Views' : 'Visitas Totales' }}</p>
            </div>

            <div class="text-center rounded-2xl p-6" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="text-2xl md:text-3xl font-black" style="color: #D4AF37;">{{ number_format($stats['daily_avg'] ?? 6997) }}</div>
                <p class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Daily Average' : 'Promedio Diario' }}</p>
            </div>

            <div class="text-center rounded-2xl p-6" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="text-2xl md:text-3xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_restaurants'] ?? 13247) }}</div>
                <p class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Restaurants Listed' : 'Restaurantes Listados' }}</p>
            </div>

            <div class="text-center rounded-2xl p-6" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-3" style="background-color: rgba(212,175,55,0.1);">
                    <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl md:text-3xl font-black" style="color: #D4AF37;">${{ number_format($stats['google_ads_value'] ?? 894566) }}</div>
                <p class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Google Ads Equivalent' : 'Valor en Google Ads' }}</p>
            </div>
        </div>

        {{-- State Stats --}}
        @if(isset($stateStats) && count($stateStats) > 0)
        <div class="max-w-3xl mx-auto mb-16">
            <div class="rounded-2xl p-8" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <h3 class="text-lg font-bold mb-6 text-center flex items-center justify-center gap-2" style="color: #F5F5F5;">
                    <svg class="w-5 h-5" style="color: #D4AF37;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    {{ app()->getLocale() === 'en' ? 'Top States by Traffic' : 'Top Estados por Trafico' }}
                </h3>
                <div class="space-y-4">
                    @foreach($stateStats as $index => $state)
                        @php
                            $maxViews = $stateStats[0]['views'] ?? 1;
                            $percentage = ($state['views'] / $maxViews) * 100;
                        @endphp
                        <div class="flex items-center gap-4">
                            <span class="text-lg font-black w-8" style="color: {{ $index === 0 ? '#D4AF37' : '#CCCCCC' }};">#{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between mb-2">
                                    <span class="font-semibold" style="color: #F5F5F5;">{{ $state['state_name'] }}</span>
                                    <span class="font-medium" style="color: #CCCCCC;">{{ number_format($state['views']) }}</span>
                                </div>
                                <div class="h-2 rounded-full overflow-hidden" style="background-color: #2A2A2A;">
                                    <div class="h-full rounded-full transition-all duration-500" style="background-color: #D4AF37; width: {{ $percentage }}%; opacity: {{ 1 - ($index * 0.15) }};"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Featured Restaurants --}}
        <div class="text-center mb-10">
            <h3 class="text-2xl font-bold" style="color: #F5F5F5;">
                {{ app()->getLocale() === 'en' ? 'Featured on Our Platform' : 'Destacados en Nuestra Plataforma' }}
            </h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
            {{-- Card 1 --}}
            <div class="group relative overflow-hidden rounded-xl aspect-[3/4]">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1504544750208-dc0358e63f7f?w=400&h=500&fit=crop" alt="Mi Tierra Cafe" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                    <p class="text-xs font-medium" style="color: #D4AF37;">San Antonio, TX</p>
                    <h4 class="font-bold text-sm md:text-base" style="color: #F5F5F5;">Mi Tierra Cafe & Bakery</h4>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="group relative overflow-hidden rounded-xl aspect-[3/4]">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=400&h=500&fit=crop" alt="Gracias Madre" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                    <p class="text-xs font-medium" style="color: #D4AF37;">West Hollywood, CA</p>
                    <h4 class="font-bold text-sm md:text-base" style="color: #F5F5F5;">Gracias Madre</h4>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="group relative overflow-hidden rounded-xl aspect-[3/4] hidden md:block">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1613514785940-daed07799d9b?w=400&h=500&fit=crop" alt="Columbia Restaurant" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                    <p class="text-xs font-medium" style="color: #D4AF37;">Tampa, FL</p>
                    <h4 class="font-bold text-sm md:text-base" style="color: #F5F5F5;">Columbia Restaurant</h4>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="group relative overflow-hidden rounded-xl aspect-[3/4] hidden md:block">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=400&h=500&fit=crop" alt="The Taco Stand" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                    <p class="text-xs font-medium" style="color: #D4AF37;">La Jolla, CA</p>
                    <h4 class="font-bold text-sm md:text-base" style="color: #F5F5F5;">The Taco Stand</h4>
                </div>
            </div>

            {{-- Card 5 --}}
            <div class="group relative overflow-hidden rounded-xl aspect-[3/4] hidden md:block">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1599974579688-8dbdd335c77f?w=400&h=500&fit=crop" alt="Cafe Tu Tu Tango" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute bottom-0 left-0 right-0 p-4 z-20">
                    <p class="text-xs font-medium" style="color: #D4AF37;">Orlando, FL</p>
                    <h4 class="font-bold text-sm md:text-base" style="color: #F5F5F5;">Cafe Tu Tu Tango</h4>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 6. COMPARISON TABLE --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'How We Compare' : 'Como Nos Comparamos' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'The only platform built exclusively for Mexican restaurants' : 'La unica plataforma hecha exclusivamente para restaurantes mexicanos' }}
            </p>
        </div>

        <div class="overflow-x-auto rounded-2xl" style="border: 1px solid rgba(255,255,255,0.05);">
            <table class="w-full" style="min-width: 600px;">
                <thead>
                    <tr style="background-color: #2A2A2A;">
                        <th class="py-4 px-4 md:px-6 text-left text-sm font-medium" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Feature' : 'Caracteristica' }}</th>
                        <th class="py-4 px-4 md:px-6 text-center" style="background-color: rgba(212,175,55,0.1);">
                            <span class="font-bold" style="color: #D4AF37;">FAMER</span>
                        </th>
                        <th class="py-4 px-4 md:px-6 text-center text-sm font-medium" style="color: #CCCCCC;">Yelp</th>
                        <th class="py-4 px-4 md:px-6 text-center text-sm font-medium" style="color: #CCCCCC;">Google</th>
                        <th class="py-4 px-4 md:px-6 text-center text-sm font-medium" style="color: #CCCCCC;">TripAdvisor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rows = app()->getLocale() === 'en'
                            ? [
                                'Mexican food focus',
                                'No competitor ads on your page',
                                'Focused audience',
                                'Coupons & promotions included',
                                'Spanish support',
                                'Starting price'
                            ]
                            : [
                                'Enfoque en comida mexicana',
                                'Sin anuncios de competidores',
                                'Audiencia enfocada',
                                'Cupones y promociones incluidos',
                                'Soporte en espanol',
                                'Precio inicial'
                            ];
                        $famer =  ['check','check','check','check','check','$29/mo'];
                        $yelp =   ['x','x','x','x','partial','$300+/mo'];
                        $google = ['x','x','x','x','partial','N/A'];
                        $trip =   ['x','x','x','x','partial','$99+/mo'];
                    @endphp
                    @foreach($rows as $i => $row)
                    <tr style="border-top: 1px solid rgba(255,255,255,0.05); {{ $i % 2 === 0 ? 'background-color: #0B0B0B;' : 'background-color: #1A1A1A;' }}">
                        <td class="py-4 px-4 md:px-6 text-sm font-medium" style="color: #F5F5F5;">{{ $row }}</td>
                        <td class="py-4 px-4 md:px-6 text-center" style="background-color: rgba(212,175,55,0.03);">
                            @if($famer[$i] === 'check')
                                <svg class="w-5 h-5 mx-auto" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <span class="text-sm font-bold" style="color: #D4AF37;">{{ $famer[$i] }}</span>
                            @endif
                        </td>
                        @foreach([$yelp[$i], $google[$i], $trip[$i]] as $val)
                        <td class="py-4 px-4 md:px-6 text-center">
                            @if($val === 'check')
                                <svg class="w-5 h-5 mx-auto" style="color: #4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($val === 'x')
                                <svg class="w-5 h-5 mx-auto" style="color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @elseif($val === 'partial')
                                <svg class="w-5 h-5 mx-auto" style="color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            @else
                                <span class="text-sm" style="color: #666;">{{ $val }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 7. PRICING SECTION --}}
{{-- ============================================ --}}
<section id="pricing" class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'Simple, Transparent Pricing' : 'Precios Simples y Transparentes' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'Start free. Upgrade when you are ready.' : 'Empieza gratis. Mejora cuando estes listo.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 items-start">
            {{-- FREE Plan --}}
            <div class="rounded-2xl p-8" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <h3 class="text-lg font-bold mb-2" style="color: #F5F5F5;">Listado Gratis</h3>
                <div class="mb-6">
                    <span class="text-4xl font-black" style="color: #F5F5F5;">$0</span>
                    <span class="text-sm" style="color: #CCCCCC;">/mes</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach(['Aparece en el directorio', 'Info basica (nombre, direccion, telefono)', 'Integracion con Google Maps', 'Verificar propiedad del restaurante', 'Editar informacion basica', 'Resenas de clientes', 'Horarios y contacto', 'Hasta 5 fotos'] as $feature)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm" style="color: #CCCCCC;">{{ $feature }}</span>
                    </li>
                    @endforeach
                    @foreach(['Sin prioridad en busquedas', 'Sin analiticas avanzadas'] as $excluded)
                    <li class="flex items-center gap-3 opacity-40">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="text-sm" style="color: #666;">{{ $excluded }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}" class="block w-full text-center py-3 px-6 rounded-xl font-semibold transition-all duration-300 hover:bg-white/10" style="border: 1px solid rgba(255,255,255,0.1); color: #F5F5F5;">
                    Reclamar Gratis
                </a>
            </div>

            {{-- PREMIUM Plan (Highlighted) --}}
            <div class="rounded-2xl p-8 relative md:-mt-4 md:mb-0" style="background-color: #1A1A1A; border: 2px solid #D4AF37; box-shadow: 0 0 40px rgba(212,175,55,0.1);">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold" style="background-color: #D4AF37; color: #0B0B0B;">MAS POPULAR</span>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: #D4AF37;">Premium</h3>
                <div class="mb-2">
                    <span class="text-2xl line-through" style="color: #666;">$39</span>
                    <span class="text-4xl font-black ml-2" style="color: #F5F5F5;">$9.99</span>
                    <span class="text-sm" style="color: #CCCCCC;">/primer mes</span>
                </div>
                <p class="text-sm mb-6" style="color: #D4AF37;">Despues $39/mes. Cancela cuando quieras.</p>
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Todo lo de Free PLUS:' => true,
                        'Badge Destacado "Premium"' => false,
                        'Top 3 en busquedas locales' => false,
                        'Menu Digital + QR Code' => false,
                        'Widget de Pedidos Online' => false,
                        'Sistema de Reservaciones' => false,
                        'Dashboard de Analiticas' => false,
                        'Fotos y Videos Ilimitados' => false,
                        'Chatbot AI (ES/EN) 24/7' => false,
                        'Programa de Lealtad' => false,
                        'Cupones y Promociones' => false,
                        'Soporte Prioritario' => false,
                    ] as $feature => $isBold)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm {{ $isBold ? 'font-bold' : '' }}" style="color: #F5F5F5;">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}?plan=premium" class="block w-full text-center py-3 px-6 rounded-xl font-bold transition-all duration-300 hover:scale-105" style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 4px 20px rgba(212,175,55,0.25);">
                    Suscribirse por $9.99
                </a>
                <p class="text-center text-xs mt-3" style="color: #999;">4 Cupones Trimestrales (10% off) en MF Group</p>
            </div>

            {{-- ELITE Plan --}}
            <div class="rounded-2xl p-8" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <h3 class="text-lg font-bold mb-2" style="color: #F5F5F5;">Elite</h3>
                <div class="mb-6">
                    <span class="text-4xl font-black" style="color: #F5F5F5;">$79</span>
                    <span class="text-sm" style="color: #CCCCCC;">/mes</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Todo lo de Premium PLUS:' => true,
                        'App Movil White Label' => false,
                        'Website Builder Completo' => false,
                        'Exposicion Maxima Nacional' => false,
                        'Posicion #1 en Rankings' => false,
                        'Analytics Avanzados + Reportes' => false,
                        'Gerente de Cuenta Dedicado' => false,
                        'Branding Personalizado' => false,
                        'Integracion con Redes Sociales' => false,
                        'Campanas de Email Marketing' => false,
                        'Sin anuncios de competidores' => false,
                    ] as $feature => $isBold)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm {{ $isBold ? 'font-bold' : '' }}" style="color: #CCCCCC;">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}?plan=elite" class="block w-full text-center py-3 px-6 rounded-xl font-semibold transition-all duration-300 hover:bg-white/10" style="border: 1px solid rgba(255,255,255,0.1); color: #F5F5F5;">
                    Ir a Elite
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 8. FINAL CTA --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28 relative" style="background: linear-gradient(to bottom, #1A1A1A, #0B0B0B);">
    <div class="absolute inset-0" style="background: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(212,175,55,0.05) 0%, transparent 70%);"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-bold mb-6" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
            {{ app()->getLocale() === 'en' ? 'Start Getting More Customers Today' : 'Empieza a Recibir Mas Clientes Hoy' }}
        </h2>
        <p class="text-lg mb-10" style="color: #CCCCCC;">
            {{ app()->getLocale() === 'en'
                ? 'Join thousands of Mexican restaurant owners who are already growing their business on FAMER.'
                : 'Unete a miles de duenos de restaurantes mexicanos que ya estan creciendo su negocio en FAMER.' }}
        </p>
        <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center px-10 py-5 text-lg font-bold rounded-xl transition-all duration-300 hover:scale-105" style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 4px 30px rgba(212,175,55,0.3);">
            {{ app()->getLocale() === 'en' ? 'Claim Your Restaurant' : 'Reclama Tu Restaurante' }}
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <p class="mt-6 text-sm" style="color: #CCCCCC;">
            {{ app()->getLocale() === 'en' ? 'Free to get started. Upgrade anytime.' : 'Gratis para empezar. Mejora cuando quieras.' }}
        </p>
    </div>
</section>

{{-- ============================================ --}}
{{-- 9. FAQ SECTION --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center mb-14">
            <span class="text-xs font-bold tracking-widest uppercase mb-4 block" style="color: #D4AF37;">
                {{ app()->getLocale() === 'en' ? 'FAQ' : 'Preguntas Frecuentes' }}
            </span>
            <h2 class="text-3xl md:text-5xl font-bold" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'Common Questions' : 'Preguntas Comunes' }}
            </h2>
        </div>

        {{-- FAQ Accordion --}}
        <div class="space-y-3" id="famer-faq">

            @php
            $isEn = app()->getLocale() === 'en';
            $faqs = [
                [
                    'q_es' => '¿Cuánto cuesta FAMER?',
                    'q_en' => 'How much does FAMER cost?',
                    'a_es' => 'FAMER es gratis para siempre en el plan básico. El plan Premium cuesta $39/mes e incluye menú digital, reservaciones, pedidos online, loyalty program y más. El plan Elite a $79/mes agrega website propio, email marketing y gestión avanzada.',
                    'a_en' => 'FAMER is free forever on the basic plan. Premium is $39/month and includes digital menu, reservations, online ordering, loyalty program, and more. Elite at $79/month adds your own website, email marketing, and advanced management.',
                ],
                [
                    'q_es' => '¿Cómo verifico que soy el dueño de mi restaurante?',
                    'q_en' => 'How do I verify I\'m the restaurant owner?',
                    'a_es' => 'Buscas tu restaurante en FAMER, haces clic en "Reclamar", y verificamos tu propiedad por teléfono o email. El proceso toma menos de 24 horas.',
                    'a_en' => 'Search for your restaurant on FAMER, click "Claim", and we verify ownership by phone or email. The process takes less than 24 hours.',
                ],
                [
                    'q_es' => '¿Qué pasa si mi restaurante no está en FAMER?',
                    'q_en' => 'What if my restaurant isn\'t listed on FAMER?',
                    'a_es' => 'Puedes agregarlo gratis en minutos desde la sección "Agregar Restaurante". Aparecerá en el directorio inmediatamente después de la revisión.',
                    'a_en' => 'You can add it for free in minutes from the "Add Restaurant" section. It will appear in the directory immediately after review.',
                ],
                [
                    'q_es' => '¿FAMER reemplaza a Yelp, Google My Business o DoorDash?',
                    'q_en' => 'Does FAMER replace Yelp, Google My Business or DoorDash?',
                    'a_es' => 'FAMER complementa y en muchos casos reemplaza varias plataformas. Tienes directorio, reseñas, menú digital, pedidos online, reservaciones y marketing — todo en uno. Sin las altas comisiones de DoorDash ni las cuotas de Yelp.',
                    'a_en' => 'FAMER complements and in many cases replaces multiple platforms. You get directory, reviews, digital menu, online ordering, reservations and marketing — all in one. Without DoorDash\'s high commissions or Yelp\'s fees.',
                ],
                [
                    'q_es' => '¿Puedo cancelar en cualquier momento?',
                    'q_en' => 'Can I cancel at any time?',
                    'a_es' => 'Sí, puedes cancelar cuando quieras. Tu perfil básico permanece activo en el directorio de forma gratuita. Sin contratos ni penalizaciones.',
                    'a_en' => 'Yes, you can cancel whenever you want. Your basic profile remains active in the directory for free. No contracts or penalties.',
                ],
                [
                    'q_es' => '¿FAMER funciona para restaurantes en México también?',
                    'q_en' => 'Does FAMER work for restaurants in Mexico too?',
                    'a_es' => 'Sí. FAMER incluye los 32 estados de México además de los 50 estados de EE.UU. Si tienes restaurantes en ambos países, puedes gestionarlos desde un solo panel.',
                    'a_en' => 'Yes. FAMER includes all 32 Mexican states plus all 50 US states. If you have restaurants in both countries, you can manage them from a single dashboard.',
                ],
                [
                    'q_es' => '¿Qué es el FAMER Score?',
                    'q_en' => 'What is the FAMER Score?',
                    'a_es' => 'Es una calificación de 0 a 100 que mide qué tan completo y visible es tu restaurante en línea. Evalúa fotos, calificaciones, información de contacto, horarios y más. Es gratis y te dice exactamente qué mejorar.',
                    'a_en' => 'It\'s a 0-100 score that measures how complete and visible your restaurant is online. It evaluates photos, ratings, contact info, hours, and more. It\'s free and tells you exactly what to improve.',
                ],
                [
                    'q_es' => '¿Hay soporte en español?',
                    'q_en' => 'Is there Spanish support?',
                    'a_es' => 'Sí, FAMER fue construido para la comunidad mexicana. Todo el soporte, la plataforma y las comunicaciones están disponibles en español.',
                    'a_en' => 'Yes, FAMER was built for the Mexican community. All support, the platform, and communications are available in Spanish.',
                ],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
            <div class="famer-faq-item rounded-xl overflow-hidden transition-all duration-300"
                 style="background-color: #1A1A1A; border: 1px solid #2A2A2A;"
                 onmouseenter="this.style.borderColor='#D4AF37'"
                 onmouseleave="if(!this.classList.contains('famer-faq-open')) this.style.borderColor='#2A2A2A'">

                {{-- Question row --}}
                <button type="button"
                        class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none"
                        onclick="famerToggleFaq({{ $i }})"
                        aria-expanded="false"
                        id="faq-btn-{{ $i }}">
                    <span class="text-base font-semibold pr-4" style="color: #D4AF37;">
                        {{ $isEn ? $faq['q_en'] : $faq['q_es'] }}
                    </span>
                    <svg id="faq-chevron-{{ $i }}"
                         class="w-5 h-5 flex-shrink-0 transition-transform duration-300"
                         style="color: #D4AF37;"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Answer panel --}}
                <div id="faq-answer-{{ $i }}"
                     style="max-height: 0; overflow: hidden; transition: max-height 0.35s ease;">
                    <p class="px-6 pb-5 text-sm leading-relaxed" style="color: #CCCCCC;">
                        {{ $isEn ? $faq['a_en'] : $faq['a_es'] }}
                    </p>
                </div>
            </div>
            @endforeach

        </div>{{-- /FAQ accordion --}}

        {{-- Bottom CTA --}}
        <div class="text-center mt-12">
            <p class="text-sm mb-4" style="color: #CCCCCC;">
                {{ $isEn ? 'Still have questions?' : '¿Tienes más preguntas?' }}
            </p>
            <a href="mailto:hola@restaurantesmexicanosfamosos.com.mx"
               class="inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 hover:bg-white/5"
               style="border: 1px solid #D4AF37; color: #D4AF37;">
                {{ $isEn ? 'Contact Us' : 'Contáctanos' }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

    </div>
</section>

{{-- FAQ Accordion JS --}}
<script>
function famerToggleFaq(index) {
    var answer = document.getElementById('faq-answer-' + index);
    var chevron = document.getElementById('faq-chevron-' + index);
    var btn = document.getElementById('faq-btn-' + index);
    var item = btn.closest('.famer-faq-item');
    var isOpen = item.classList.contains('famer-faq-open');

    // Close all other items
    document.querySelectorAll('.famer-faq-item').forEach(function(el, i) {
        el.classList.remove('famer-faq-open');
        el.style.borderColor = '#2A2A2A';
        var a = document.getElementById('faq-answer-' + i);
        var c = document.getElementById('faq-chevron-' + i);
        var b = document.getElementById('faq-btn-' + i);
        if (a) a.style.maxHeight = '0';
        if (c) c.style.transform = 'rotate(0deg)';
        if (b) b.setAttribute('aria-expanded', 'false');
    });

    // Toggle clicked item
    if (!isOpen) {
        item.classList.add('famer-faq-open');
        item.style.borderColor = '#D4AF37';
        answer.style.maxHeight = answer.scrollHeight + 'px';
        chevron.style.transform = 'rotate(180deg)';
        btn.setAttribute('aria-expanded', 'true');
    }
}
</script>

{{-- FAQPage JSON-LD Schema --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "¿Cuánto cuesta FAMER? / How much does FAMER cost?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "FAMER es gratis para siempre en el plan básico. El plan Premium cuesta $39/mes e incluye menú digital, reservaciones, pedidos online, loyalty program y más. El plan Elite a $79/mes agrega website propio, email marketing y gestión avanzada. | FAMER is free forever on the basic plan. Premium is $39/month and includes digital menu, reservations, online ordering, loyalty program, and more. Elite at $79/month adds your own website, email marketing, and advanced management."
            }
        },
        {
            "@type": "Question",
            "name": "¿Cómo verifico que soy el dueño de mi restaurante? / How do I verify I'm the restaurant owner?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Buscas tu restaurante en FAMER, haces clic en 'Reclamar', y verificamos tu propiedad por teléfono o email. El proceso toma menos de 24 horas. | Search for your restaurant on FAMER, click 'Claim', and we verify ownership by phone or email. The process takes less than 24 hours."
            }
        },
        {
            "@type": "Question",
            "name": "¿Qué pasa si mi restaurante no está en FAMER? / What if my restaurant isn't listed on FAMER?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Puedes agregarlo gratis en minutos desde la sección 'Agregar Restaurante'. Aparecerá en el directorio inmediatamente después de la revisión. | You can add it for free in minutes from the 'Add Restaurant' section. It will appear in the directory immediately after review."
            }
        },
        {
            "@type": "Question",
            "name": "¿FAMER reemplaza a Yelp, Google My Business o DoorDash? / Does FAMER replace Yelp, Google My Business or DoorDash?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "FAMER complementa y en muchos casos reemplaza varias plataformas. Tienes directorio, reseñas, menú digital, pedidos online, reservaciones y marketing — todo en uno. Sin las altas comisiones de DoorDash ni las cuotas de Yelp. | FAMER complements and in many cases replaces multiple platforms. You get directory, reviews, digital menu, online ordering, reservations and marketing — all in one. Without DoorDash's high commissions or Yelp's fees."
            }
        },
        {
            "@type": "Question",
            "name": "¿Puedo cancelar en cualquier momento? / Can I cancel at any time?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sí, puedes cancelar cuando quieras. Tu perfil básico permanece activo en el directorio de forma gratuita. Sin contratos ni penalizaciones. | Yes, you can cancel whenever you want. Your basic profile remains active in the directory for free. No contracts or penalties."
            }
        },
        {
            "@type": "Question",
            "name": "¿FAMER funciona para restaurantes en México también? / Does FAMER work for restaurants in Mexico too?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sí. FAMER incluye los 32 estados de México además de los 50 estados de EE.UU. Si tienes restaurantes en ambos países, puedes gestionarlos desde un solo panel. | Yes. FAMER includes all 32 Mexican states plus all 50 US states. If you have restaurants in both countries, you can manage them from a single dashboard."
            }
        },
        {
            "@type": "Question",
            "name": "¿Qué es el FAMER Score? / What is the FAMER Score?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Es una calificación de 0 a 100 que mide qué tan completo y visible es tu restaurante en línea. Evalúa fotos, calificaciones, información de contacto, horarios y más. Es gratis y te dice exactamente qué mejorar. | It's a 0-100 score that measures how complete and visible your restaurant is online. It evaluates photos, ratings, contact info, hours, and more. It's free and tells you exactly what to improve."
            }
        },
        {
            "@type": "Question",
            "name": "¿Hay soporte en español? / Is there Spanish support?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "Sí, FAMER fue construido para la comunidad mexicana. Todo el soporte, la plataforma y las comunicaciones están disponibles en español. | Yes, FAMER was built for the Mexican community. All support, the platform, and communications are available in Spanish."
            }
        }
    ]
}
</script>

</div>
