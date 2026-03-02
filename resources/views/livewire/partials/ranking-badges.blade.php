@php
    $rankings = $restaurant->rankings()
        ->where('year', now()->year - 1)
        ->where('position', '<=', 25)
        ->orderBy('ranking_scope')
        ->orderBy('position')
        ->get();
    
    $isOwner = auth()->check() && (
        $restaurant->user_id === auth()->id() ||
        (isset($restaurant->owner_id) && $restaurant->owner_id === auth()->id())
    );
@endphp

@if($rankings->count() > 0)
<div class="mt-4">
    <div class="flex flex-wrap gap-2">
        @foreach($rankings as $ranking)
            @php
                $bgStyle = match(true) {
                    $ranking->position == 1 => 'background: linear-gradient(to right, #fbbf24, #f59e0b);',
                    $ranking->position <= 3 => 'background: linear-gradient(to right, #f59e0b, #f97316);',
                    $ranking->position <= 5 => 'background: linear-gradient(to right, #fb923c, #ef4444);',
                    $ranking->position <= 10 => 'background: linear-gradient(to right, #f87171, #ec4899);',
                    default => 'background: linear-gradient(to right, #9ca3af, #6b7280);',
                };
                $scopeIcon = match($ranking->ranking_type) {
                    'national' => '🇺🇸',
                    'state' => '📍',
                    'city' => '🏙️',
                    default => '🏆',
                };
            @endphp
            <a href="{{ url('/guia') }}?scope={{ $ranking->ranking_type }}{{ $ranking->ranking_type !== 'national' ? '&state=' . $ranking->ranking_scope : '' }}" 
               class="inline-flex items-center px-3 py-1.5 text-white text-sm font-semibold rounded-full shadow-md hover:shadow-lg transition-all transform hover:scale-105"
               style="{{ $bgStyle }}">
                <span class="mr-1">{{ $scopeIcon }}</span>
                <span class="mr-1">🏆</span>
                {{ $ranking->badge_name }}
            </a>
        @endforeach
    </div>
    
    @if($isOwner)
    <div class="mt-3">
        <a href="{{ route('ranking-certificate.download', ['restaurant' => $restaurant, 'year' => now()->year - 1]) }}" 
           class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg shadow transition-colors"
           style="background-color: #d97706;">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar Certificado FAMER Awards
        </a>
    </div>
    @endif
</div>
@endif
