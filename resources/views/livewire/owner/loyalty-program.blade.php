<div class="space-y-6">
    {{-- Flash message --}}
    @if(session()->has('loyalty_success'))
        <div class="rounded-lg p-4" style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3);">
            <p style="color:#4ADE80;">{{ session('loyalty_success') }}</p>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-xl p-4" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <p class="text-xs uppercase tracking-wide" style="color:#9CA3AF;">Recompensas</p>
            <p class="text-2xl font-bold mt-1" style="color:#F5F5F5;">{{ $stats['total_rewards'] }}</p>
            <p class="text-xs mt-1" style="color:#6B7280;">{{ $stats['active_rewards'] }} activas</p>
        </div>
        <div class="rounded-xl p-4" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <p class="text-xs uppercase tracking-wide" style="color:#9CA3AF;">Canjes totales</p>
            <p class="text-2xl font-bold mt-1" style="color:#F5F5F5;">{{ $stats['total_redemptions'] }}</p>
        </div>
        <div class="rounded-xl p-4" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <p class="text-xs uppercase tracking-wide" style="color:#9CA3AF;">Puntos distribuidos</p>
            <p class="text-2xl font-bold mt-1" style="color:#D4AF37;">{{ number_format($stats['total_points_distributed']) }}</p>
        </div>
        <div class="rounded-xl p-4" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <p class="text-xs uppercase tracking-wide" style="color:#9CA3AF;">Estado</p>
            <p class="text-lg font-bold mt-1" style="color:{{ $loyaltyEnabled ? '#4ADE80' : '#9CA3AF' }};">
                {{ $loyaltyEnabled ? 'Activo' : 'Inactivo' }}
            </p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b" style="border-color:#2A2A2A;">
        <button wire:click="switchTab('settings')"
                class="px-4 py-2 font-semibold transition-colors"
                style="{{ $activeTab === 'settings' ? 'color:#D4AF37; border-bottom:2px solid #D4AF37;' : 'color:#9CA3AF;' }}">
            Configuración
        </button>
        <button wire:click="switchTab('rewards')"
                class="px-4 py-2 font-semibold transition-colors"
                style="{{ $activeTab === 'rewards' ? 'color:#D4AF37; border-bottom:2px solid #D4AF37;' : 'color:#9CA3AF;' }}">
            Recompensas
        </button>
        <button wire:click="switchTab('redemptions')"
                class="px-4 py-2 font-semibold transition-colors"
                style="{{ $activeTab === 'redemptions' ? 'color:#D4AF37; border-bottom:2px solid #D4AF37;' : 'color:#9CA3AF;' }}">
            Canjes
        </button>
        <button wire:click="switchTab('customers')"
                class="px-4 py-2 font-semibold transition-colors"
                style="{{ $activeTab === 'customers' ? 'color:#D4AF37; border-bottom:2px solid #D4AF37;' : 'color:#9CA3AF;' }}">
            Top Clientes
        </button>
    </div>

    {{-- SETTINGS TAB --}}
    @if($activeTab === 'settings')
        <div class="rounded-xl p-6" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <h3 class="text-lg font-bold mb-4" style="color:#F5F5F5;">Configuración del programa</h3>

            <div class="space-y-4 max-w-xl">
                {{-- Enable toggle --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="loyaltyEnabled" class="mt-1" style="accent-color:#D4AF37; width:1.25rem; height:1.25rem;">
                    <div>
                        <p class="font-semibold" style="color:#F5F5F5;">Activar programa de lealtad</p>
                        <p class="text-sm mt-1" style="color:#9CA3AF;">Permite que tus clientes acumulen puntos y canjeen recompensas.</p>
                    </div>
                </label>

                {{-- Points per dollar --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Puntos por dólar gastado</label>
                    <input type="number" wire:model="pointsPerDollar" min="0" max="100"
                           class="w-full rounded-lg px-4 py-2.5"
                           style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                    <p class="text-xs mt-1" style="color:#6B7280;">Ej: 1 = 1 punto por cada dólar gastado</p>
                    @error('pointsPerDollar') <p class="text-xs mt-1" style="color:#FCA5A5;">{{ $message }}</p> @enderror
                </div>

                {{-- Points per visit --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Puntos por visita</label>
                    <input type="number" wire:model="pointsPerVisit" min="0" max="1000"
                           class="w-full rounded-lg px-4 py-2.5"
                           style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                    <p class="text-xs mt-1" style="color:#6B7280;">Puntos que se otorgan al registrar una visita (además de los por dólar)</p>
                    @error('pointsPerVisit') <p class="text-xs mt-1" style="color:#FCA5A5;">{{ $message }}</p> @enderror
                </div>

                <button wire:click="saveSettings"
                        class="px-6 py-2.5 rounded-lg font-semibold transition-colors"
                        style="background:#D4AF37; color:#0B0B0B;">
                    Guardar Configuración
                </button>
            </div>
        </div>
    @endif

    {{-- REWARDS TAB --}}
    @if($activeTab === 'rewards')
        <div class="rounded-xl p-6" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold" style="color:#F5F5F5;">Recompensas</h3>
                <button wire:click="openRewardModal"
                        class="px-4 py-2 rounded-lg font-semibold text-sm"
                        style="background:#D4AF37; color:#0B0B0B;">
                    + Nueva Recompensa
                </button>
            </div>

            @if($rewards->isEmpty())
                <div class="text-center py-12">
                    <p style="color:#9CA3AF;">Aún no tienes recompensas configuradas.</p>
                    <p class="text-sm mt-2" style="color:#6B7280;">Crea tu primera recompensa para empezar.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($rewards as $reward)
                        <div class="rounded-lg p-4 flex items-center justify-between" style="background:#111; border:1px solid #2A2A2A;">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <p class="font-semibold" style="color:#F5F5F5;">{{ $reward->name }}</p>
                                    @if($reward->is_active)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background:rgba(74,222,128,0.1); color:#4ADE80;">Activa</span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background:#2A2A2A; color:#9CA3AF;">Inactiva</span>
                                    @endif
                                </div>
                                <p class="text-sm mt-1" style="color:#9CA3AF;">{{ $reward->formatted_reward }}</p>
                                <p class="text-xs mt-1" style="color:#D4AF37;">{{ number_format($reward->points_required) }} puntos</p>
                                @if($reward->description)
                                    <p class="text-xs mt-1" style="color:#6B7280;">{{ $reward->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleRewardActive({{ $reward->id }})"
                                        class="text-xs px-3 py-1.5 rounded-lg"
                                        style="background:#2A2A2A; color:#F5F5F5;">
                                    {{ $reward->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                                <button wire:click="openRewardModal({{ $reward->id }})"
                                        class="text-xs px-3 py-1.5 rounded-lg"
                                        style="background:#2A2A2A; color:#D4AF37;">
                                    Editar
                                </button>
                                <button wire:click="deleteReward({{ $reward->id }})"
                                        wire:confirm="¿Eliminar esta recompensa?"
                                        class="text-xs px-3 py-1.5 rounded-lg"
                                        style="background:rgba(220,38,38,0.1); color:#F87171;">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- REDEMPTIONS TAB --}}
    @if($activeTab === 'redemptions')
        <div class="rounded-xl p-6" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <h3 class="text-lg font-bold mb-4" style="color:#F5F5F5;">Historial de canjes</h3>

            @if($redemptions->isEmpty())
                <p style="color:#9CA3AF;">Aún no hay canjes registrados.</p>
            @else
                <div class="space-y-2">
                    @foreach($redemptions as $redemption)
                        <div class="rounded-lg p-3 flex items-center justify-between" style="background:#111; border:1px solid #2A2A2A;">
                            <div>
                                <p class="font-semibold text-sm" style="color:#F5F5F5;">{{ $redemption->reward?->name ?? 'N/A' }}</p>
                                <p class="text-xs mt-0.5" style="color:#9CA3AF;">
                                    {{ $redemption->customer?->name ?? $redemption->customer?->email ?? 'Cliente' }} · Código: <span style="color:#D4AF37;">{{ $redemption->redemption_code }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                      style="{{ $redemption->status === 'used' ? 'background:rgba(74,222,128,0.1); color:#4ADE80;' : ($redemption->status === 'expired' ? 'background:rgba(156,163,175,0.1); color:#9CA3AF;' : 'background:rgba(212,175,55,0.1); color:#D4AF37;') }}">
                                    {{ ucfirst($redemption->status) }}
                                </span>
                                <p class="text-xs mt-1" style="color:#6B7280;">{{ $redemption->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $redemptions->links() }}</div>
            @endif
        </div>
    @endif

    {{-- CUSTOMERS TAB --}}
    @if($activeTab === 'customers')
        <div class="rounded-xl p-6" style="background:#1A1A1A; border:1px solid #2A2A2A;">
            <h3 class="text-lg font-bold mb-4" style="color:#F5F5F5;">Top clientes por puntos</h3>

            @if($topCustomers->isEmpty())
                <p style="color:#9CA3AF;">Aún no tienes clientes con puntos.</p>
                <p class="text-sm mt-2" style="color:#6B7280;">Importa tu lista de clientes desde Email Marketing y empieza a registrar visitas.</p>
            @else
                <div class="space-y-2">
                    @foreach($topCustomers as $idx => $customer)
                        <div class="rounded-lg p-3 flex items-center justify-between" style="background:#111; border:1px solid #2A2A2A;">
                            <div class="flex items-center gap-3">
                                <span class="font-bold" style="color:#D4AF37;">#{{ $idx + 1 }}</span>
                                <div>
                                    <p class="font-semibold text-sm" style="color:#F5F5F5;">{{ $customer->name ?? $customer->email }}</p>
                                    <p class="text-xs mt-0.5" style="color:#9CA3AF;">{{ $customer->email }} · {{ $customer->visits_count }} visitas</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold" style="color:#D4AF37;">{{ number_format($customer->points) }}</p>
                                <p class="text-xs" style="color:#6B7280;">puntos</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- REWARD MODAL --}}
    @if($showRewardModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.8);">
            <div class="w-full max-w-lg rounded-xl p-6" style="background:#1A1A1A; border:1px solid #2A2A2A;">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold" style="color:#F5F5F5;">
                        {{ $editingRewardId ? 'Editar Recompensa' : 'Nueva Recompensa' }}
                    </h3>
                    <button wire:click="closeRewardModal" style="color:#9CA3AF;">✕</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Nombre *</label>
                        <input type="text" wire:model="rewardName" placeholder="Ej: Entrada gratis"
                               class="w-full rounded-lg px-4 py-2.5"
                               style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                        @error('rewardName') <p class="text-xs mt-1" style="color:#FCA5A5;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Descripción</label>
                        <textarea wire:model="rewardDescription" rows="2"
                                  class="w-full rounded-lg px-4 py-2.5"
                                  style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Puntos requeridos *</label>
                        <input type="number" wire:model="rewardPointsRequired" min="1"
                               class="w-full rounded-lg px-4 py-2.5"
                               style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                        @error('rewardPointsRequired') <p class="text-xs mt-1" style="color:#FCA5A5;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Tipo de recompensa *</label>
                        <select wire:model.live="rewardType"
                                class="w-full rounded-lg px-4 py-2.5"
                                style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                            @foreach($rewardTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($rewardType === 'discount_percentage' || $rewardType === 'discount_fixed')
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">
                                Valor del descuento ({{ $rewardType === 'discount_percentage' ? '%' : '$' }})
                            </label>
                            <input type="number" wire:model="rewardValue" step="0.01" min="0"
                                   class="w-full rounded-lg px-4 py-2.5"
                                   style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                        </div>
                    @endif

                    @if($rewardType === 'free_item')
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Nombre del artículo</label>
                            <input type="text" wire:model="rewardFreeItemName" placeholder="Ej: Margarita"
                                   class="w-full rounded-lg px-4 py-2.5"
                                   style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#CCCCCC;">Límite de usos (opcional)</label>
                        <input type="number" wire:model="rewardUsageLimit" min="1" placeholder="Sin límite"
                               class="w-full rounded-lg px-4 py-2.5"
                               style="background:#111; border:1px solid #2A2A2A; color:#F5F5F5;">
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="rewardIsActive" style="accent-color:#D4AF37;">
                        <span style="color:#F5F5F5;">Recompensa activa</span>
                    </label>
                </div>

                <div class="flex gap-2 mt-6 justify-end">
                    <button wire:click="closeRewardModal"
                            class="px-4 py-2 rounded-lg font-semibold"
                            style="background:#2A2A2A; color:#F5F5F5;">
                        Cancelar
                    </button>
                    <button wire:click="saveReward"
                            class="px-4 py-2 rounded-lg font-semibold"
                            style="background:#D4AF37; color:#0B0B0B;">
                        {{ $editingRewardId ? 'Actualizar' : 'Crear' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
