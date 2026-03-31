<div>
@if($isMexico)
    @include("partials.mexico.hero")
    @include("partials.mexico.top100")
    @include("partials.mexico.top-states")
    @include("partials.mexico.about")
@else
    @include("partials.usa.hero")

    {{-- Trust / Authority Strip --}}
    <section class="py-6 border-t border-b border-[#D4AF37]/10" style="background-color: #1A1A1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">12,000+</div>
                        <div class="text-gray-400 text-xs font-medium">Restaurants</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">2M+</div>
                        <div class="text-gray-400 text-xs font-medium">Annual Visits</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">Top 10</div>
                        <div class="text-gray-400 text-xs font-medium">Rankings by City</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">Verified</div>
                        <div class="text-gray-400 text-xs font-medium">Listings</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include("partials.usa.top-restaurants")

    {{-- Owner Conversion Block --}}
    <section class="py-16 md:py-20" style="background-color: #1A1A1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-10 lg:gap-16">
                {{-- Left: Copy --}}
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-display font-black text-[#F5F5F5] mb-3 leading-tight">
                        Own a Mexican Restaurant?
                    </h2>
                    <p class="text-[#CCCCCC] text-lg mb-8">
                        Your customers are already searching for you.
                    </p>
                    <ul class="space-y-4 mb-8 inline-block text-left">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#D4AF37] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-300">Be found by more customers searching nearby</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#D4AF37] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-300">Appear in top city rankings</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#D4AF37] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-300">Add menus, photos, and promotions</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-[#D4AF37] mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-300">Turn visibility into real sales</span>
                        </li>
                    </ul>
                </div>
                {{-- Right: CTA --}}
                <div class="shrink-0">
                    <a href="/claim" class="inline-flex items-center justify-center px-10 py-5 bg-[#D4AF37] text-[#0B0B0B] font-bold text-lg rounded-xl hover:bg-[#c9a432] transition-all shadow-lg shadow-[#D4AF37]/20 hover:shadow-[#D4AF37]/30 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Claim Your Restaurant
                    </a>
                    <p class="text-gray-500 text-sm mt-3 text-center">Free to get started</p>
                </div>
            </div>
        </div>
    </section>

    @include("partials.usa.categories")
    @include("partials.usa.top-states")
    @include("partials.usa.about")

    {{-- Final Double CTA --}}
    <section class="py-16 md:py-20" style="background-color: #0B0B0B;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8">
                {{-- Left: Visitors --}}
                <div class="rounded-2xl p-8 md:p-10 border border-white/10 text-center" style="background-color: #1A1A1A;">
                    <svg class="w-10 h-10 text-[#D4AF37] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="text-2xl font-display font-bold text-[#F5F5F5] mb-2">Looking for the best Mexican restaurant?</h3>
                    <p class="text-[#CCCCCC] mb-6">Browse top-rated spots near you, curated by real reviews.</p>
                    <a href="/restaurantes" class="inline-flex items-center justify-center px-8 py-4 bg-[#D4AF37] text-[#0B0B0B] font-bold rounded-xl hover:bg-[#c9a432] transition-all shadow-lg shadow-[#D4AF37]/20 w-full sm:w-auto">
                        Explore Restaurants
                    </a>
                </div>
                {{-- Right: Owners --}}
                <div class="rounded-2xl p-8 md:p-10 border border-[#D4AF37]/20 text-center" style="background-color: #1A1A1A;">
                    <svg class="w-10 h-10 text-[#D4AF37] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-2xl font-display font-bold text-[#F5F5F5] mb-2">Own a restaurant? Get discovered today.</h3>
                    <p class="text-[#CCCCCC] mb-6">Claim your listing and reach thousands of hungry customers.</p>
                    <a href="/claim" class="inline-flex items-center justify-center px-8 py-4 border-2 border-[#D4AF37] text-[#D4AF37] font-bold rounded-xl hover:bg-[#D4AF37]/10 transition-all w-full sm:w-auto">
                        Claim Your Listing
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif
</div>
