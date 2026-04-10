<div style="font-family:'Poppins',sans-serif; color:#F5F5F5; min-height:100vh; padding:1.5rem;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.75rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:800; color:#F5F5F5; margin:0 0 0.25rem; display:flex; align-items:center; gap:0.5rem;">
                <span style="color:#D4AF37;">⚡</span> Ofertas Relampago
            </h1>
            <p style="font-size:0.8125rem; color:#9CA3AF; margin:0;">Crea descuentos y promociones por tiempo limitado para tu restaurante.</p>
        </div>
        @if(!$showForm)
        <button
            wire:click="openForm"
            style="background:#D4AF37; color:#0B0B0B; font-size:0.875rem; font-weight:700; padding:0.625rem 1.25rem; border-radius:0.625rem; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:0.5rem; transition:opacity 0.15s;"
            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
            <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Crear Oferta
        </button>
        @endif
    </div>

    {{-- Success / error banners --}}
    @if($successMessage)
    <div style="background:#1F3D2B; border:1px solid #166534; border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.625rem; font-size:0.875rem; color:#86EFAC;">
        <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ $successMessage }}
    </div>
    @endif

    @if($errorMessage)
    <div style="background:#3B1515; border:1px solid #8B1E1E; border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.625rem; font-size:0.875rem; color:#FCA5A5;">
        <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        {{ $errorMessage }}
    </div>
    @endif

    {{-- CREATE / EDIT FORM --}}
    @if($showForm)
    <div style="background:#1A1A1A; border:1px solid #D4AF37; border-radius:1rem; padding:1.75rem; margin-bottom:2rem;">
        <h2 style="font-size:1rem; font-weight:700; color:#F5F5F5; margin:0 0 1.5rem; display:flex; align-items:center; gap:0.5rem;">
            <span style="color:#D4AF37;">🎁</span>
            {{ $editingDealId ? 'Editar Oferta' : 'Nueva Oferta' }}
        </h2>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem;">

            {{-- Title --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Título de la oferta *</label>
                <input
                    wire:model="title"
                    type="text"
                    placeholder="Ej: 20% off en tacos el martes"
                    style="width:100%; background:#0B0B0B; border:1px solid {{ $errors->has('title') ? '#8B1E1E' : '#2A2A2A' }}; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='{{ $errors->has('title') ? '#8B1E1E' : '#2A2A2A' }}'">
                @error('title') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Discount type --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Tipo de descuento *</label>
                <select
                    wire:model="discount_type"
                    style="width:100%; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box; appearance:none; cursor:pointer;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                    <option value="percentage">Porcentaje (%)</option>
                    <option value="fixed">Monto fijo ($)</option>
                    <option value="bogo">2 x 1 (BOGO)</option>
                    <option value="free_item">Artículo gratis</option>
                </select>
                @error('discount_type') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Discount value --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">
                    Valor
                    @if($discount_type === 'percentage') (%) @elseif($discount_type === 'fixed') ($) @else (0 si no aplica) @endif
                    *
                </label>
                <input
                    wire:model="discount_value"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="{{ $discount_type === 'percentage' ? '20' : ($discount_type === 'fixed' ? '50.00' : '0') }}"
                    style="width:100%; background:#0B0B0B; border:1px solid {{ $errors->has('discount_value') ? '#8B1E1E' : '#2A2A2A' }}; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='{{ $errors->has('discount_value') ? '#8B1E1E' : '#2A2A2A' }}'">
                @error('discount_value') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Promo code --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Código promo (opcional)</label>
                <input
                    wire:model="promo_code"
                    type="text"
                    placeholder="Ej: TACO20 (se genera auto si se deja vacío)"
                    style="width:100%; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box; font-family:monospace;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                @error('promo_code') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Applicable for --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Aplica para *</label>
                <select
                    wire:model="applicable_for"
                    style="width:100%; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box; appearance:none; cursor:pointer;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
                    <option value="all">Todos (comer aquí, llevar, delivery)</option>
                    <option value="dine_in">Solo comer aquí</option>
                    <option value="takeout">Solo para llevar</option>
                    <option value="delivery">Solo delivery</option>
                </select>
            </div>

            {{-- Max redemptions --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Máx. usos (opcional)</label>
                <input
                    wire:model="max_redemptions"
                    type="number"
                    min="1"
                    placeholder="Sin límite si se deja vacío"
                    style="width:100%; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'">
            </div>

            {{-- Start date --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Inicio *</label>
                <input
                    wire:model="start_date"
                    type="datetime-local"
                    style="width:100%; background:#0B0B0B; border:1px solid {{ $errors->has('start_date') ? '#8B1E1E' : '#2A2A2A' }}; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='{{ $errors->has('start_date') ? '#8B1E1E' : '#2A2A2A' }}'">
                @error('start_date') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- End date --}}
            <div>
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Fin *</label>
                <input
                    wire:model="end_date"
                    type="datetime-local"
                    style="width:100%; background:#0B0B0B; border:1px solid {{ $errors->has('end_date') ? '#8B1E1E' : '#2A2A2A' }}; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='{{ $errors->has('end_date') ? '#8B1E1E' : '#2A2A2A' }}'">
                @error('end_date') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.04em; display:block; margin-bottom:0.375rem;">Descripción (opcional)</label>
                <textarea
                    wire:model="description"
                    rows="3"
                    placeholder="Condiciones, restricciones o más detalles de la oferta..."
                    style="width:100%; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; padding:0.625rem 0.875rem; color:#F5F5F5; font-size:0.875rem; outline:none; box-sizing:border-box; resize:vertical; line-height:1.5;"
                    onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"></textarea>
                @error('description') <p style="color:#FCA5A5; font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Form actions --}}
        <div style="display:flex; gap:0.75rem; margin-top:1.5rem; flex-wrap:wrap;">
            <button
                wire:click="save"
                wire:loading.attr="disabled"
                style="background:#D4AF37; color:#0B0B0B; font-size:0.875rem; font-weight:700; padding:0.625rem 1.5rem; border-radius:0.625rem; border:none; cursor:pointer; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <span wire:loading.remove wire:target="save">{{ $editingDealId ? 'Guardar cambios' : 'Crear Oferta' }}</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
            <button
                wire:click="cancelForm"
                style="background:#2A2A2A; color:#F5F5F5; font-size:0.875rem; font-weight:600; padding:0.625rem 1.25rem; border-radius:0.625rem; border:1px solid #3A3A3A; cursor:pointer; transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                Cancelar
            </button>
        </div>
    </div>
    @endif

    {{-- DEALS LIST --}}
    @if($deals->isEmpty())
    <div style="background:#1A1A1A; border:1px dashed #2A2A2A; border-radius:1rem; padding:3rem 2rem; text-align:center;">
        <div style="font-size:3rem; margin-bottom:1rem;">⚡</div>
        <p style="font-size:1rem; font-weight:600; color:#F5F5F5; margin:0 0 0.5rem;">Sin ofertas creadas</p>
        <p style="font-size:0.875rem; color:#9CA3AF; margin:0 0 1.5rem;">Crea tu primera oferta relámpago para atraer más clientes.</p>
        @if(!$showForm)
        <button
            wire:click="openForm"
            style="background:#D4AF37; color:#0B0B0B; font-size:0.875rem; font-weight:700; padding:0.625rem 1.5rem; border-radius:0.625rem; border:none; cursor:pointer;">
            Crear primera oferta
        </button>
        @endif
    </div>
    @else
    <div style="display:flex; flex-direction:column; gap:0.875rem;">
        @foreach($deals as $deal)
            @php
                $now = now();
                $isActive  = $deal->is_active && $deal->starts_at <= $now && $deal->ends_at >= $now;
                $isExpired = $deal->ends_at < $now;
                $isPending = $deal->starts_at > $now;

                if ($isExpired)      { $statusLabel = 'Expirada'; $statusBg = '#2A2A2A'; $statusColor = '#9CA3AF'; }
                elseif ($isPending)  { $statusLabel = 'Programada'; $statusBg = '#1A2A3A'; $statusColor = '#60A5FA'; }
                elseif ($isActive)   { $statusLabel = 'Activa'; $statusBg = '#1F3D2B'; $statusColor = '#86EFAC'; }
                else                 { $statusLabel = 'Pausada'; $statusBg = '#2A1A0B'; $statusColor = '#FCD34D'; }

                $typeLabels = [
                    'percentage' => '%',
                    'fixed'      => '$',
                    'bogo'       => '2x1',
                    'free_item'  => 'FREE',
                ];
            @endphp
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.875rem; padding:1.25rem 1.5rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">

                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:0.625rem; flex-wrap:wrap; margin-bottom:0.375rem;">
                        {{-- Discount badge --}}
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.75rem; font-weight:800; padding:0.2rem 0.625rem; border-radius:9999px; font-family:monospace; letter-spacing:0.04em;">
                            @if($deal->discount_type === 'percentage')
                                {{ number_format($deal->discount_value, 0) }}% OFF
                            @elseif($deal->discount_type === 'fixed')
                                ${{ number_format($deal->discount_value, 2) }} OFF
                            @elseif($deal->discount_type === 'bogo')
                                2x1
                            @else
                                GRATIS
                            @endif
                        </span>
                        {{-- Status badge --}}
                        <span style="background:{{ $statusBg }}; color:{{ $statusColor }}; font-size:0.6875rem; font-weight:700; padding:0.2rem 0.625rem; border-radius:9999px; letter-spacing:0.03em;">
                            {{ $statusLabel }}
                        </span>
                        {{-- Active toggle --}}
                        @if(!$isExpired)
                        <span style="font-size:0.75rem; color:#9CA3AF;">
                            <button wire:click="toggleActive({{ $deal->id }})" style="background:none; border:none; cursor:pointer; color:{{ $deal->is_active ? '#FCD34D' : '#9CA3AF' }}; font-size:0.75rem; text-decoration:underline; padding:0;">
                                {{ $deal->is_active ? 'Pausar' : 'Activar' }}
                            </button>
                        </span>
                        @endif
                    </div>
                    <p style="font-size:0.9375rem; font-weight:700; color:#F5F5F5; margin:0 0 0.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $deal->title }}</p>
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.375rem;">
                        <span style="font-size:0.75rem; color:#9CA3AF;">
                            Código: <span style="color:#D4AF37; font-family:monospace; font-weight:600;">{{ $deal->code }}</span>
                        </span>
                        <span style="font-size:0.75rem; color:#9CA3AF;">·</span>
                        <span style="font-size:0.75rem; color:#9CA3AF;">
                            {{ $deal->starts_at->format('d M') }} — {{ $deal->ends_at->format('d M Y') }}
                        </span>
                        @if($deal->max_redemptions)
                        <span style="font-size:0.75rem; color:#9CA3AF;">·</span>
                        <span style="font-size:0.75rem; color:#9CA3AF;">
                            {{ $deal->current_redemptions }}/{{ $deal->max_redemptions }} usos
                        </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                    @if(!$isExpired)
                    <button
                        wire:click="editDeal({{ $deal->id }})"
                        title="Editar"
                        style="background:#2A2A2A; border:1px solid #3A3A3A; border-radius:0.5rem; padding:0.5rem; cursor:pointer; color:#D4AF37; display:inline-flex; align-items:center; transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    @endif
                    <button
                        wire:click="deleteDeal({{ $deal->id }})"
                        wire:confirm="¿Eliminar esta oferta? Esta acción no se puede deshacer."
                        title="Eliminar"
                        style="background:#2A0B0B; border:1px solid #8B1E1E; border-radius:0.5rem; padding:0.5rem; cursor:pointer; color:#FCA5A5; display:inline-flex; align-items:center; transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

            </div>
        @endforeach
    </div>
    @endif

</div>
