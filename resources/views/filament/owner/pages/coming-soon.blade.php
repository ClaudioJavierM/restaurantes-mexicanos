<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 400px; text-align: center;">
        <div style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%); border-radius: 1rem; padding: 3rem; max-width: 500px; border: 2px solid #4b5563;">
            
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f97316, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h2 style="font-size: 1.75rem; font-weight: bold; color: white; margin-bottom: 0.75rem;">
                {{ $featureName ?? 'Nueva Funcion' }}
            </h2>
            
            <div style="display: inline-block; background-color: #f97316; color: white; padding: 0.25rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem;">
                EN DESARROLLO
            </div>
            
            <p style="color: #9ca3af; font-size: 1rem; line-height: 1.6; margin-bottom: 1.5rem;">
                {{ $featureDescription ?? 'Esta funcion estara disponible muy pronto.' }}
            </p>
            
            <div style="background-color: #111827; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Beneficios cuando este disponible:</p>
                <ul style="list-style: none; padding: 0; margin: 0; text-align: left;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Aumenta tus ventas
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Fideliza a tus clientes
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; color: #d1d5db; font-size: 0.875rem;">
                        <svg style="width: 1rem; height: 1rem; color: #22c55e;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        Destaca sobre la competencia
                    </li>
                </ul>
            </div>
            
            <p style="color: #6b7280; font-size: 0.75rem;">
                Te notificaremos cuando este disponible
            </p>
        </div>
    </div>
</x-filament-panels::page>
