<div class="min-h-screen py-8" style="background:#0B0B0B;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg" style="background:#14532d; border:1px solid #16a34a; color:#bbf7d0;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Mis Listas</h1>
                <p class="mt-1" style="color:#9CA3AF;">Organiza tus restaurantes favoritos en colecciones personalizadas</p>
            </div>
            <button
                wire:click="toggleCreateForm"
                type="button"
                style="background:#D4AF37; color:#0B0B0B; padding:10px 20px; border-radius:8px; font-weight:700; font-size:0.9rem; border:none; cursor:pointer; display:flex; align-items:center; gap:8px;"
                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Lista
            </button>
        </div>

        {{-- Create Form (collapsible) --}}
        @if($showCreateForm)
            <div class="mb-8 rounded-xl p-6" style="background:#1A1A1A; border:1px solid #D4AF37;">
                <h2 class="text-xl font-bold mb-5" style="color:#D4AF37; font-family:'Playfair Display',serif;">Crear Nueva Lista</h2>

                <form wire:submit.prevent="createCollection">
                    {{-- Name --}}
                    <div class="mb-4">
                        <label style="display:block; color:#9CA3AF; font-size:0.85rem; margin-bottom:6px;">
                            Nombre de la lista <span style="color:#D4AF37;">*</span>
                        </label>
                        <input
                            wire:model="newName"
                            type="text"
                            maxlength="100"
                            placeholder="Ej. Mis taquizas favoritas en Austin"
                            style="width:100%; background:#0B0B0B; border:1px solid #3A3A3A; border-radius:8px; padding:10px 14px; color:#F5F5F5; font-size:0.95rem; outline:none; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#3A3A3A'"
                        >
                        @error('newName')
                            <p style="color:#f87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label style="display:block; color:#9CA3AF; font-size:0.85rem; margin-bottom:6px;">
                            Descripción <span style="color:#6B7280;">(opcional)</span>
                        </label>
                        <textarea
                            wire:model="newDescription"
                            maxlength="300"
                            rows="2"
                            placeholder="Describe tu lista..."
                            style="width:100%; background:#0B0B0B; border:1px solid #3A3A3A; border-radius:8px; padding:10px 14px; color:#F5F5F5; font-size:0.95rem; outline:none; resize:vertical; box-sizing:border-box;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#3A3A3A'"
                        ></textarea>
                        @error('newDescription')
                            <p style="color:#f87171; font-size:0.8rem; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Privacy Toggle --}}
                    <div class="mb-6 flex items-center gap-3">
                        <button
                            type="button"
                            wire:click="$set('newIsPublic', !$newIsPublic)"
                            style="position:relative; width:48px; height:26px; border-radius:99px; border:none; cursor:pointer; transition:background 0.2s; background:{{ $newIsPublic ? '#D4AF37' : '#3A3A3A' }};"
                        >
                            <span style="position:absolute; top:3px; width:20px; height:20px; border-radius:50%; background:#fff; transition:left 0.2s; left:{{ $newIsPublic ? '25px' : '3px' }};"></span>
                        </button>
                        <span style="color:#F5F5F5; font-size:0.9rem;">
                            {{ $newIsPublic ? 'Pública — visible para todos' : 'Privada — solo tú la ves' }}
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            style="background:#D4AF37; color:#0B0B0B; padding:10px 24px; border-radius:8px; font-weight:700; border:none; cursor:pointer;"
                            onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                        >
                            Crear Lista
                        </button>
                        <button
                            type="button"
                            wire:click="toggleCreateForm"
                            style="background:#2A2A2A; color:#9CA3AF; padding:10px 24px; border-radius:8px; font-weight:600; border:1px solid #3A3A3A; cursor:pointer;"
                            onmouseover="this.style.color='#F5F5F5'" onmouseout="this.style.color='#9CA3AF'"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Detail View --}}
        @if($selectedCollection)
            <div class="mb-8">
                {{-- Back button --}}
                <button
                    wire:click="selectCollection(null)"
                    type="button"
                    style="display:inline-flex; align-items:center; gap:6px; color:#9CA3AF; font-size:0.9rem; background:none; border:none; cursor:pointer; margin-bottom:20px;"
                    onmouseover="this.style.color='#F5F5F5'" onmouseout="this.style.color='#9CA3AF'"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver a Mis Listas
                </button>

                {{-- Collection header --}}
                <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <h2 class="text-2xl font-bold" style="color:#F5F5F5; font-family:'Playfair Display',serif;">
                                {{ $selectedCollection->name }}
                            </h2>
                            @if($selectedCollection->is_public)
                                <span style="background:#1c3a1c; border:1px solid #16a34a; color:#4ade80; font-size:0.75rem; padding:2px 8px; border-radius:99px; font-weight:600;">
                                    Pública
                                </span>
                            @else
                                <span style="background:#2A2A2A; border:1px solid #3A3A3A; color:#9CA3AF; font-size:0.75rem; padding:2px 8px; border-radius:99px; font-weight:600;">
                                    Privada
                                </span>
                            @endif
                        </div>
                        @if($selectedCollection->description)
                            <p style="color:#9CA3AF;">{{ $selectedCollection->description }}</p>
                        @endif
                        <p style="color:#6B7280; font-size:0.85rem; margin-top:4px;">
                            {{ $selectedCollection->items_count ?? $selectedCollection->items->count() }} restaurante(s)
                        </p>
                    </div>
                    <button
                        wire:click="deleteCollection({{ $selectedCollection->id }})"
                        wire:confirm="¿Eliminar esta lista? No se puede deshacer."
                        type="button"
                        style="background:#2A2A2A; border:1px solid #7f1d1d; color:#f87171; padding:8px 16px; border-radius:8px; font-size:0.85rem; font-weight:600; cursor:pointer;"
                        onmouseover="this.style.background='#7f1d1d'" onmouseout="this.style.background='#2A2A2A'"
                    >
                        Eliminar Lista
                    </button>
                </div>

                {{-- Items grid --}}
                @if($selectedCollection->items->count() > 0)
                    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px;">
                        @foreach($selectedCollection->items as $item)
                            @php $restaurant = $item->restaurant; @endphp
                            @if($restaurant)
                                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; transition:transform 0.2s;"
                                     onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                    {{-- Image --}}
                                    <a href="{{ route('restaurants.show', $restaurant->slug) }}" style="display:block; position:relative;">
                                        @php $imgUrl = $restaurant->getDisplayImageUrl(); @endphp
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $restaurant->name }}"
                                                 style="width:100%; height:180px; object-fit:cover;" loading="lazy">
                                        @else
                                            <div style="width:100%; height:180px; background:#111; display:flex; align-items:center; justify-content:center; font-size:3rem;">
                                                🍽️
                                            </div>
                                        @endif
                                        {{-- Remove overlay --}}
                                        <button
                                            wire:click.prevent="removeItem({{ $item->id }})"
                                            wire:confirm="¿Quitar este restaurante de la lista?"
                                            type="button"
                                            style="position:absolute; top:10px; right:10px; width:32px; height:32px; border-radius:50%; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                                            title="Quitar de la lista"
                                        >
                                            <svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </a>
                                    {{-- Content --}}
                                    <div style="padding:14px;">
                                        <a href="{{ route('restaurants.show', $restaurant->slug) }}">
                                            <h3 style="color:#F5F5F5; font-weight:700; font-size:1rem; margin:0 0 4px; line-height:1.3;"
                                                onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                                                {{ $restaurant->name }}
                                            </h3>
                                        </a>
                                        <p style="color:#9CA3AF; font-size:0.82rem; margin:0 0 6px;">
                                            {{ $restaurant->city }}{{ $restaurant->state ? ', '.$restaurant->state->code : '' }}
                                        </p>
                                        @if($item->note)
                                            <p style="color:#6B7280; font-size:0.82rem; font-style:italic; margin:0 0 8px; border-left:2px solid #D4AF37; padding-left:8px;">
                                                "{{ $item->note }}"
                                            </p>
                                        @endif
                                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                                           style="display:block; text-align:center; background:#D4AF37; color:#0B0B0B; padding:8px; border-radius:6px; font-weight:700; font-size:0.85rem; text-decoration:none; margin-top:8px;"
                                           onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
                                            Ver Restaurante
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:60px 20px; border:1px dashed #3A3A3A; border-radius:12px;">
                        <div style="font-size:3rem; margin-bottom:12px;">📋</div>
                        <p style="color:#9CA3AF; margin:0 0 4px;">Esta lista está vacía</p>
                        <p style="color:#6B7280; font-size:0.85rem;">Usa el botón 🔖 en cualquier restaurante para agregarlo</p>
                    </div>
                @endif
            </div>

        {{-- Collections Grid --}}
        @else
            @if($collections->count() > 0)
                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:24px;">
                    @foreach($collections as $collection)
                        @php $coverUrl = $collection->cover_image_url; @endphp
                        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:14px; overflow:hidden; cursor:pointer; transition:border-color 0.2s, transform 0.2s;"
                             wire:click="selectCollection({{ $collection->id }})"
                             onmouseover="this.style.borderColor='#D4AF37'; this.style.transform='scale(1.02)'"
                             onmouseout="this.style.borderColor='#2A2A2A'; this.style.transform='scale(1)'">

                            {{-- Cover image --}}
                            @if($coverUrl)
                                <img src="{{ $coverUrl }}" alt="{{ $collection->name }}"
                                     style="width:100%; height:160px; object-fit:cover;" loading="lazy">
                            @else
                                <div style="width:100%; height:160px; background:linear-gradient(135deg,#1A1A1A,#2A2A2A); display:flex; align-items:center; justify-content:center;">
                                    <svg width="48" height="48" fill="none" stroke="#3A3A3A" stroke-width="1" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div style="padding:16px;">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:6px;">
                                    <h3 style="color:#F5F5F5; font-weight:700; font-size:1.05rem; margin:0; line-height:1.3; flex:1;">
                                        {{ $collection->name }}
                                    </h3>
                                    @if($collection->is_public)
                                        <span style="flex-shrink:0; background:#1c3a1c; border:1px solid #16a34a; color:#4ade80; font-size:0.7rem; padding:2px 7px; border-radius:99px; font-weight:600;">
                                            Pública
                                        </span>
                                    @else
                                        <span style="flex-shrink:0; background:#2A2A2A; border:1px solid #3A3A3A; color:#6B7280; font-size:0.7rem; padding:2px 7px; border-radius:99px; font-weight:600;">
                                            Privada
                                        </span>
                                    @endif
                                </div>

                                @if($collection->description)
                                    <p style="color:#9CA3AF; font-size:0.83rem; margin:0 0 10px; line-height:1.4; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                        {{ $collection->description }}
                                    </p>
                                @endif

                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                    <span style="color:#D4AF37; font-size:0.83rem; font-weight:600;">
                                        {{ $collection->items_count }} {{ $collection->items_count === 1 ? 'restaurante' : 'restaurantes' }}
                                    </span>
                                    <button
                                        wire:click.stop="deleteCollection({{ $collection->id }})"
                                        wire:confirm="¿Eliminar '{{ addslashes($collection->name) }}'? No se puede deshacer."
                                        type="button"
                                        style="background:none; border:none; color:#6B7280; cursor:pointer; padding:4px; border-radius:4px; line-height:0;"
                                        title="Eliminar lista"
                                        onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#6B7280'"
                                    >
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty state --}}
                <div style="text-align:center; padding:80px 20px; border:1px dashed #3A3A3A; border-radius:16px;">
                    <div style="font-size:4rem; margin-bottom:16px;">📋</div>
                    <h3 style="color:#F5F5F5; font-size:1.3rem; font-weight:700; margin:0 0 8px; font-family:'Playfair Display',serif;">
                        Aún no tienes listas
                    </h3>
                    <p style="color:#9CA3AF; margin:0 0 24px;">
                        Crea tu primera lista para organizar tus restaurantes favoritos
                    </p>
                    <button
                        wire:click="toggleCreateForm"
                        type="button"
                        style="background:#D4AF37; color:#0B0B0B; padding:12px 28px; border-radius:8px; font-weight:700; border:none; cursor:pointer; font-size:0.95rem;"
                        onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'"
                    >
                        + Crear mi primera lista
                    </button>
                </div>
            @endif
        @endif

    </div>
</div>
