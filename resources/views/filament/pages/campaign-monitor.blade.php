<x-filament-panels::page>
@php
    $currentPeriod = $period ?? 30;
    $periods       = [7 => '7 días', 14 => '14 días', 30 => '30 días', 90 => '90 días'];
    $deliveryRate  = $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0;
@endphp

{{-- ── Selector de período ── --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-1">
        @foreach($periods as $days => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $days]) }}"
               class="px-4 py-1.5 text-sm font-semibold rounded-lg transition-all
                      {{ $currentPeriod == $days
                         ? 'bg-white dark:bg-gray-700 text-amber-600 dark:text-amber-400 shadow-sm border border-gray-200 dark:border-gray-600'
                         : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <a href="{{ request()->fullUrlWithQuery(['period' => $currentPeriod]) }}"
       class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <x-heroicon-o-arrow-path class="w-4 h-4" />
        Actualizar
    </a>
</div>

{{-- ── Alerta de bounce ── --}}
@if($bounceRate > 3)
    <div class="flex items-center gap-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/60 rounded-xl px-4 py-3 mb-5">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 shrink-0" />
        <p class="text-sm text-red-700 dark:text-red-300 font-medium">
            Bounce rate crítico <strong>{{ $bounceRate }}%</strong> — revisar lista de emails inmediatamente
        </p>
    </div>
@elseif($bounceRate > 1)
    <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-xl px-4 py-3 mb-5">
        <x-heroicon-o-exclamation-circle class="w-5 h-5 text-amber-500 shrink-0" />
        <p class="text-sm text-amber-700 dark:text-amber-300 font-medium">
            Bounce rate elevado <strong>{{ $bounceRate }}%</strong> — monitorear de cerca
        </p>
    </div>
@endif

{{-- ── 6 KPI Cards ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">

    {{-- Enviados --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2">Enviados</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalSent) }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Últimos {{ $currentPeriod }}d</p>
    </div>

    {{-- Entregados --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2">Entregados</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalDelivered) }}</p>
        <p class="text-xs font-semibold mt-1 {{ $deliveryRate >= 95 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
            {{ $deliveryRate }}% delivery
        </p>
    </div>

    {{-- Abiertos — card destacada --}}
    <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-amber-600 dark:text-amber-500 mb-2">Abiertos</p>
        <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format($totalOpened) }}</p>
        <p class="text-xs font-semibold text-amber-500 dark:text-amber-500/80 mt-1">{{ $openRate }}% open rate</p>
    </div>

    {{-- Clicks --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2">Clicks</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($totalClicked) }}</p>
        <p class="text-xs font-semibold text-gray-400 mt-1">{{ $clickRate }}% click rate</p>
    </div>

    {{-- Tiempo promedio apertura --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2">Tiempo Apertura</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
            {{ $avgTimeToOpenHours }}<span class="text-lg text-gray-400 font-semibold">h</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">promedio sent→open</p>
    </div>

    {{-- Rebotados --}}
    @php
        $isCritical = $bounceRate > 3;
        $isWarning  = $bounceRate > 1;
    @endphp
    <div class="rounded-xl p-4 shadow-sm border
        {{ $isCritical ? 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-800/50'
         : ($isWarning ? 'bg-orange-50 dark:bg-orange-950/20 border-orange-200 dark:border-orange-800/50'
         : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700') }}">
        <p class="text-xs font-semibold mb-2
            {{ $isCritical ? 'text-red-500' : ($isWarning ? 'text-orange-500' : 'text-gray-400 dark:text-gray-500') }}">
            Rebotados
        </p>
        <p class="text-3xl font-bold tracking-tight
            {{ $isCritical ? 'text-red-600 dark:text-red-400' : ($isWarning ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white') }}">
            {{ number_format($totalBounced) }}
        </p>
        <p class="text-xs font-semibold mt-1
            {{ $isCritical ? 'text-red-500' : ($isWarning ? 'text-orange-400' : 'text-gray-400') }}">
            {{ $bounceRate }}% bounce
        </p>
    </div>

</div>

{{-- ── Funnel + Open Rate Semanal ── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

    {{-- Funnel de conversión --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">
            Funnel de Conversión — {{ $currentPeriod }} días
        </p>
        @php
            $funnelSteps = [
                ['label' => 'Enviados',   'value' => $totalSent,      'pct' => 100,                                                          'color' => 'bg-gray-300 dark:bg-gray-600'],
                ['label' => 'Entregados', 'value' => $totalDelivered, 'pct' => $totalSent > 0 ? round($totalDelivered/$totalSent*100,1) : 0, 'color' => 'bg-emerald-400 dark:bg-emerald-500'],
                ['label' => 'Abiertos',   'value' => $totalOpened,    'pct' => $totalSent > 0 ? round($totalOpened/$totalSent*100,1) : 0,    'color' => 'bg-amber-400'],
                ['label' => 'Clicks',     'value' => $totalClicked,   'pct' => $totalSent > 0 ? round($totalClicked/$totalSent*100,1) : 0,   'color' => 'bg-amber-300'],
            ];
        @endphp
        <div class="space-y-4">
            @foreach($funnelSteps as $step)
                <div>
                    <div class="flex justify-between items-baseline mb-1.5">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $step['label'] }}</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($step['value']) }}</span>
                            <span class="text-xs text-gray-400">{{ $step['pct'] }}%</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="{{ $step['color'] }} h-2 rounded-full transition-all duration-700"
                             style="width: {{ max($step['pct'], 0.5) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Open Rate Semanal --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm flex flex-col justify-between">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-4">Open Rate Semanal</p>

        <div class="space-y-4">
            <div>
                <p class="text-xs text-gray-400 mb-1">Esta semana</p>
                <p class="text-4xl font-bold text-amber-600 dark:text-amber-400 tracking-tight">
                    {{ $openRateThisWeek }}<span class="text-xl text-amber-400 dark:text-amber-600">%</span>
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-1">Semana anterior</p>
                <p class="text-2xl font-semibold text-gray-400 dark:text-gray-500 tracking-tight">
                    {{ $openRateLastWeek }}<span class="text-base">%</span>
                </p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            @if($openRateDelta > 0)
                <div class="flex items-center gap-2">
                    <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-emerald-500" />
                    <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">+{{ $openRateDelta }}pp</span>
                </div>
            @elseif($openRateDelta < 0)
                <div class="flex items-center gap-2">
                    <x-heroicon-o-arrow-trending-down class="w-5 h-5 text-red-400" />
                    <span class="text-lg font-bold text-red-500 dark:text-red-400">{{ $openRateDelta }}pp</span>
                </div>
            @else
                <span class="text-sm text-gray-400 font-medium">— Sin cambio</span>
            @endif
        </div>
    </div>

</div>

{{-- ── Tabla por categoría ── --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm mb-5 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Funnel por Campaña</p>
        <span class="text-xs text-gray-400">{{ $currentPeriod }} días</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
                    <th class="px-5 py-2.5 text-left text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Categoría</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Enviados</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Delivery</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Apertura</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Clicks</th>
                    <th class="px-5 py-2.5 text-right text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wide">Rebotes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($byCategory as $row)
                    @php
                        $delivPct  = $row->sent > 0 ? round(($row->delivered / $row->sent) * 100, 1) : 0;
                        $openPct   = $row->sent > 0 ? round(($row->opened   / $row->sent) * 100, 1) : 0;
                        $clickPct  = $row->sent > 0 ? round(($row->clicked  / $row->sent) * 100, 1) : 0;
                        $bouncePct = $row->sent > 0 ? round(($row->bounced  / $row->sent) * 100, 1) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($row->category ?? 'other') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-300 tabular-nums">
                            {{ number_format($row->sent) }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs font-semibold {{ $delivPct >= 95 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $delivPct }}%</span>
                                <div class="w-14 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                    <div class="{{ $delivPct >= 95 ? 'bg-emerald-400' : 'bg-amber-400' }} h-1.5 rounded-full" style="width:{{ $delivPct }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs font-semibold {{ $openPct >= 25 ? 'text-amber-600 dark:text-amber-400' : ($openPct >= 10 ? 'text-amber-500' : 'text-gray-400') }}">{{ $openPct }}%</span>
                                <div class="w-14 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                    <div class="bg-amber-400 h-1.5 rounded-full" style="width:{{ min($openPct * 2.5, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $clickPct }}%</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="text-xs font-semibold {{ $bouncePct > 3 ? 'text-red-600 dark:text-red-400' : ($bouncePct > 1 ? 'text-orange-500' : 'text-gray-400') }}">
                                {{ number_format($row->bounced) }}
                                @if($bouncePct > 0)<span class="text-gray-400"> ({{ $bouncePct }}%)</span>@endif
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">Sin datos para este período.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Top Abiertos + Supresiones ── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">

    {{-- Top 5 emails más abiertos --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Top Emails Abiertos</p>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($topOpened as $i => $log)
                <li class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                    <span class="text-xs font-bold text-gray-300 dark:text-gray-600 w-5 text-center shrink-0">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ $log->to_email }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ Str::limit($log->subject ?? '—', 45) }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-xs font-bold shrink-0">
                        {{ $log->open_count }}
                    </span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-gray-400">Sin aperturas aún.</li>
            @endforelse
        </ul>
    </div>

    {{-- Supresiones --}}
    @php $totalSuppressions = array_sum($suppressionsByReason); @endphp
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Supresiones Activas</p>
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-2.5 py-1 rounded-full">
                {{ number_format($totalSuppressions) }} total
            </span>
        </div>
        <div class="grid grid-cols-2 gap-3">

            @php $bouncedCount = $suppressionsByReason['bounced'] ?? $suppressionsByReason['hard_bounce'] ?? 0; @endphp
            <div class="bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/40 rounded-lg p-3.5">
                <p class="text-xs font-semibold text-red-400 mb-2">Bounced</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($bouncedCount) }}</p>
                <p class="text-xs text-gray-400 mt-1">Hard + soft</p>
            </div>

            <div class="bg-orange-50 dark:bg-orange-950/20 border border-orange-100 dark:border-orange-900/40 rounded-lg p-3.5">
                <p class="text-xs font-semibold text-orange-400 mb-2">Spam</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($suppressionsByReason['complained'] ?? $totalComplained) }}</p>
                <p class="text-xs text-gray-400 mt-1">Umbral Gmail: 0.1%</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 rounded-lg p-3.5">
                <p class="text-xs font-semibold text-gray-400 mb-2">Desuscriptos</p>
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ number_format($suppressionsByReason['unsubscribed'] ?? $totalUnsubscribed) }}</p>
                <p class="text-xs text-gray-400 mt-1">Opt-out voluntario</p>
            </div>

            @php
                $otherReasons = array_filter($suppressionsByReason, fn($k) => !in_array($k, ['bounced','hard_bounce','complained','unsubscribed']), ARRAY_FILTER_USE_KEY);
                $otherCount   = array_sum($otherReasons);
            @endphp
            <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 rounded-lg p-3.5">
                <p class="text-xs font-semibold text-gray-400 mb-2">Otros</p>
                <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ number_format($otherCount) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ count($otherReasons) ? implode(', ', array_keys($otherReasons)) : '—' }}</p>
            </div>

        </div>
    </div>

</div>

{{-- ── Feed de actividad reciente ── --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Actividad Reciente</p>
        <a href="/admin/email-logs"
           class="flex items-center gap-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
            Ver historial completo
            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
        </a>
    </div>
    @php
        $eventConfig = [
            'sent'         => ['label' => 'Enviado',   'dot' => 'bg-gray-400',    'text' => 'text-gray-500'],
            'delivered'    => ['label' => 'Entregado', 'dot' => 'bg-emerald-400', 'text' => 'text-emerald-600 dark:text-emerald-400'],
            'opened'       => ['label' => 'Abierto',   'dot' => 'bg-amber-400',   'text' => 'text-amber-600 dark:text-amber-400'],
            'clicked'      => ['label' => 'Click',     'dot' => 'bg-amber-300',   'text' => 'text-amber-500'],
            'bounced'      => ['label' => 'Rebote',    'dot' => 'bg-red-400',     'text' => 'text-red-600 dark:text-red-400'],
            'complained'   => ['label' => 'Spam',      'dot' => 'bg-red-500',     'text' => 'text-red-700 dark:text-red-500'],
            'unsubscribed' => ['label' => 'Unsub',     'dot' => 'bg-orange-400',  'text' => 'text-orange-600 dark:text-orange-400'],
            'delayed'      => ['label' => 'Delayed',   'dot' => 'bg-yellow-400',  'text' => 'text-yellow-600'],
        ];
    @endphp
    <ul class="divide-y divide-gray-100 dark:divide-gray-800 max-h-72 overflow-y-auto">
        @forelse($recentEvents as $event)
            @php
                $type     = $event->event_type ?? $event->type ?? 'sent';
                $cfg      = $eventConfig[$type] ?? ['label' => ucfirst($type), 'dot' => 'bg-gray-400', 'text' => 'text-gray-500'];
                $email    = Str::limit($event->email ?? $event->subscriber_email ?? '—', 40);
                $occurred = $event->occurred_at ?? $event->created_at;
            @endphp
            <li class="px-5 py-2.5 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} shrink-0"></span>
                <span class="text-xs font-semibold {{ $cfg['text'] }} w-16 shrink-0">{{ $cfg['label'] }}</span>
                <span class="text-xs text-gray-600 dark:text-gray-400 flex-1 truncate font-mono">{{ $email }}</span>
                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $occurred?->diffForHumans() ?? '—' }}</span>
            </li>
        @empty
            <li class="px-5 py-10 text-center text-sm text-gray-400">Sin eventos recientes.</li>
        @endforelse
    </ul>
</div>

</x-filament-panels::page>
