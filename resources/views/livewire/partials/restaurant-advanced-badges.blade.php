<!-- Advanced Mexican Filters Badges -->
<div class="flex flex-wrap gap-2 mt-3">
    <!-- Price Range -->
    @if($restaurant->price_range)
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
            💰 {{ $restaurant->price_range }}
        </span>
    @endif

    <!-- Spice Level -->

    <!-- Mexican Region -->
    @if($restaurant->mexican_region)
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800 border border-red-200">
            🇲🇽 {{ $restaurant->mexican_region }}
        </span>
    @endif

    <!-- Authenticity Badges -->
    @foreach($restaurant->authenticity_badges as $badge)
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-{{ $badge['color'] }}-100 text-{{ $badge['color'] }}-800 border border-{{ $badge['color'] }}-200"
              title="{{ $badge['name'] }}">
            {{ $badge['icon'] }}
        </span>
    @endforeach

    <!-- Dietary Options (mostrar solo 2) -->
    @if($restaurant->dietary_options && count($restaurant->dietary_options) > 0)
        @foreach(array_slice($restaurant->dietary_options, 0, 2) as $option)
            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                🥗 {{ \App\Models\Restaurant::getDietaryOptions()[$option] ?? $option }}
            </span>
        @endforeach
        @if(count($restaurant->dietary_options) > 2)
            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                +{{ count($restaurant->dietary_options) - 2 }}
            </span>
        @endif
    @endif

    <!-- Accepts Reservations -->
    @if($restaurant->accepts_reservations)
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
            📅 Reservaciones
        </span>
    @endif

    <!-- Online Ordering -->
    @if($restaurant->online_ordering)
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
            🛒 Orden Online
        </span>
    @endif
</div>
