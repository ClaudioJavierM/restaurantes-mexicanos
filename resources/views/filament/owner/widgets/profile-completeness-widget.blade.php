@php
    $data       = $this->getCompletenessData();
    $score      = $data['score'];
    $items      = $data['items'];
    $restaurant = $data['restaurant'];

    $badgeLabel = match(true) {
        $score >= 90 => '¡Perfil completo!',
        $score >= 71 => 'Perfil casi completo',
        $score >= 41 => 'Perfil en progreso',
        default      => 'Tu perfil necesita atención',
    };

    $badgeClasses = match(true) {
        $score >= 90 => 'bg-green-900/60 text-green-300 border border-green-700',
        $score >= 71 => 'bg-blue-900/60 text-blue-300 border border-blue-700',
        $score >= 41 => 'bg-yellow-900/60 text-yellow-300 border border-yellow-700',
        default      => 'bg-red-900/60 text-red-300 border border-red-700',
    };

    $progressWidth = min($score, 100);
@endphp

<div class="rounded-xl border border-gray-700/50 bg-gray-900 p-6 shadow-lg">

    {{-- Header --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg" style="background-color:#1c1710;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-100">Completitud del Perfil</h3>
                @if($restaurant)
                    <p class="text-xs text-gray-400">{{ $restaurant->name }}</p>
                @endif
            </div>
        </div>

        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
            {{ $badgeLabel }}
        </span>
    </div>

    @if(!$restaurant)
        {{-- No restaurant state --}}
        <div class="rounded-lg border border-yellow-800/40 bg-yellow-900/20 p-4 text-center">
            <p class="text-sm text-yellow-300">No tienes un restaurante asociado a tu cuenta.</p>
            <a href="/owner/claim" class="mt-2 inline-block text-sm font-medium" style="color:#D4AF37;">
                Reclamar mi restaurante →
            </a>
        </div>
    @else
        {{-- Score display --}}
        <div class="mb-4 flex items-end gap-2">
            <span class="leading-none" style="font-size:2.25rem; font-weight:700; color:#D4AF37;">{{ $score }}</span>
            <span class="mb-1 text-lg font-medium text-gray-400">/ 100 pts</span>
        </div>

        {{-- Progress bar --}}
        <div class="mb-6 h-3 w-full overflow-hidden rounded-full bg-gray-700">
            <div
                class="h-3 rounded-full transition-all duration-700"
                style="width: {{ $progressWidth }}%; background: linear-gradient(90deg, #b8942a 0%, #D4AF37 60%, #f0d060 100%);"
            ></div>
        </div>

        {{-- Checklist --}}
        <ul class="space-y-2.5">
            @foreach($items as $item)
                <li class="flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 transition-colors
                    {{ $item['done'] ? 'bg-green-900/20' : 'bg-gray-800/60 hover:bg-gray-800' }}">

                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Status icon --}}
                        @if($item['done'])
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-800/60">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @else
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-gray-600">
                            </span>
                        @endif

                        {{-- Label --}}
                        <span class="truncate text-sm {{ $item['done'] ? 'text-gray-300' : 'text-gray-200 font-medium' }}">
                            {{ $item['label'] }}
                        </span>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        {{-- Action link (only if not done) --}}
                        @if(!$item['done'])
                            <a
                                href="{{ $item['url'] }}"
                                class="rounded px-2.5 py-1 text-xs font-semibold transition-colors hover:bg-yellow-900/40"
                                style="color:#D4AF37; border:1px solid #5a4a1a;"
                            >
                                {{ $item['action'] }}
                            </a>
                        @endif

                        {{-- Points badge --}}
                        <span class="text-xs font-medium tabular-nums {{ $item['done'] ? 'text-green-400' : 'text-gray-500' }}">
                            +{{ $item['points'] }} pts
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
