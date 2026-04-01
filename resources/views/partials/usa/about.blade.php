{{-- How We Rank + Platform Section --}}
<section class="py-20 lg:py-28" style="background-color: #1A1A1A;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Title --}}
        <div class="text-center mb-16">
            <span class="inline-block px-5 py-1.5 bg-[#D4AF37]/10 text-[#D4AF37] rounded-full text-xs font-semibold tracking-[0.2em] uppercase border border-[#D4AF37]/20 mb-6">
                {{ __('app.how_we_select') }}
            </span>
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-5 tracking-tight">
                Rankings Powered by Real Data
            </h2>
            <p class="text-gray-400 max-w-3xl mx-auto text-lg leading-relaxed">
                We aggregate and analyze reviews from multiple platforms to create the most trusted ranking of Mexican restaurants in the United States.
            </p>
        </div>

        {{-- Review Platforms Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16 max-w-4xl mx-auto">
            <div class="bg-[#0B0B0B] rounded-xl p-5 text-center border border-white/5 hover:border-[#D4AF37]/20 transition-colors">
                <div class="text-2xl font-bold text-white mb-1">Google</div>
                <div class="text-gray-500 text-xs">Reviews & Ratings</div>
            </div>
            <div class="bg-[#0B0B0B] rounded-xl p-5 text-center border border-white/5 hover:border-[#D4AF37]/20 transition-colors">
                <div class="text-2xl font-bold text-white mb-1">Yelp</div>
                <div class="text-gray-500 text-xs">Reviews & Ratings</div>
            </div>
            <div class="bg-[#0B0B0B] rounded-xl p-5 text-center border border-white/5 hover:border-[#D4AF37]/20 transition-colors">
                <div class="text-2xl font-bold text-white mb-1">TripAdvisor</div>
                <div class="text-gray-500 text-xs">Reviews & Ratings</div>
            </div>
            <div class="bg-[#0B0B0B] rounded-xl p-5 text-center border border-white/5 hover:border-[#D4AF37]/20 transition-colors">
                <div class="text-2xl font-bold text-white mb-1">Facebook</div>
                <div class="text-gray-500 text-xs">Reviews & Ratings</div>
            </div>
        </div>

        {{-- How It Works - For Diners --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-20">

            {{-- Left: Our Algorithm --}}
            <div class="bg-[#0B0B0B] rounded-2xl p-8 lg:p-10 border border-white/5">
                <div class="flex items-center mb-8">
                    <div class="w-10 h-10 rounded-xl bg-[#D4AF37]/10 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Our Algorithm</h3>
                </div>
                <div class="space-y-5">
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-[#D4AF37] text-[#0B0B0B] flex items-center justify-center text-xs font-bold mr-4 mt-0.5">1</span>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Average Rating</h4>
                            <p class="text-gray-500 text-sm">Combined rating across Google, Yelp, TripAdvisor, and Facebook.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-[#D4AF37] text-[#0B0B0B] flex items-center justify-center text-xs font-bold mr-4 mt-0.5">2</span>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Total Reviews</h4>
                            <p class="text-gray-500 text-sm">Volume of reviews measures real popularity, not just scores.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-[#D4AF37] text-[#0B0B0B] flex items-center justify-center text-xs font-bold mr-4 mt-0.5">3</span>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Consistency</h4>
                            <p class="text-gray-500 text-sm">Quality consistency across time and platforms matters.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-[#D4AF37] text-[#0B0B0B] flex items-center justify-center text-xs font-bold mr-4 mt-0.5">4</span>
                        <div>
                            <h4 class="text-white font-medium text-sm mb-1">Verification</h4>
                            <p class="text-gray-500 text-sm">Every restaurant is verified as active and operating.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: What You Get --}}
            <div class="bg-[#0B0B0B] rounded-2xl p-8 lg:p-10 border border-white/5">
                <div class="flex items-center mb-8">
                    <div class="w-10 h-10 rounded-xl bg-[#1F3D2B]/40 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">For You</h3>
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
        </div>

        {{-- Stats Section --}}
        <div class="rounded-2xl p-8 lg:p-12" style="background-color: #0B0B0B;">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#D4AF37] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">{{ number_format(floor(($stats['total_restaurants'] ?? 24000) / 1000) * 1000) }}+</div>
                    <div class="text-gray-500 text-sm">{{ __('app.verified_restaurants') }}</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#1F3D2B] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">50</div>
                    <div class="text-gray-500 text-sm">{{ __('app.states_covered') }}</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#8B1E1E] hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">8+</div>
                    <div class="text-gray-500 text-sm">Review Platforms</div>
                </div>
                <div class="bg-[#1A1A1A] rounded-xl p-6 text-center border-t-2 border-[#F5F5F5]/30 hover:scale-[1.02] transition-transform duration-300">
                    <div class="text-3xl lg:text-4xl font-bold text-white mb-1">2M+</div>
                    <div class="text-gray-500 text-sm">Reviews Analyzed</div>
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
