{{-- Hero Section for USA - Premium Dark Design --}}
<div class="relative overflow-hidden" style="background-color: #0B0B0B;">
    {{-- Subtle geometric pattern overlay --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 viewBox=%220 0 40 40%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M20 0L40 20L20 40L0 20Z%22 fill=%22none%22 stroke=%22%23D4AF37%22 stroke-width=%220.5%22/%3E%3C/svg%3E'); background-size: 40px 40px;"></div>

    {{-- Radial gold glow at top center --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] opacity-[0.07]" style="background: radial-gradient(ellipse at center, #D4AF37 0%, transparent 70%);"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 lg:py-28">
        <div class="text-center">
            {{-- Badge pill --}}
            <div class="inline-flex items-center px-5 py-2 rounded-full border border-[#D4AF37]/40 bg-[#D4AF37]/10 backdrop-blur-sm mb-8">
                <svg class="w-4 h-4 mr-2 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-[#D4AF37] text-sm font-semibold tracking-wide">{{ number_format($stats['total_restaurants'] ?? 12000) }}+ Restaurants Across 50 States</span>
            </div>

            {{-- Main Headline --}}
            <h1 class="font-display text-4xl md:text-6xl lg:text-7xl font-black text-[#F5F5F5] mb-5 leading-[1.1] tracking-tight">
                {{ __('app.hero_title_1') }}<br>
                <span class="text-[#D4AF37]">{{ __('app.hero_title_2') }}</span>
            </h1>

            {{-- Subtitle --}}
            <p class="text-lg md:text-xl text-[#CCCCCC] mb-10 max-w-2xl mx-auto leading-relaxed">
                Top-rated, verified, and curated for people who love real Mexican food.
            </p>

            {{-- Search Bar --}}
            <div class="max-w-3xl mx-auto mb-8">
                <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-3 md:p-4 border border-white/10 shadow-2xl shadow-black/40">
                    <form wire:submit.prevent="searchRestaurants" class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" wire:model="search" placeholder="{{ __('app.search_placeholder') }}"
                                   class="w-full pl-12 pr-4 py-4 bg-white/10 backdrop-blur rounded-xl text-white font-medium placeholder-gray-400 focus:ring-2 focus:ring-[#D4AF37]/50 focus:bg-white/15 border border-white/10 transition-all">
                        </div>
                        <select wire:model="selectedState" class="px-4 py-4 bg-white/10 backdrop-blur rounded-xl text-white font-medium focus:ring-2 focus:ring-[#D4AF37]/50 border border-white/10 transition-all appearance-none cursor-pointer">
                            <option value="" class="bg-[#1A1A1A] text-white">{{ __('app.all_states') }}</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}" class="bg-[#1A1A1A] text-white">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-8 py-4 bg-[#D4AF37] hover:bg-[#c9a432] text-[#0B0B0B] font-bold rounded-xl transition-all shadow-lg shadow-[#D4AF37]/20 hover:shadow-[#D4AF37]/30 active:scale-[0.98]">
                            {{ __('app.search_button') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- City Shortcuts --}}
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <span class="text-gray-500 text-sm mr-1 self-center">Popular:</span>
                @foreach(['Dallas', 'Houston', 'Los Angeles', 'Chicago', 'Miami'] as $city)
                    <a href="/restaurantes?search={{ urlencode($city) }}" class="px-4 py-1.5 rounded-full bg-white/[0.06] text-gray-300 text-sm font-medium hover:text-[#D4AF37] hover:bg-white/10 border border-white/[0.06] hover:border-[#D4AF37]/30 transition-all">
                        {{ $city }}
                    </a>
                @endforeach
            </div>

            {{-- Stats Row --}}
            <div class="flex flex-wrap justify-center gap-10 md:gap-16 mb-12">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black text-[#D4AF37] tracking-tight">{{ number_format($stats['total_restaurants'] ?? 0) }}</div>
                    <div class="text-gray-400 text-sm font-medium mt-1">{{ __('app.stats_restaurants') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black text-[#D4AF37] tracking-tight">{{ $stats['total_states'] ?? 0 }}</div>
                    <div class="text-gray-400 text-sm font-medium mt-1">{{ __('app.stats_states') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-black text-[#D4AF37] tracking-tight">5+</div>
                    <div class="text-gray-400 text-sm font-medium mt-1">{{ __('app.review_sources') }}</div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/restaurantes" class="inline-flex items-center justify-center px-8 py-4 bg-[#D4AF37] text-[#0B0B0B] font-bold text-lg rounded-xl hover:bg-[#c9a432] transition-all shadow-lg shadow-[#D4AF37]/20 hover:shadow-[#D4AF37]/30 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('app.explore_restaurants') }}
                </a>
                <a href="/claim" class="inline-flex items-center justify-center px-8 py-4 border-2 border-[#D4AF37] text-[#D4AF37] font-bold text-lg rounded-xl hover:bg-[#D4AF37]/10 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Claim Your Restaurant
                </a>
            </div>
        </div>
    </div>
</div>
