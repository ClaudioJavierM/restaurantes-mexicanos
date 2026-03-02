<x-filament-panels::page>
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600">{{ number_format($subscribedCustomers) }}</div>
                <div class="text-sm text-gray-500">Suscriptores SMS</div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-success-600">{{ number_format($totalSent) }}</div>
                <div class="text-sm text-gray-500">SMS Enviados (30d)</div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-info-600">{{ number_format($totalDelivered) }}</div>
                <div class="text-sm text-gray-500">Entregados</div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-warning-600">{{ number_format($totalClicked) }}</div>
                <div class="text-sm text-gray-500">Clicks</div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <div class="text-center">
                <div class="text-3xl font-bold text-danger-600">{{ $clickRate }}%</div>
                <div class="text-sm text-gray-500">Click Rate</div>
            </div>
        </x-filament::card>
    </div>

    {{-- Tabs --}}
    <div class="mb-4">
        <nav class="flex space-x-4">
            <button 
                wire:click="$set('activeTab', 'automations')"
                class="px-4 py-2 rounded-lg font-medium transition {{ $activeTab === 'automations' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                Automatizaciones
            </button>
            <button 
                wire:click="$set('activeTab', 'logs')"
                class="px-4 py-2 rounded-lg font-medium transition {{ $activeTab === 'logs' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
            >
                Historial de Envíos
            </button>
        </nav>
    </div>

    @if($activeTab === 'automations')
        {{-- Action Buttons --}}
        <div class="flex justify-between items-center mb-4">
            <div class="flex gap-2">
                <x-filament::button wire:click="createAutomation" icon="heroicon-o-plus">
                    Nueva Automatización
                </x-filament::button>
                <x-filament::button wire:click="sendTestSms" color="gray" icon="heroicon-o-paper-airplane">
                    Enviar SMS de Prueba
                </x-filament::button>
            </div>
        </div>

        {{-- Automation Form Modal --}}
        @if($showAutomationForm)
            <x-filament::card class="mb-6">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $editingAutomation ? 'Editar Automatización' : 'Nueva Automatización SMS' }}
                </h3>

                <form wire:submit.prevent="saveAutomation" class="space-y-4">
                    @foreach($this->getAutomationForm() as $field)
                        {{ $field }}
                    @endforeach

                    <div class="flex justify-end gap-2 mt-4">
                        <x-filament::button type="button" wire:click="cancelForm" color="gray">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button type="submit">
                            {{ $editingAutomation ? 'Actualizar' : 'Crear' }} Automatización
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::card>
        @endif

        {{-- Automations Table --}}
        {{ $this->table }}

        {{-- Quick Setup Templates --}}
        <x-filament::card class="mt-6">
            <h3 class="text-lg font-semibold mb-4">🚀 Configuración Rápida</h3>
            <p class="text-sm text-gray-500 mb-4">Activa estas automatizaciones recomendadas con un clic:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $quickSetups = [
                        ['trigger' => 'abandoned_cart', 'name' => 'Carrito Abandonado', 'icon' => '🛒', 'delay' => 15, 'desc' => 'Recupera ventas perdidas'],
                        ['trigger' => 'winback', 'name' => 'Win-Back 45 días', 'icon' => '👋', 'delay' => 0, 'desc' => 'Reactiva clientes inactivos'],
                        ['trigger' => 'birthday', 'name' => 'Cumpleaños', 'icon' => '🎂', 'delay' => 0, 'desc' => 'Regalo de cumpleaños'],
                        ['trigger' => 'post_order', 'name' => 'Confirmación', 'icon' => '✅', 'delay' => 0, 'desc' => 'Confirma pedidos'],
                    ];
                @endphp

                @foreach($quickSetups as $setup)
                    <div class="p-4 border rounded-lg hover:border-primary-500 transition cursor-pointer"
                         wire:click="$set('automationData', {
                             'trigger_type': '{{ $setup['trigger'] }}',
                             'name': '{{ $setup['name'] }}',
                             'delay_minutes': {{ $setup['delay'] }},
                             'is_active': true
                         }); $set('showAutomationForm', true); loadDefaultTemplate('{{ $setup['trigger'] }}')">
                        <div class="text-2xl mb-2">{{ $setup['icon'] }}</div>
                        <div class="font-semibold">{{ $setup['name'] }}</div>
                        <div class="text-xs text-gray-500">{{ $setup['desc'] }}</div>
                    </div>
                @endforeach
            </div>
        </x-filament::card>
    @else
        {{-- SMS Logs --}}
        <x-filament::card>
            <h3 class="text-lg font-semibold mb-4">Últimos SMS Enviados</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Fecha</th>
                            <th class="text-left py-2">Cliente</th>
                            <th class="text-left py-2">Teléfono</th>
                            <th class="text-left py-2">Tipo</th>
                            <th class="text-left py-2">Estado</th>
                            <th class="text-left py-2">Mensaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->recentLogs as $log)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">{{ $log->created_at->format('d/m H:i') }}</td>
                                <td class="py-2">{{ $log->customer?->name ?? '-' }}</td>
                                <td class="py-2">{{ $log->phone }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $log->trigger_type === 'abandoned_cart' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $log->trigger_type === 'winback' ? 'bg-orange-100 text-orange-700' : '' }}
                                        {{ $log->trigger_type === 'birthday' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $log->trigger_type === 'post_order' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ !in_array($log->trigger_type, ['abandoned_cart', 'winback', 'birthday', 'post_order']) ? 'bg-gray-100 text-gray-700' : '' }}
                                    ">
                                        {{ \App\Models\SmsAutomation::triggerTypes()[$log->trigger_type] ?? $log->trigger_type }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    @if($log->status === 'sent' || $log->status === 'delivered')
                                        <span class="text-green-600">✓ Enviado</span>
                                    @elseif($log->status === 'clicked')
                                        <span class="text-blue-600">👆 Click</span>
                                    @elseif($log->status === 'failed')
                                        <span class="text-red-600">✗ Error</span>
                                    @else
                                        <span class="text-gray-500">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="py-2 max-w-xs truncate" title="{{ $log->message }}">
                                    {{ Str::limit($log->message, 50) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    No hay SMS enviados aún
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    @endif

    {{-- Help Section --}}
    <x-filament::card class="mt-6">
        <h3 class="text-lg font-semibold mb-2">📱 Variables Disponibles</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
            <code class="bg-gray-100 px-2 py-1 rounded">{customer_name}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{restaurant_name}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{cart_total}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{points}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{coupon_code}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{coupon_discount}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{order_url}</code>
            <code class="bg-gray-100 px-2 py-1 rounded">{days_since_order}</code>
        </div>
    </x-filament::card>
</x-filament-panels::page>
