<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mi Perfil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Direcciones de entrega usadas --}}
            @php
                $savedAddresses = auth()->user()->orders()
                    ->where('order_type', 'delivery')
                    ->whereNotNull('delivery_address')
                    ->select('delivery_address','delivery_city','delivery_zip')
                    ->distinct()
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp
            @if($savedAddresses->count() > 0)
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">
                        📍 Mis Direcciones de Entrega
                    </h2>
                    <p class="text-sm text-gray-500 mb-4">Direcciones usadas en pedidos anteriores</p>
                    <div class="space-y-2">
                        @foreach($savedAddresses as $addr)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-sm text-gray-700">
                                    {{ $addr->delivery_address }}, {{ $addr->delivery_city }}
                                    @if($addr->delivery_zip) {{ $addr->delivery_zip }} @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
