@php
    $rankings = $restaurant->rankings()
        ->where('year', now()->year - 1)
        ->where('position', '<=', 25)
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
                $badgeClasses = match(true) {
                    $ranking->position == 1 => 'bg-gradient-to-r from-amber-600 to-yellow-500 text-white ring-2 ring-amber-300/50 shadow-md',
                    $ranking->position <= 3 => 'bg-gradient-to-r from-amber-700 to-amber-600 text-white ring-1 ring-amber-400/40 shadow-sm',
                    $ranking->position <= 10 => 'bg-gradient-to-r from-slate-600 to-slate-500 text-white ring-1 ring-slate-400/30 shadow-sm',
                    default => 'bg-gray-200 text-gray-700 ring-1 ring-gray-300',
                };
                $iconSvg = match(true) {
                    $ranking->position <= 3 => '<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v1a2 2 0 002 2h1.06a7.04 7.04 0 003.272 4.35L8.12 15.7A2 2 0 009.98 18h.04a2 2 0 001.86-2.3l-1.212-4.35A7.04 7.04 0 0013.94 7H15a2 2 0 002-2V4a2 2 0 00-2-2H5z" clip-rule="evenodd"/></svg>',
                    default => '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>',
                };
                $scopeLabel = match($ranking->ranking_type) {
                    'national' => $ranking->ranking_scope === 'usa' ? 'USA' : strtoupper($ranking->ranking_scope),
                    'state' => $ranking->ranking_scope,
                    'city' => $ranking->ranking_scope,
                    default => $ranking->ranking_scope,
                };
            @endphp
            <a href="{{ url('/guia') }}?scope={{ $ranking->ranking_type }}{{ $ranking->ranking_type !== 'national' ? '&state=' . $ranking->ranking_scope : '' }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide uppercase transition-all hover:scale-105 hover:shadow-lg {{ $badgeClasses }}">
                {!! $iconSvg !!}
                <span>{{ $ranking->badge_name }}</span>
            </a>
        @endforeach
    </div>

    @if($isOwner)
    <div class="mt-3">
        <a href="{{ route('ranking-certificate.download', ['restaurant' => $restaurant, 'year' => now()->year - 1]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 text-amber-300 text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition-colors ring-1 ring-amber-400/30">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Descargar Certificado FAMER Awards
        </a>
    </div>
    @endif
</div>
@endif
