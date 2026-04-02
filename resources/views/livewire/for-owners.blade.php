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
                <a href="#how-it-works" class="inline-flex items-center px-8 py-4 text-lg font-semibold rounded-xl transition-all duration-300 hover:bg-white/5" style="border: 1px solid #D4AF37; color: #D4AF37;">
                    {{ app()->getLocale() === 'en' ? 'See How It Works' : 'Como Funciona' }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
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

</div>
