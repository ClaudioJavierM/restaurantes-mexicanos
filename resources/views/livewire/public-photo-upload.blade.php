<div style="background:#1A1A1A; border-radius:12px; padding:28px; margin-bottom:24px; border:1px solid #2A2A2A;">

    {{-- ============================================================
         GUEST STATE — not logged in
    ============================================================ --}}
    @guest
    <div style="text-align:center; padding:12px 0;">
        <div style="font-size:2.5rem; margin-bottom:12px;">📷</div>
        <h3 style="color:#F5F5F5; font-size:1.25rem; font-weight:700; margin:0 0 8px 0; font-family:'Playfair Display',serif;">
            ¿Visitaste este restaurante?
        </h3>
        <p style="color:#999; font-size:0.95rem; margin:0 0 20px 0;">
            Sube tus fotos y ayuda a otros a decidir
        </p>
        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
           style="display:inline-block; background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:0.95rem; padding:12px 28px; border-radius:8px; text-decoration:none; transition:background 0.2s;"
           onmouseover="this.style.background='#b8962e'" onmouseout="this.style.background='#D4AF37'">
            Iniciar sesión para subir fotos
        </a>
    </div>
    @endguest

    {{-- ============================================================
         AUTH STATE — logged in
    ============================================================ --}}
    @auth

    {{-- SUCCESS STATE --}}
    @if($success)
    <div style="text-align:center; padding:20px 0;">
        <div style="font-size:3rem; margin-bottom:12px;">✓</div>
        <h3 style="color:#D4AF37; font-size:1.2rem; font-weight:700; margin:0 0 8px 0;">
            ¡Gracias! Tus {{ $uploadedCount }} {{ $uploadedCount === 1 ? 'foto fue enviada' : 'fotos fueron enviadas' }}
        </h3>
        <p style="color:#999; font-size:0.9rem; margin:0 0 20px 0;">
            Serán revisadas por nuestro equipo en las próximas 24 horas.
        </p>
        <button wire:click="resetForm"
                style="background:transparent; border:1px solid #D4AF37; color:#D4AF37; font-weight:600; font-size:0.9rem; padding:10px 24px; border-radius:8px; cursor:pointer;">
            Subir más fotos
        </button>
    </div>

    {{-- UPLOAD FORM --}}
    @else
    <h3 style="color:#F5F5F5; font-size:1.1rem; font-weight:700; margin:0 0 20px 0; display:flex; align-items:center; gap:10px; font-family:'Playfair Display',serif;">
        <span style="color:#D4AF37;">📷</span>
        ¿Estuviste aquí? Comparte tus fotos
    </h3>

    <form wire:submit.prevent="upload"
          x-data="{
              previews: [],
              isDragging: false,
              photoCount: 0,
              handleFiles(files) {
                  this.previews = [];
                  this.photoCount = 0;
                  const maxFiles = 5;
                  const list = Array.from(files).slice(0, maxFiles);
                  list.forEach(f => {
                      if (f.type.startsWith('image/')) {
                          this.previews.push(URL.createObjectURL(f));
                          this.photoCount++;
                      }
                  });
              },
              handleDrop(e) {
                  this.isDragging = false;
                  this.handleFiles(e.dataTransfer.files);
                  const input = $refs.fileInput;
                  const dt = new DataTransfer();
                  Array.from(e.dataTransfer.files).slice(0,5).forEach(f => dt.items.add(f));
                  input.files = dt.files;
                  input.dispatchEvent(new Event('change', { bubbles: true }));
              }
          }">

        {{-- Drag & Drop Zone --}}
        <div style="margin-bottom:18px;">
            <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.fileInput.click()"
                :style="isDragging
                    ? 'border:2px dashed #D4AF37; background:rgba(212,175,55,0.08); transform:scale(1.01);'
                    : 'border:2px dashed #3A3A3A; background:#111; cursor:pointer;'"
                style="border-radius:10px; padding:36px 20px; text-align:center; transition:all 0.2s; cursor:pointer; position:relative;"
            >
                <input
                    type="file"
                    wire:model="photos"
                    multiple
                    accept="image/jpg,image/jpeg,image/png,image/webp"
                    style="display:none;"
                    x-ref="fileInput"
                    @change="handleFiles($event.target.files)"
                >

                {{-- Upload Icon --}}
                <div wire:loading.remove wire:target="photos">
                    <svg style="width:40px; height:40px; margin:0 auto 12px; color:#555;" :style="isDragging ? 'color:#D4AF37;' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p style="color:#ccc; font-size:0.95rem; margin:0 0 4px 0;" x-show="!isDragging">
                        Arrastra tus fotos aquí o <span style="color:#D4AF37; font-weight:600;">haz clic para seleccionar</span>
                    </p>
                    <p style="color:#D4AF37; font-size:1rem; font-weight:600; margin:0;" x-show="isDragging">
                        Suelta las fotos aquí
                    </p>
                    <p style="color:#666; font-size:0.8rem; margin:6px 0 0 0;">
                        Hasta 5 fotos &bull; JPG, PNG, WEBP &bull; Máx 5MB cada una
                    </p>
                </div>

                {{-- Uploading spinner --}}
                <div wire:loading wire:target="photos" style="padding:10px 0;">
                    <svg style="width:28px; height:28px; animation:spin 1s linear infinite; margin:0 auto 8px; color:#D4AF37;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p style="color:#D4AF37; font-size:0.9rem; margin:0;">Procesando fotos...</p>
                </div>
            </div>

            {{-- Validation errors for photos --}}
            @error('photos')
                <p style="color:#e53e3e; font-size:0.85rem; margin:6px 0 0 0;">{{ $message }}</p>
            @enderror
            @error('photos.*')
                <p style="color:#e53e3e; font-size:0.85rem; margin:6px 0 0 0;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Preview Strip (Alpine local previews) --}}
        <template x-if="previews.length > 0">
            <div style="margin-bottom:18px;" wire:loading.remove wire:target="photos">
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
                    <template x-for="(src, i) in previews" :key="i">
                        <div style="position:relative; width:72px; height:72px; border-radius:8px; overflow:hidden; border:2px solid #D4AF37;">
                            <img :src="src" style="width:100%; height:100%; object-fit:cover;" :alt="'Foto ' + (i+1)">
                            <div style="position:absolute; top:2px; left:2px; background:rgba(0,0,0,0.7); color:#fff; font-size:0.65rem; font-weight:700; padding:2px 5px; border-radius:4px;" x-text="i+1"></div>
                        </div>
                    </template>
                </div>
                <p style="color:#888; font-size:0.82rem; margin:0;">
                    <span x-text="photoCount"></span>
                    <span x-text="photoCount === 1 ? ' foto seleccionada' : ' fotos seleccionadas'"></span>
                    <span style="color:#555;"> — se revisarán antes de publicarse</span>
                </p>
            </div>
        </template>

        {{-- Caption --}}
        <div style="margin-bottom:18px;">
            <label style="display:block; color:#aaa; font-size:0.85rem; font-weight:600; margin-bottom:6px;">
                Descripción <span style="color:#555; font-weight:400;">(opcional, máx. 200 caracteres)</span>
            </label>
            <textarea
                wire:model="caption"
                rows="2"
                maxlength="200"
                placeholder="¿Qué probaste? ¿Cuándo fuiste? Cuéntanos..."
                style="width:100%; background:#111; border:1px solid #3A3A3A; border-radius:8px; color:#F5F5F5; font-size:0.9rem; padding:10px 14px; resize:vertical; font-family:inherit; box-sizing:border-box; outline:none;"
                onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#3A3A3A'"
            ></textarea>
            @error('caption')
                <p style="color:#e53e3e; font-size:0.85rem; margin:4px 0 0 0;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div style="display:flex; align-items:center; justify-content:flex-end;">
            <button
                type="submit"
                wire:loading.attr="disabled"
                style="background:#D4AF37; color:#0B0B0B; font-weight:700; font-size:0.95rem; padding:12px 32px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; gap:8px; transition:background 0.2s;"
                onmouseover="this.style.background='#b8962e'" onmouseout="this.style.background='#D4AF37'"
            >
                {{-- Default label --}}
                <span wire:loading.remove wire:target="upload">
                    <svg style="width:18px; height:18px; display:inline; margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Subir Fotos
                </span>
                {{-- Loading label --}}
                <span wire:loading wire:target="upload" style="display:flex; align-items:center; gap:8px;">
                    <svg style="width:18px; height:18px; animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Subiendo...
                </span>
            </button>
        </div>

    </form>
    @endif

    @endauth

    {{-- ============================================================
         PHOTO STRIP — approved visitor photos (visible always)
    ============================================================ --}}
    @if($photoCount > 0 || $recentPhotos->count() > 0)
    <div style="margin-top:28px; padding-top:24px; border-top:1px solid #2A2A2A;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <p style="color:#888; font-size:0.88rem; margin:0;">
                <span style="color:#D4AF37; font-weight:700;">{{ $photoCount }}</span>
                {{ $photoCount === 1 ? 'foto de visitantes' : 'fotos de visitantes' }}
            </p>
            @if($photoCount > 6)
            <a href="#gallery"
               style="color:#D4AF37; font-size:0.85rem; font-weight:600; text-decoration:none;"
               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                Ver todas las fotos →
            </a>
            @endif
        </div>

        {{-- Thumbnails strip --}}
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            @foreach($recentPhotos as $photo)
            <a href="#gallery"
               title="{{ $photo->caption ?: 'Foto de ' . ($photo->user?->name ?? 'visitante') }}"
               style="display:block; width:72px; height:72px; border-radius:8px; overflow:hidden; border:1px solid #2A2A2A; flex-shrink:0; transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                <img src="{{ $photo->getThumbnailUrl() }}"
                     alt="{{ $photo->caption ?: 'Foto del restaurante' }}"
                     loading="lazy"
                     style="width:100%; height:100%; object-fit:cover; display:block;">
            </a>
            @endforeach

            @if($photoCount > 6)
            <a href="#gallery"
               style="display:flex; align-items:center; justify-content:center; width:72px; height:72px; border-radius:8px; background:#111; border:1px dashed #3A3A3A; color:#888; font-size:0.8rem; font-weight:700; text-decoration:none; flex-shrink:0; text-align:center; line-height:1.2; transition:border-color 0.2s;"
               onmouseover="this.style.borderColor='#D4AF37'; this.style.color='#D4AF37';" onmouseout="this.style.borderColor='#3A3A3A'; this.style.color='#888';">
                +{{ $photoCount - 6 }}<br>más
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Login prompt event listener (dispatched when guest tries to upload) --}}
    <div x-data
         x-on:show-login-prompt.window="
            if (!document.getElementById('login-prompt-toast')) {
                const t = document.createElement('div');
                t.id = 'login-prompt-toast';
                t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#1A1A1A;border:1px solid #D4AF37;border-radius:10px;padding:16px 20px;z-index:9999;box-shadow:0 8px 32px rgba(0,0,0,0.5);max-width:320px;';
                t.innerHTML = '<p style=\"color:#F5F5F5;font-size:0.9rem;margin:0 0 12px 0;\">Inicia sesión para subir fotos</p><a href=\'/login\' style=\"display:inline-block;background:#D4AF37;color:#0B0B0B;font-weight:700;font-size:0.85rem;padding:8px 18px;border-radius:6px;text-decoration:none;\">Iniciar sesión</a><button onclick=\"document.getElementById(\'login-prompt-toast\').remove()\" style=\"position:absolute;top:8px;right:10px;background:none;border:none;color:#888;font-size:1.1rem;cursor:pointer;\">&times;</button>';
                t.style.position = 'fixed';
                document.body.appendChild(t);
                setTimeout(() => { if(t.parentNode) t.parentNode.removeChild(t); }, 5000);
            }
         ">
    </div>

</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
