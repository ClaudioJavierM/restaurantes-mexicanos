@props(['feature' => 'esta funcion', 'benefits' => []])

<div style="position: relative; min-height: 400px;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(17, 24, 39, 0.9); z-index: 50; display: flex; align-items: center; justify-content: center; border-radius: 0.75rem;">
        <div style="text-align: center; padding: 2rem; max-width: 500px;">
            <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <svg style="width: 2rem; height: 2rem; color: #1a1a2e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: bold; color: #ffffff; margin-bottom: 0.5rem;">{{ $feature }}</h3>
            <p style="color: #9ca3af; margin-bottom: 1.25rem; font-size: 0.9375rem;">
                Actualiza tu plan para acceder a {{ strtolower($feature) }} y mejorar los resultados de tu restaurante.
            </p>
            
            @if(count($benefits) > 0)
            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
                    @foreach($benefits as $benefit)
                    <span style="background-color: #374151; color: #d1d5db; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem;">{{ $benefit }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            
            <a href="{{ url('/owner/upgrade-subscription') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #d4af37, #f4e4a6); color: #1a1a2e; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Ver Planes Disponibles
            </a>
            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.75rem;">Desde $29/mes - Primer mes $9.99</p>
        </div>
    </div>
    
    {{-- Blurred background content --}}
    <div style="filter: blur(4px); pointer-events: none; opacity: 0.4;">
        {{ $slot }}
    </div>
</div>
