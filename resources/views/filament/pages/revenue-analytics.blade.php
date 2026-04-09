<x-filament-panels::page>

    {{-- ─── KPI Cards ──────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        {{-- MRR --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
             style="background: linear-gradient(135deg, #1A1A1A 0%, #2A2A2A 100%); border: 1px solid #D4AF3740;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #D4AF37;">MRR</span>
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($mrr) }}</div>
            <div class="text-xs text-gray-400">Ingreso mensual recurrente</div>
        </div>

        {{-- ARR --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
             style="background: linear-gradient(135deg, #1A1A1A 0%, #2A2A2A 100%); border: 1px solid #D4AF3740;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase" style="color: #D4AF37;">ARR</span>
                <svg class="w-5 h-5" style="color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">${{ number_format($arr) }}</div>
            <div class="text-xs text-gray-400">Ingreso anual recurrente</div>
        </div>

        {{-- Active Subscribers --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
             style="background: linear-gradient(135deg, #1A1A1A 0%, #2A2A2A 100%); border: 1px solid #16a34a40;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase text-green-400">Suscriptores</span>
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($totalActive) }}</div>
            <div class="text-xs text-gray-400">Con plan activo</div>
        </div>

        {{-- Churn Risk --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
             style="background: linear-gradient(135deg, #1A1A1A 0%, #2A2A2A 100%); border: 1px solid #dc262640;">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold tracking-widest uppercase text-red-400">Riesgo Churn</span>
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="text-3xl font-bold text-white">{{ number_format($churnRisk) }}</div>
            <div class="text-xs text-gray-400">Vencen en 30 días</div>
        </div>

    </div>

    {{-- ─── Revenue Breakdown Table ─────────────────────────────────────────── --}}
    <div class="rounded-2xl mb-6 overflow-hidden"
         style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <div class="px-6 py-4" style="border-bottom: 1px solid #2A2A2A;">
            <h2 class="text-base font-semibold text-white flex items-center gap-2">
                <span style="color: #D4AF37;">▸</span>
                Desglose de Ingresos
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #2A2A2A;">
                        <th class="text-left px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Plan</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Activos</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Precio</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">MRR</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">ARR</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Elite --}}
                    <tr style="border-bottom: 1px solid #2A2A2A20;">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background: #7c3aed20; color: #a78bfa;">Elite 👑</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-white font-semibold">{{ number_format($eliteActive) }}</td>
                        <td class="px-6 py-4 text-right text-gray-400">$79/mes</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #D4AF37;">${{ number_format($eliteActive * 79) }}</td>
                        <td class="px-6 py-4 text-right text-gray-300">${{ number_format($eliteActive * 79 * 12) }}</td>
                    </tr>
                    {{-- Premium --}}
                    <tr style="border-bottom: 1px solid #2A2A2A20;">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background: #d9770620; color: #fbbf24;">Premium ⭐</span>
                        </td>
                        <td class="px-6 py-4 text-right text-white font-semibold">{{ number_format($premiumActive) }}</td>
                        <td class="px-6 py-4 text-right text-gray-400">$29/mes</td>
                        <td class="px-6 py-4 text-right font-bold" style="color: #D4AF37;">${{ number_format($premiumActive * 29) }}</td>
                        <td class="px-6 py-4 text-right text-gray-300">${{ number_format($premiumActive * 29 * 12) }}</td>
                    </tr>
                    {{-- Claimed --}}
                    <tr style="border-bottom: 1px solid #2A2A2A20;">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background: #0369a120; color: #38bdf8;">Reclamado</span>
                        </td>
                        <td class="px-6 py-4 text-right text-white font-semibold">{{ number_format($claimedCount) }}</td>
                        <td class="px-6 py-4 text-right text-gray-400">$0/mes</td>
                        <td class="px-6 py-4 text-right text-gray-500">$0</td>
                        <td class="px-6 py-4 text-right text-gray-500">$0</td>
                    </tr>
                    {{-- Sin plan --}}
                    <tr style="border-bottom: 1px solid #2A2A2A;">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-gray-400">Sin plan</span>
                        </td>
                        <td class="px-6 py-4 text-right text-white font-semibold">{{ number_format($freeCount) }}</td>
                        <td class="px-6 py-4 text-right text-gray-400">$0/mes</td>
                        <td class="px-6 py-4 text-right text-gray-500">$0</td>
                        <td class="px-6 py-4 text-right text-gray-500">$0</td>
                    </tr>
                    {{-- Total --}}
                    <tr style="background: #D4AF3710;">
                        <td class="px-6 py-4 font-bold text-white">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-white">
                            {{ number_format($eliteActive + $premiumActive + $claimedCount + $freeCount) }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-400">—</td>
                        <td class="px-6 py-4 text-right text-xl font-bold" style="color: #D4AF37;">${{ number_format($mrr) }}</td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-white">${{ number_format($arr) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Subscription Pipeline Funnel ───────────────────────────────────── --}}
    <div class="rounded-2xl mb-6 p-6"
         style="background: #1A1A1A; border: 1px solid #2A2A2A;">
        <h2 class="text-base font-semibold text-white mb-5 flex items-center gap-2">
            <span style="color: #D4AF37;">▸</span>
            Pipeline de Conversión
        </h2>
        <div class="flex flex-col sm:flex-row items-center gap-0">

            {{-- Step 1: Aprobados --}}
            <div class="flex-1 flex flex-col items-center text-center p-4">
                <div class="text-2xl font-bold text-white mb-1">{{ number_format($totalApproved) }}</div>
                <div class="text-xs text-gray-400 mb-2">Total Aprobados</div>
                <div class="w-full rounded-full h-2 bg-gray-700">
                    <div class="h-2 rounded-full bg-gray-400" style="width: 100%;"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">100%</div>
            </div>

            {{-- Arrow --}}
            <div class="text-gray-600 text-xl sm:rotate-0 rotate-90">›</div>

            {{-- Step 2: Reclamados --}}
            <div class="flex-1 flex flex-col items-center text-center p-4">
                <div class="text-2xl font-bold text-blue-400 mb-1">{{ number_format($totalClaimed) }}</div>
                <div class="text-xs text-gray-400 mb-2">Reclamados</div>
                <div class="w-full rounded-full h-2 bg-gray-700">
                    <div class="h-2 rounded-full bg-blue-500" style="width: {{ min(100, $claimRate) }}%;"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $claimRate }}%</div>
            </div>

            {{-- Arrow --}}
            <div class="text-gray-600 text-xl sm:rotate-0 rotate-90">›</div>

            {{-- Step 3: Con Suscripción --}}
            <div class="flex-1 flex flex-col items-center text-center p-4">
                <div class="text-2xl font-bold mb-1" style="color: #D4AF37;">{{ number_format($withSub) }}</div>
                <div class="text-xs text-gray-400 mb-2">Con Suscripción</div>
                <div class="w-full rounded-full h-2 bg-gray-700">
                    <div class="h-2 rounded-full" style="width: {{ min(100, $subRate) }}%; background: #D4AF37;"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $subRate }}% de reclamados</div>
            </div>

            {{-- Arrow --}}
            <div class="text-gray-600 text-xl sm:rotate-0 rotate-90">›</div>

            {{-- Step 4: Elite --}}
            <div class="flex-1 flex flex-col items-center text-center p-4">
                <div class="text-2xl font-bold text-purple-400 mb-1">{{ number_format($eliteActive) }}</div>
                <div class="text-xs text-gray-400 mb-2">Elite 👑</div>
                <div class="w-full rounded-full h-2 bg-gray-700">
                    <div class="h-2 rounded-full bg-purple-500" style="width: {{ min(100, $eliteRate) }}%;"></div>
                </div>
                <div class="text-xs text-gray-500 mt-1">{{ $eliteRate }}% de subs</div>
            </div>

        </div>
    </div>

    {{-- ─── Expiring Subscriptions ──────────────────────────────────────────── --}}
    <div class="rounded-2xl overflow-hidden"
         style="background: #1A1A1A; border: 1px solid #dc262640;">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid #2A2A2A;">
            <h2 class="text-base font-semibold text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Vencimientos Próximos (30 días)
            </h2>
            <span class="text-xs px-2 py-1 rounded-full text-red-400 font-semibold"
                  style="background: #dc262620;">
                {{ $expiringRestaurants->count() }} restaurantes
            </span>
        </div>

        @if($expiringRestaurants->isEmpty())
            <div class="px-6 py-10 text-center text-gray-500">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Sin vencimientos próximos. ¡Todo en orden!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid #2A2A2A;">
                            <th class="text-left px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Restaurante</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Propietario</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Plan</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Estado</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Vence</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold tracking-wider text-gray-400 uppercase">Días</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringRestaurants as $restaurant)
                            @php
                                $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($restaurant->subscription_expires_at), false);
                                $urgentClass = $daysLeft <= 7 ? 'text-red-400' : 'text-yellow-400';
                            @endphp
                            <tr style="border-bottom: 1px solid #2A2A2A20;" class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $restaurant->name }}</div>
                                    @if($restaurant->state)
                                        <div class="text-xs text-gray-500">{{ $restaurant->state->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-300">{{ $restaurant->owner_name ?? '—' }}</div>
                                    @if($restaurant->owner_email)
                                        <a href="mailto:{{ $restaurant->owner_email }}"
                                           class="text-xs hover:underline" style="color: #D4AF37;">
                                            {{ $restaurant->owner_email }}
                                        </a>
                                    @endif
                                    @if($restaurant->owner_phone)
                                        <div class="text-xs text-gray-500">{{ $restaurant->owner_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $tierColors = [
                                            'elite'   => 'background: #7c3aed20; color: #a78bfa;',
                                            'premium' => 'background: #d9770620; color: #fbbf24;',
                                            'claimed' => 'background: #0369a120; color: #38bdf8;',
                                        ];
                                        $tierLabels = [
                                            'elite'   => 'Elite 👑',
                                            'premium' => 'Premium ⭐',
                                            'claimed' => 'Reclamado',
                                        ];
                                        $tierStyle = $tierColors[$restaurant->subscription_tier] ?? 'background: #37415120; color: #9ca3af;';
                                        $tierLabel = $tierLabels[$restaurant->subscription_tier] ?? 'Sin plan';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          style="{{ $tierStyle }}">
                                        {{ $tierLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusLabels = [
                                            'active'   => ['label' => 'Activo', 'style' => 'background: #16a34a20; color: #4ade80;'],
                                            'canceled' => ['label' => 'Cancelado', 'style' => 'background: #dc262620; color: #f87171;'],
                                            'expired'  => ['label' => 'Expirado', 'style' => 'background: #d9770620; color: #fb923c;'],
                                            'past_due' => ['label' => 'Pago vencido', 'style' => 'background: #dc262620; color: #f87171;'],
                                        ];
                                        $statusInfo = $statusLabels[$restaurant->subscription_status] ?? ['label' => '—', 'style' => 'color: #9ca3af;'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          style="{{ $statusInfo['style'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-300">
                                    {{ \Carbon\Carbon::parse($restaurant->subscription_expires_at)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold {{ $urgentClass }}">
                                    {{ $daysLeft }}d
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-filament-panels::page>
