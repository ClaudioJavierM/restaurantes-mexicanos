<x-filament-panels::page>
@php
    $currentPeriod = $period ?? 30;
    $periods       = [7 => '7d', 14 => '14d', 30 => '30d', 90 => '90d'];
    $deliveryRate  = $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 1) : 0;
@endphp

<style>
.cm-card {
    background: rgb(30 32 40);
    border: 1px solid rgb(55 58 70);
    border-radius: 12px;
    padding: 20px;
}
.cm-card-amber {
    background: linear-gradient(135deg, rgb(41 35 20) 0%, rgb(35 30 18) 100%);
    border: 1px solid rgb(120 90 20);
    border-radius: 12px;
    padding: 20px;
}
.cm-card-red {
    background: rgb(35 20 20);
    border: 1px solid rgb(120 40 40);
    border-radius: 12px;
    padding: 20px;
}
.cm-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgb(100 108 130);
    margin-bottom: 8px;
}
.cm-number {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    color: rgb(235 238 248);
    letter-spacing: -0.02em;
}
.cm-number-amber {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    color: rgb(251 191 36);
    letter-spacing: -0.02em;
}
.cm-number-red {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    color: rgb(248 113 113);
    letter-spacing: -0.02em;
}
.cm-sub {
    font-size: 11px;
    color: rgb(90 98 118);
    margin-top: 6px;
    font-weight: 500;
}
.cm-sub-amber { font-size: 11px; color: rgb(180 130 30); margin-top: 6px; font-weight: 600; }
.cm-sub-green { font-size: 11px; color: rgb(52 211 153); margin-top: 6px; font-weight: 600; }
.cm-sub-red   { font-size: 11px; color: rgb(248 113 113); margin-top: 6px; font-weight: 600; }
.cm-accentline {
    height: 3px;
    border-radius: 999px;
    margin-bottom: 14px;
}
.cm-section {
    background: rgb(24 26 33);
    border: 1px solid rgb(48 52 65);
    border-radius: 14px;
    overflow: hidden;
}
.cm-section-header {
    padding: 14px 20px;
    border-bottom: 1px solid rgb(40 44 56);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cm-section-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgb(90 98 118);
}
</style>

