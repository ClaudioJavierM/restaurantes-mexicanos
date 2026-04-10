<div style="position:relative; display:inline-block;">

    @if(!auth()->check())
        {{-- Not authenticated --}}
        <a href="/login"
           title="Inicia sesión para guardar en una lista"
           style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:8px; background:#2A2A2A; border:1px solid #3A3A3A; color:#6B7280; cursor:pointer; text-decoration:none;"
           onmouseover="this.style.borderColor='#D4AF37'; this.style.color='#D4AF37';"
           onmouseout="this.style.borderColor='#3A3A3A'; this.style.color='#6B7280';">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </a>
    @else
        {{-- Bookmark button --}}
        <button
            wire:click="toggleDropdown"
            type="button"
            title="{{ $isInAnyCollection ? 'Guardado en una lista' : 'Guardar en lista' }}"
            style="display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:8px; border:1px solid {{ $isInAnyCollection ? '#D4AF37' : '#3A3A3A' }}; background:{{ $isInAnyCollection ? 'rgba(212,175,55,0.1)' : '#2A2A2A' }}; color:{{ $isInAnyCollection ? '#D4AF37' : '#9CA3AF' }}; cursor:pointer;"
            onmouseover="this.style.borderColor='#D4AF37'; this.style.color='#D4AF37';"
            onmouseout="this.style.borderColor='{{ $isInAnyCollection ? '#D4AF37' : '#3A3A3A' }}'; this.style.color='{{ $isInAnyCollection ? '#D4AF37' : '#9CA3AF' }}';"
        >
            <svg width="18" height="18" fill="{{ $isInAnyCollection ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </button>

        {{-- Dropdown --}}
        @if($showDropdown)
            <div
                x-data
                x-on:click.outside="$wire.closeDropdown()"
                style="position:absolute; z-index:9999; top:calc(100% + 8px); right:0; min-width:240px; max-width:280px; background:#1A1A1A; border:1px solid #3A3A3A; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.8); overflow:hidden;"
            >
                {{-- Header --}}
                <div style="padding:12px 14px 8px; border-bottom:1px solid #2A2A2A;">
                    <p style="color:#9CA3AF; font-size:0.78rem; font-weight:600; margin:0; text-transform:uppercase; letter-spacing:0.05em;">
                        Guardar en lista
                    </p>
                </div>

                {{-- Collections list --}}
                <div style="max-height:220px; overflow-y:auto; padding:6px 0;">
                    @forelse($collections as $collection)
                        @php $inThis = $collection->has_restaurant > 0; @endphp
                        <button
                            wire:click="toggleCollection({{ $collection->id }})"
                            type="button"
                            style="display:flex; align-items:center; gap:10px; width:100%; padding:9px 14px; background:none; border:none; cursor:pointer; text-align:left; transition:background 0.15s;"
                            onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='none'"
                        >
                            {{-- Checkbox --}}
                            <span style="flex-shrink:0; width:18px; height:18px; border-radius:4px; border:2px solid {{ $inThis ? '#D4AF37' : '#4A4A4A' }}; background:{{ $inThis ? '#D4AF37' : 'transparent' }}; display:flex; align-items:center; justify-content:center;">
                                @if($inThis)
                                    <svg width="10" height="10" fill="none" stroke="#0B0B0B" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </span>
                            <span style="flex:1; overflow:hidden;">
                                <span style="display:block; color:#F5F5F5; font-size:0.88rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $collection->name }}
                                </span>
                                <span style="display:block; color:#6B7280; font-size:0.75rem;">
                                    {{ $collection->items_count }} restaurante(s)
                                    @if(!$collection->is_public)
                                        · Privada
                                    @endif
                                </span>
                            </span>
                        </button>
                    @empty
                        <div style="padding:16px 14px; text-align:center;">
                            <p style="color:#6B7280; font-size:0.82rem; margin:0;">Aún no tienes listas</p>
                        </div>
                    @endforelse
                </div>

                {{-- Divider --}}
                <div style="border-top:1px solid #2A2A2A;">
                    @if(!$showNewInput)
                        {{-- "Nueva lista" trigger --}}
                        <button
                            wire:click="$set('showNewInput', true)"
                            type="button"
                            style="display:flex; align-items:center; gap:8px; width:100%; padding:10px 14px; background:none; border:none; cursor:pointer; color:#D4AF37; font-size:0.85rem; font-weight:600;"
                            onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='none'"
                        >
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva lista...
                        </button>
                    @else
                        {{-- Inline new list form --}}
                        <div style="padding:10px 14px;">
                            <div style="display:flex; gap:6px; align-items:center;">
                                <input
                                    wire:model="newListName"
                                    type="text"
                                    maxlength="100"
                                    placeholder="Nombre de la lista"
                                    autofocus
                                    style="flex:1; background:#0B0B0B; border:1px solid #3A3A3A; border-radius:6px; padding:7px 10px; color:#F5F5F5; font-size:0.83rem; outline:none; min-width:0;"
                                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#3A3A3A'"
                                    wire:keydown.enter="createAndAdd(newListName)"
                                >
                                <button
                                    wire:click="createAndAdd(newListName)"
                                    type="button"
                                    style="flex-shrink:0; background:#D4AF37; color:#0B0B0B; border:none; border-radius:6px; padding:7px 12px; font-weight:700; font-size:0.82rem; cursor:pointer; white-space:nowrap;"
                                    onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                                >
                                    Crear
                                </button>
                                <button
                                    wire:click="$set('showNewInput', false)"
                                    type="button"
                                    style="flex-shrink:0; background:none; border:none; color:#6B7280; cursor:pointer; padding:6px; border-radius:4px; line-height:0;"
                                    onmouseover="this.style.color='#F5F5F5'" onmouseout="this.style.color='#6B7280'"
                                >
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif

</div>
