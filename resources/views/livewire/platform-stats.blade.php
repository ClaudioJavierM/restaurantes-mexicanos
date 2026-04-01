<div class="min-h-screen bg-gradient-to-b from-gray-900 to-gray-800">
    {{-- Hero Section with Live Counter --}}
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-red-900/50 via-gray-900 to-green-900/30"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22 opacity=%220.05%22>🌮</text></svg>'); background-size: 80px;"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 bg-green-500/20 rounded-full text-green-400 text-sm font-medium mb-6">
                    <span class="animate-pulse mr-2">●</span> Live Platform Stats
                </div>
                
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Your Customers Are
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 via-yellow-400 to-green-400">
                        Searching For You
                    </span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-12 max-w-3xl mx-auto">
                    FAMER is the fastest-growing Mexican restaurant directory in the USA. 
                    See the real numbers - updated in real-time.
                </p>

                {{-- Main Stats Counter --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mb-12">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl md:text-5xl font-bold text-white mb-2" 
                             x-data="{ count: 0 }" 
                             x-init="setTimeout(() => { 
                                let target = {{ $stats['total_views'] ?? 0 }};
                                let duration = 2000;
                                let start = 0;
                                let increment = target / (duration / 16);
                                let timer = setInterval(() => {
                                    start += increment;
                                    if (start >= target) { clearInterval(timer); start = target; }
                                    count = Math.floor(start);
                                }, 16);
                             }, 500)">
                            <span x-text="count.toLocaleString()">0</span>
                        </div>
                        <div class="text-gray-400 text-sm">Total Views</div>
                        <div class="text-green-400 text-xs mt-1">↑ Since {{ $stats['start_date'] ?? 'launch' }}</div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl md:text-5xl font-bold text-yellow-400 mb-2"
                             x-data="{ count: 0 }" 
                             x-init="setTimeout(() => { 
                                let target = {{ $stats['daily_avg'] ?? 0 }};
                                let duration = 2000;
                                let start = 0;
                                let increment = target / (duration / 16);
                                let timer = setInterval(() => {
                                    start += increment;
                                    if (start >= target) { clearInterval(timer); start = target; }
                                    count = Math.floor(start);
                                }, 16);
                             }, 700)">
                            <span x-text="count.toLocaleString()">0</span>
                        </div>
                        <div class="text-gray-400 text-sm">Daily Average</div>
                        <div class="text-yellow-400 text-xs mt-1">Views per day</div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl md:text-5xl font-bold text-green-400 mb-2"
                             x-data="{ count: 0 }" 
                             x-init="setTimeout(() => { 
                                let target = {{ $stats['total_restaurants'] ?? 0 }};
                                let duration = 2000;
                                let start = 0;
                                let increment = target / (duration / 16);
                                let timer = setInterval(() => {
                                    start += increment;
                                    if (start >= target) { clearInterval(timer); start = target; }
                                    count = Math.floor(start);
                                }, 16);
                             }, 900)">
                            <span x-text="count.toLocaleString()">0</span>
                        </div>
                        <div class="text-gray-400 text-sm">Restaurants</div>
                        <div class="text-green-400 text-xs mt-1">Listed in directory</div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
                        <div class="text-4xl md:text-5xl font-bold text-red-400 mb-2">
                            +{{ $stats['weekly_growth'] ?? 0 }}%
                        </div>
                        <div class="text-gray-400 text-sm">Weekly Growth</div>
                        <div class="text-red-400 text-xs mt-1">Week over week</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold rounded-xl text-lg transition shadow-lg hover:shadow-red-500/25">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Claim Your Restaurant FREE
                    </a>
                    <a href="{{ route('for-owners') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl text-lg transition border border-white/20">
                        View Premium Plans
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Value Proposition Section --}}
    <div class="bg-gradient-to-b from-gray-800 to-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    The Numbers Don't Lie
                </h2>
                <p class="text-gray-400 text-lg">Real metrics, real value for your restaurant</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Google Ads Comparison --}}
                <div class="bg-gradient-to-br from-blue-900/50 to-blue-800/30 rounded-2xl p-8 border border-blue-500/20">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Google Ads Equivalent</h3>
                    <div class="text-4xl font-bold text-blue-400 mb-2">${{ number_format($stats['google_ads_equivalent'] ?? 0) }}</div>
                    <p class="text-gray-400">Based on $1.50-3.00 average CPC for restaurant keywords</p>
                </div>

                {{-- Annual Projection --}}
                <div class="bg-gradient-to-br from-green-900/50 to-green-800/30 rounded-2xl p-8 border border-green-500/20">
                    <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Annual Projection</h3>
                    <div class="text-4xl font-bold text-green-400 mb-2">{{ number_format($stats['yearly_projection'] ?? 0) }}+</div>
                    <p class="text-gray-400">Projected views this year based on current growth</p>
                </div>

                {{-- Peak Performance --}}
                <div class="bg-gradient-to-br from-yellow-900/50 to-yellow-800/30 rounded-2xl p-8 border border-yellow-500/20">
                    <div class="w-14 h-14 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Peak Day</h3>
                    <div class="text-4xl font-bold text-yellow-400 mb-2">{{ number_format($stats['peak_day_views'] ?? 0) }}</div>
                    <p class="text-gray-400">{{ $stats['peak_day_date'] ?? 'Record day' }} - our busiest day</p>
                </div>
            </div>
        </div>
    </div>

    {{-- State Heat Map Section --}}
    <div class="bg-gray-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Where Are The Views Coming From?
                </h2>
                <p class="text-gray-400 text-lg">Top states by visitor traffic</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- State List --}}
                <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                    <h3 class="text-xl font-bold text-white mb-6">Top 10 States</h3>
                    <div class="space-y-4">
                        @foreach(array_slice($stateStats, 0, 10) as $index => $state)
                            @php
                                $maxViews = $stateStats[0]['views'] ?? 1;
                                $percentage = ($state['views'] / $maxViews) * 100;
                                $colors = ['from-red-500 to-red-600', 'from-green-500 to-green-600', 'from-blue-500 to-blue-600', 'from-yellow-500 to-yellow-600', 'from-purple-500 to-purple-600'];
                                $color = $colors[$index % 5];
                            @endphp
                            <div class="flex items-center gap-4">
                                <div class="w-8 text-gray-500 font-bold">#{{ $index + 1 }}</div>
                                <div class="flex-1">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-white font-medium">{{ $state['state_name'] }}</span>
                                        <span class="text-gray-400">{{ number_format($state['views']) }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r {{ $color }} rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Weekly Growth Chart --}}
                <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                    <h3 class="text-xl font-bold text-white mb-6">Weekly Growth Trend</h3>
                    <div class="h-64 flex items-end justify-between gap-2">
                        @php
                            $maxWeekly = max(array_column($weeklyGrowth, 'views')) ?: 1;
                        @endphp
                        @foreach($weeklyGrowth as $week)
                            @php
                                $height = ($week['views'] / $maxWeekly) * 100;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-2">
                                <div class="w-full bg-gradient-to-t from-green-600 to-green-400 rounded-t-lg transition-all duration-500" 
                                     style="height: {{ max(5, $height) }}%"
                                     title="{{ number_format($week['views']) }} views">
                                </div>
                                <span class="text-xs text-gray-500 truncate w-full text-center">{{ $week['week'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center text-gray-400 text-sm">
                        Last 8 weeks of traffic
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROI Calculator Section --}}
    <div class="bg-gradient-to-b from-gray-900 to-gray-800 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-green-900/30 to-emerald-900/30 rounded-3xl p-8 md:p-12 border border-green-500/20">
                <div class="text-center mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        Simple ROI Math
                    </h2>
                    <p class="text-gray-400 text-lg">How Premium pays for itself</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 text-center">
                    <div class="p-6">
                        <div class="text-5xl font-bold text-white mb-2">$39</div>
                        <div class="text-gray-400">Premium Plan / month</div>
                    </div>
                    <div class="p-6 flex items-center justify-center">
                        <div class="text-4xl text-green-400">÷</div>
                    </div>
                    <div class="p-6">
                        <div class="text-5xl font-bold text-green-400">1</div>
                        <div class="text-gray-400">New customer needed</div>
                    </div>
                </div>

                <div class="mt-8 p-6 bg-white/5 rounded-xl">
                    <p class="text-gray-300 text-center">
                        <span class="text-white font-bold">One new customer per month</span> from FAMER and your subscription pays for itself. 
                        With an average ticket of $30-50, that's <span class="text-green-400 font-bold">instant positive ROI</span>.
                    </p>
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('for-owners') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold rounded-xl text-lg transition shadow-lg">
                        Start Your Free Trial
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Testimonials Section (Placeholder) --}}
    <div class="bg-gray-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    What Restaurant Owners Say
                </h2>
                <p class="text-gray-400 text-lg">Join hundreds of successful Mexican restaurants</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Testimonial 1 --}}
                <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6 italic">"We've seen a 40% increase in new customers since joining FAMER Premium. The investment paid for itself in the first week!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white font-bold">M</div>
                        <div>
                            <div class="text-white font-medium">Maria G.</div>
                            <div class="text-gray-500 text-sm">Taqueria Los Amigos, TX</div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6 italic">"The analytics dashboard shows me exactly where my customers are finding me. Worth every penny of the $39/month."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold">J</div>
                        <div>
                            <div class="text-white font-medium">Jose R.</div>
                            <div class="text-gray-500 text-sm">El Mexicano, CA</div>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="bg-white/5 rounded-2xl p-8 border border-white/10">
                    <div class="flex items-center gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-300 mb-6 italic">"Finally, a directory that understands Mexican restaurants. The QR menu feature alone has transformed how we serve customers."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold">C</div>
                        <div>
                            <div class="text-white font-medium">Carlos M.</div>
                            <div class="text-gray-500 text-sm">Sabor Mexicano, FL</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Final CTA --}}
    <div class="bg-gradient-to-r from-red-700 via-red-600 to-green-700 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">
                Ready to Get More Customers?
            </h2>
            <p class="text-xl text-white/90 mb-8">
                Join {{ number_format($stats['total_restaurants'] ?? 0) }}+ restaurants already listed. 
                Claim yours free today!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('claim.restaurant') }}" class="inline-flex items-center justify-center px-10 py-5 bg-white text-red-600 font-bold rounded-xl text-xl transition shadow-xl hover:shadow-2xl hover:scale-105 transform">
                    <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Claim Your Restaurant FREE
                </a>
            </div>
            <p class="text-white/70 mt-6 text-sm">
                No credit card required • Takes less than 2 minutes • Cancel anytime
            </p>
        </div>
    </div>

    {{-- Live Counter Footer --}}
    <div class="bg-gray-900 py-6 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-2 text-gray-400">
                <span class="animate-pulse text-green-400">●</span>
                <span>{{ number_format($stats['today_views'] ?? 0) }} views today</span>
                <span class="mx-2">•</span>
                <span>{{ number_format($stats['this_month_views'] ?? 0) }} this month</span>
                <span class="mx-2">•</span>
                <span>Updated in real-time</span>
            </div>
        </div>
    </div>
</div>
