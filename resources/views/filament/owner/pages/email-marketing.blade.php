<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-2xl mx-auto px-6">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-500/10 flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path>
                </svg>
            </div>

            {{-- Badge --}}
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-yellow-500/10 text-yellow-500 text-sm font-semibold mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pronto
            </span>

            {{-- Title --}}
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Email Marketing
            </h2>

            {{-- Description --}}
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Muy pronto podras crear y enviar campanas de email marketing directamente a tus clientes. 
                Mantente en contacto con promociones, eventos especiales y novedades de tu restaurante.
            </p>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Base de Clientes</h3>
                    <p class="text-xs text-gray-500 mt-1">Importa y gestiona tu lista de contactos</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Campanas Inteligentes</h3>
                    <p class="text-xs text-gray-500 mt-1">Plantillas profesionales listas para enviar</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Metricas y Reportes</h3>
                    <p class="text-xs text-gray-500 mt-1">Tasa de apertura, clics y conversiones</p>
                </div>
            </div>

            {{-- CTA --}}
            <p class="text-sm text-gray-400">
                Estamos trabajando para traerte la mejor herramienta de email marketing para restaurantes.
            </p>
        </div>
    </div>
</x-filament-panels::page>
