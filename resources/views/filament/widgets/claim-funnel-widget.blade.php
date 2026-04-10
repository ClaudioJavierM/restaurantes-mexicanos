<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Funnel de Conversión — Claim de Restaurantes
        </x-slot>
        <x-slot name="description">
            {{ $period }} · {{ number_format($pageViews) }} visitantes → {{ number_format($completed) }} claims completados
        </x-slot>

        @php
            $data = $this->getViewData();
            $steps = $data['steps'];
            $conversionRate = $data['conversionRate'];
            $premiumUpgrades = $data['premiumUpgrades'];
            $pageViews = $data['pageViews'];
            $completed = $data['completed'];
        @endphp

        {{-- Funnel Steps --}}
        <div class="space-y-3">
            @foreach($steps as $index => $step)
                @php
                    $barWidth = $step['pct_total'];
                    $isFirst = $index === 0;
                    $isLast = $index === count($steps) - 1;
                    $colors = [
                        'bg-amber-500',
                        'bg-amber-400',
                        'bg-yellow-400',
                        'bg-green-500',
                        'bg-green-400',
                        'bg-emerald-500',
                    ];
                    $color = $colors[$index] ?? 'bg-blue-500';
                @endphp

                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-xs font-bold flex items-center justify-center text-gray-600 dark:text-gray-300">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="font-bold text-gray-900 dark:text-white">
                                {{ number_format($step['count']) }}
                            </span>
                            @if(!$isFirst)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $step['pct_prev'] }}% del paso anterior
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-5 overflow-hidden">
                        <div
                            class="{{ $color }} h-5 rounded-full transition-all duration-500 flex items-center justify-end pr-2"
                            style="width: {{ max($barWidth, $step['count'] > 0 ? 2 : 0) }}%"
                        >
                            @if($barWidth >= 8)
                                <span class="text-xs font-semibold text-white">{{ $step['pct_total'] }}%</span>
                            @endif
                        </div>
                    </div>

                    {{-- Drop-off arrow --}}
                    @if(!$isLast && $step['count'] > 0)
                        @php
                            $nextStep = $steps[$index + 1];
                            $dropOff = 100 - $nextStep['pct_prev'];
                        @endphp
                        @if($dropOff > 0)
                            <div class="mt-1 ml-8 text-xs text-red-500 dark:text-red-400">
                                ↓ {{ $dropOff }}% abandona aquí
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Summary Cards --}}
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ $conversionRate }}%
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Tasa de conversión total</div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ number_format($pageViews) }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Visitaron /claim</div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ number_format($completed) }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Claims completados</div>
            </div>

            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                    {{ number_format($premiumUpgrades) }}
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Upgrades a Premium</div>
            </div>
        </div>

        @if($pageViews === 0)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-center">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Sin datos aún. Los eventos de claim comenzarán a registrarse cuando los propietarios visiten <strong>/claim</strong>.
                </p>
            </div>
        @endif

        <div class="mt-4 text-xs text-gray-400 dark:text-gray-500 text-right">
            Período: {{ $period }}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
