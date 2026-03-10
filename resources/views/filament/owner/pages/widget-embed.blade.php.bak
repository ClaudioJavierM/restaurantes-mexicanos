<x-filament-panels::page>
    <div class="space-y-6">
        @if($restaurant)
            @if(!$isPremium)
            <div style="position: relative;">
                <div style="filter: blur(4px); pointer-events: none;">
            @endif
            
            <div style="background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%); border-radius: 0.75rem; padding: 1.5rem; color: white; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Widget para tu Sitio Web</h2>
                <p style="opacity: 0.9;">Agrega informacion de tu restaurante, resenas y calificaciones directamente en tu sitio web.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin-bottom: 1rem;">Codigo de Instalacion</h3>
                    <p style="font-size: 0.875rem; color: #9ca3af; margin-bottom: 1rem;">Copia este codigo y pegalo en el HTML de tu sitio web donde quieras mostrar el widget.</p>
                    
                    <div style="background-color: #111827; border-radius: 0.5rem; padding: 1rem; font-family: monospace; font-size: 0.75rem; color: #10b981; overflow-x: auto;">
                        <pre style="margin: 0; white-space: pre-wrap;">{{ $embedCode }}</pre>
                    </div>
                    
                    <button 
                        onclick="navigator.clipboard.writeText(document.querySelector('pre').textContent); alert('Codigo copiado!')"
                        style="margin-top: 1rem; background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 500;"
                    >
                        Copiar Codigo
                    </button>
                </div>

                <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin-bottom: 1rem;">Caracteristicas del Widget</h3>
                    <ul style="list-style: none; padding: 0; margin: 0; space-y: 0.5rem;">
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; margin-bottom: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Calificacion y resenas recientes
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; margin-bottom: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Horarios de operacion
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; margin-bottom: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Boton de reservaciones
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; margin-bottom: 0.5rem;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Link a tu menu digital
                        </li>
                        <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db;">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Actualizado automaticamente
                        </li>
                    </ul>
                </div>
            </div>

            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151; margin-top: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: white; margin-bottom: 1rem;">Estadisticas del Widget</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div style="text-align: center;">
                        <p style="font-size: 2rem; font-weight: bold; color: #3b82f6;">{{ $widgetToken?->views ?? 0 }}</p>
                        <p style="font-size: 0.875rem; color: #9ca3af;">Vistas Totales</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="font-size: 2rem; font-weight: bold; color: #22c55e;">Activo</p>
                        <p style="font-size: 0.875rem; color: #9ca3af;">Estado</p>
                    </div>
                    <div style="text-align: center;">
                        <button wire:click="regenerateToken" style="background-color: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer;">
                            Regenerar Token
                        </button>
                        <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.5rem;">Si crees que fue comprometido</p>
                    </div>
                </div>
            </div>

            @if(!$isPremium)
                </div>
                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.5); border-radius: 0.75rem;">
                    <div style="background-color: #1f2937; padding: 2rem; border-radius: 0.75rem; text-align: center; max-width: 400px;">
                        <h3 style="font-size: 1.25rem; font-weight: bold; color: white; margin-bottom: 0.5rem;">Funcion Premium</h3>
                        <p style="color: #9ca3af; margin-bottom: 1rem;">El Widget Web esta disponible en planes Premium y Elite.</p>
                        <a href="/owner/upgrade-subscription" style="display: inline-block; background: linear-gradient(135deg, #f97316, #dc2626); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                            Ver Planes Disponibles
                        </a>
                    </div>
                </div>
            </div>
            @endif
        @else
            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; border-radius: 0.75rem; padding: 1.5rem; text-align: center;">
                <p style="color: #92400e;">No tienes un restaurante asociado.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
