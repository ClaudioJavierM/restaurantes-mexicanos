<x-filament-panels::page>

    {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Conversion Funnel</h1>
            <p class="text-sm mt-1" style="color: #9CA3AF;">Análisis completo del embudo de conversión FAMER</p>
        </div>
        {{-- Period Selector --}}
        <div class="flex items-center gap-2 p-1 rounded-xl" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
            @foreach([7 => '7d', 14 => '14d', 30 => '30d', 90 => '90d'] as $days => $label)
                <a href="?period={{ $days }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150"
                   style="{{ $period == $days
                       ? 'background: #D4AF37; color: #0B0B0B; font-weight: 700;'
                       : 'color: #9CA3AF;' }}
                       text-decoration: none;">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ─── Section 1: Funnel Visual ───────────────────────────────────────── --}}
    <div class="rounded-xl p-6 mb-6" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-6 flex items-center gap-2">
            <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Embudo de Conversión
        </h2>

        <div class="space-y-4">
            @foreach($funnel as $i => $step)
                <div class="relative">
                    {{-- Step Header --}}
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center"
                                  style="background: #D4AF3720; color: #D4AF37; border: 1px solid #D4AF3740;">{{ $i + 1 }}</span>
                            <span class="text-sm font-medium text-white">{{ $step['label'] }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-right">
                            <span class="text-lg font-bold text-white">{{ number_format($step['count']) }}</span>
                            <span class="text-sm font-semibold w-14 text-right"
                                  style="color: #D4AF37;">{{ $step['pct'] }}%</span>
                            @if($step['drop'] > 0)
                                <span class="text-xs font-medium w-20 text-right" style="color: #EF4444;">
                                    -{{ $step['drop'] }}% drop
                                </span>
                            @else
                                <span class="w-20"></span>
                            @endif
                        </div>
                    </div>
                    {{-- Bar --}}
                    <div class="h-8 rounded-lg overflow-hidden" style="background: #0B0B0B;">
                        <div class="h-full rounded-lg flex items-center pl-3 transition-all duration-700"
                             style="width: {{ $step['pct'] }}%; background: linear-gradient(90deg, #D4AF37 0%, #B8960E 100%); min-width: {{ $step['count'] > 0 ? '2%' : '0' }};">
                        </div>
                    </div>
                    {{-- Connector --}}
                    @if(!$loop->last)
                        <div class="flex justify-center mt-1">
                            <div class="w-px h-3" style="background: #2A2A2A;"></div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Section 2: KPI Cards ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- MRR --}}
        <div class="rounded-xl p-6 flex flex-col gap-2"
             style="background: #1A1A1A; border: 1px solid #D4AF3740;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #D4AF37;">MRR</span>
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($revenueStats['mrr'] ?? 0) }}</div>
            <div class="text-xs" style="color: #9CA3AF;">Ingreso mensual recurrente</div>
        </div>

        {{-- ARR --}}
        <div class="rounded-xl p-6 flex flex-col gap-2"
             style="background: #1A1A1A; border: 1px solid #D4AF3740;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #D4AF37;">ARR</span>
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($revenueStats['arr'] ?? 0) }}</div>
            <div class="text-xs" style="color: #9CA3AF;">Ingreso anual proyectado</div>
        </div>

        {{-- Paying Customers --}}
        <div class="rounded-xl p-6 flex flex-col gap-2"
             style="background: #1A1A1A; border: 1px solid #10B98140;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #10B981;">Clientes</span>
                <svg class="w-5 h-5" style="color: #10B981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format(($revenueStats['premium_count'] ?? 0) + ($revenueStats['elite_count'] ?? 0)) }}</div>
            <div class="flex gap-3 text-xs" style="color: #9CA3AF;">
                <span>Premium: {{ $revenueStats['premium_count'] ?? 0 }}</span>
                <span style="color: #D4AF37;">Elite: {{ $revenueStats['elite_count'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Avg LTV --}}
        <div class="rounded-xl p-6 flex flex-col gap-2"
             style="background: #1A1A1A; border: 1px solid #6366F140;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #818CF8;">Avg LTV</span>
                <svg class="w-5 h-5" style="color: #818CF8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($revenueStats['avg_ltv'] ?? 0) }}</div>
            <div class="text-xs" style="color: #9CA3AF;">8 meses retención promedio</div>
        </div>

    </div>

    {{-- ─── Section 3: Abandonment Analysis ───────────────────────────────── --}}
    <div class="rounded-xl p-6 mb-6" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
            <svg class="w-5 h-5" style="color: #EF4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Análisis de Abandono
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Started but abandoned --}}
            <div class="rounded-xl p-5" style="background: #0B0B0B; border: 1px solid #EF444430;">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="text-2xl font-bold text-white">{{ number_format($abandonByStep['started_not_finished'] ?? 0) }}</div>
                        <div class="text-sm font-medium mt-1" style="color: #EF4444;">Iniciaron y abandonaron</div>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: #EF444420;">
                        <svg class="w-5 h-5" style="color: #EF4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
                <div class="text-xs px-3 py-2 rounded-lg" style="background: #1A1A1A; color: #9CA3AF;">
                    Reciben email de abandono automatico
                </div>
            </div>

            {{-- Claimed free --}}
            <div class="rounded-xl p-5" style="background: #0B0B0B; border: 1px solid #F59E0B30;">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="text-2xl font-bold text-white">{{ number_format($abandonByStep['claimed_free'] ?? 0) }}</div>
                        <div class="text-sm font-medium mt-1" style="color: #F59E0B;">Reclamaron gratis</div>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: #F59E0B20;">
                        <svg class="w-5 h-5" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-xs px-3 py-2 rounded-lg" style="background: #1A1A1A; color: #9CA3AF;">
                    Ven UpgradeBanner en su dashboard
                </div>
            </div>

            {{-- Paid and canceled --}}
            <div class="rounded-xl p-5" style="background: #0B0B0B; border: 1px solid #6366F130;">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="text-2xl font-bold text-white">{{ number_format($abandonByStep['paid_canceled'] ?? 0) }}</div>
                        <div class="text-sm font-medium mt-1" style="color: #818CF8;">Cancelaron suscripción</div>
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background: #6366F120;">
                        <svg class="w-5 h-5" style="color: #818CF8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                </div>
                <div class="text-xs px-3 py-2 rounded-lg" style="background: #1A1A1A; color: #9CA3AF;">
                    Churn — candidatos a win-back
                </div>
            </div>

        </div>
    </div>

    {{-- ─── Section 4: Country Breakdown ──────────────────────────────────── --}}
    @if(!empty($conversionByCountry))
    <div class="rounded-xl p-6 mb-6" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
            <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
            </svg>
            Conversión por País
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #2A2A2A;">
                        <th class="text-left pb-3 pr-6 font-semibold" style="color: #9CA3AF;">País</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Total Aprobados</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Reclamados</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Tasa Claim</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Pagando</th>
                        <th class="text-right pb-3 font-semibold" style="color: #9CA3AF;">Tasa Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conversionByCountry as $row)
                        <tr style="border-bottom: 1px solid #2A2A2A20;">
                            <td class="py-3 pr-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $row['country'] === 'US' ? '🇺🇸' : '🇲🇽' }}</span>
                                    <span class="font-semibold text-white">{{ $row['country'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 pr-6 text-right text-white font-medium">{{ number_format($row['total']) }}</td>
                            <td class="py-3 pr-6 text-right text-white font-medium">{{ number_format($row['claimed']) }}</td>
                            <td class="py-3 pr-6 text-right">
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold"
                                      style="background: #D4AF3720; color: #D4AF37;">
                                    {{ $row['claim_rate'] }}%
                                </span>
                            </td>
                            <td class="py-3 pr-6 text-right text-white font-medium">{{ number_format($row['paid']) }}</td>
                            <td class="py-3 text-right">
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold"
                                      style="background: #10B98120; color: #10B981;">
                                    {{ $row['paid_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── Section 5: Weekly Cohorts ──────────────────────────────────────── --}}
    @if(!empty($cohorts))
    <div class="rounded-xl p-6 mb-6" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
            <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Cohortes Semanales (últimas 8 semanas)
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #2A2A2A;">
                        <th class="text-left pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Semana</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Iniciaron</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Completaron</th>
                        <th class="text-right pb-3 pr-6 font-semibold" style="color: #9CA3AF;">Convirtieron</th>
                        <th class="text-right pb-3 font-semibold" style="color: #9CA3AF;">Tasa Claim</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cohorts as $cohort)
                        <tr style="border-bottom: 1px solid #2A2A2A20;" class="{{ $loop->last ? '' : '' }}">
                            <td class="py-3 pr-6 font-mono text-sm" style="color: #D4AF37;">{{ $cohort['week'] }}</td>
                            <td class="py-3 pr-6 text-right text-white">{{ number_format($cohort['initiated']) }}</td>
                            <td class="py-3 pr-6 text-right text-white">{{ number_format($cohort['completed']) }}</td>
                            <td class="py-3 pr-6 text-right" style="color: #10B981; font-weight: 600;">{{ number_format($cohort['paid']) }}</td>
                            <td class="py-3 text-right">
                                @php $rateColor = $cohort['rate'] >= 50 ? '#10B981' : ($cohort['rate'] >= 25 ? '#D4AF37' : '#EF4444'); @endphp
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold"
                                      style="background: {{ $rateColor }}20; color: {{ $rateColor }};">
                                    {{ $cohort['rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ─── Section 6: Daily Claims Sparkline ─────────────────────────────── --}}
    @if(!empty($dailySignups))
    <div class="rounded-xl p-6 mb-6" style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Claims Diarios (últimos 30 días)
        </h2>
        <p class="text-xs mb-6" style="color: #9CA3AF;">Número de claims completados por día</p>

        {{-- Sparkline bars --}}
        <div class="flex items-end gap-1 h-28">
            @foreach($dailySignups as $day)
                <div class="flex-1 flex flex-col items-center gap-1 group relative">
                    {{-- Tooltip --}}
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-150 z-10 pointer-events-none">
                        <div class="rounded-lg px-2 py-1 text-xs whitespace-nowrap" style="background: #2A2A2A; color: #F5F5F5; border: 1px solid #D4AF3740;">
                            {{ $day['label'] }}: {{ $day['count'] }}
                        </div>
                    </div>
                    {{-- Bar --}}
                    <div class="w-full rounded-t-sm transition-all duration-500"
                         style="height: {{ max($day['pct'], $day['count'] > 0 ? 4 : 1) }}%;
                                background: {{ $day['count'] > 0 ? 'linear-gradient(180deg, #D4AF37 0%, #B8960E 100%)' : '#2A2A2A' }};
                                min-height: 3px;">
                    </div>
                    {{-- Label --}}
                    @if($loop->iteration % 5 === 0 || $loop->first || $loop->last)
                        <span class="text-xs mt-1" style="color: #6B7280; font-size: 0.6rem;">{{ $day['label'] }}</span>
                    @else
                        <span class="text-xs mt-1" style="color: transparent; font-size: 0.6rem;">-</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Daily totals summary --}}
        <div class="grid grid-cols-3 gap-4 mt-6 pt-4" style="border-top: 1px solid #2A2A2A;">
            @php
                $totalClaims = collect($dailySignups)->sum('count');
                $avgPerDay   = count($dailySignups) > 0 ? round($totalClaims / count($dailySignups), 1) : 0;
                $maxDay      = collect($dailySignups)->sortByDesc('count')->first();
            @endphp
            <div class="text-center">
                <div class="text-xl font-bold text-white">{{ number_format($totalClaims) }}</div>
                <div class="text-xs mt-1" style="color: #9CA3AF;">Total claims (30d)</div>
            </div>
            <div class="text-center">
                <div class="text-xl font-bold" style="color: #D4AF37;">{{ $avgPerDay }}</div>
                <div class="text-xs mt-1" style="color: #9CA3AF;">Promedio diario</div>
            </div>
            <div class="text-center">
                <div class="text-xl font-bold" style="color: #10B981;">{{ $maxDay ? $maxDay['count'] : 0 }}</div>
                <div class="text-xs mt-1" style="color: #9CA3AF;">Mejor día {{ $maxDay ? '(' . $maxDay['label'] . ')' : '' }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ─── Footer timestamp ────────────────────────────────────────────────── --}}
    <div class="text-right text-xs pb-2" style="color: #4B5563;">
        Actualizado {{ now()->format('d/m/Y H:i') }} CST
    </div>

</x-filament-panels::page>
