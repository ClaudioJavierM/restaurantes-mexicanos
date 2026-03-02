<div>
    <button
        wire:click="toggleFavorite"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border transition-all duration-200 {{ $isFavorited ? 'bg-red-50 border-red-300 text-red-700 hover:bg-red-100' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}"
        title="{{ $isFavorited ? __('Remove from favorites') : __('Add to favorites') }}"
    >
        <!-- Heart Icon -->
        <svg
            class="w-5 h-5 transition-transform duration-200 {{ $isFavorited ? 'scale-110' : '' }}"
            fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>

        <!-- Text -->
        <span class="font-medium text-sm">
            @if($isFavorited)
                {{ __('Saved') }}
            @else
                {{ __('Save') }}
            @endif
        </span>

        <!-- Counter (optional) -->
        @if($favoritesCount > 0)
            <span class="text-xs px-2 py-0.5 rounded-full {{ $isFavorited ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-700' }}">
                {{ $favoritesCount }}
            </span>
        @endif
    </button>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mt-2 p-2 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>
