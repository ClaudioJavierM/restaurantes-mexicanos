{{-- Order Card Partial
     Variables: $order (Order model with items loaded), $column (string status key)
--}}
@php
    use Carbon\Carbon;

    $minutesAgo = $order->created_at->diffInMinutes(now());
    $timeLabel  = $order->created_at->diffForHumans();

    // Color accent per column
    $accentClass = match ($column) {
        'pending'          => 'border-[#D4AF37]/40 hover:border-[#D4AF37]/70',
        'confirmed'        => 'border-blue-500/30 hover:border-blue-400/60',
        'preparing'        => 'border-orange-500/30 hover:border-orange-400/60',
        'ready'            => 'border-green-500/30 hover:border-green-400/60',
        'out_for_delivery' => 'border-purple-500/30 hover:border-purple-400/60',
        'completed'        => 'border-gray-600/20 hover:border-gray-500/40 opacity-75',
        'cancelled'        => 'border-red-800/20 hover:border-red-700/40 opacity-60',
        default            => 'border-[#2A2A2A]',
    };

    // Order type badge
    $typeBadge = match ($order->order_type) {
        'pickup'   => ['label' => 'Para llevar', 'class' => 'bg-blue-900/50 text-blue-300'],
        'delivery' => ['label' => 'Delivery',    'class' => 'bg-purple-900/50 text-purple-300'],
        'dine_in'  => ['label' => 'Comer aquí',  'class' => 'bg-green-900/50 text-green-300'],
        default    => ['label' => $order->order_type, 'class' => 'bg-gray-800 text-gray-300'],
    };

    // Urgency: highlight orders pending > 10 min
    $isUrgent = ($column === 'pending' && $minutesAgo >= 10);
@endphp

<div
    wire:key="card-{{ $order->id }}"
    class="group relative rounded-xl border bg-[#2A2A2A] p-4 transition-all duration-200
           {{ $accentClass }} {{ $isUrgent ? 'ring-1 ring-red-500/50' : '' }}"
