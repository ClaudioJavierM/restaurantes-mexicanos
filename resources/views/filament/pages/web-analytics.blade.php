<x-filament-panels::page>
    @if(!$isConfigured)
        <div class="flex items-center justify-center min-h-[50vh]">
            <div class="text-center max-w-xl">
                <div class="w-16 h-16 mx-auto rounded-full bg-yellow-500/10 flex items-center justify-center mb-6">
                    <x-heroicon-o-chart-bar class="w-8 h-8 text-yellow-500" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Google Analytics no configurado</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    Configura un Service Account de Google con acceso a GA4 y Search Console para ver datos reales.
                </p>
                <div class="text-left bg-gray-50 dark:bg-white/5 rounded-xl p-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p><strong>1.</strong> Crea un Service Account en Google Cloud Console</p>
                    <p><strong>2.</strong> Descarga el JSON y coloca en <code>storage/app/gsc-service-account.json</code></p>
                    <p><strong>3.</strong> Configura en .env: <code>GA4_PROPERTY_ID=tu_property_id</code></p>
                    <p><strong>4.</strong> Agrega el email del Service Account como Viewer en GA4 y Search Console</p>
                </div>
            </div>
        </div>
    @else
        {{-- Period Selector --}}
        <div class="flex gap-2 mb-6">
            @foreach([7 => '7 dias', 14 => '14 dias', 30 => '30 dias', 90 => '90 dias'] as $d => $label)
                <button wire:click="setDays({{ $d }})"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                        {{ $days === $d ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Overview Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Usuarios</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($overview['active_users'] ?? 0) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Sesiones</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($overview['sessions'] ?? 0) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Vistas de pagina</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($overview['pageviews'] ?? 0) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tasa de rebote</p>
                <p class="text-2xl font-bold {{ ($overview['bounce_rate'] ?? 0) > 60 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} mt-1">{{ $overview['bounce_rate'] ?? 0 }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Duracion promedio</p>
                @php $dur = $overview['avg_session_duration'] ?? 0; $min = floor($dur/60); $sec = $dur % 60; @endphp
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $min }}:{{ str_pad($sec, 2, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nuevos usuarios</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($overview['new_users'] ?? 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Daily Traffic Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Trafico diario</h3>
                @if(!empty($dailyTraffic))
                    <div class="space-y-1 max-h-[300px] overflow-y-auto">
                        @php $maxSessions = max(array_column($dailyTraffic, 'sessions') ?: [1]); @endphp
                        @foreach($dailyTraffic as $day)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-20 text-gray-500 dark:text-gray-400 flex-shrink-0">{{ \Carbon\Carbon::parse($day['date'])->format('d M') }}</span>
                                <div class="flex-1 h-5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width:{{ ($day['sessions'] / max($maxSessions, 1)) * 100 }}%"></div>
                                </div>
                                <span class="w-16 text-right text-gray-900 dark:text-white font-medium">{{ number_format($day['sessions']) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Sin datos disponibles</p>
                @endif
            </div>

            {{-- Traffic Sources --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Fuentes de trafico</h3>
                @if(!empty($trafficSources))
                    @php $totalSessions = max(array_sum(array_column($trafficSources, 'sessions')), 1); @endphp
                    <div class="space-y-3">
                        @foreach($trafficSources as $source)
                            @php $pct = round(($source['sessions'] / $totalSessions) * 100, 1); @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $source['channel'] }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ number_format($source['sessions']) }} ({{ $pct }}%)</span>
                                </div>
                                <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ match(true) {
                                        str_contains(strtolower($source['channel']), 'organic') => 'bg-green-500',
                                        str_contains(strtolower($source['channel']), 'direct') => 'bg-blue-500',
                                        str_contains(strtolower($source['channel']), 'social') => 'bg-pink-500',
                                        str_contains(strtolower($source['channel']), 'referral') => 'bg-purple-500',
                                        str_contains(strtolower($source['channel']), 'paid') => 'bg-orange-500',
                                        default => 'bg-gray-400',
                                    } }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Sin datos disponibles</p>
                @endif
            </div>
        </div>

        {{-- Top Pages --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Paginas mas visitadas</h3>
            @if(!empty($topPages))
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Pagina</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Vistas</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Usuarios</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Duracion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($topPages as $page)
                                <tr>
                                    <td class="py-2 text-sm text-gray-900 dark:text-white max-w-md truncate">{{ $page['path'] }}</td>
                                    <td class="py-2 text-sm text-right text-gray-900 dark:text-white font-medium">{{ number_format($page['pageviews']) }}</td>
                                    <td class="py-2 text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($page['users']) }}</td>
                                    <td class="py-2 text-sm text-right text-gray-500 dark:text-gray-400">{{ floor($page['avg_duration']/60) }}:{{ str_pad($page['avg_duration'] % 60, 2, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Sin datos disponibles</p>
            @endif
        </div>

        {{-- Search Console Keywords --}}
        @if(!empty($searchConsole))
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top Keywords (Search Console)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Keyword</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Clicks</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Impresiones</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">CTR</th>
                                <th class="text-right py-2 text-sm font-medium text-gray-500 dark:text-gray-400">Posicion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach(array_slice($searchConsole, 0, 30) as $row)
                                <tr>
                                    <td class="py-2 text-sm text-gray-900 dark:text-white">{{ $row['keys'][0] ?? '' }}</td>
                                    <td class="py-2 text-sm text-right text-blue-600 dark:text-blue-400 font-medium">{{ number_format($row['clicks'] ?? 0) }}</td>
                                    <td class="py-2 text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($row['impressions'] ?? 0) }}</td>
                                    <td class="py-2 text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format(($row['ctr'] ?? 0) * 100, 1) }}%</td>
                                    <td class="py-2 text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($row['position'] ?? 0, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</x-filament-panels::page>
