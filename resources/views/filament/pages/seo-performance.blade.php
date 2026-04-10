<x-filament-panels::page>

    {{-- ================================================================
         BANNER: Sin datos GSC configurados
    ================================================================ --}}
    @if (! $this->hasData)
        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 p-6">
            <div class="flex items-start gap-4">
                <x-heroicon-o-magnifying-glass-circle class="h-8 w-8 text-amber-400 shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-base font-semibold text-amber-300 mb-1">
                        Conecta Google Search Console para ver datos reales
                    </h3>
                    <p class="text-sm text-amber-200/80 mb-3">
                        Esta página muestra keywords, impressiones, CTR y posiciones desde GSC.
                        Actualmente la tabla <code class="bg-amber-900/40 px-1 rounded text-xs">gsc_performance</code> está vacía.
                    </p>
                    <ol class="text-sm text-amber-200/70 space-y-1 list-decimal list-inside">
                        <li>Crea un Service Account en Google Cloud Console con acceso a Search Console API</li>
                        <li>Agrega el email del Service Account como usuario en Google Search Console (permiso: Lector)</li>
                        <li>Agrega al <code class="bg-amber-900/40 px-1 rounded text-xs">.env</code>:
                            <br>
                            <code class="block mt-1 bg-amber-900/30 px-2 py-1 rounded text-xs font-mono">
                                GOOGLE_SERVICE_ACCOUNT_JSON={"type":"service_account","project_id":"..."}
                            </code>
                        </li>
                        <li>Ejecuta: <code class="bg-amber-900/40 px-1 rounded text-xs">php artisan famer:sync-gsc --days=30</code></li>
                    </ol>
                </div>
            </div>
        </div>

    @else
        {{-- ============================================================
             MÉTRICAS PRINCIPALES
        ============================================================ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Clicks --}}
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Clicks (30d)</p>
                <p class="text-3xl font-bold text-white">{{ number_format($this->totalClicks) }}</p>
                <p class="text-xs text-gray-500 mt-1">desde Google Search</p>
            </div>

            {{-- Impressiones --}}
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Impressiones (30d)</p>
                <p class="text-3xl font-bold text-white">{{ number_format($this->totalImpressions) }}</p>
                <p class="text-xs text-gray-500 mt-1">veces mostrado en Google</p>
            </div>

            {{-- CTR Promedio --}}
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">CTR Promedio</p>
                <p class="text-3xl font-bold
                    @if ($this->avgCtr >= 5) text-green-400
                    @elseif ($this->avgCtr >= 2) text-amber-400
                    @else text-red-400 @endif">
                    {{ $this->avgCtr }}%
                </p>
                <p class="text-xs text-gray-500 mt-1">click-through rate</p>
            </div>

            {{-- Posición Promedio --}}
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Posición Promedio</p>
                <p class="text-3xl font-bold
                    @if ($this->avgPosition <= 3) text-green-400
                    @elseif ($this->avgPosition <= 10) text-amber-400
                    @else text-red-400 @endif">
                    #{{ $this->avgPosition }}
                </p>
                <p class="text-xs text-gray-500 mt-1">en resultados de búsqueda</p>
            </div>
        </div>

        {{-- ============================================================
             TENDENCIA DE CLICKS (últimos 30 días)
        ============================================================ --}}
        @if (! empty($this->clicksByDay))
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <h3 class="text-sm font-semibold text-gray-300 mb-4">Tendencia de Clicks — Últimos 30 días</h3>
                <div class="overflow-x-auto">
                    <div class="flex items-end gap-1 h-28 min-w-0">
                        @php
                            $maxClicks = max(array_column($this->clicksByDay, 'clicks') ?: [1]);
                        @endphp
                        @foreach ($this->clicksByDay as $day)
                            @php
                                $height = $maxClicks > 0 ? max(4, round(($day['clicks'] / $maxClicks) * 100)) : 4;
                            @endphp
                            <div class="flex flex-col items-center gap-1 flex-1 min-w-[12px]" title="{{ $day['date'] }}: {{ number_format($day['clicks']) }} clicks">
                                <div class="w-full bg-amber-500 rounded-t opacity-80 hover:opacity-100 transition-opacity"
                                     style="height: {{ $height }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $this->clicksByDay[0]['date'] ?? '' }}</span>
                        <span>{{ $this->clicksByDay[count($this->clicksByDay) - 1]['date'] ?? '' }}</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- ============================================================
                 TOP 20 KEYWORDS
            ============================================================ --}}
            @if (! empty($this->topKeywords))
                <div class="lg:col-span-2 rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                    <h3 class="text-sm font-semibold text-gray-300 mb-4">Top 20 Keywords por Clicks</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs text-gray-500 border-b border-gray-700">
                                    <th class="text-left py-2 pr-3">Keyword</th>
                                    <th class="text-right py-2 px-2">Clicks</th>
                                    <th class="text-right py-2 px-2">Imp.</th>
                                    <th class="text-right py-2 px-2">CTR</th>
                                    <th class="text-right py-2 pl-2">Pos.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                @foreach ($this->topKeywords as $kw)
                                    <tr class="hover:bg-gray-700/30 transition-colors">
                                        <td class="py-2 pr-3 text-gray-200 max-w-[220px] truncate">{{ $kw['query'] }}</td>
                                        <td class="py-2 px-2 text-right text-amber-400 font-medium">{{ number_format($kw['clicks']) }}</td>
                                        <td class="py-2 px-2 text-right text-gray-400">{{ number_format($kw['impressions']) }}</td>
                                        <td class="py-2 px-2 text-right text-gray-400">{{ $kw['ctr'] }}</td>
                                        <td class="py-2 pl-2 text-right">
                                            <span class="font-medium
                                                @if ($kw['position'] <= 3) text-green-400
                                                @elseif ($kw['position'] <= 10) text-amber-400
                                                @else text-gray-400 @endif">
                                                #{{ $kw['position'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ============================================================
                 DESGLOSE POR DEVICE + TOP PÁGINAS
            ============================================================ --}}
            <div class="flex flex-col gap-4">

                {{-- Device breakdown --}}
                @if (! empty($this->byDevice))
                    <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                        <h3 class="text-sm font-semibold text-gray-300 mb-4">Clicks por Device</h3>
                        @php $totalDeviceClicks = array_sum(array_column($this->byDevice, 'clicks')) ?: 1; @endphp
                        <div class="space-y-3">
                            @foreach ($this->byDevice as $dev)
                                @php $pct = round(($dev['clicks'] / $totalDeviceClicks) * 100); @endphp
                                <div>
                                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                                        <span>{{ $dev['device'] }}</span>
                                        <span>{{ number_format($dev['clicks']) }} clicks ({{ $pct }}%)</span>
                                    </div>
                                    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Top páginas --}}
                @if (! empty($this->topPages))
                    <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                        <h3 class="text-sm font-semibold text-gray-300 mb-3">Top 10 Páginas por Clicks</h3>
                        <div class="space-y-2">
                            @foreach ($this->topPages as $pg)
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs text-gray-400 truncate flex-1" title="{{ $pg['page'] }}">
                                        {{ preg_replace('#^https?://[^/]+#', '', $pg['page']) }}
                                    </p>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-medium text-amber-400">{{ number_format($pg['clicks']) }}</span>
                                        <span class="text-xs text-gray-600 ml-1">#{{ $pg['position'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================
             OPORTUNIDADES: posición 4-10
        ============================================================ --}}
        @if (! empty($this->opportunities))
            <div class="rounded-xl bg-gray-800/60 border border-gray-700 p-5">
                <div class="flex items-center gap-2 mb-1">
                    <x-heroicon-o-arrow-trending-up class="h-5 w-5 text-green-400" />
                    <h3 class="text-sm font-semibold text-gray-300">Oportunidades — Posición 4 a 10 (cerca del Top 3)</h3>
                </div>
                <p class="text-xs text-gray-500 mb-4">Keywords con alto potencial de subir al Top 3 con optimización de contenido</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 border-b border-gray-700">
                                <th class="text-left py-2 pr-3">Keyword</th>
                                <th class="text-right py-2 px-2">Imp.</th>
                                <th class="text-right py-2 px-2">Clicks</th>
                                <th class="text-right py-2 px-2">CTR</th>
                                <th class="text-right py-2 pl-2">Pos.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @foreach ($this->opportunities as $opp)
                                <tr class="hover:bg-green-900/10 transition-colors">
                                    <td class="py-2 pr-3 text-gray-200">{{ $opp['query'] }}</td>
                                    <td class="py-2 px-2 text-right text-gray-400">{{ number_format($opp['impressions']) }}</td>
                                    <td class="py-2 px-2 text-right text-amber-400">{{ number_format($opp['clicks']) }}</td>
                                    <td class="py-2 px-2 text-right text-gray-400">{{ $opp['ctr'] }}</td>
                                    <td class="py-2 pl-2 text-right">
                                        <span class="font-semibold text-green-400">#{{ $opp['position'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @endif

    {{-- Footer: info del último sync --}}
    <div class="text-xs text-gray-600 text-right">
        Datos: Google Search Console — últimos 30 días
        @if ($this->hasData)
            · Sync diario 6:00 AM ET
        @endif
    </div>

</x-filament-panels::page>
