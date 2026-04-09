<div style="min-height:100vh; background:#0B0B0B; padding:2rem 1rem; font-family:'Poppins',sans-serif;">
    <div style="max-width:640px; margin:0 auto;">

        {{-- Back link --}}
        <a href="/mi-cuenta"
           style="display:inline-flex; align-items:center; gap:0.4rem; color:#9CA3AF; font-size:0.875rem; text-decoration:none; margin-bottom:2rem; transition:color .2s;"
           onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#9CA3AF'">
            ← Mi Cuenta
        </a>

        {{-- Page title --}}
        <h1 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#F5F5F5; margin:0 0 0.25rem;">
            Mi Perfil
        </h1>
        <p style="color:#6B7280; font-size:0.9rem; margin:0 0 2.5rem;">
            Actualiza tu información personal
        </p>

        {{-- Avatar section --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.75rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:1.5rem;">
            @php $avatar = auth()->user()->avatar; @endphp
            @if($avatar)
                <img src="{{ $avatar }}" alt="{{ $name }}"
                     style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #D4AF37;">
            @else
                <div style="width:80px; height:80px; border-radius:50%; background:#D4AF37; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <span style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#0B0B0B;">
                        {{ strtoupper(substr($name ?: auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
            @endif
            <div>
                <p style="color:#F5F5F5; font-weight:600; font-size:1rem; margin:0 0 0.25rem;">{{ auth()->user()->name }}</p>
                <p style="color:#6B7280; font-size:0.8125rem; margin:0;">{{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Success banner --}}
        <div x-data="{}"
             x-show="$wire.saved"
             x-init="$watch('$wire.saved', v => { if(v) setTimeout(() => $wire.saved = false, 3000) })"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-1"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display:none; background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); border-radius:0.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem; color:#4ADE80; font-size:0.9rem; font-weight:500;">
            ✓ ¡Perfil actualizado correctamente!
        </div>

        {{-- Profile form --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.75rem; margin-bottom:1.5rem;">
            <h2 style="font-family:'Poppins',sans-serif; font-size:0.9375rem; font-weight:600; color:#F5F5F5; margin:0 0 1.5rem; padding-bottom:1rem; border-bottom:1px solid #2A2A2A;">
                Información Personal
            </h2>

            <form wire:submit="save" style="display:flex; flex-direction:column; gap:1.25rem;">

                {{-- Nombre --}}
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                        Nombre Completo
                    </label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Tu nombre completo"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'">
                    @error('name')
                        <p style="color:#EF4444; font-size:0.8125rem; margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="tu@correo.com"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'">
                    @error('email')
                        <p style="color:#EF4444; font-size:0.8125rem; margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                        Teléfono <span style="color:#6B7280; font-weight:400; text-transform:none; letter-spacing:0;">(opcional)</span>
                    </label>
                    <input
                        type="tel"
                        wire:model="phone"
                        placeholder="+52 33 1234 5678"
                        style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                        onfocus="this.style.borderColor='rgba(212,175,55,0.5)'"
                        onblur="this.style.borderColor='#2A2A2A'">
                    @error('phone')
                        <p style="color:#EF4444; font-size:0.8125rem; margin-top:0.375rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Save button --}}
                <div style="padding-top:0.5rem;">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        style="background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem 2rem; border-radius:0.75rem; font-weight:700; font-size:1rem; cursor:pointer; font-family:'Poppins',sans-serif; transition:opacity .2s;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <span wire:loading.remove wire:target="save">Guardar Cambios</span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>
                </div>

            </form>
        </div>

        {{-- Account info (read-only) --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.75rem; margin-bottom:1.5rem;">
            <h2 style="font-family:'Poppins',sans-serif; font-size:0.9375rem; font-weight:600; color:#F5F5F5; margin:0 0 1.25rem; padding-bottom:1rem; border-bottom:1px solid #2A2A2A;">
                Información de Cuenta
            </h2>

            <div style="display:flex; flex-direction:column; gap:1rem;">
                {{-- Account type --}}
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.875rem; color:#9CA3AF;">Tipo de cuenta</span>
                    @php
                        $role = auth()->user()->role ?? 'customer';
                        $roleLabel = match($role) {
                            'admin'   => 'Administrador',
                            'owner'   => 'Dueño',
                            default   => 'Cliente',
                        };
                        $roleColor = match($role) {
                            'admin'   => '#EF4444',
                            'owner'   => '#D4AF37',
                            default   => '#4ADE80',
                        };
                    @endphp
                    <span style="font-size:0.75rem; font-weight:700; color:{{ $roleColor }}; background:{{ $roleColor }}1a; border:1px solid {{ $roleColor }}4d; border-radius:999px; padding:0.25rem 0.875rem; letter-spacing:0.03em;">
                        {{ $roleLabel }}
                    </span>
                </div>

                {{-- Member since --}}
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.875rem; color:#9CA3AF;">Miembro desde</span>
                    <span style="font-size:0.875rem; color:#F5F5F5; font-weight:500;">
                        {{ auth()->user()->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Password section --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <p style="color:#F5F5F5; font-size:0.9rem; font-weight:500; margin:0 0 0.25rem;">Contraseña</p>
                <p style="color:#6B7280; font-size:0.8125rem; margin:0;">Cambia tu contraseña de acceso</p>
            </div>
            <a href="{{ route('password.request') }}"
               style="color:#D4AF37; font-size:0.875rem; font-weight:600; text-decoration:none; white-space:nowrap; transition:opacity .2s;"
               onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                Cambiar →
            </a>
        </div>

    </div>
</div>