{{-- ── Selector de período ── --}}
<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
    <div style="display:flex; gap:4px; background:rgb(24 26 33); border:1px solid rgb(48 52 65); border-radius:12px; padding:4px;">
        @foreach($periods as $days => $label)
            <a href="{{ request()->fullUrlWithQuery(['period' => $days]) }}"
               style="padding: 6px 16px; font-size:13px; font-weight:600; border-radius:8px; text-decoration:none; transition:all 0.15s;
                      {{ $currentPeriod == $days
                         ? 'background:rgb(251 191 36); color:rgb(15 15 15);'
                         : 'color:rgb(100 108 130);' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <a href="{{ request()->fullUrlWithQuery(['period' => $currentPeriod]) }}"
       style="display:flex; align-items:center; gap:6px; font-size:12px; color:rgb(80 88 108); text-decoration:none;">
        <x-heroicon-o-arrow-path style="width:14px; height:14px;" />
        Actualizar
    </a>
</div>

{{-- ── Alerta bounce ── --}}
@if($bounceRate > 3)
    <div style="display:flex; align-items:center; gap:12px; background:rgb(45 18 18); border:1px solid rgb(160 50 50); border-radius:12px; padding:14px 20px; margin-bottom:20px;">
        <x-heroicon-o-exclamation-triangle style="width:20px; height:20px; color:rgb(248 113 113); flex-shrink:0;" />
        <p style="font-size:13px; color:rgb(252 165 165); font-weight:500; margin:0;">
            Bounce rate crítico — <strong style="color:rgb(248 113 113);">{{ $bounceRate }}%</strong> · revisar lista de emails inmediatamente
        </p>
    </div>
@elseif($bounceRate > 1)
    <div style="display:flex; align-items:center; gap:12px; background:rgb(40 32 10); border:1px solid rgb(160 120 30); border-radius:12px; padding:14px 20px; margin-bottom:20px;">
        <x-heroicon-o-exclamation-circle style="width:20px; height:20px; color:rgb(251 191 36); flex-shrink:0;" />
        <p style="font-size:13px; color:rgb(253 230 138); font-weight:500; margin:0;">
            Bounce rate elevado — <strong>{{ $bounceRate }}%</strong> · monitorear de cerca
        </p>
    </div>
@endif

{{-- ── 6 KPI Cards ── --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px;">

    {{-- Enviados --}}
    <div class="cm-card">
        <div class="cm-accentline" style="background:rgb(80 90 120); width:32px;"></div>
        <div class="cm-label">Enviados</div>
        <div class="cm-number">{{ number_format($totalSent) }}</div>
        <div class="cm-sub">Últimos {{ $currentPeriod }}d</div>
    </div>

    {{-- Entregados --}}
    <div class="cm-card">
        <div class="cm-accentline" style="background:{{ $deliveryRate >= 95 ? 'rgb(52 211 153)' : 'rgb(251 191 36)' }}; width:32px;"></div>
        <div class="cm-label">Entregados</div>
        <div class="cm-number" style="color:{{ $deliveryRate >= 95 ? 'rgb(52 211 153)' : 'rgb(251 191 36)' }};">{{ number_format($totalDelivered) }}</div>
        <div class="cm-sub" style="color:{{ $deliveryRate >= 95 ? 'rgb(52 211 153)' : 'rgb(251 191 36)' }};">{{ $deliveryRate }}% delivery rate</div>
    </div>

    {{-- Abiertos — hero card --}}
    <div class="cm-card-amber">
        <div class="cm-accentline" style="background:rgb(251 191 36); width:48px;"></div>
        <div class="cm-label" style="color:rgb(160 120 30);">★ Abiertos</div>
        <div class="cm-number-amber">{{ number_format($totalOpened) }}</div>
        <div class="cm-sub-amber">{{ $openRate }}% open rate</div>
    </div>

</div>

<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px;">

    {{-- Clicks --}}
    <div class="cm-card">
        <div class="cm-accentline" style="background:rgb(96 165 250); width:32px;"></div>
        <div class="cm-label">Clicks</div>
        <div class="cm-number" style="color:rgb(147 197 253);">{{ number_format($totalClicked) }}</div>
        <div class="cm-sub">{{ $clickRate }}% click rate</div>
    </div>

    {{-- Tiempo apertura --}}
    <div class="cm-card">
        <div class="cm-accentline" style="background:rgb(167 139 250); width:32px;"></div>
        <div class="cm-label">Tiempo Apertura</div>
        <div class="cm-number" style="color:rgb(196 181 253);">
            {{ $avgTimeToOpenHours }}<span style="font-size:18px; color:rgb(100 85 160);">h</span>
        </div>
        <div class="cm-sub">promedio sent→open</div>
    </div>

    {{-- Rebotados --}}
    @php
        $isCritical = $bounceRate > 3;
        $isWarning  = $bounceRate > 1;
    @endphp
    @if($isCritical || $isWarning)
        <div class="cm-card-red">
            <div class="cm-accentline" style="background:rgb(248 113 113); width:32px;"></div>
            <div class="cm-label" style="color:rgb(160 60 60);">⚠ Rebotados</div>
            <div class="cm-number-red">{{ number_format($totalBounced) }}</div>
            <div class="cm-sub-red">{{ $bounceRate }}% bounce rate</div>
        </div>
    @else
        <div class="cm-card">
            <div class="cm-accentline" style="background:rgb(55 60 75); width:32px;"></div>
            <div class="cm-label">Rebotados</div>
            <div class="cm-number">{{ number_format($totalBounced) }}</div>
            <div class="cm-sub">{{ $bounceRate }}% bounce rate</div>
        </div>
    @endif

</div>

{{-- ── Funnel + Open Rate Semanal ── --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px;">

    {{-- Funnel --}}
    <div class="cm-section">
        <div class="cm-section-header">
            <span class="cm-section-title">Funnel de Conversión</span>
            <span style="font-size:11px; color:rgb(70 78 98);">Últimos {{ $currentPeriod }}d</span>
        </div>
        <div style="padding:20px;">
            @php
                $funnelSteps = [
                    ['label' => 'Enviados',   'value' => $totalSent,      'pct' => 100,
                     'color' => 'rgb(80 90 120)', 'textcolor' => 'rgb(140 150 180)'],
                    ['label' => 'Entregados', 'value' => $totalDelivered,
                     'pct'   => $totalSent > 0 ? round($totalDelivered/$totalSent*100,1) : 0,
                     'color' => 'rgb(52 211 153)', 'textcolor' => 'rgb(52 211 153)'],
                    ['label' => 'Abiertos',   'value' => $totalOpened,
                     'pct'   => $totalSent > 0 ? round($totalOpened/$totalSent*100,1) : 0,
                     'color' => 'rgb(251 191 36)', 'textcolor' => 'rgb(251 191 36)'],
                    ['label' => 'Clicks',     'value' => $totalClicked,
                     'pct'   => $totalSent > 0 ? round($totalClicked/$totalSent*100,1) : 0,
                     'color' => 'rgb(96 165 250)', 'textcolor' => 'rgb(147 197 253)'],
                ];
            @endphp
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($funnelSteps as $step)
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:6px;">
                            <span style="font-size:13px; font-weight:600; color:rgb(160 168 195);">{{ $step['label'] }}</span>
                            <div style="display:flex; align-items:baseline; gap:10px;">
                                <span style="font-size:16px; font-weight:700; color:{{ $step['textcolor'] }};">{{ number_format($step['value']) }}</span>
                                <span style="font-size:12px; font-weight:600; color:rgb(70 78 98);">{{ $step['pct'] }}%</span>
                            </div>
                        </div>
                        <div style="height:6px; background:rgb(40 44 56); border-radius:999px; overflow:hidden;">
                            <div style="height:6px; border-radius:999px; background:{{ $step['color'] }}; width:{{ max($step['pct'], 0.5) }}%; transition:width 0.7s ease;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Open Rate Semanal --}}
    <div class="cm-section">
        <div class="cm-section-header">
            <span class="cm-section-title">Open Rate Semanal</span>
        </div>
        <div style="padding:20px; display:flex; flex-direction:column; justify-content:space-between; height:calc(100% - 49px);">
            <div>
                <div style="font-size:11px; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Esta semana</div>
                <div style="font-size:44px; font-weight:800; color:rgb(251 191 36); letter-spacing:-0.02em; line-height:1;">
                    {{ $openRateThisWeek }}<span style="font-size:22px; color:rgb(140 100 20);">%</span>
                </div>
            </div>
            <div style="margin-top:16px;">
                <div style="font-size:11px; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Semana anterior</div>
                <div style="font-size:28px; font-weight:700; color:rgb(80 88 108); letter-spacing:-0.01em; line-height:1;">
                    {{ $openRateLastWeek }}<span style="font-size:16px;">%</span>
                </div>
            </div>
            <div style="margin-top:20px; padding-top:16px; border-top:1px solid rgb(40 44 56);">
                @if($openRateDelta > 0)
                    <div style="display:flex; align-items:center; gap:8px;">
                        <x-heroicon-o-arrow-trending-up style="width:20px; height:20px; color:rgb(52 211 153);" />
                        <span style="font-size:20px; font-weight:800; color:rgb(52 211 153);">+{{ $openRateDelta }}pp</span>
                    </div>
                @elseif($openRateDelta < 0)
                    <div style="display:flex; align-items:center; gap:8px;">
                        <x-heroicon-o-arrow-trending-down style="width:20px; height:20px; color:rgb(248 113 113);" />
                        <span style="font-size:20px; font-weight:800; color:rgb(248 113 113);">{{ $openRateDelta }}pp</span>
                    </div>
                @else
                    <span style="font-size:13px; color:rgb(70 78 98);">Sin cambio vs semana anterior</span>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Tabla por categoría ── --}}
<div class="cm-section" style="margin-bottom:16px;">
    <div class="cm-section-header">
        <span class="cm-section-title">Funnel por Campaña</span>
        <span style="font-size:11px; color:rgb(70 78 98);">{{ $currentPeriod }} días</span>
    </div>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:1px solid rgb(40 44 56);">
                <th style="padding:10px 20px; text-align:left; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Categoría</th>
                <th style="padding:10px 20px; text-align:right; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Enviados</th>
                <th style="padding:10px 20px; text-align:right; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Delivery</th>
                <th style="padding:10px 20px; text-align:right; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Apertura</th>
                <th style="padding:10px 20px; text-align:right; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Clicks</th>
                <th style="padding:10px 20px; text-align:right; font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em;">Rebotes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byCategory as $row)
                @php
                    $delivPct  = $row->sent > 0 ? round(($row->delivered / $row->sent) * 100, 1) : 0;
                    $openPct   = $row->sent > 0 ? round(($row->opened   / $row->sent) * 100, 1) : 0;
                    $clickPct  = $row->sent > 0 ? round(($row->clicked  / $row->sent) * 100, 1) : 0;
                    $bouncePct = $row->sent > 0 ? round(($row->bounced  / $row->sent) * 100, 1) : 0;
                    $delivColor = $delivPct >= 95 ? 'rgb(52 211 153)' : 'rgb(251 191 36)';
                    $openColor  = $openPct >= 25 ? 'rgb(251 191 36)' : ($openPct >= 10 ? 'rgb(180 140 30)' : 'rgb(90 98 118)');
                    $bounceColor= $bouncePct > 3 ? 'rgb(248 113 113)' : ($bouncePct > 1 ? 'rgb(251 146 60)' : 'rgb(70 78 98)');
                @endphp
                <tr style="border-bottom:1px solid rgb(35 38 48);">
                    <td style="padding:12px 20px;">
                        <span style="font-size:13px; font-weight:600; color:rgb(200 208 228);">
                            {{ \App\Filament\Pages\CampaignMonitor::getCategoryLabel($row->category ?? 'other') }}
                        </span>
                    </td>
                    <td style="padding:12px 20px; text-align:right; font-size:14px; font-weight:700; color:rgb(160 168 195); font-variant-numeric:tabular-nums;">
                        {{ number_format($row->sent) }}
                    </td>
                    <td style="padding:12px 20px; text-align:right;">
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                            <span style="font-size:12px; font-weight:700; color:{{ $delivColor }};">{{ $delivPct }}%</span>
                            <div style="width:56px; height:4px; background:rgb(40 44 56); border-radius:999px; overflow:hidden;">
                                <div style="height:4px; border-radius:999px; background:{{ $delivColor }}; width:{{ $delivPct }}%;"></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 20px; text-align:right;">
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                            <span style="font-size:12px; font-weight:700; color:{{ $openColor }};">{{ $openPct }}%</span>
                            <div style="width:56px; height:4px; background:rgb(40 44 56); border-radius:999px; overflow:hidden;">
                                <div style="height:4px; border-radius:999px; background:rgb(251 191 36); width:{{ min($openPct*2.5,100) }}%;"></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 20px; text-align:right; font-size:12px; font-weight:600; color:rgb(147 197 253);">
                        {{ $clickPct }}%
                    </td>
                    <td style="padding:12px 20px; text-align:right; font-size:12px; font-weight:600; color:{{ $bounceColor }};">
                        {{ number_format($row->bounced) }}
                        @if($bouncePct > 0)<span style="font-size:10px; opacity:0.7;"> ({{ $bouncePct }}%)</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="padding:40px; text-align:center; font-size:13px; color:rgb(70 78 98);">Sin datos para este período.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Top Abiertos + Supresiones ── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">

    {{-- Top emails abiertos --}}
    <div class="cm-section">
        <div class="cm-section-header">
            <span class="cm-section-title">Top Emails Abiertos</span>
        </div>
        <ul style="list-style:none; margin:0; padding:0; divide-y;">
            @forelse($topOpened as $i => $log)
                <li style="padding:12px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid rgb(35 38 48);">
                    <span style="font-size:11px; font-weight:800; color:rgb(55 60 75); width:18px; text-align:center; flex-shrink:0;">{{ $i + 1 }}</span>
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:12px; color:rgb(180 188 215); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0; font-family:monospace;">{{ $log->to_email }}</p>
                        <p style="font-size:11px; color:rgb(70 78 98); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:4px 0 0 0;">{{ Str::limit($log->subject ?? '—', 42) }}</p>
                    </div>
                    <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; background:rgb(41 35 20); border:1px solid rgb(120 90 20); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:rgb(251 191 36);">
                        {{ $log->open_count }}
                    </div>
                </li>
            @empty
                <li style="padding:40px; text-align:center; font-size:13px; color:rgb(70 78 98);">Sin aperturas aún.</li>
            @endforelse
        </ul>
    </div>

    {{-- Supresiones --}}
    @php $totalSuppressions = array_sum($suppressionsByReason); @endphp
    <div class="cm-section">
        <div class="cm-section-header">
            <span class="cm-section-title">Supresiones Activas</span>
            <span style="font-size:12px; font-weight:700; color:rgb(160 168 195); background:rgb(35 38 48); border:1px solid rgb(55 60 75); padding:3px 10px; border-radius:999px;">
                {{ number_format($totalSuppressions) }}
            </span>
        </div>
        <div style="padding:16px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">

            @php $bouncedCount = $suppressionsByReason['bounced'] ?? $suppressionsByReason['hard_bounce'] ?? 0; @endphp
            <div style="background:rgb(35 18 18); border:1px solid rgb(100 35 35); border-radius:10px; padding:14px;">
                <div style="font-size:10px; font-weight:700; color:rgb(140 60 60); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Bounced</div>
                <div style="font-size:26px; font-weight:800; color:rgb(248 113 113);">{{ number_format($bouncedCount) }}</div>
                <div style="font-size:10px; color:rgb(90 50 50); margin-top:4px;">Hard + soft bounce</div>
            </div>

            <div style="background:rgb(38 25 10); border:1px solid rgb(110 60 15); border-radius:10px; padding:14px;">
                <div style="font-size:10px; font-weight:700; color:rgb(140 90 30); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Spam</div>
                <div style="font-size:26px; font-weight:800; color:rgb(251 146 60);">{{ number_format($suppressionsByReason['complained'] ?? $totalComplained) }}</div>
                <div style="font-size:10px; color:rgb(100 70 30); margin-top:4px;">Umbral Gmail: 0.1%</div>
            </div>

            <div style="background:rgb(22 25 35); border:1px solid rgb(45 50 65); border-radius:10px; padding:14px;">
                <div style="font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Desuscriptos</div>
                <div style="font-size:26px; font-weight:800; color:rgb(140 150 180);">{{ number_format($suppressionsByReason['unsubscribed'] ?? $totalUnsubscribed) }}</div>
                <div style="font-size:10px; color:rgb(55 62 80); margin-top:4px;">Opt-out voluntario</div>
            </div>

            @php
                $otherReasons = array_filter($suppressionsByReason, fn($k) => !in_array($k, ['bounced','hard_bounce','complained','unsubscribed']), ARRAY_FILTER_USE_KEY);
                $otherCount   = array_sum($otherReasons);
            @endphp
            <div style="background:rgb(22 25 35); border:1px solid rgb(45 50 65); border-radius:10px; padding:14px;">
                <div style="font-size:10px; font-weight:700; color:rgb(70 78 98); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:8px;">Otros</div>
                <div style="font-size:26px; font-weight:800; color:rgb(100 108 130);">{{ number_format($otherCount) }}</div>
                <div style="font-size:10px; color:rgb(55 62 80); margin-top:4px;">{{ count($otherReasons) ? implode(', ', array_keys($otherReasons)) : '—' }}</div>
            </div>

        </div>
    </div>

</div>

{{-- ── Feed de actividad ── --}}
<div class="cm-section">
    <div class="cm-section-header">
        <span class="cm-section-title">Actividad Reciente</span>
        <a href="/admin/email-logs" style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:rgb(180 140 30); text-decoration:none;">
            Ver historial completo
            <x-heroicon-o-arrow-right style="width:14px; height:14px;" />
        </a>
    </div>
    @php
        $eventConfig = [
            'sent'         => ['label' => 'Enviado',   'dot' => 'rgb(80 90 120)',    'text' => 'rgb(100 108 130)'],
            'delivered'    => ['label' => 'Entregado', 'dot' => 'rgb(52 211 153)',   'text' => 'rgb(52 211 153)'],
            'opened'       => ['label' => 'Abierto',   'dot' => 'rgb(251 191 36)',   'text' => 'rgb(251 191 36)'],
            'clicked'      => ['label' => 'Click',     'dot' => 'rgb(96 165 250)',   'text' => 'rgb(147 197 253)'],
            'bounced'      => ['label' => 'Rebote',    'dot' => 'rgb(248 113 113)',  'text' => 'rgb(248 113 113)'],
            'complained'   => ['label' => 'Spam',      'dot' => 'rgb(239 68 68)',    'text' => 'rgb(252 165 165)'],
            'unsubscribed' => ['label' => 'Unsub',     'dot' => 'rgb(251 146 60)',   'text' => 'rgb(253 186 116)'],
            'delayed'      => ['label' => 'Delayed',   'dot' => 'rgb(250 204 21)',   'text' => 'rgb(253 224 71)'],
        ];
    @endphp
    <ul style="list-style:none; margin:0; padding:0; max-height:260px; overflow-y:auto;">
        @forelse($recentEvents as $event)
            @php
                $type     = $event->event_type ?? $event->type ?? 'sent';
                $cfg      = $eventConfig[$type] ?? ['label' => ucfirst($type), 'dot' => 'rgb(80 90 120)', 'text' => 'rgb(100 108 130)'];
                $email    = Str::limit($event->email ?? $event->subscriber_email ?? '—', 42);
                $occurred = $event->occurred_at ?? $event->created_at;
            @endphp
            <li style="padding:10px 20px; display:flex; align-items:center; gap:12px; border-bottom:1px solid rgb(35 38 48);">
                <span style="width:8px; height:8px; border-radius:50%; background:{{ $cfg['dot'] }}; flex-shrink:0; box-shadow:0 0 6px {{ $cfg['dot'] }};"></span>
                <span style="font-size:11px; font-weight:700; color:{{ $cfg['text'] }}; width:60px; flex-shrink:0;">{{ $cfg['label'] }}</span>
                <span style="font-size:12px; color:rgb(120 128 155); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-family:monospace;">{{ $email }}</span>
                <span style="font-size:11px; color:rgb(55 62 80); white-space:nowrap;">{{ $occurred?->diffForHumans() ?? '—' }}</span>
            </li>
        @empty
            <li style="padding:40px; text-align:center; font-size:13px; color:rgb(70 78 98);">Sin eventos recientes.</li>
        @endforelse
    </ul>
</div>

</x-filament-panels::page>
