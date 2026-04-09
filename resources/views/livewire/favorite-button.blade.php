<div>
    <button
        wire:click="toggleFavorite"
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-all duration-200"
        style="{{ $isFavorited ? 'background:#2A2A2A; border:1px solid #D4AF37; color:#D4AF37;' : 'background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;' }}"
        title="{{ $isFavorited ? 'Quitar de favoritos' : 'Guardar en favoritos' }}"
    >
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
        <span class="text-sm">
            {{ $isFavorited ? 'Guardado' : 'Guardar' }}
        </span>
    </button>
</div>
