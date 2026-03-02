{{-- Top States Section --}}
<section class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-2 bg-green-600 text-white rounded-full text-sm font-bold mb-4">
                {{ __('app.explore_by_state') }}
            </span>
            <h2 class="text-3xl md:text-4xl font-display font-black text-gray-900 mb-4">
                {{ __('app.discover_by_state') }}
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                {{ __('app.find_best_restaurants_state') }}
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($states->sortByDesc("restaurants_count")->take(10) ?? [] as $state)
                <a href="/restaurantes?state={{ $state->name }}" 
                   class="group bg-white rounded-xl p-6 text-center hover:shadow-xl transition-all border-2 border-transparent hover:border-green-500 transform hover:-translate-y-1">
                    <div class="text-4xl mb-3">
                        @php
                            $stateEmojis = [
                                'Texas' => '🤠',
                                'California' => '☀️',
                                'Florida' => '🌴',
                                'Illinois' => '🏙️',
                                'Arizona' => '🌵',
                                'New York' => '��',
                                'Nevada' => '🎰',
                                'Colorado' => '⛰️',
                                'Georgia' => '🍑',
                                'New Mexico' => '🌶️',
                            ];
                        @endphp
                        {{ $stateEmojis[$state->name] ?? '📍' }}
                    </div>
                    <div class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">{{ $state->name }}</div>
                    <div class="text-sm text-gray-500">{{ number_format($state->restaurants_count) }} {{ __('app.restaurantes_text') }}</div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="/guia" class="inline-flex items-center px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition-all">
                {{ __('app.view_all_states') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