>
    {{-- Urgent indicator --}}
    @if ($isUrgent)
        <div class="absolute -top-1.5 left-4 flex items-center gap-1 rounded-full bg-red-700 px-2 py-0.5 text-[10px] font-bold text-white shadow">
            <span class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>
            URGENTE · {{ $minutesAgo }}min
        </div>
    @endif

    {{-- ── CARD HEADER ── --}}
    <div class="mb-3 flex items-start justify-between gap-2">
        <div>
            <span class="font-mono text-sm font-bold text-white tracking-wider">
                #{{ $order->order_number }}
            </span>
        </div>
        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $typeBadge['class'] }}">
            {{ $typeBadge['label'] }}
        </span>
    </div>

    {{-- ── CUSTOMER INFO ── --}}
    <div class="mb-3 space-y-1">
        <p class="text-sm font-semibold text-white leading-tight">
            {{ $order->customer_name }}
        </p>
        @if ($order->customer_phone)
            <a href="tel:{{ $order->customer_phone }}"
               class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-[#D4AF37] transition-colors">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
                {{ $order->customer_phone }}
            </a>
        @endif
    </div>

    {{-- ── ITEMS LIST ── --}}
    <div class="mb-3 rounded-lg bg-[#1A1A1A] p-3 space-y-1.5">
        @foreach ($order->items as $item)
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <span class="text-xs text-white leading-snug">{{ $item->name }}</span>
                    @if ($item->modifiers_text)
                        <p class="text-[10px] text-gray-500 leading-snug mt-0.5">{{ $item->modifiers_text }}</p>
                    @endif
                    @if ($item->special_instructions)
                        <p class="text-[10px] text-[#D4AF37]/70 leading-snug mt-0.5 italic">
                            "{{ $item->special_instructions }}"
                        </p>
                    @endif
                </div>
                <span class="flex-shrink-0 rounded bg-[#0B0B0B] px-1.5 py-0.5 text-xs font-bold text-gray-300">
                    ×{{ $item->quantity }}
                </span>
            </div>
        @endforeach

        @if ($order->special_instructions)
            <div class="mt-2 rounded border border-[#D4AF37]/20 bg-[#D4AF37]/5 px-2.5 py-1.5">
                <p class="text-[11px] text-[#D4AF37]/80 leading-snug">
                    <span class="font-semibold">Nota:</span> {{ $order->special_instructions }}
                </p>
            </div>
        @endif
    </div>

    {{-- ── TOTAL + TIME ── --}}
    <div class="mb-3 flex items-center justify-between">
        <span class="text-lg font-bold text-[#D4AF37]">
            ${{ number_format($order->total, 2) }}
        </span>
        <span class="text-xs text-gray-500 italic" title="{{ $order->created_at->format('d M Y, H:i') }}">
            {{ $timeLabel }}
        </span>
    </div>

    {{-- ── ACTION BUTTONS ── --}}
    @if (!in_array($column, ['completed', 'cancelled']))
        <div class="flex gap-2">

            {{-- Primary action button --}}
            @if ($column === 'pending')
                <button
                    wire:click="confirmOrder({{ $order->id }})"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="confirmOrder({{ $order->id }})"
                    class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white
                           transition hover:bg-blue-500 active:scale-95"
                >
                    <span wire:loading.remove wire:target="confirmOrder({{ $order->id }})">✓ Confirmar</span>
                    <span wire:loading wire:target="confirmOrder({{ $order->id }})">...</span>
                </button>

            @elseif ($column === 'confirmed')
                <button
                    wire:click="startPreparing({{ $order->id }})"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="startPreparing({{ $order->id }})"
                    class="flex-1 rounded-lg bg-orange-600 px-3 py-2 text-xs font-bold text-white
                           transition hover:bg-orange-500 active:scale-95"
                >
                    <span wire:loading.remove wire:target="startPreparing({{ $order->id }})">👨‍🍳 Preparar</span>
                    <span wire:loading wire:target="startPreparing({{ $order->id }})">...</span>
                </button>

            @elseif ($column === 'preparing')
                <button
                    wire:click="markReady({{ $order->id }})"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="markReady({{ $order->id }})"
                    class="flex-1 rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white
                           transition hover:bg-green-500 active:scale-95"
                >
                    <span wire:loading.remove wire:target="markReady({{ $order->id }})">🔔 Listo</span>
                    <span wire:loading wire:target="markReady({{ $order->id }})">...</span>
                </button>

            @elseif ($column === 'ready')
                <button
                    wire:click="markDelivered({{ $order->id }})"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="markDelivered({{ $order->id }})"
                    class="flex-1 rounded-lg bg-[#D4AF37] px-3 py-2 text-xs font-bold text-[#0B0B0B]
                           transition hover:bg-yellow-400 active:scale-95"
                >
                    <span wire:loading.remove wire:target="markDelivered({{ $order->id }})">✅ Entregado</span>
                    <span wire:loading wire:target="markDelivered({{ $order->id }})">...</span>
                </button>

            @elseif ($column === 'out_for_delivery')
                <button
                    wire:click="markDelivered({{ $order->id }})"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="markDelivered({{ $order->id }})"
                    class="flex-1 rounded-lg bg-[#D4AF37] px-3 py-2 text-xs font-bold text-[#0B0B0B]
                           transition hover:bg-yellow-400 active:scale-95"
                >
                    <span wire:loading.remove wire:target="markDelivered({{ $order->id }})">✅ Completar</span>
                    <span wire:loading wire:target="markDelivered({{ $order->id }})">...</span>
                </button>
            @endif

            {{-- Cancel button (only if cancellable) --}}
            @if ($order->canBeCancelled())
                <button
                    wire:click="openCancelModal({{ $order->id }})"
                    class="rounded-lg border border-red-800/40 bg-red-900/20 px-2.5 py-2 text-xs font-bold text-red-400
                           transition hover:bg-red-800/40 hover:text-red-300 active:scale-95"
                    title="Cancelar orden"
                >
                    ✕
                </button>
            @endif

        </div>
    @else
        {{-- Status badge for terminal states --}}
        <div class="text-center">
            @if ($column === 'completed')
                <span class="text-xs text-gray-500 italic">
                    Completado {{ $order->completed_at?->diffForHumans() ?? '' }}
                </span>
            @else
                <span class="text-xs text-red-500/70 italic">
                    Cancelado {{ $order->cancelled_at?->diffForHumans() ?? '' }}
                </span>
            @endif
        </div>
    @endif

</div>
