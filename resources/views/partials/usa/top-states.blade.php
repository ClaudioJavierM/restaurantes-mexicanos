{{-- Top States Section --}}
<section class="py-20 lg:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-5 py-1.5 bg-[#D4AF37]/10 text-[#D4AF37] rounded-full text-xs font-semibold tracking-[0.2em] uppercase border border-[#D4AF37]/20 mb-6">
                {{ __('app.explore_by_state') }}
            </span>
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-5 tracking-tight">
                {{ __('app.discover_by_state') }}
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed">
                {{ __('app.find_best_restaurants_state') }}
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($states->sortByDesc("restaurants_count")->take(10) ?? [] as $state)
                <a href="/restaurantes?state={{ $state->name }}"
                   class="group bg-[#1A1A1A] rounded-xl p-6 text-center border border-white/5 hover:border-[#D4AF37]/30 transition-all duration-300 hover:-translate-y-1">
                    <div class="text-4xl mb-3">
                        @php
                            $stateEmojis = [
                                'Texas' => '🤠',
                                'California' => '☀️',
                                'Florida' => '🌴',
                                'Illinois' => '🏙️',
                                'Arizona' => '🌵',
                                'New York' => '🗽',
                                'Nevada' => '🎰',
                                'Colorado' => '⛰️',
                                'Georgia' => '🍑',
                                'New Mexico' => '🌶️',
                            ];
                        @endphp
                        {{ $stateEmojis[$state->name] ?? '📍' }}
                    </div>
                    <div class="font-semibold text-white group-hover:text-[#D4AF37] transition-colors duration-300">{{ $state->name }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ number_format($state->restaurants_count) }} {{ __('app.restaurantes_text') }}</div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="/guia" class="inline-flex items-center px-7 py-3.5 bg-[#2A2A2A] text-white font-medium rounded-xl hover:bg-[#D4AF37] hover:text-[#0B0B0B] transition-all duration-300 text-sm tracking-wide border border-white/5 hover:border-[#D4AF37]">
                {{ __('app.view_all_states') }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
