<div style="background-color: #0B0B0B; min-height: 100vh;">

    {{-- Gold glow at top --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] pointer-events-none" style="background: radial-gradient(ellipse at center, rgba(212,175,55,0.06) 0%, transparent 70%); z-index: 0;"></div>

    {{-- ─── HERO ─── --}}
    <section class="relative pt-20 pb-12 px-4 text-center">
        {{-- Badge --}}
        <div class="inline-flex items-center px-5 py-2 rounded-full border border-[#D4AF37]/40 bg-[#D4AF37]/10 backdrop-blur-sm mb-8">
            <svg class="w-4 h-4 mr-2 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span style="color: #D4AF37;" class="text-sm font-semibold tracking-wide">Sin contratos · Cancela cuando quieras</span>
        </div>

        <h1 class="font-display text-4xl md:text-6xl font-black mb-5 leading-tight tracking-tight" style="color: #F5F5F5;">
            Planes simples.<br>
            <span style="color: #D4AF37;">Sin sorpresas.</span>
        </h1>

        <p class="text-lg md:text-xl max-w-2xl mx-auto leading-relaxed" style="color: #CCCCCC;">
            Elige el plan perfecto para tu restaurante. Cancela cuando quieras.
        </p>
    </section>

    {{-- ─── PRICING CARDS ─── --}}
    <section class="relative max-w-6xl mx-auto px-4 pb-16">
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-stretch">

            {{-- ── FREE ── --}}
            <div class="flex-1 flex flex-col rounded-2xl p-8" style="background-color: #1A1A1A; border: 1px solid #2A2A2A; max-width: 380px; margin: 0 auto; width: 100%;">
                <div class="mb-6">
                    <div class="text-sm font-semibold tracking-widest uppercase mb-3" style="color: #9CA3AF;">Free</div>
                    <div class="flex items-end gap-1 mb-2">
                        <span class="text-5xl font-black" style="color: #F5F5F5;">$0</span>
                        <span class="text-lg mb-2" style="color: #9CA3AF;">/mes</span>
                    </div>
                    <p class="text-sm" style="color: #9CA3AF;">Para empezar sin costo</p>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Aparece en el directorio</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Info básica verificada</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Integración Google Maps</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Badge de propietario verificado</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #EF4444;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        <span style="color: #9CA3AF;">Prioridad en búsquedas</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #EF4444;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        <span style="color: #9CA3AF;">Analytics avanzados</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #EF4444;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        <span style="color: #9CA3AF;">Menú digital QR</span>
                    </li>
                </ul>

                <a href="/claim"
                   class="block w-full text-center py-4 rounded-xl font-bold text-base transition-all hover:opacity-80"
                   style="border: 2px solid #D4AF37; color: #D4AF37; background: transparent;">
                    Reclamar Gratis
                </a>
            </div>

            {{-- ── PREMIUM (highlighted) ── --}}
            <div class="flex-1 flex flex-col rounded-2xl p-8 relative" style="background-color: #1A1A1A; border: 2px solid #D4AF37; max-width: 380px; margin: 0 auto; width: 100%; box-shadow: 0 0 40px rgba(212,175,55,0.15);">
                {{-- Badge --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase" style="background-color: #D4AF37; color: #0B0B0B;">MÁS POPULAR</span>
                </div>

                <div class="mb-6">
                    <div class="text-sm font-semibold tracking-widest uppercase mb-3" style="color: #D4AF37;">Premium</div>
                    <div class="flex items-end gap-2 mb-1">
                        <span class="text-2xl line-through font-bold" style="color: #9CA3AF;">$39</span>
                        <span class="text-5xl font-black" style="color: #F5F5F5;">$9.99</span>
                        <span class="text-lg mb-2" style="color: #9CA3AF;">/mes</span>
                    </div>
                    <p class="text-sm" style="color: #D4AF37;">Primer mes · luego $39/mes</p>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #D4AF37;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;"><strong style="color: #F5F5F5;">Todo en Free</strong> +</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Badge Destacado</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Top 3 búsquedas locales</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Menú Digital + QR Code</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Sistema de Reservaciones</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Dashboard Analytics</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Chatbot IA 24/7</span>
                    </li>
                </ul>

                <a href="/claim"
                   class="block w-full text-center py-4 rounded-xl font-bold text-base transition-all hover:opacity-90 active:scale-[0.98]"
                   style="background-color: #D4AF37; color: #0B0B0B; box-shadow: 0 8px 24px rgba(212,175,55,0.25);">
                    Comenzar por $9.99
                </a>
            </div>

            {{-- ── ELITE ── --}}
            <div class="flex-1 flex flex-col rounded-2xl p-8 relative" style="background-color: #1A1A1A; border: 1px solid rgba(212,175,55,0.4); max-width: 380px; margin: 0 auto; width: 100%;">
                {{-- Badge --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                    <span class="px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase" style="background: linear-gradient(135deg, #D4AF37, #a8892a); color: #0B0B0B;">ELITE</span>
                </div>

                <div class="mb-6">
                    <div class="text-sm font-semibold tracking-widest uppercase mb-3" style="color: #D4AF37; opacity: 0.8;">Elite</div>
                    <div class="flex items-end gap-1 mb-1">
                        <span class="text-5xl font-black" style="color: #F5F5F5;">$79</span>
                        <span class="text-lg mb-2" style="color: #9CA3AF;">/mes</span>
                    </div>
                    <p class="text-sm font-semibold" style="color: #4ADE80;">30 días GRATIS · luego $79/mes</p>
                </div>

                <ul class="space-y-3 flex-1 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #D4AF37;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;"><strong style="color: #F5F5F5;">Todo en Premium</strong> +</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">App Móvil White Label</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Website Builder</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Posición #1 en ciudad</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Account Manager</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Fotografía Profesional trimestral</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color: #4ADE80;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span style="color: #CCCCCC;">Cobertura Medios y PR</span>
                    </li>
                </ul>

                <a href="/claim"
                   class="block w-full text-center py-4 rounded-xl font-bold text-base transition-all hover:opacity-80"
                   style="border: 2px solid #D4AF37; color: #D4AF37; background: transparent;">
                    Comenzar Gratis
                </a>
            </div>

        </div>
    </section>

    {{-- ─── TRUST ROW ─── --}}
    <section class="py-8 px-4 text-center" style="border-top: 1px solid #2A2A2A; border-bottom: 1px solid #2A2A2A;">
        <div class="flex flex-wrap justify-center gap-8 md:gap-16">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="font-semibold" style="color: #F5F5F5;">30,000+ Restaurantes</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="font-semibold" style="color: #F5F5F5;">50 Estados</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span class="font-semibold" style="color: #F5F5F5;">Verificados con Google</span>
            </div>
        </div>
    </section>

    {{-- ─── FAQ ─── --}}
    <section class="max-w-3xl mx-auto px-4 py-16">
        <h2 class="text-3xl font-black text-center mb-10" style="color: #F5F5F5;">Preguntas frecuentes</h2>

        <div class="space-y-3" x-data="{ open: null }">

            {{-- Q1 --}}
            <div class="rounded-xl overflow-hidden" style="background-color: #1A1A1A; border: 1px solid #2A2A2A;">
                <button @click="open = open === 1 ? null : 1"
                        class="w-full flex items-center justify-between px-6 py-5 text-left transition-colors hover:bg-white/5">
                    <span class="font-semibold text-base" style="color: #F5F5F5;">¿Puedo cancelar cuando quiera?</span>
                    <svg class="w-5 h-5 transition-transform duration-200 flex-shrink-0 ml-4"
                         style="color: #D4AF37;"
                         :class="open === 1 ? 'rotate-45' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-5">
                    <p style="color: #CCCCCC;">Sí, sin contratos ni penalizaciones. Puedes cancelar tu suscripción en cualquier momento desde tu panel de propietario.</p>
                </div>
            </div>

            {{-- Q2 --}}
            <div class="rounded-xl overflow-hidden" style="background-color: #1A1A1A; border: 1px solid #2A2A2A;">
                <button @click="open = open === 2 ? null : 2"
                        class="w-full flex items-center justify-between px-6 py-5 text-left transition-colors hover:bg-white/5">
                    <span class="font-semibold text-base" style="color: #F5F5F5;">¿Cómo funciona el trial Elite de 30 días?</span>
                    <svg class="w-5 h-5 transition-transform duration-200 flex-shrink-0 ml-4"
                         style="color: #D4AF37;"
                         :class="open === 2 ? 'rotate-45' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-5">
                    <p style="color: #CCCCCC;">Accedes a todas las funciones Elite gratis durante 30 días. Después, se cobra $79/mes automáticamente. Cancela antes del día 30 si no quedas satisfecho y no te cobramos nada.</p>
                </div>
            </div>

            {{-- Q3 --}}
            <div class="rounded-xl overflow-hidden" style="background-color: #1A1A1A; border: 1px solid #2A2A2A;">
                <button @click="open = open === 3 ? null : 3"
                        class="w-full flex items-center justify-between px-6 py-5 text-left transition-colors hover:bg-white/5">
                    <span class="font-semibold text-base" style="color: #F5F5F5;">¿Aceptan tarjetas de crédito mexicanas?</span>
                    <svg class="w-5 h-5 transition-transform duration-200 flex-shrink-0 ml-4"
                         style="color: #D4AF37;"
                         :class="open === 3 ? 'rotate-45' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-5">
                    <p style="color: #CCCCCC;">Sí, aceptamos todas las tarjetas Visa, Mastercard y American Express, incluyendo tarjetas emitidas en México.</p>
                </div>
            </div>

            {{-- Q4 --}}
            <div class="rounded-xl overflow-hidden" style="background-color: #1A1A1A; border: 1px solid #2A2A2A;">
                <button @click="open = open === 4 ? null : 4"
                        class="w-full flex items-center justify-between px-6 py-5 text-left transition-colors hover:bg-white/5">
                    <span class="font-semibold text-base" style="color: #F5F5F5;">¿Mi restaurante ya está en el directorio?</span>
                    <svg class="w-5 h-5 transition-transform duration-200 flex-shrink-0 ml-4"
                         style="color: #D4AF37;"
                         :class="open === 4 ? 'rotate-45' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-5">
                    <p style="color: #CCCCCC;">Con más de 30,000 restaurantes listados, probablemente sí. Búscalo en <a href="/claim" style="color: #D4AF37; text-decoration: underline;">/claim</a> y reclamalo gratis hoy.</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ─── BOTTOM CTA ─── --}}
    <section class="pb-20 px-4 text-center">
        <p class="text-base mb-6" style="color: #9CA3AF;">¿Tienes preguntas? <a href="/contact" style="color: #D4AF37; text-decoration: underline;">Contáctanos</a></p>
    </section>

</div>
