<x-filament-panels::page>
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMembers) }}</div>
                <div class="text-sm text-gray-500">Miembros</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($totalPointsIssued) }}</div>
                <div class="text-sm text-gray-500">Puntos Totales</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($totalRedemptions) }}</div>
                <div class="text-sm text-gray-500">Canjes</div>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $activeRewards }}</div>
                <div class="text-sm text-gray-500">Recompensas Activas</div>
            </div>
        </x-filament::section>
    </div>

    {{-- Settings --}}
    <x-filament::section>
        <x-slot name="heading">Configuracion del Programa</x-slot>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div>
                    <h3 class="font-medium">Programa de Lealtad</h3>
                    <p class="text-sm text-gray-500">Activa el programa para comenzar a dar puntos</p>
                </div>
                <x-filament::button wire:click="toggleLoyalty" :color="$loyaltyEnabled ? 'success' : 'gray'">
                    {{ $loyaltyEnabled ? 'Activado' : 'Desactivado' }}
                </x-filament::button>
            </div>

            @if($loyaltyEnabled)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Puntos por cada $1 gastado</label>
                        <input type="number" wire:model="pointsPerDollar" min="1" max="100" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Puntos por visita</label>
                        <input type="number" wire:model="pointsPerVisit" min="0" max="1000" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-filament::button wire:click="saveSettings" color="success">Guardar Configuracion</x-filament::button>
                </div>
            @endif
        </div>
    </x-filament::section>

    {{-- Rewards --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <span>Recompensas</span>
                <x-filament::button wire:click="newReward" size="sm" icon="heroicon-o-plus">Nueva Recompensa</x-filament::button>
            </div>
        </x-slot>

        <div class="space-y-3">
            @forelse($this->rewards as $reward)
                <div class="flex items-center justify-between p-4 border dark:border-gray-700 rounded-lg {{ $reward->is_active ? '' : 'opacity-50' }}">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium">{{ $reward->name }}</h4>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $reward->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $reward->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $reward->formatted_reward }}</p>
                        <p class="text-sm text-yellow-600 font-medium">{{ number_format($reward->points_required) }} puntos</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">{{ $reward->redemption_count }} canjes</span>
                        <x-filament::icon-button icon="heroicon-o-pencil" wire:click="editReward({{ $reward->id }})" />
                        <x-filament::icon-button icon="{{ $reward->is_active ? 'heroicon-o-pause' : 'heroicon-o-play' }}" wire:click="toggleRewardStatus({{ $reward->id }})" />
                        <x-filament::icon-button icon="heroicon-o-trash" color="danger" wire:click="deleteReward({{ $reward->id }})" wire:confirm="Eliminar esta recompensa?" />
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    <p>No hay recompensas configuradas</p>
                    <x-filament::button wire:click="newReward" class="mt-4" size="sm">Crear primera recompensa</x-filament::button>
                </div>
            @endforelse
        </div>
    </x-filament::section>

    {{-- Top Members & Recent Redemptions --}}
    <div class="grid md:grid-cols-2 gap-6 mt-6">
        <x-filament::section>
            <x-slot name="heading">Top Miembros</x-slot>
            <div class="space-y-2">
                @forelse($this->topMembers as $index => $member)
                    <div class="flex items-center justify-between p-2 {{ $index < 3 ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }} rounded">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 flex items-center justify-center text-sm font-medium {{ $index < 3 ? 'text-yellow-600' : 'text-gray-400' }}">{{ $index + 1 }}</span>
                            <div>
                                <p class="font-medium text-sm">{{ $member->display_name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->email }}</p>
                            </div>
                        </div>
                        <span class="font-bold text-yellow-600">{{ number_format($member->points) }} pts</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No hay miembros aun</p>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Canjes Recientes</x-slot>
            <div class="space-y-2">
                @forelse($this->recentRedemptions as $redemption)
                    <div class="flex items-center justify-between p-2 border-b dark:border-gray-700 last:border-0">
                        <div>
                            <p class="font-medium text-sm">{{ $redemption->customer->display_name }}</p>
                            <p class="text-xs text-gray-500">{{ $redemption->reward->name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $redemption->status === 'used' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $redemption->status === 'used' ? 'Usado' : 'Pendiente' }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $redemption->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No hay canjes aun</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    {{-- Reward Form Modal --}}
    <x-filament::modal id="reward-form" :visible="$showRewardForm" width="lg">
        <x-slot name="header">
            <h2 class="text-xl font-bold">{{ $editingRewardId ? 'Editar Recompensa' : 'Nueva Recompensa' }}</h2>
        </x-slot>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" wire:model="rewardData.name" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600" placeholder="Ej: Postre Gratis">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Descripcion</label>
                <textarea wire:model="rewardData.description" rows="2" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Puntos Requeridos</label>
                <input type="number" wire:model="rewardData.points_required" min="1" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tipo de Recompensa</label>
                <select wire:model.live="rewardData.reward_type" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                    <option value="discount_percentage">Descuento (%)</option>
                    <option value="discount_fixed">Descuento ($)</option>
                    <option value="free_item">Articulo Gratis</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>
            @if(in_array($rewardData['reward_type'] ?? '', ['discount_percentage', 'discount_fixed']))
                <div>
                    <label class="block text-sm font-medium mb-1">Valor del Descuento</label>
                    <input type="number" wire:model="rewardData.reward_value" min="1" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                </div>
            @elseif(($rewardData['reward_type'] ?? '') === 'free_item')
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre del Articulo</label>
                    <input type="text" wire:model="rewardData.free_item_name" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600" placeholder="Ej: Postre del dia">
                </div>
            @endif
        </div>
        
        <x-slot name="footer">
            <x-filament::button wire:click="$set('showRewardForm', false)" color="gray">Cancelar</x-filament::button>
            <x-filament::button wire:click="saveReward" color="success">Guardar</x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>
