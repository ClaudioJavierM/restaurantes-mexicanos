<x-filament-panels::page>
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCustomers) }}</div>
                <div class="text-sm text-gray-500">Total Clientes</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($subscribedCustomers) }}</div>
                <div class="text-sm text-gray-500">Suscritos</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalCampaigns) }}</div>
                <div class="text-sm text-gray-500">Campanas</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($sentEmails) }}</div>
                <div class="text-sm text-gray-500">Emails Enviados</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $avgOpenRate }}%</div>
                <div class="text-sm text-gray-500">Tasa Apertura</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-4">
            <button wire:click="$set('activeTab', 'campaigns')" 
                class="px-4 py-2 font-medium {{ $activeTab === 'campaigns' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700' }}">
                Campanas
            </button>
            <button wire:click="$set('activeTab', 'customers')" 
                class="px-4 py-2 font-medium {{ $activeTab === 'customers' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700' }}">
                Clientes
            </button>
        </nav>
    </div>

    @if($activeTab === 'campaigns')
        {{-- Campaigns Tab --}}
        <div class="mb-4 flex justify-end gap-2">
            <x-filament::button wire:click="$set('showCampaignForm', true)" icon="heroicon-o-plus">
                Nueva Campana
            </x-filament::button>
        </div>
        
        {{ $this->table }}

        {{-- Campaign Form Modal --}}
        <x-filament::modal id="campaign-form" :visible="$showCampaignForm" width="3xl">
            <x-slot name="header">
                <h2 class="text-xl font-bold">{{ $editingCampaign ? 'Editar Campana' : 'Nueva Campana' }}</h2>
            </x-slot>
            
            <form wire:submit="saveCampaign">
                @foreach($this->getCampaignFormSchema() as $component)
                    {{ $component }}
                @endforeach
            </form>
            
            <x-slot name="footer">
                <x-filament::button wire:click="$set('showCampaignForm', false)" color="gray">
                    Cancelar
                </x-filament::button>
                <x-filament::button wire:click="saveCampaign" color="success">
                    Guardar
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

    @else
        {{-- Customers Tab --}}
        <div class="mb-4 flex justify-end gap-2">
            <x-filament::button wire:click="$set('showImportForm', true)" icon="heroicon-o-arrow-up-tray" color="gray">
                Importar CSV
            </x-filament::button>
            <x-filament::button wire:click="$set('showCustomerForm', true)" icon="heroicon-o-plus">
                Agregar Cliente
            </x-filament::button>
        </div>

        <x-filament::section>
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Nombre</th>
                        <th class="text-left py-2">Fuente</th>
                        <th class="text-left py-2">Visitas</th>
                        <th class="text-left py-2">Estado</th>
                        <th class="text-right py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getCustomersQuery()->limit(20)->get() as $customer)
                        <tr class="border-b">
                            <td class="py-2">{{ $customer->email }}</td>
                            <td class="py-2">{{ $customer->name ?? '-' }}</td>
                            <td class="py-2"><span class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs">{{ $customer->source }}</span></td>
                            <td class="py-2">{{ $customer->visits_count }}</td>
                            <td class="py-2">
                                <button wire:click="toggleSubscription({{ $customer->id }})" 
                                    class="px-2 py-1 rounded text-xs {{ $customer->email_subscribed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $customer->email_subscribed ? 'Suscrito' : 'No suscrito' }}
                                </button>
                            </td>
                            <td class="py-2 text-right">
                                <x-filament::icon-button icon="heroicon-o-trash" color="danger" wire:click="deleteCustomer({{ $customer->id }})" wire:confirm="Eliminar este cliente?" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">No hay clientes aun</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-filament::section>

        {{-- Customer Form Modal --}}
        <x-filament::modal id="customer-form" :visible="$showCustomerForm" width="md">
            <x-slot name="header">
                <h2 class="text-xl font-bold">Agregar Cliente</h2>
            </x-slot>
            
            <div class="space-y-4">
                <x-filament::input.wrapper>
                    <x-filament::input type="email" wire:model="customerData.email" placeholder="Email" />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="customerData.name" placeholder="Nombre" />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input type="tel" wire:model="customerData.phone" placeholder="Telefono" />
                </x-filament::input.wrapper>
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="customerData.birthday" />
                </x-filament::input.wrapper>
            </div>
            
            <x-slot name="footer">
                <x-filament::button wire:click="$set('showCustomerForm', false)" color="gray">Cancelar</x-filament::button>
                <x-filament::button wire:click="saveCustomer" color="success">Guardar</x-filament::button>
            </x-slot>
        </x-filament::modal>

        {{-- Import Modal --}}
        <x-filament::modal id="import-form" :visible="$showImportForm" width="md">
            <x-slot name="header">
                <h2 class="text-xl font-bold">Importar Clientes desde CSV</h2>
            </x-slot>
            
            <div class="space-y-4">
                <p class="text-sm text-gray-500">El archivo debe tener columnas: email, name, phone</p>
                <input type="file" wire:model="csvFile" accept=".csv" class="w-full" />
            </div>
            
            <x-slot name="footer">
                <x-filament::button wire:click="$set('showImportForm', false)" color="gray">Cancelar</x-filament::button>
                <x-filament::button wire:click="processImport" color="success">Importar</x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endif
</x-filament-panels::page>
