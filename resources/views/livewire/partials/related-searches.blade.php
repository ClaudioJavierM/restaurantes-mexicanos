{{-- Related Searches / Tags for SEO - Yelp Style --}}
@php
    // Build related searches based on restaurant data
    $relatedSearches = [];
    
    // Add category-based searches
    if ($restaurant->category) {
        $relatedSearches[] = [
            'label' => $restaurant->category->name . ' en ' . $restaurant->city,
            'query' => 'category=' . $restaurant->category->slug . '&state=' . ($restaurant->state?->code ?? ''),
        ];
    }
    
    // Add location-based searches
    $relatedSearches[] = [
        'label' => 'Restaurantes Mexicanos en ' . $restaurant->city,
        'query' => 'search=' . urlencode($restaurant->city) . '&state=' . ($restaurant->state?->code ?? ''),
    ];
    
    $relatedSearches[] = [
        'label' => 'Comida Mexicana cerca de ' . $restaurant->city,
        'query' => 'search=' . urlencode('comida mexicana') . '&state=' . ($restaurant->state?->code ?? ''),
    ];
    
    // Add Yelp categories if available
    $yelpCategories = is_array($restaurant->yelp_categories) ? $restaurant->yelp_categories : [];
    foreach ($yelpCategories as $cat) {
        $title = $cat['title'] ?? $cat['alias'] ?? null;
        if ($title && !str_contains(strtolower($title), 'mexican')) {
            $relatedSearches[] = [
                'label' => $title . ' en ' . ($restaurant->state?->code ?? ''),
                'query' => 'search=' . urlencode($title),
            ];
        }
    }
    
    // Add common Mexican food searches
    $stateCode = $restaurant->state?->code ?? '';
    $commonSearches = [
        ['label' => 'Tacos cerca de mi', 'query' => 'search=tacos&state=' . $stateCode],
        ['label' => 'Burritos en ' . $stateCode, 'query' => 'search=burritos&state=' . $stateCode],
        ['label' => 'Mariscos Mexicanos', 'query' => 'search=mariscos&state=' . $stateCode],
        ['label' => 'Birria cerca de ' . $restaurant->city, 'query' => 'search=birria&state=' . $stateCode],
        ['label' => 'Carnitas en ' . $stateCode, 'query' => 'search=carnitas&state=' . $stateCode],
    ];
    
    // Merge and dedupe
    $allSearches = array_merge($relatedSearches, $commonSearches);
    $uniqueLabels = [];
    $finalSearches = [];
    foreach ($allSearches as $search) {
        if (!in_array($search['label'], $uniqueLabels)) {
            $uniqueLabels[] = $search['label'];
            $finalSearches[] = $search;
        }
    }
    $finalSearches = array_slice($finalSearches, 0, 10);
@endphp

@if(count($finalSearches) > 0)
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Busquedas Relacionadas</h2>
    <p class="text-gray-500 text-sm mb-6">Descubre mas opciones de comida mexicana</p>
    
    <div class="flex flex-wrap gap-3">
        @foreach($finalSearches as $search)
        <a href="/restaurantes?{{ $search['query'] }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-600 rounded-full text-sm font-medium transition-colors border border-gray-200 hover:border-red-200">
            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            {{ $search['label'] }}
        </a>
        @endforeach
    </div>
    
    {{-- SEO-friendly breadcrumb/links --}}
    <div class="mt-6 pt-6 border-t border-gray-200">
        <nav class="text-sm text-gray-500">
            <ol class="flex flex-wrap items-center gap-2">
                <li>
                    <a href="/" class="hover:text-red-600">Inicio</a>
                </li>
                <li class="text-gray-300">/</li>
                <li>
                    <a href="/restaurantes" class="hover:text-red-600">Restaurantes</a>
                </li>
                @if($restaurant->state)
                <li class="text-gray-300">/</li>
                <li>
                    <a href="/restaurantes?state={{ $restaurant->state->code }}" class="hover:text-red-600">{{ $restaurant->state->name }}</a>
                </li>
                @endif
                <li class="text-gray-300">/</li>
                <li>
                    <a href="/restaurantes?search={{ urlencode($restaurant->city) }}&state={{ $restaurant->state?->code ?? '' }}" class="hover:text-red-600">{{ $restaurant->city }}</a>
                </li>
                <li class="text-gray-300">/</li>
                <li class="text-gray-700 font-medium">{{ $restaurant->name }}</li>
            </ol>
        </nav>
    </div>
</div>
@endif
