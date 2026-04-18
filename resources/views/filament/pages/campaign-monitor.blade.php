<x-filament-panels::page>
@php
    $currentPeriod = $period ?? 30;
    $periods       = [7 => '7d', 14 => '14d', 30 => '30d', 90 => '90d'];
    $deliveryRate  = $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0;
@endphp

{{-- ══════════════════════════════════════════════════════════
     HEADER: Período + alerta
     ══════════════════════════════════════════════════════════ --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-1 bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-xl p-1">
        @foreach($periods as $days => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $days]) }}"
               class="px-4 py-1.5 text-sm font-semibold rounded-lg transition-all
                      {{ $currentPeriod == $days
                         ? 'bg-amber-400 text-gray-950 shadow'
                         : 'text-gray-400 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <a href="{{ request()->fullUrlWithQuery(['period' => $currentPeriod]) }}"
       class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-300 transition-colors">
        <x-heroicon-o-arrow-path class="w-3.5 h-3.5" />
        Actualizar
    </a>
</div>

@if($bounceRate > 3)
    <div class="flex items-center gap-3 bg-red-950/60 border border-red-800 rounded-xl px-5 py-3 mb-6">
        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-400 shrink-0" />
        <p class="text-sm text-red-300 font-medium">
            Bounce rate crítico <span class="font-bold text-red-200">{{ $bounceRate }}%</span> — revisar lista de emails inmediatamente
        </p>
    </div>
@elseif($bounceRate > 1)
    <div class="flex items-center gap-3 bg-amber-950/40 border border-amber-800/60 rounded-xl px-5 py-3 mb-6">
        <x-heroicon-o-exclamation-circle class="w-5 h-5 text-amber-400 shrink-0" />
        <p class="text-sm text-amber-300 font-medium">
            Bounce rate elevado <span class="font-bold">{{ $bounceRate }}%</span> — monitorear
        </p>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════
     KPI CARDS — fila principal
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 mb-6">

    {{-- Enviados --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-5 flex flex-col gap-1">
        <p class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">Enviados</p>
        <p class="text-3xl font-extrabold text-white leading-none">{{ number_format($totalSent) }}</p>
        <p class="text-xs text-gray-600">Últimos {{ $currentPeriod }}d</p>
    </div>

    {{-- Entregados --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-5 flex flex-col gap-1">
        <p class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">Entregados</p>
        <p class="text-3xl font-extrabold text-white leading-none">{{ number_format($totalDelivered) }}</p>
        <p class="text-xs {{ $deliveryRate >= 95 ? 'text-emerald-500' : 'text-amber-500' }} font-semibold">
            {{ $deliveryRate }}% delivery
        </p>
    </div>

    {{-- Abiertos --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-amber-800/40 rounded-2xl p-5 flex flex-col gap-1 relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-0.5 bg-amber-400/60 rounded-t-2xl"></div>
        <p class="text-[10px] font-bold tracking-widest text-amber-500/80 uppercase">Abiertos</p>
        <p class="text-3xl font-extrabold text-amber-400 leading-none">{{ number_format($totalOpened) }}</p>
        <p class="text-xs text-amber-600 font-semibold">{{ $openRate }}% open rate</p>
    </div>

    {{-- Clicks --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-5 flex flex-col gap-1">
        <p class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">Clicks</p>
        <p class="text-3xl font-extrabold text-white leading-none">{{ number_format($totalClicked) }}</p>
        <p class="text-xs text-gray-500 font-semibold">{{ $clickRate }}% click rate</p>
    </div>

    {{-- Avg apertura --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-5 flex flex-col gap-1">
        <p class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">Tiempo Apertura</p>
        <p class="text-3xl font-extrabold text-white leading-none">{{ $avgTimeToOpenHours }}<span class="text-lg font-bold text-gray-500">h</span></p>
        <p class="text-xs text-gray-600">promedio sent→open</p>
    </div>

    {{-- Rebotados --}}
    @php
        $bClass = $bounceRate > 3 ? 'border-red-700/60 text-red-400' : ($bounceRate > 1 ? 'border-orange-700/40 text-orange-400' : 'border-gray-800 text-gray-400');
        $bLine  = $bounceRate > 3 ? 'bg-red-500/60' : ($bounceRate > 1 ? 'bg-orange-500/40' : 'bg-transparent');
    @endphp
    <div class="bg-gray-900 dark:bg-gray-950 border {{ $bClass }} rounded-2xl p-5 flex flex-col gap-1 relative overflow-hidden">
        @if($bounceRate > 1)
            <div class="absolute inset-x-0 top-0 h-0.5 {{ $bLine }} rounded-t-2xl"></div>
        @endif
        <p class="text-[10px] font-bold tracking-widest text-gray-500 uppercase">Rebotados</p>
        <p class="text-3xl font-extrabold {{ $bClass }} leading-none">{{ number_format($totalBounced) }}</p>
        <p class="text-xs {{ $bClass }} font-semibold">{{ $bounceRate }}% bounce</p>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     FILA 2: Funnel + Semana vs semana
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Funnel de conversión (2/3) --}}
    <div class="lg:col-span-2 bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-6">
        <p class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-5">Funnel de Conversión — {{ $currentPeriod }} días</p>
        @php
            $funnelSteps = [
                ['label' => 'Enviados',   'value' => $totalSent,      'pct' => 100,                                                                     'bar' => 'bg-gray-600'],
                ['label' => 'Entregados', 'value' => $totalDelivered, 'pct' => $totalSent > 0 ? round($totalDelivered/$totalSent*100,1) : 0,             'bar' => 'bg-emerald-600'],
                ['label' => 'Abiertos',   'value' => $totalOpened,    'pct' => $totalSent > 0 ? round($totalOpened/$totalSent*100,1) : 0,                'bar' => 'bg-amber-500'],
                ['label' => 'Clicks',     'value' => $totalClicked,   'pct' => $totalSent > 0 ? round($totalClicked/$totalSent*100,1) : 0,               'bar' => 'bg-amber-300'],
            ];
        @endphp
        <div class="space-y-3">
            @foreach($funnelSteps as $step)
                <div>
                    <div class="flex justify-between items-baseline mb-1.5">
                        <span class="text-xs font-semibold text-gray-400">{{ $step['label'] }}</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-bold text-white">{{ number_format($step['value']) }}</span>
                            <span class="text-xs font-semibold text-gray-500">{{ $step['pct'] }}%</span>
                        </div>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="{{ $step['bar'] }} h-2 rounded-full transition-all duration-700"
                             style="width: {{ max($step['pct'], 0.5) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Semana vs semana (1/3) --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-6 flex flex-col justify-between">
        <p class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-4">Open Rate Semanal</p>

        <div class="space-y-4">
            <div>
                <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Esta semana</p>
                <p class="text-4xl font-extrabold text-amber-400">{{ $openRateThisWeek }}<span class="text-xl text-amber-600">%</span></p>
            </div>
            <div>
                <p class="text-[10px] text-gray-600 uppercase tracking-wide mb-1">Semana anterior</p>
                <p class="text-2xl font-bold text-gray-500">{{ $openRateLastWeek }}<span class="text-base text-gray-600">%</span></p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-800">
            @if($openRateDelta > 0)
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-emerald-950 border border-emerald-800">
                        <x-heroicon-o-arrow-trending-up class="w-3.5 h-3.5 text-emerald-400" />
                    </span>
                    <span class="text-lg font-extrabold text-emerald-400">+{{ $openRateDelta }}pp</span>
                </div>
            @elseif($openRateDelta < 0)
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-full bg-red-950 border border-red-800">
                        <x-heroicon-o-arrow-trending-down class="w-3.5 h-3.5 text-red-400" />
                    </span>
                    <span class="text-lg font-extrabold text-red-400">{{ $openRateDelta }}pp</span>
                </div>
            @else
                <span class="text-sm text-gray-600 font-medium">Sin cambio</span>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     TABLA: Funnel por categoría
     ══════════════════════════════════════════════════════════ --}}
<div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
        <p class="text-xs font-bold tracking-widest text-gray-500 uppercase">Funnel por Campaña</p>
        <span class="text-xs text-gray-600">{{ $currentPeriod }} días</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-800/60">
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-600 uppercase tracking-widest">Categoría</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-widest">Enviados</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-widest">Delivery</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-widest">Apertura</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-widest">Clicks</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-600 uppercase tracking-widest">Rebotes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byCategory as $row)
                    @php
                        $delivPct = $row->sent > 0 ? round(($row->delivered / $row->sent) * 100, 1) : 0;
                        $openPct  = $row->sent > 0 ? round(($row->opened   / $row->sent) * 100, 1) : 0;
                        $clickPct = $row->sent > 0 ? round(($row->clicked  / $row->sent) * 100, 1) : 0;
                        $bouncePct= $row->sent > 0 ? round(($row->bounced  / $row->sent) * 100, 1) : 0;
                    @endphp
                    <tr class="border-b border-gray-800/40 hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-3.5">
                            <span class="text-sm font-semibold text-white">
                                {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($row->category ?? 'other') }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-right text-sm font-mono text-gray-300">{{ number_format($row->sent) }}</td>

                        {{-- Delivery con mini bar --}}
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs font-bold {{ $delivPct >= 95 ? 'text-emerald-400' : 'text-amber-400' }}">{{ $delivPct }}%</span>
                                <div class="w-16 h-1 bg-gray-800 rounded-full overflow-hidden">
                                    <div class="{{ $delivPct >= 95 ? 'bg-emerald-500' : 'bg-amber-500' }} h-1 rounded-full" style="width:{{ $delivPct }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Open rate con mini bar --}}
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-xs font-bold {{ $openPct >= 25 ? 'text-amber-400' : ($openPct >= 10 ? 'text-amber-600' : 'text-gray-500') }}">{{ $openPct }}%</span>
                                <div class="w-16 h-1 bg-gray-800 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-1 rounded-full" style="width:{{ min($openPct * 2, 100) }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Click rate --}}
                        <td class="px-6 py-3.5 text-right">
                            <span class="text-xs font-bold text-gray-400">{{ $clickPct }}%</span>
                        </td>

                        {{-- Bounce --}}
                        <td class="px-6 py-3.5 text-right">
                            <span class="text-xs font-semibold {{ $bouncePct > 3 ? 'text-red-400' : ($bouncePct > 1 ? 'text-orange-400' : 'text-gray-600') }}">
                                {{ number_format($row->bounced) }}
                                @if($bouncePct > 0) <span class="text-[10px]">({{ $bouncePct }}%)</span> @endif
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-600">Sin datos para este período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     FILA 3: Top Abiertos + Supresiones
     ══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

    {{-- Top 5 emails más abiertos --}}
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800">
            <p class="text-xs font-bold tracking-widest text-gray-500 uppercase">Top Emails Abiertos</p>
        </div>
        <ul class="divide-y divide-gray-800/50">
            @forelse($topOpened as $i => $log)
                <li class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-800/20 transition-colors">
                    <span class="text-xs font-extrabold text-gray-700 w-5 text-center">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-mono text-gray-300 truncate">{{ $log->to_email }}</p>
                        @if($log->restaurant_name ?? $log->to_name)
                            <p class="text-[10px] text-gray-600 truncate mt-0.5">{{ $log->restaurant_name ?? $log->to_name }}</p>
                        @endif
                        <p class="text-[10px] text-gray-600 truncate mt-0.5">{{ Str::limit($log->subject ?? '—', 45) }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-950/60 border border-amber-800/50 text-amber-400 text-xs font-extrabold">
                            {{ $log->open_count }}
                        </span>
                    </div>
                </li>
            @empty
                <li class="px-6 py-10 text-center text-sm text-gray-600">Sin aperturas aún.</li>
            @endforelse
        </ul>
    </div>

    {{-- Supresiones activas --}}
    @php $totalSuppressions = array_sum($suppressionsByReason); @endphp
    <div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <p class="text-xs font-bold tracking-widest text-gray-500 uppercase">Supresiones Activas</p>
            <span class="text-xs font-bold text-gray-400 bg-gray-800 border border-gray-700 px-2.5 py-1 rounded-full">
                {{ number_format($totalSuppressions) }} total
            </span>
        </div>
        <div class="grid grid-cols-2 gap-3">

            @php $bouncedCount = $suppressionsByReason['bounced'] ?? $suppressionsByReason['hard_bounce'] ?? 0; @endphp
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-4">
                <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold mb-2">Bounced</p>
                <p class="text-2xl font-extrabold text-red-400">{{ number_format($bouncedCount) }}</p>
                <p class="text-[10px] text-gray-600 mt-1">Hard + soft</p>
            </div>

            <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-4">
                <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold mb-2">Spam</p>
                <p class="text-2xl font-extrabold text-orange-400">{{ number_format($suppressionsByReason['complained'] ?? $totalComplained) }}</p>
                <p class="text-[10px] text-gray-600 mt-1">Umbral Gmail: 0.1%</p>
            </div>

            <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-4">
                <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold mb-2">Desuscriptos</p>
                <p class="text-2xl font-extrabold text-gray-300">{{ number_format($suppressionsByReason['unsubscribed'] ?? $totalUnsubscribed) }}</p>
                <p class="text-[10px] text-gray-600 mt-1">Opt-out voluntario</p>
            </div>

            @php
                $otherReasons = array_filter($suppressionsByReason, fn($k) => !in_array($k, ['bounced', 'hard_bounce', 'complained', 'unsubscribed']), ARRAY_FILTER_USE_KEY);
                $otherCount   = array_sum($otherReasons);
            @endphp
            <div class="bg-gray-800/50 border border-gray-700/50 rounded-xl p-4">
                <p class="text-[10px] text-gray-500 uppercase tracking-wide font-bold mb-2">Otros</p>
                <p class="text-2xl font-extrabold text-gray-400">{{ number_format($otherCount) }}</p>
                <p class="text-[10px] text-gray-600 mt-1">{{ count($otherReasons) ? implode(', ', array_keys($otherReasons)) : '—' }}</p>
            </div>

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     FEED: Eventos recientes
     ══════════════════════════════════════════════════════════ --}}
<div class="bg-gray-900 dark:bg-gray-950 border border-gray-800 rounded-2xl overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
        <p class="text-xs font-bold tracking-widest text-gray-500 uppercase">Actividad Reciente</p>
        <a href="/admin/email-logs"
           class="flex items-center gap-1.5 text-xs font-semibold text-amber-500 hover:text-amber-400 transition-colors">
            Ver historial completo
            <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
        </a>
    </div>
    @php
        $eventConfig = [
            'sent'         => ['label' => 'Enviado',     'dot' => 'bg-gray-500',    'text' => 'text-gray-400'],
            'delivered'    => ['label' => 'Entregado',   'dot' => 'bg-emerald-500', 'text' => 'text-emerald-400'],
            'opened'       => ['label' => 'Abierto',     'dot' => 'bg-amber-400',   'text' => 'text-amber-400'],
            'clicked'      => ['label' => 'Click',       'dot' => 'bg-amber-300',   'text' => 'text-amber-300'],
            'bounced'      => ['label' => 'Rebote',      'dot' => 'bg-red-500',     'text' => 'text-red-400'],
            'complained'   => ['label' => 'Spam',        'dot' => 'bg-red-600',     'text' => 'text-red-500'],
            'unsubscribed' => ['label' => 'Unsub',       'dot' => 'bg-orange-500',  'text' => 'text-orange-400'],
            'delayed'      => ['label' => 'Delayed',     'dot' => 'bg-yellow-600',  'text' => 'text-yellow-500'],
        ];
    @endphp
    <ul class="divide-y divide-gray-800/40 max-h-80 overflow-y-auto">
        @forelse($recentEvents as $event)
            @php
                $type     = $event->event_type ?? $event->type ?? 'sent';
                $cfg      = $eventConfig[$type] ?? ['label' => ucfirst($type), 'dot' => 'bg-gray-500', 'text' => 'text-gray-400'];
                $email    = Str::limit($event->email ?? $event->subscriber_email ?? '—', 38);
                $occurred = $event->occurred_at ?? $event->created_at;
            @endphp
            <li class="px-6 py-3 flex items-center gap-4 hover:bg-gray-800/20 transition-colors">
                <span class="w-2 h-2 rounded-full {{ $cfg['dot'] }} shrink-0"></span>
                <span class="text-xs font-bold {{ $cfg['text'] }} w-16 shrink-0">{{ $cfg['label'] }}</span>
                <span class="text-xs font-mono text-gray-400 flex-1 truncate">{{ $email }}</span>
                <span class="text-[10px] text-gray-600 whitespace-nowrap">{{ $occurred?->diffForHumans() ?? '—' }}</span>
            </li>
        @empty
            <li class="px-6 py-10 text-center text-sm text-gray-600">Sin eventos recientes.</li>
        @endforelse
    </ul>
</div>

</x-filament-panels::page>
