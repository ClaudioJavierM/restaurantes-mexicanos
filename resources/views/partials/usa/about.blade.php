{{-- Platform Benefits + About Section --}}
<section class="py-20 lg:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Title --}}
        <div class="text-center mb-16">
            <span class="inline-block px-5 py-1.5 bg-[#D4AF37]/10 text-[#D4AF37] rounded-full text-xs font-semibold tracking-[0.2em] uppercase border border-[#D4AF37]/20 mb-6">
                The Platform
            </span>
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-5 tracking-tight">
                Everything Diners Need.<br class="hidden sm:block"> Everything Restaurants Need.
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed">
                {!! __('app.about_famer_description') !!}
            </p>
        </div>

        {{-- Two-Track Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20">

            {{-- For Diners --}}
            <div class="bg-[#0B0B0B] rounded-2xl p-8 lg:p-10 border border-white/5">
                <div class="flex items-center mb-8">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">For Diners</h3>
                </div>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#1F3D2B]/40 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Discover Top Restaurants</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Find the highest-rated Mexican restaurants near you, powered by multi-platform data.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#1F3D2B]/40 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Reserve Tables</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Book your table directly with participating restaurants in just a few taps.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#1F3D2B]/40 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">View Digital Menus</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Browse full menus with photos and prices before you visit.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#1F3D2B]/40 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Find Offers & Trending Spots</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Discover deals, new openings, and the hottest spots in your area.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- For Restaurants --}}
            <div class="bg-[#0B0B0B] rounded-2xl p-8 lg:p-10 border border-white/5">
                <div class="flex items-center mb-8">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">For Restaurants</h3>
                </div>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#8B1E1E]/30 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Claim Your Listing</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Take control of your restaurant profile and keep it accurate and up-to-date.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#8B1E1E]/30 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Get Discovered Faster</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Appear in search results and recommendations across the platform.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#8B1E1E]/30 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Promote with Ease</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Run promotions, feature specials, and attract new customers effortlessly.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-[#8B1E1E]/30 flex items-center justify-center mr-4 mt-0.5">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Upgrade Visibility</h4>
                            <p class="text-gray-500 text-sm leading-relaxed">Premium and Elite tiers boost your ranking and unlock powerful marketing tools.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Section --}}
        <div class="rounded-2xl p-8 lg:p-12" style="background-color: #0B0B0B;">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#D4AF37] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">10K+</div>
                    <div class="text-gray-500 text-sm">{{ __('app.verified_restaurants') }}</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#1F3D2B] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">50</div>
                    <div class="text-gray-500 text-sm">{{ __('app.states_covered') }}</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#8B1E1E] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">5+</div>
                    <div class="text-gray-500 text-sm">Fuentes de Datos</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#F5F5F5]/30 hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">2M+</div>
                    <div class="text-gray-500 text-sm">Resenas Analizadas</div>
                </div>
            </div>

            {{-- FAMER Awards CTA --}}
            <div class="text-center mt-10">
                <a href="/famer-awards" class="inline-flex items-center px-8 py-4 bg-[#D4AF37] text-[#0B0B0B] font-semibold text-sm rounded-xl hover:bg-[#D4AF37]/90 transition-all duration-300 tracking-wide">
                    Ver FAMER Awards {{ now()->year - 1 }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>
</section>
