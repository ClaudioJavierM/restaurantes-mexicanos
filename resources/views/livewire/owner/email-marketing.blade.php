<div class="min-h-screen bg-gray-900 text-white">
    {{-- Header --}}
    <div class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Email Marketing</h1>
                    <p class="text-gray-400 mt-1">{{ $restaurant->name }}</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="openImport" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Importar
                    </button>
                    <button wire:click="newCampaign" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva Campana
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Overview --}}
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-white">{{ number_format($stats['total_customers']) }}</div>
                <div class="text-sm text-gray-400">Total Clientes</div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-green-400">{{ number_format($stats['subscribed']) }}</div>
                <div class="text-sm text-gray-400">Suscritos</div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-blue-400">{{ number_format($stats['sent_campaigns']) }}</div>
                <div class="text-sm text-gray-400">Campanas Enviadas</div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-purple-400">{{ number_format($stats['total_sent']) }}</div>
                <div class="text-sm text-gray-400">Emails Enviados</div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-yellow-400">{{ $stats['avg_open_rate'] }}%</div>
                <div class="text-sm text-gray-400">Tasa Apertura</div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700">
                <div class="text-2xl font-bold text-pink-400">{{ $stats['avg_click_rate'] }}%</div>
                <div class="text-sm text-gray-400">Tasa Clicks</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex gap-4 border-b border-gray-700">
            <button wire:click="$set('activeTab', 'campaigns')" class="px-4 py-3 font-medium {{ $activeTab === 'campaigns' ? 'text-green-400 border-b-2 border-green-400' : 'text-gray-400 hover:text-white' }}">
                Campanas
            </button>
            <button wire:click="$set('activeTab', 'customers')" class="px-4 py-3 font-medium {{ $activeTab === 'customers' ? 'text-green-400 border-b-2 border-green-400' : 'text-gray-400 hover:text-white' }}">
                Clientes
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 py-6">
        @if($activeTab === 'campaigns')
            {{-- Campaigns List --}}
            <div class="space-y-4">
                @forelse($this->campaigns as $campaign)
                    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold">{{ $campaign->name }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                        $campaign->status === 'sent' ? 'bg-green-500/20 text-green-400' : 
                                        ($campaign->status === 'scheduled' ? 'bg-blue-500/20 text-blue-400' : 
                                        ($campaign->status === 'sending' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-gray-500/20 text-gray-400')) }}">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </div>
                                <p class="text-gray-400 text-sm mt-1">{{ $campaign->subject }}</p>
                                <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                                    <span>{{ $campaign->type }}</span>
                                    <span>{{ $campaign->created_at->diffForHumans() }}</span>
                                    @if($campaign->sent_count > 0)
                                        <span>{{ number_format($campaign->sent_count) }} enviados</span>
                                        <span class="text-green-400">{{ $campaign->open_rate }}% aperturas</span>
                                        <span class="text-blue-400">{{ $campaign->click_rate }}% clicks</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($campaign->canEdit())
                                    <button wire:click="editCampaign({{ $campaign->id }})" class="p-2 hover:bg-gray-700 rounded-lg" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                @endif
                                <button wire:click="previewCampaign({{ $campaign->id }})" class="p-2 hover:bg-gray-700 rounded-lg" title="Vista previa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @if($campaign->canSend())
                                    <button wire:click="sendCampaignNow({{ $campaign->id }})" wire:confirm="Enviar campana ahora?" class="p-2 hover:bg-green-600 bg-green-600/20 text-green-400 rounded-lg" title="Enviar ahora">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </button>
                                @endif
                                @if($campaign->canCancel())
                                    <button wire:click="cancelCampaign({{ $campaign->id }})" class="p-2 hover:bg-red-600 bg-red-600/20 text-red-400 rounded-lg" title="Cancelar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                                @if($campaign->isDraft())
                                    <button wire:click="deleteCampaign({{ $campaign->id }})" wire:confirm="Eliminar esta campana?" class="p-2 hover:bg-red-600 rounded-lg text-red-400" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="text-gray-400">No hay campanas aun</p>
                        <button wire:click="newCampaign" class="mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg">Crear primera campana</button>
                    </div>
                @endforelse

                {{ $this->campaigns->links() }}
            </div>
        @else
            {{-- Customers List --}}
            <div class="mb-4 flex gap-4">
                <input type="text" wire:model.live.debounce.300ms="searchCustomers" placeholder="Buscar por email o nombre..." class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <select wire:model.live="filterSource" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg">
                    <option value="">Todas las fuentes</option>
                    <option value="manual">Manual</option>
                    <option value="qr_code">QR Code</option>
                    <option value="reservation">Reservacion</option>
                    <option value="order">Pedido</option>
                    <option value="import">Importado</option>
                </select>
                <button wire:click="newCustomer" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg">+ Agregar</button>
            </div>

            <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr class="border-b border-gray-700">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Nombre</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Fuente</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Visitas</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Puntos</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-400">Estado</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-400">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($this->customers as $customer)
                            <tr class="hover:bg-gray-750">
                                <td class="px-4 py-3 text-sm">{{ $customer->email }}</td>
                                <td class="px-4 py-3 text-sm">{{ $customer->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm"><span class="px-2 py-1 bg-gray-700 rounded text-xs">{{ $customer->source }}</span></td>
                                <td class="px-4 py-3 text-sm">{{ $customer->visits_count }}</td>
                                <td class="px-4 py-3 text-sm">{{ number_format($customer->points) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button wire:click="toggleSubscription({{ $customer->id }})" class="px-2 py-1 rounded text-xs {{ $customer->email_subscribed ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $customer->email_subscribed ? 'Suscrito' : 'No suscrito' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <button wire:click="editCustomer({{ $customer->id }})" class="p-1 hover:bg-gray-700 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteCustomer({{ $customer->id }})" wire:confirm="Eliminar este cliente?" class="p-1 hover:bg-red-600 rounded text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">No hay clientes aun</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $this->customers->links() }}</div>
        @endif
    </div>

    {{-- Campaign Modal --}}
    @if($showCampaignModal)
    <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-gray-800 rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold">{{ $editingCampaignId ? 'Editar Campana' : 'Nueva Campana' }}</h2>
                <button wire:click="$set('showCampaignModal', false)" class="p-2 hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nombre de la campana</label>
                        <input type="text" wire:model="campaignName" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Ej: Promocion de Verano">
                        @error('campaignName') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Tipo</label>
                        <select wire:model="campaignType" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                            @foreach($this->campaignTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Asunto del email</label>
                    <input type="text" wire:model="campaignSubject" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" placeholder="Ej: 20% de descuento este fin de semana!">
                    @error('campaignSubject') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Texto de preview (opcional)</label>
                    <input type="text" wire:model="campaignPreviewText" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg" placeholder="Texto que aparece en la bandeja de entrada">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Contenido del email</label>
                    <textarea wire:model="campaignContent" rows="10" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg font-mono text-sm" placeholder="Escribe el contenido de tu email aqui...\n\nPuedes usar:\n{nombre} - Nombre del cliente\n{restaurante} - Nombre de tu restaurante"></textarea>
                    @error('campaignContent') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- Coupon Section --}}
                <div class="bg-gray-750 rounded-lg p-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="includeCoupon" class="rounded bg-gray-700 border-gray-600 text-green-500 focus:ring-green-500">
                        <span class="font-medium">Incluir cupon de descuento</span>
                    </label>
                    @if($includeCoupon)
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Codigo (opcional)</label>
                                <input type="text" wire:model="couponCode" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm" placeholder="Auto-generado">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Descuento</label>
                                <input type="text" wire:model="couponDiscount" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm" placeholder="Ej: 20% o $10">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Expira</label>
                                <input type="date" wire:model="couponExpiry" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm">
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Audience Preview --}}
                <div class="bg-gray-750 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">Audiencia</span>
                        <span class="text-green-400">{{ number_format($this->audiencePreviewCount) }} destinatarios</span>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-700 flex justify-end gap-3">
                <button wire:click="$set('showCampaignModal', false)" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                <button wire:click="saveCampaign" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg">Guardar Campana</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Customer Modal --}}
    @if($showCustomerModal)
    <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-gray-800 rounded-xl max-w-lg w-full">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold">{{ $editingCustomerId ? 'Editar Cliente' : 'Nuevo Cliente' }}</h2>
                <button wire:click="$set('showCustomerModal', false)" class="p-2 hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Email *</label>
                    <input type="email" wire:model="customerEmail" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                    @error('customerEmail') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nombre</label>
                    <input type="text" wire:model="customerName" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Telefono</label>
                    <input type="tel" wire:model="customerPhone" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Cumpleanos</label>
                    <input type="date" wire:model="customerBirthday" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                </div>
            </div>
            <div class="p-6 border-t border-gray-700 flex justify-end gap-3">
                <button wire:click="$set('showCustomerModal', false)" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                <button wire:click="saveCustomer" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Import Modal --}}
    @if($showImportModal)
    <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-gray-800 rounded-xl max-w-lg w-full">
            <div class="p-6 border-b border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold">Importar Clientes</h2>
                <button wire:click="$set('showImportModal', false)" class="p-2 hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="border-2 border-dashed border-gray-600 rounded-lg p-8 text-center">
                    <input type="file" wire:model="importFile" accept=".csv" class="hidden" id="csvFile">
                    <label for="csvFile" class="cursor-pointer">
                        <svg class="w-12 h-12 mx-auto text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-gray-400">Arrastra un archivo CSV o haz clic para seleccionar</p>
                        <p class="text-sm text-gray-500 mt-2">El archivo debe tener columnas: email, name, phone</p>
                    </label>
                </div>
                @if($importCount > 0)
                    <div class="bg-gray-750 rounded-lg p-4">
                        <p class="text-green-400">{{ $importCount }} registros encontrados</p>
                        @if(count($importPreview) > 0)
                            <div class="mt-2 text-sm text-gray-400">
                                <p>Preview:</p>
                                @foreach($importPreview as $row)
                                    <p>{{ $row['email'] ?? $row['Email'] ?? '-' }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-6 border-t border-gray-700 flex justify-end gap-3">
                <button wire:click="$set('showImportModal', false)" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">Cancelar</button>
                <button wire:click="processImport" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg" {{ $importCount === 0 ? 'disabled' : '' }}>Importar</button>
            </div>
        </div>
    </div>
    @endif
</div>
