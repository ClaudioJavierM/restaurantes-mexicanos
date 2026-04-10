<x-filament-panels::page>

    {{-- ─── Selector de período ──────────────────────────────────────────── --}}
    @php
        $currentPeriod = $period ?? 30;
        $periods = [7 => '7 días', 14 => '14 días', 30 => '30 días', 90 => '90 días'];
    @endphp
    <div class="flex items-center gap-2 mb-6">
        <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">Período:</span>
        @foreach($periods as $days => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $days]) }}"
               class="px-3 py-1.5 text-sm rounded-lg font-medium transition-colors
                      {{ $currentPeriod == $days
                         ? 'bg-primary-600 text-white'
                         : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ─── Alertas automáticas ─────────────────────────────────────────── --}}
    @if($bounceRate > 3)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-red-800 font-semibold">
                ⚠️ Alerta crítica: Bounce rate en {{ $bounceRate }}% — revisar lista de emails inmediatamente
            </p>
        </div>
    @elseif($bounceRate > 1)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <p class="text-amber-800">
                Aviso: Bounce rate en {{ $bounceRate }}% — monitorear
            </p>
        </div>
    @endif

    {{-- ─── 6 Stat Cards (fila superior) ──────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">

        {{-- Total Enviados --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Total Enviados</p>
            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($totalSent) }}</p>
        </div>

        {{-- Entregados --}}
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide mb-1">Entregados</p>
            <p class="text-3xl font-bold text-green-900 dark:text-green-100">{{ number_format($totalDelivered) }}</p>
            @if($totalSent > 0)
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">{{ round(($totalDelivered / $totalSent) * 100, 1) }}%</p>
            @endif
        </div>

        {{-- Abiertos --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide mb-1">Abiertos</p>
            <p class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ number_format($totalOpened) }}</p>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">{{ $openRate }}% open rate</p>
        </div>

        {{-- Clicks --}}
        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
            <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Clicks</p>
            <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($totalClicked) }}</p>
            <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">{{ $clickRate }}% click rate</p>
        </div>

        {{-- Tiempo promedio apertura --}}
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4">
            <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-1">Avg. Apertura</p>
            <p class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">{{ $avgTimeToOpenHours }}</p>
            <p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">horas (sent→open)</p>
        </div>

        {{-- Rebotados --}}
        @php
            $bounceColorBg    = $bounceRate > 3 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
                              : ($bounceRate > 1 ? 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-800'
                              : 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800');
            $bounceColorText  = $bounceRate > 3 ? 'text-red-600 dark:text-red-400'
                              : ($bounceRate > 1 ? 'text-orange-600 dark:text-orange-400'
                              : 'text-green-600 dark:text-green-400');
            $bounceColorValue = $bounceRate > 3 ? 'text-red-900 dark:text-red-100'
                              : ($bounceRate > 1 ? 'text-orange-900 dark:text-orange-100'
                              : 'text-green-900 dark:text-green-100');
        @endphp
        <div class="border rounded-xl p-4 {{ $bounceColorBg }}">
            <p class="text-xs font-medium uppercase tracking-wide mb-1 {{ $bounceColorText }}">Rebotados</p>
            <p class="text-3xl font-bold {{ $bounceColorValue }}">{{ number_format($totalBounced) }}</p>
            <p class="text-sm mt-1 {{ $bounceColorText }}">{{ $bounceRate }}% bounce rate</p>
        </div>

    </div>

    {{-- ─── Semana vs semana anterior ──────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-8">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Open Rate — Esta semana vs semana anterior</h2>
        <div class="flex flex-wrap gap-8 items-center">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Esta semana (7d)</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $openRateThisWeek }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Semana anterior</p>
                <p class="text-3xl font-bold text-gray-500 dark:text-gray-400">{{ $openRateLastWeek }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Cambio</p>
                @if($openRateDelta > 0)
                    <p class="text-2xl font-bold text-green-600">+{{ $openRateDelta }}pp ↑</p>
                @elseif($openRateDelta < 0)
                    <p class="text-2xl font-bold text-red-600">{{ $openRateDelta }}pp ↓</p>
                @else
                    <p class="text-2xl font-bold text-gray-400">Sin cambio</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Funnel de conversión (visual) ─────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-8">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">Funnel de Conversión — Últimos {{ $period }} días</h2>

        @php
            $funnelSteps = [
                ['label' => 'Enviados',    'value' => $totalSent,      'pct' => 100,                                                                          'color' => 'bg-blue-500'],
                ['label' => 'Entregados',  'value' => $totalDelivered, 'pct' => $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0,           'color' => 'bg-green-500'],
                ['label' => 'Abiertos',    'value' => $totalOpened,    'pct' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0,              'color' => 'bg-amber-500'],
                ['label' => 'Clicks',      'value' => $totalClicked,   'pct' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 1) : 0,             'color' => 'bg-purple-500'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach($funnelSteps as $step)
                <div class="flex items-center gap-4">
                    <div class="w-24 text-sm text-gray-600 dark:text-gray-400 font-medium text-right shrink-0">{{ $step['label'] }}</div>
                    <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-7 relative overflow-hidden">
                        <div class="{{ $step['color'] }} h-7 rounded-full transition-all duration-500"
                             style="width: {{ max($step['pct'], 1) }}%"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white mix-blend-difference">
                            {{ number_format($step['value']) }} ({{ $step['pct'] }}%)
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Funnel por categoría ────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Funnel por Campaña / Categoría</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Enviados</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entregados</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Delivery</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abiertos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Open</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clicks</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Click</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rebotados</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($byCategory as $row)
                        @php
                            $delivPct = $row->sent > 0 ? round(($row->delivered / $row->sent) * 100, 1) : 0;
                            $openPct  = $row->sent > 0 ? round(($row->opened / $row->sent) * 100, 1) : 0;
                            $clickPct = $row->sent > 0 ? round(($row->clicked / $row->sent) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($row->category ?? 'other') }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-gray-700 dark:text-gray-300">{{ number_format($row->sent) }}</td>
                            <td class="px-6 py-3 text-sm text-right text-green-700 dark:text-green-400">{{ number_format($row->delivered) }}</td>
                            <td class="px-6 py-3 text-sm text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $delivPct >= 95 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    {{ $delivPct }}%
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-amber-700 dark:text-amber-400">{{ number_format($row->opened) }}</td>
                            <td class="px-6 py-3 text-sm text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $openPct >= 25 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                     : ($openPct >= 10 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400'
                                     : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400') }}">
                                    {{ $openPct }}%
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-purple-700 dark:text-purple-400">{{ number_format($row->clicked) }}</td>
                            <td class="px-6 py-3 text-sm text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    {{ $clickPct }}%
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-red-700 dark:text-red-400">{{ number_format($row->bounced) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Sin datos de campañas para el período seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Top 5 emails más abiertos ──────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Top 5 Emails Más Abiertos</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email / Restaurante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aperturas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Última apertura</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($topOpened as $i => $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-3 text-sm font-bold text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 text-sm">
                                <p class="font-medium text-gray-900 dark:text-white font-mono text-xs">{{ $log->to_email }}</p>
                                @if($log->restaurant_name)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $log->restaurant_name }}</p>
                                @elseif($log->to_name)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $log->to_name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $log->subject ?? '—' }}</td>
                            <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400">
                                {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($log->category ?? 'other') }}
                            </td>
                            <td class="px-6 py-3 text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                    {{ $log->open_count }}x
                                </span>
                            </td>
                            <td class="px-6 py-3 text-xs text-right text-gray-400 dark:text-gray-500 whitespace-nowrap">
                                {{ $log->opened_at?->diffForHumans() ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Sin datos de aperturas aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Supresiones activas ─────────────────────────────────────────── --}}
    @php $totalSuppressions = array_sum($suppressionsByReason); @endphp
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-8">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-5">
            Supresiones Activas
            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                {{ number_format($totalSuppressions) }} total
            </span>
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            {{-- Bounced --}}
            @php $bouncedCount = $suppressionsByReason['bounced'] ?? $suppressionsByReason['hard_bounce'] ?? 0; @endphp
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide mb-1">Bounced</p>
                <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ number_format($bouncedCount) }}</p>
                <p class="text-xs text-red-500 mt-1">Hard + soft bounces</p>
            </div>

            {{-- Complained --}}
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                <p class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-1">Spam Complaints</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($suppressionsByReason['complained'] ?? $totalComplained) }}</p>
                <p class="text-xs text-orange-500 mt-1">Gmail umbral: 0.1%</p>
            </div>

            {{-- Unsubscribed --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
                <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide mb-1">Desuscriptos</p>
                <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ number_format($suppressionsByReason['unsubscribed'] ?? $totalUnsubscribed) }}</p>
                <p class="text-xs text-yellow-500 mt-1">Opt-out voluntarios</p>
            </div>

            {{-- Otros --}}
            @php
                $otherReasons = array_filter($suppressionsByReason, fn($k) => !in_array($k, ['bounced', 'hard_bounce', 'complained', 'unsubscribed']), ARRAY_FILTER_USE_KEY);
                $otherCount = array_sum($otherReasons);
            @endphp
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1">Otros</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($otherCount) }}</p>
                @if(count($otherReasons))
                    <p class="text-xs text-gray-500 mt-1">{{ implode(', ', array_keys($otherReasons)) }}</p>
                @else
                    <p class="text-xs text-gray-400 mt-1">—</p>
                @endif
            </div>

        </div>
    </div>

    {{-- ─── Últimas 50 aperturas (newsletter_events) ───────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Últimas 50 Aperturas</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">newsletter_events — event_type = opened</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaña</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuente</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cuándo</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentOpens as $open)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-2.5 text-xs font-mono text-gray-700 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($open->email ?? '—', 35) }}</td>
                            <td class="px-6 py-2.5 text-xs text-gray-600 dark:text-gray-400">{{ $open->campaign_name ?? '—' }}</td>
                            <td class="px-6 py-2.5 text-xs text-gray-500 dark:text-gray-500">{{ $open->source ?? 'Resend' }}</td>
                            <td class="px-6 py-2.5 text-xs text-right text-gray-400 whitespace-nowrap">
                                {{ $open->occurred_at?->diffForHumans() ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Sin aperturas registradas en newsletter_events.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Feed de eventos recientes ───────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Eventos Recientes (últimos 20)</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">newsletter_events</span>
        </div>

        @php
            $eventBadge = [
                'sent'         => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                'delivered'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'opened'       => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                'clicked'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                'bounced'      => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'complained'   => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'unsubscribed' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            ];
        @endphp

        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($recentEvents as $event)
                @php
                    $type       = $event->event_type ?? $event->type ?? 'sent';
                    $badgeClass = $eventBadge[$type] ?? 'bg-gray-100 text-gray-700';
                    $email      = \Illuminate\Support\Str::limit($event->email ?? $event->subscriber_email ?? '—', 30);
                    $source     = $event->source ?? (str_contains($event->message_id ?? '', 'listmonk') ? 'Listmonk' : 'Resend');
                    $occurred   = $event->occurred_at ?? $event->created_at;
                @endphp
                <li class="px-6 py-3 flex items-center gap-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium w-24 justify-center {{ $badgeClass }}">
                        {{ ucfirst($type) }}
                    </span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1 truncate font-mono text-xs">{{ $email }}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $source }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $occurred ? $occurred->diffForHumans() : '—' }}
                    </span>
                </li>
            @empty
                <li class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    Sin eventos recientes.
                </li>
            @endforelse
        </ul>
    </div>

    {{-- ─── Acciones rápidas ────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Acciones</h2>
        <div class="flex flex-wrap gap-3">

            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex-1 min-w-60">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Dry-run Email 1</p>
                <code class="text-xs text-gray-700 dark:text-gray-300 break-all">
                    php artisan famer:send-emails 1 --dry-run
                </code>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex-1 min-w-60">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Email Health Check</p>
                <code class="text-xs text-gray-700 dark:text-gray-300 break-all">
                    php artisan famer:email-health
                </code>
            </div>

            <div class="flex items-center">
                <a href="/admin/email-logs"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <x-heroicon-o-inbox-stack class="w-4 h-4" />
                    Ver Email Logs completos
                </a>
            </div>

        </div>
    </div>

</x-filament-panels::page>
