{{-- Data Completeness Indicator --}}
@php
    $completeness = 0;
    $sources = [];

    // Basic info (40 points max)
    if ($restaurant->name) $completeness += 5;
    if ($restaurant->address) $completeness += 5;
    if ($restaurant->city) $completeness += 5;
    if ($restaurant->phone) $completeness += 5;
    if ($restaurant->website) $completeness += 10;
    if ($restaurant->description) $completeness += 10;

    // Photos (20 points)
    $photoCount = $restaurant->getMedia('photos')->count();
    if ($photoCount > 0) {
        $completeness += min($photoCount * 4, 20); // Up to 5 photos = 20 points
    }

    // Data sources (40 points max)
    if ($restaurant->google_verified || $restaurant->google_place_id) {
        $completeness += 20;
        $sources[] = 'Google';
    }
    if ($restaurant->yelp_id || $restaurant->import_source === 'yelp') {
        $completeness += 20;
        $sources[] = 'Yelp';
    }

    // Cap at 100
    $completeness = min($completeness, 100);

    // Determine color
    $color = match(true) {
        $completeness >= 80 => 'emerald',
        $completeness >= 60 => 'blue',
        $completeness >= 40 => 'yellow',
        default => 'gray',
    };

    $label = match(true) {
        $completeness >= 90 => 'Perfil Completo',
        $completeness >= 70 => 'Muy Completo',
        $completeness >= 50 => 'Bueno',
        default => 'Basico',
    };
@endphp

@if(count($sources) > 1)
    {{-- Show data fusion badge only when we have multiple sources --}}
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-sm" title="Datos combinados de {{ implode(' + ', $sources) }} para mayor precision">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Datos Fusionados
    </span>
@endif
