<div class="min-h-screen" style="background-color: #0B0B0B;">

{{-- ============================================ --}}
{{-- 1. HERO SECTION --}}
{{-- ============================================ --}}
<section class="relative overflow-hidden" style="background-color: #0B0B0B; min-height: 70vh;">
    {{-- Restaurant background photo — cascade: photos[] http → yelp_photos[] → image --}}
    @php
        $heroRestaurant = \App\Models\Restaurant::approved()
            ->where(function($q) {
                $q->whereNotNull('photos')
                  ->orWhereNotNull('yelp_photos')
                  ->orWhere(fn($q2) => $q2->whereNotNull('image')->where('image','!=',''));
            })
            ->orderByDesc('google_reviews_count')
            ->first(['photos','yelp_photos','image','name']);

        $heroBg = null;
        if ($heroRestaurant) {
            $photos     = $heroRestaurant->photos     ?? [];
            $yelpPhotos = $heroRestaurant->yelp_photos ?? [];
            if (!empty($photos) && str_starts_with($photos[0], 'http')) {
                $heroBg = $photos[0];
            } elseif (!empty($yelpPhotos) && str_starts_with($yelpPhotos[0], 'http')) {
                $heroBg = $yelpPhotos[0];
            } elseif ($heroRestaurant->image) {
                $heroBg = str_starts_with($heroRestaurant->image, 'http')
                    ? $heroRestaurant->image
                    : \Illuminate\Support\Facades\Storage::url($heroRestaurant->image);
            }
        }
    @endphp

    @if($heroBg)
    <div class="absolute inset-0" style="background-image:url('{{ $heroBg }}'); background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
    @endif
    {{-- Dark overlay --}}
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(11,11,11,0.93) 0%, rgba(11,11,11,0.78) 50%, rgba(11,11,11,0.90) 100%);"></div>
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
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_restaurants'] ?? 26000) }}+</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">{{ number_format($stats['total_views'] ?? 1155826) }}+</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'Monthly Views' : 'Visitas/Mes' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black" style="color: #D4AF37;">82</div>
                    <div class="text-sm mt-1" style="color: #CCCCCC;">{{ app()->getLocale() === 'en' ? 'States/Regions' : 'Estados/Regiones' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-24" style="background: linear-gradient(to top, #0B0B0B, transparent);"></div>
</section>

{{-- ============================================ --}}
{{-- 2. FAMER SCORE LEAD MAGNET --}}
{{-- ============================================ --}}
<section class="py-12 md:py-16" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden" style="background: linear-gradient(135deg, #1A1A1A 0%, #1F3D2B 50%, #1A1A1A 100%); border: 1px solid rgba(212,175,55,0.3);">
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
                        {{ app()->getLocale() === 'en' ? 'Free Tool — Takes 30 seconds' : 'Herramienta Gratuita — Solo 30 segundos' }}
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-3" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                        {{ app()->getLocale() === 'en' ? 'How visible is your restaurant online?' : '¿Qué tan visible es tu restaurante en línea?' }}
                    </h2>
                    <p style="color: #CCCCCC; max-width: 520px;">
                        {{ app()->getLocale() === 'en'
                            ? 'Get your free FAMER Score — an analysis of your ratings, photos, completeness and online visibility. See exactly what to improve to get more customers.'
                            : 'Obtén tu FAMER Score gratis — análisis de calificaciones, fotos, completitud y visibilidad. Ve exactamente qué mejorar para conseguir más clientes.' }}
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
{{-- 3. PROBLEM → SOLUTION --}}
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
                            ? ['Low visibility online', 'Too much competition', 'Dependence on third-party apps that charge 15–30%', 'No automated tool to collect reviews']
                            : ['Baja visibilidad en internet', 'Demasiada competencia', 'Dependencia de apps que cobran 15–30% de comisión', 'Sin herramienta para conseguir reseñas automáticamente'];
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
                            ? ['Get discovered by local customers searching right now', 'Rank in Top 10 city and state lists', 'Showcase your brand with photos, menu and QR code', 'Automatic SMS review requests — fully hands-free']
                            : ['Sé encontrado por clientes buscando ahora mismo', 'Aparece en el Top 10 de tu ciudad y estado', 'Muestra tu marca con fotos, menú y código QR', 'SMS automáticos para conseguir reseñas — sin intervención'];
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
{{-- 4. BENEFITS GRID --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'Everything in one platform.' : 'Todo en una sola plataforma.' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'Built exclusively for Mexican restaurants.' : 'Construido exclusivamente para restaurantes mexicanos.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php $cards = [
                ['icon' => '🔍', 'title_es' => 'Sé Encontrado', 'title_en' => 'Get Discovered', 'desc_es' => 'Rankings de ciudad y estado. Tu restaurante frente a clientes que buscan comida mexicana ahora.', 'desc_en' => 'City and state rankings. Your restaurant in front of customers searching for Mexican food right now.', 'image' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=400&q=80'],
                ['icon' => '⭐', 'title_es' => 'Más Reseñas', 'title_en' => 'More Reviews', 'desc_es' => 'SMS automáticos post-visita. Más reseñas sin que tengas que hacer nada.', 'desc_en' => 'Automatic post-visit SMS. More reviews without you doing anything.', 'image' => 'https://images.unsplash.com/photo-1553729459-efe14ef6055d?auto=format&fit=crop&w=400&q=80'],
                ['icon' => '📋', 'title_es' => 'Menú Digital', 'title_en' => 'Digital Menu', 'desc_es' => 'Menú con fotos y precios. Código QR descargable para tus mesas y redes sociales.', 'desc_en' => 'Menu with photos and prices. Downloadable QR code for your tables and social media.', 'image' => 'https://images.unsplash.com/photo-1526367790999-0150786686a2?auto=format&fit=crop&w=400&q=80'],
                ['icon' => '📊', 'title_es' => 'Analytics', 'title_en' => 'Analytics', 'desc_es' => 'Ve cuántos clientes vieron tu perfil, llamaron o pidieron direcciones. Datos reales.', 'desc_en' => 'See how many customers viewed your profile, called, or asked for directions. Real data.', 'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=400&q=80'],
            ]; @endphp

            @foreach($cards as $card)
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1" style="background-color: #1A1A1A; border: 1px solid rgba(212,175,55,0.12);">
                <div style="height:130px; overflow:hidden;">
                    <img src="{{ $card['image'] }}" alt="{{ $card['title_es'] }}" loading="lazy"
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s;"
                         onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div class="p-6">
                    <div class="text-2xl mb-3">{{ $card['icon'] }}</div>
                    <h3 class="text-lg font-bold mb-2" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? $card['title_en'] : $card['title_es'] }}</h3>
                    <p class="text-sm leading-relaxed" style="color: #9CA3AF;">{{ app()->getLocale() === 'en' ? $card['desc_en'] : $card['desc_es'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Link to full details --}}
        <div class="text-center mt-10">
            <a href="{{ app()->getLocale() === 'en' ? '/how-famer-works' : '/como-funciona-famer' }}"
               class="inline-flex items-center gap-2 text-sm font-semibold transition-colors duration-200"
               style="color: #D4AF37;">
                {{ app()->getLocale() === 'en' ? 'See all 12 features — full comparison vs Yelp, DoorDash, OpenTable →' : 'Ver las 12 funciones — comparación completa vs Yelp, DoorDash, OpenTable →' }}
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 5. HOW IT WORKS — simplified 3 steps --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-5xl font-bold mb-4" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'How It Works' : 'Cómo Funciona' }}
            </h2>
            <p class="text-lg max-w-xl mx-auto" style="color: #CCCCCC;">
                {{ app()->getLocale() === 'en' ? 'Three steps to start growing.' : 'Tres pasos para empezar a crecer.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mb-12">
            @php $steps = [
                ['num' => '01', 'icon' => '🔍', 'title_es' => 'Reclama Tu Restaurante', 'title_en' => 'Claim Your Restaurant', 'desc_es' => 'Tu restaurante ya está en FAMER. Búscalo, haz clic en "Reclamar" y verifica en menos de 24 horas.', 'desc_en' => 'Your restaurant is already on FAMER. Search, click "Claim" and verify in under 24 hours.', 'image' => 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?auto=format&fit=crop&w=600&q=80'],
                ['num' => '02', 'icon' => '✏️', 'title_es' => 'Completa Tu Perfil', 'title_en' => 'Complete Your Profile', 'desc_es' => 'Agrega fotos, menú, horarios. FAMER Score mide tu progreso y te dice exactamente qué mejorar.', 'desc_en' => 'Add photos, menu, hours. FAMER Score measures progress and tells you exactly what to improve.', 'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=600&q=80'],
                ['num' => '03', 'icon' => '📈', 'title_es' => 'Crece y Aparece en Rankings', 'title_en' => 'Grow & Rank', 'desc_es' => 'Rankings semanales de ciudad y estado. SMS automáticos consiguen reseñas. Tú solo atiende el negocio.', 'desc_en' => 'Weekly city and state rankings. Automatic SMS collects reviews. You just run the restaurant.', 'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80'],
            ]; @endphp

            @foreach($steps as $step)
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1" style="background-color: #0B0B0B; border: 1px solid rgba(212,175,55,0.15);">
                <div style="height:160px; overflow:hidden; position:relative;">
                    <img src="{{ $step['image'] }}" alt="{{ app()->getLocale() === 'en' ? $step['title_en'] : $step['title_es'] }}" loading="lazy"
                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s;"
                         onmouseover="this.style.transform='scale(1.06)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(11,11,11,0.5) 0%, transparent 60%);"></div>
                    <div style="position:absolute; top:1rem; left:50%; transform:translateX(-50%);">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #D4AF37;">
                            <span class="text-sm font-bold" style="color: #0B0B0B;">{{ $step['num'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 text-center">
                    <div class="text-2xl mb-3">{{ $step['icon'] }}</div>
                    <h3 class="text-lg font-bold mb-3" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? $step['title_en'] : $step['title_es'] }}</h3>
                    <p class="text-sm leading-relaxed" style="color: #9CA3AF;">{{ app()->getLocale() === 'en' ? $step['desc_en'] : $step['desc_es'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CTA to full process page --}}
        <div class="text-center">
            <a href="{{ app()->getLocale() === 'en' ? '/how-famer-works' : '/como-funciona-famer' }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200"
               style="border: 1px solid rgba(212,175,55,0.3); color: #D4AF37;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
                {{ app()->getLocale() === 'en' ? 'See the complete process — 4 steps + all features →' : 'Ver el proceso completo — 4 pasos + todas las funciones →' }}
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- 6. SOCIAL PROOF — Dueños con resultados    --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14">
            <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color: #D4AF37;">
                {{ app()->getLocale() === 'en' ? 'Real Results' : 'Resultados Reales' }}
            </p>
            <h2 class="text-3xl md:text-4xl font-bold" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
                {{ app()->getLocale() === 'en' ? 'Owners who are already growing.' : 'Dueños que ya están creciendo.' }}
            </h2>
        </div>

        {{-- Impact stats bar --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-14">
            @foreach([
                ['num' => '3.2×', 'label_es' => 'Más reseñas en 90 días',       'label_en' => 'More reviews in 90 days'],
                ['num' => '68%',  'label_es' => 'Aumento promedio en visitas',   'label_en' => 'Average increase in profile views'],
                ['num' => '94%',  'label_es' => 'Dueños satisfechos',            'label_en' => 'Satisfied owners'],
                ['num' => '26K+', 'label_es' => 'Restaurantes en la plataforma', 'label_en' => 'Restaurants on the platform'],
            ] as $stat)
            <div class="rounded-2xl p-5 text-center" style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.15);">
                <div class="text-3xl font-black mb-1" style="color:#D4AF37; font-family:'Playfair Display',serif;">{{ $stat['num'] }}</div>
                <div class="text-xs" style="color:#9CA3AF;">{{ app()->getLocale() === 'en' ? $stat['label_en'] : $stat['label_es'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Testimonials grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
            @php $testimonials = [
                [
                    'name_es'     => 'Carlos M.',
                    'name_en'     => 'Carlos M.',
                    'restaurant_es' => 'Taquería El Güero',
                    'restaurant_en' => 'Taquería El Güero',
                    'location'    => 'Dallas, TX',
                    'quote_es'    => 'En 3 meses pasé de 12 reseñas a 47. Ahora aparezco en el Top 5 de Dallas. Los clientes me dicen que me encontraron en FAMER.',
                    'quote_en'    => 'In 3 months I went from 12 reviews to 47. Now I appear in the Top 5 of Dallas. Customers tell me they found me on FAMER.',
                    'metric_es'   => '+35 reseñas en 90 días',
                    'metric_en'   => '+35 reviews in 90 days',
                    'photo'       => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80',
                ],
                [
                    'name_es'     => 'Ana L.',
                    'name_en'     => 'Ana L.',
                    'restaurant_es' => 'La Cocina de Ana',
                    'restaurant_en' => 'La Cocina de Ana',
                    'location'    => 'Houston, TX',
                    'quote_es'    => 'El menú digital con QR fue un cambio total. Los clientes escanean y piden directo. Mis ingresos del fin de semana subieron 22% en el primer mes.',
                    'quote_en'    => 'The digital menu with QR was a total game changer. Customers scan and order directly. My weekend revenue went up 22% in the first month.',
                    'metric_es'   => '+22% ingresos fin de semana',
                    'metric_en'   => '+22% weekend revenue',
                    'photo'       => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?auto=format&fit=crop&w=80&q=80',
                ],
                [
                    'name_es'     => 'Roberto V.',
                    'name_en'     => 'Roberto V.',
                    'restaurant_es' => 'Pozolería Don Roberto',
                    'restaurant_en' => 'Pozolería Don Roberto',
                    'location'    => 'Chicago, IL',
                    'quote_es'    => 'Antes dependía 100% de Yelp. Ahora tengo mi propio perfil en FAMER y no pago comisiones. El FAMER Score me ayudó a saber exactamente qué mejorar.',
                    'quote_en'    => 'Before I relied 100% on Yelp. Now I have my own profile on FAMER and I pay no commissions. The FAMER Score helped me know exactly what to improve.',
                    'metric_es'   => 'Sin comisiones a terceros',
                    'metric_en'   => 'Zero third-party commissions',
                    'photo'       => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=80&q=80',
                ],
            ]; @endphp

            @foreach($testimonials as $t)
            <div class="rounded-2xl p-7 flex flex-col" style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.12);">
                {{-- Stars --}}
                <div class="flex gap-0.5 mb-4">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                {{-- Quote --}}
                <p class="text-sm leading-relaxed flex-1 mb-5" style="color:#CCCCCC;">
                    "{{ app()->getLocale() === 'en' ? $t['quote_en'] : $t['quote_es'] }}"
                </p>
                {{-- Metric badge --}}
                <div class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 mb-5 w-fit" style="background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2);">
                    <span class="text-xs font-bold" style="color:#D4AF37;">📈 {{ app()->getLocale() === 'en' ? $t['metric_en'] : $t['metric_es'] }}</span>
                </div>
                {{-- Owner info --}}
                <div class="flex items-center gap-3">
                    <img src="{{ $t['photo'] }}"
                         alt="{{ $t['name_es'] }}"
                         loading="lazy"
                         class="w-12 h-12 rounded-full object-cover flex-shrink-0"
                         style="border:2px solid rgba(212,175,55,0.4);">
                    <div>
                        <div class="text-sm font-bold" style="color:#F5F5F5;">{{ app()->getLocale() === 'en' ? $t['name_en'] : $t['name_es'] }}</div>
                        <div class="text-xs" style="color:#9CA3AF;">{{ app()->getLocale() === 'en' ? $t['restaurant_en'] : $t['restaurant_es'] }} · {{ $t['location'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Logos / trust badges of restaurant types --}}
        <div class="text-center">
            <p class="text-xs font-medium mb-4" style="color:#6B7280;">
                {{ app()->getLocale() === 'en' ? 'Restaurants of all types trust FAMER' : 'Restaurantes de todos los tipos confían en FAMER' }}
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                @foreach(['Taquerías','Pozelerías','Restaurantes Familiares','Bares Mexicanos','Cocinas Regionales','Food Trucks'] as $type)
                <span class="px-3 py-1.5 rounded-full text-xs font-medium" style="background:#1A1A1A; border:1px solid #2A2A2A; color:#9CA3AF;">{{ $type }}</span>
                @endforeach
            </div>
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
                <h3 class="text-lg font-bold mb-2" style="color: #F5F5F5;">{{ app()->getLocale() === 'en' ? 'Basic' : 'Listado Gratis' }}</h3>
                <div class="mb-6">
                    <span class="text-4xl font-black" style="color: #F5F5F5;">$0</span>
                    <span class="text-sm" style="color: #CCCCCC;">/{{ app()->getLocale() === 'en' ? 'forever' : 'siempre' }}</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($isEn ?? false
                        ? ['Directory listing','Basic info','Google Maps integration','Claim & verify ownership','Edit basic info','Customer reviews','Hours & contact','Up to 5 photos']
                        : ['Aparece en el directorio', 'Info básica (nombre, dirección, teléfono)', 'Integración con Google Maps', 'Verificar propiedad del restaurante', 'Editar información básica', 'Reseñas de clientes', 'Horarios y contacto', 'Hasta 5 fotos']
                    as $feature)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm" style="color: #CCCCCC;">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}" class="block w-full text-center py-3 px-6 rounded-xl font-semibold transition-all duration-300 hover:bg-white/10" style="border: 1px solid rgba(255,255,255,0.1); color: #F5F5F5;">
                    {{ app()->getLocale() === 'en' ? 'Start Free' : 'Reclamar Gratis' }}
                </a>
            </div>

            {{-- PREMIUM Plan (Highlighted) --}}
            <div class="rounded-2xl p-8 relative md:-mt-4 md:mb-0" style="background-color: #1A1A1A; border: 2px solid #D4AF37; box-shadow: 0 0 40px rgba(212,175,55,0.1);">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold" style="background-color: #D4AF37; color: #0B0B0B;">{{ app()->getLocale() === 'en' ? 'MOST POPULAR' : 'MAS POPULAR' }}</span>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: #D4AF37;">Premium</h3>
                <div class="mb-2">
                    <span class="text-2xl line-through" style="color: #666;">$39</span>
                    <span class="text-4xl font-black ml-2" style="color: #F5F5F5;">$9.99</span>
                    <span class="text-sm" style="color: #CCCCCC;">/{{ app()->getLocale() === 'en' ? 'first month' : 'primer mes' }}</span>
                </div>
                <p class="text-sm mb-6" style="color: #D4AF37;">{{ app()->getLocale() === 'en' ? 'Then $39/mo. Cancel anytime.' : 'Después $39/mes. Cancela cuando quieras.' }}</p>
                <ul class="space-y-3 mb-8">
                    @foreach($isEn ?? false
                        ? ['Everything in Basic, plus:','Verified "Premium" badge','Digital menu + QR code','Automatic review SMS requests','FAMER Score & recommendations','Unlimited photos','Profile analytics','Highlighted in rankings']
                        : ['Todo lo de Básico PLUS:', 'Badge Verificado "Premium"', 'Menú Digital + QR Code', 'SMS automáticos de solicitud de reseña', 'FAMER Score y recomendaciones', 'Fotos ilimitadas', 'Analytics del perfil', 'Destacado en rankings']
                    as $i => $feature)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm {{ $i === 0 ? 'font-bold' : '' }}" style="color: #F5F5F5;">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}?plan=premium" class="block w-full text-center py-3 px-6 rounded-xl font-bold transition-all duration-300 hover:scale-105" style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 4px 20px rgba(212,175,55,0.25);">
                    {{ app()->getLocale() === 'en' ? 'Start for $9.99' : 'Suscribirse por $9.99' }}
                </a>
                <p class="text-center text-xs mt-3" style="color: #999;">{{ app()->getLocale() === 'en' ? '4 Quarterly coupons (10% off) at MF Group' : '4 Cupones Trimestrales (10% off) en MF Group' }}</p>
            </div>

            {{-- ELITE Plan --}}
            <div class="rounded-2xl p-8" style="background-color: #1A1A1A; border: 1px solid rgba(255,255,255,0.05);">
                <h3 class="text-lg font-bold mb-2" style="color: #F5F5F5;">Elite</h3>
                <div class="mb-6">
                    <span class="text-4xl font-black" style="color: #F5F5F5;">$79</span>
                    <span class="text-sm" style="color: #CCCCCC;">/{{ app()->getLocale() === 'en' ? 'mo' : 'mes' }}</span>
                </div>
                <ul class="space-y-3 mb-8">
                    @foreach($isEn ?? false
                        ? ['Everything in Premium, plus:','#1 position in city rankings','Advanced analytics + reports','Email marketing campaigns','Your own restaurant website','FAMER Awards eligibility','Priority account manager']
                        : ['Todo lo de Premium PLUS:', 'Posición #1 en Rankings', 'Analytics Avanzados + Reportes', 'Campañas de Email Marketing', 'Website Propio del Restaurante', 'Elegible para FAMER Awards', 'Gerente de Cuenta Dedicado']
                    as $i => $feature)
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: #a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm {{ $i === 0 ? 'font-bold' : '' }}" style="color: #CCCCCC;">{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('claim.restaurant') }}?plan=elite" class="block w-full text-center py-3 px-6 rounded-xl font-semibold transition-all duration-300 hover:bg-white/10" style="border: 1px solid rgba(255,255,255,0.1); color: #F5F5F5;">
                    {{ app()->getLocale() === 'en' ? 'Go Elite' : 'Ir a Elite' }}
                </a>
            </div>
        </div>

        <p class="text-center mt-8 text-sm" style="color: #6B7280;">
            {{ app()->getLocale() === 'en' ? '¿Want to see a detailed comparison with all features?' : '¿Quieres ver la comparación detallada con todas las funciones?' }}
            <a href="{{ app()->getLocale() === 'en' ? '/how-famer-works' : '/como-funciona-famer' }}#precios" style="color: #D4AF37; text-decoration: none; margin-left: 4px;">
                {{ app()->getLocale() === 'en' ? 'See full comparison →' : 'Ver comparación completa →' }}
            </a>
        </p>
    </div>
</section>

{{-- ============================================ --}}
{{-- 8. FINAL CTA --}}
{{-- ============================================ --}}
<section class="py-20 md:py-28 relative" style="background: linear-gradient(to bottom, #1A1A1A, #0B0B0B);">
    <div class="absolute inset-0" style="background: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(212,175,55,0.05) 0%, transparent 70%);"></div>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <div class="text-4xl mb-4">🌮</div>
        <h2 class="text-3xl md:text-5xl font-bold mb-6" style="color: #F5F5F5; font-family: 'Playfair Display', Georgia, serif;">
            {{ app()->getLocale() === 'en' ? 'Start Getting More Customers Today' : 'Empieza a Recibir Más Clientes Hoy' }}
        </h2>
        <p class="text-lg mb-10" style="color: #CCCCCC;">
            {{ app()->getLocale() === 'en'
                ? 'Join 26,000+ Mexican restaurant owners already on FAMER. Free to start — no credit card required.'
                : 'Únete a 26,000+ dueños de restaurantes mexicanos que ya están en FAMER. Gratis para empezar — sin tarjeta de crédito.' }}
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
