{{-- Live Orders Board — Kitchen Kanban --}}
<div
    class="min-h-screen bg-[#0B0B0B] p-4 md:p-6"
    wire:poll.5s
>

    {{-- ═══════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════ --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-['Playfair_Display'] text-2xl font-bold text-white">
                Tablero de Órdenes
            </h1>
            <p class="mt-1 text-sm text-gray-400">
                Actualización automática cada 5 segundos
                <span class="ml-2 inline-block h-2 w-2 animate-pulse rounded-full bg-[#D4AF37]"></span>
            </p>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex gap-1 rounded-xl bg-[#1A1A1A] p-1">
            <button
                wire:click="setFilter('active')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-all
                    {{ $filter === 'active' ? 'bg-[#D4AF37] text-[#0B0B0B]' : 'text-gray-400 hover:text-white' }}"
            >
                Activas
            </button>
            <button
                wire:click="setFilter('today')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-all
                    {{ $filter === 'today' ? 'bg-[#D4AF37] text-[#0B0B0B]' : 'text-gray-400 hover:text-white' }}"
            >
                Hoy
            </button>
            <button
                wire:click="setFilter('completed')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-all
                    {{ $filter === 'completed' ? 'bg-[#D4AF37] text-[#0B0B0B]' : 'text-gray-400 hover:text-white' }}"
            >
                Completadas
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         KANBAN BOARD
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:gap-3 xl:gap-4 overflow-x-auto pb-4">

        {{-- ── COL: NUEVOS (glow dorado pulsante) ── --}}
        @php $colOrders = $this->orders['pending'] ?? [] @endphp
        <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
            <div class="mb-3 flex items-center justify-between rounded-xl border border-[#D4AF37]/50 bg-[#1A1A1A] px-4 py-3 orders-col-new">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟡</span>
                    <span class="font-semibold text-[#D4AF37] tracking-wide text-sm">NUEVOS</span>
                </div>
                @if (count($colOrders) > 0)
                    <span class="rounded-full bg-[#D4AF37] px-2.5 py-0.5 text-xs font-bold text-[#0B0B0B] animate-bounce">
                        {{ count($colOrders) }}
                    </span>
                @else
                    <span class="rounded-full bg-[#2A2A2A] px-2.5 py-0.5 text-xs font-bold text-gray-500">0</span>
                @endif
            </div>
            <div class="flex flex-col gap-3 min-h-[120px]">
                @forelse ($colOrders as $order)
                    @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'pending'])
                @empty
                    <x-famer-empty-column label="Sin órdenes nuevas" />
                @endforelse
            </div>
        </div>

        {{-- ── COL: CONFIRMADOS ── --}}
        @php $colOrders = $this->orders['confirmed'] ?? [] @endphp
        <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
            <div class="mb-3 flex items-center justify-between rounded-xl border border-blue-500/30 bg-[#1A1A1A] px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🔵</span>
                    <span class="font-semibold text-blue-400 tracking-wide text-sm">CONFIRMADOS</span>
                </div>
                <span class="rounded-full {{ count($colOrders) > 0 ? 'bg-blue-500 text-white' : 'bg-[#2A2A2A] text-gray-500' }} px-2.5 py-0.5 text-xs font-bold">
                    {{ count($colOrders) }}
                </span>
            </div>
            <div class="flex flex-col gap-3 min-h-[120px]">
                @forelse ($colOrders as $order)
                    @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'confirmed'])
                @empty
                    <x-famer-empty-column label="Sin órdenes confirmadas" />
                @endforelse
            </div>
        </div>

        {{-- ── COL: PREPARANDO ── --}}
        @php $colOrders = $this->orders['preparing'] ?? [] @endphp
        <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
            <div class="mb-3 flex items-center justify-between rounded-xl border border-orange-500/30 bg-[#1A1A1A] px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟠</span>
                    <span class="font-semibold text-orange-400 tracking-wide text-sm">PREPARANDO</span>
                </div>
                <span class="rounded-full {{ count($colOrders) > 0 ? 'bg-orange-500 text-white' : 'bg-[#2A2A2A] text-gray-500' }} px-2.5 py-0.5 text-xs font-bold">
                    {{ count($colOrders) }}
                </span>
            </div>
            <div class="flex flex-col gap-3 min-h-[120px]">
                @forelse ($colOrders as $order)
                    @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'preparing'])
                @empty
                    <x-famer-empty-column label="Sin órdenes en preparación" />
                @endforelse
            </div>
        </div>

        {{-- ── COL: LISTOS ── --}}
        @php $colOrders = $this->orders['ready'] ?? [] @endphp
        <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
            <div class="mb-3 flex items-center justify-between rounded-xl border border-green-500/30 bg-[#1A1A1A] px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🟢</span>
                    <span class="font-semibold text-green-400 tracking-wide text-sm">LISTOS</span>
                </div>
                <span class="rounded-full {{ count($colOrders) > 0 ? 'bg-green-500 text-white' : 'bg-[#2A2A2A] text-gray-500' }} px-2.5 py-0.5 text-xs font-bold">
                    {{ count($colOrders) }}
                </span>
            </div>
            <div class="flex flex-col gap-3 min-h-[120px]">
                @forelse ($colOrders as $order)
                    @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'ready'])
                @empty
                    <x-famer-empty-column label="Sin órdenes listas" />
                @endforelse
            </div>
        </div>

        {{-- ── COL: EN CAMINO ── --}}
        @php $colOrders = $this->orders['out_for_delivery'] ?? [] @endphp
        <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
            <div class="mb-3 flex items-center justify-between rounded-xl border border-purple-500/30 bg-[#1A1A1A] px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">🚗</span>
                    <span class="font-semibold text-purple-400 tracking-wide text-sm">EN CAMINO</span>
                </div>
                <span class="rounded-full {{ count($colOrders) > 0 ? 'bg-purple-500 text-white' : 'bg-[#2A2A2A] text-gray-500' }} px-2.5 py-0.5 text-xs font-bold">
                    {{ count($colOrders) }}
                </span>
            </div>
            <div class="flex flex-col gap-3 min-h-[120px]">
                @forelse ($colOrders as $order)
                    @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'out_for_delivery'])
                @empty
                    <x-famer-empty-column label="Sin órdenes en camino" />
                @endforelse
            </div>
        </div>

        {{-- ── EXTRA COLS: completed / cancelled (solo en filter=completed) ── --}}
        @if ($filter === 'completed')

            @php $colOrders = $this->orders['completed'] ?? [] @endphp
            <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
                <div class="mb-3 flex items-center justify-between rounded-xl border border-gray-600/30 bg-[#1A1A1A] px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-base">✨</span>
                        <span class="font-semibold text-gray-300 tracking-wide text-sm">COMPLETADOS</span>
                    </div>
                    <span class="rounded-full bg-[#2A2A2A] px-2.5 py-0.5 text-xs font-bold text-gray-400">
                        {{ count($colOrders) }}
                    </span>
                </div>
                <div class="flex flex-col gap-3 min-h-[120px]">
                    @forelse ($colOrders as $order)
                        @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'completed'])
                    @empty
                        <x-famer-empty-column label="Sin órdenes completadas" />
                    @endforelse
                </div>
            </div>

            @php $colOrders = $this->orders['cancelled'] ?? [] @endphp
            <div class="flex-shrink-0 w-full lg:w-64 xl:w-72">
                <div class="mb-3 flex items-center justify-between rounded-xl border border-red-800/30 bg-[#1A1A1A] px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-base">❌</span>
                        <span class="font-semibold text-red-400 tracking-wide text-sm">CANCELADOS</span>
                    </div>
                    <span class="rounded-full bg-[#2A2A2A] px-2.5 py-0.5 text-xs font-bold text-gray-400">
                        {{ count($colOrders) }}
                    </span>
                </div>
                <div class="flex flex-col gap-3 min-h-[120px]">
                    @forelse ($colOrders as $order)
                        @include('livewire.owner.partials.order-card', ['order' => $order, 'column' => 'cancelled'])
                    @empty
                        <x-famer-empty-column label="Sin órdenes canceladas" />
                    @endforelse
                </div>
            </div>

        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════
         CANCEL MODAL
    ═══════════════════════════════════════════════════════ --}}
    @if ($showCancelModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4 backdrop-blur-sm"
            wire:click.self="$set('showCancelModal', false)"
        >
            <div class="w-full max-w-md rounded-2xl border border-[#2A2A2A] bg-[#1A1A1A] p-6 shadow-2xl">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-900/30">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="font-['Playfair_Display'] text-xl font-bold text-white">
                        Cancelar Orden
                    </h3>
                </div>
                <p class="mb-4 text-sm text-gray-400">
                    ¿Estás seguro de que deseas cancelar esta orden? Esta acción no se puede deshacer.
                </p>
                <div class="mb-5">
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Motivo (opcional)
                    </label>
                    <textarea
                        wire:model="cancelReason"
                        rows="3"
                        placeholder="Ej: Ingrediente agotado, restaurante cerrado..."
                        class="w-full rounded-xl border border-[#2A2A2A] bg-[#0B0B0B] px-4 py-3 text-sm text-white
                               placeholder-gray-600 focus:border-[#D4AF37]/40 focus:outline-none
                               focus:ring-1 focus:ring-[#D4AF37]/20 resize-none"
                    ></textarea>
                </div>
                <div class="flex gap-3">
                    <button
                        wire:click="$set('showCancelModal', false)"
                        class="flex-1 rounded-xl border border-[#2A2A2A] bg-transparent px-4 py-2.5 text-sm font-medium
                               text-gray-300 transition hover:border-gray-500 hover:text-white"
                    >
                        Mantener orden
                    </button>
                    <button
                        wire:click="cancelOrder"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="flex-1 rounded-xl bg-red-700 px-4 py-2.5 text-sm font-bold text-white
                               transition hover:bg-red-600 active:scale-95"
                    >
                        <span wire:loading.remove wire:target="cancelOrder">Sí, cancelar</span>
                        <span wire:loading wire:target="cancelOrder">Cancelando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

@push('styles')
<style>
@keyframes orders-glow {
    0%, 100% {
        box-shadow: 0 0 8px rgba(212,175,55,0.15),
                    0 0 20px rgba(212,175,55,0.05),
                    inset 0 0 0 1px rgba(212,175,55,0.15);
    }
    50% {
        box-shadow: 0 0 18px rgba(212,175,55,0.40),
                    0 0 40px rgba(212,175,55,0.15),
                    inset 0 0 0 1px rgba(212,175,55,0.50);
    }
}
.orders-col-new {
    animation: orders-glow 2.5s ease-in-out infinite;
}
</style>
@endpush
