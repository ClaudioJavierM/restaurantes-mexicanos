{{-- Hero Section for USA - Same style as Mexico --}}
<div class="relative overflow-hidden">
    {{-- Dark gradient background --}}
    <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-red-900 to-green-900"></div>
    <div class="absolute inset-0 bg-black/30"></div>
    
    {{-- Mexican flag accent bars --}}
    <div class="absolute top-0 left-0 w-2 h-full bg-green-600"></div>
    <div class="absolute top-0 right-0 w-2 h-full bg-red-600"></div>
    
    {{-- Pattern overlay --}}
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center px-6 py-3 rounded-full bg-yellow-500/20 backdrop-blur-sm border border-yellow-400/50 text-yellow-300 mb-8">
                <svg class="w-6 h-6 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-bold text-lg">{{ __('app.hero_badge') }}</span>
            </div>

            {{-- Main Title --}}
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-black text-white mb-6 leading-tight drop-shadow-lg">
                {{ __('app.hero_title_1') }}<br>
                <span class="text-yellow-400">
                    {{ __('app.hero_title_2') }}
                </span><br>
                <span class="text-3xl md:text-5xl text-white">{{ __('app.hero_title_3') }}</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-4xl mx-auto font-medium leading-relaxed">
                {{ __('app.hero_subtitle') }}
            </p>

            {{-- Search Box --}}
            <div class="max-w-4xl mx-auto mb-10">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                    <form wire:submit.prevent="searchRestaurants" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1 relative">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" wire:model="search" placeholder="{{ __('app.search_placeholder') }}"
                                   class="w-full pl-12 pr-4 py-4 bg-white rounded-xl text-gray-900 font-medium placeholder-gray-400 focus:ring-4 focus:ring-yellow-500/50 border-0">
                        </div>
                        <select wire:model="selectedState" class="px-4 py-4 bg-white rounded-xl text-gray-900 font-medium focus:ring-4 focus:ring-yellow-500/50 border-0">
                            <option value="">{{ __('app.all_states') }}</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-8 py-4 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold rounded-xl transition-all shadow-lg">
                            {{ __('app.search_button') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-8 mb-10">
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">{{ number_format($stats['total_restaurants'] ?? 0) }}</div>
                    <div class="text-gray-300 text-sm font-medium">{{ __('app.stats_restaurants') }}</div>
                </div>
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">{{ $stats['total_states'] ?? 0 }}</div>
                    <div class="text-gray-300 text-sm font-medium">{{ __('app.stats_states') }}</div>
                </div>
                <div class="text-center bg-white/10 rounded-xl px-6 py-4 backdrop-blur-sm">
                    <div class="text-4xl md:text-5xl font-black text-yellow-400">5+</div>
                    <div class="text-gray-300 text-sm font-medium">{{ __('app.review_sources') }}</div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/restaurantes" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-900 font-bold text-lg rounded-xl hover:bg-yellow-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('app.explore_restaurants') }}
                </a>
                <a href="/restaurantes?sort=rating" class="inline-flex items-center justify-center px-8 py-4 bg-yellow-500 text-gray-900 font-bold text-lg rounded-xl hover:bg-yellow-400 transition-all shadow-lg">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    {{ __('app.view_top_rated') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Platform Logos --}}
<div class="bg-gray-100 py-8 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <p class="text-center text-gray-500 text-sm mb-4">{{ __('app.reviews_from_platforms') }}</p>
        <div class="flex flex-wrap justify-center items-center gap-8">
            <span class="text-2xl font-bold text-gray-700">Google</span>
            <span class="text-2xl font-bold text-red-600">Yelp</span>
            <span class="text-2xl font-bold text-green-600">TripAdvisor</span>
            <span class="text-2xl font-bold text-blue-600">Facebook</span>
            <span class="text-2xl font-bold text-purple-600">Foursquare</span>
            <span class="text-2xl font-bold text-gray-800">Apple Maps</span>
            <span class="text-2xl font-bold text-green-500">Uber Eats</span>
            <span class="text-2xl font-bold text-orange-600">OpenTable</span>
        </div>
    </div>
</div>
