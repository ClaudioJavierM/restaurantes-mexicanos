<x-filament-panels::page>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-2xl mx-auto px-6">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-yellow-500/10 flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
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
                Pedidos Pickup
            </h2>

            {{-- Description --}}
            <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Muy pronto podras recibir y gestionar pedidos de pickup directamente desde tu panel.
                Tus clientes podran ordenar en linea y recoger en tu restaurante.
            </p>

            {{-- Features grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Pedidos en Tiempo Real</h3>
                    <p class="text-xs text-gray-500 mt-1">Recibe notificaciones al instante de cada pedido</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Gestion de Estados</h3>
                    <p class="text-xs text-gray-500 mt-1">Confirmar, preparar, listo y entregado</p>
                </div>

                <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Pagos Integrados</h3>
                    <p class="text-xs text-gray-500 mt-1">Cobra en linea o al momento del pickup</p>
                </div>
            </div>

            {{-- CTA --}}
            <p class="text-sm text-gray-400">
                Estamos trabajando para ofrecerte la mejor solucion de pedidos pickup para tu restaurante.
            </p>
        </div>
    </div>
</x-filament-panels::page>
