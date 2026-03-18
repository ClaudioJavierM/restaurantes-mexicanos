<x-filament-panels::page>
@if(!$restaurant)
<p style="color:#9ca3af">Sin restaurante asociado.</p>
@else
<div style="display:flex;flex-direction:column;gap:1.5rem;" wire:poll.30s="loadActiveOrders">

    {{-- Órdenes activas --}}
    @if(count($activeOrders) > 0)
    <div style="background-color:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;background:linear-gradient(135deg,#7c2d12,#111827);display:flex;align-items:center;gap:0.75rem;">
            <span style="font-size:1.25rem;">🔔</span>
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">Órdenes Activas ({{ count($activeOrders) }})</h3>
        </div>
        <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;">
            @foreach($activeOrders as $order)
            @php
                $sc = match($order['status']) {
                    'pending'   => ['bg'=>'#7c2d12','text'=>'#fca5a5','label'=>'🆕 Nuevo'],
                    'confirmed' => ['bg'=>'#1e3a5f','text'=>'#93c5fd','label'=>'✅ Confirmado'],
                    'preparing' => ['bg'=>'#713f12','text'=>'#fde68a','label'=>'👨‍🍳 Preparando'],
                    default     => ['bg'=>'#1f2937','text'=>'#9ca3af','label'=>ucfirst($order['status'])],
                };
            @endphp
            <div style="background-color:#1f2937;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;">
                    <div>
                        <p style="font-size:0.75rem;color:#9ca3af;margin:0;">{{ $order['table']['name'] ?? 'Mesa' }}</p>
                        <p style="font-size:0.875rem;font-weight:600;color:#fff;margin:0.125rem 0 0;">{{ $order['order_number'] }}</p>
                    </div>
                    <span style="font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:9999px;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">{{ $sc['label'] }}</span>
                </div>
                <div style="margin-bottom:0.75rem;">
                    @foreach(($order['items'] ?? []) as $item)
                    <p style="font-size:0.8rem;color:#d1d5db;margin:0.125rem 0;">{{ $item['quantity'] }}× {{ $item['name'] }} <span style="color:#9ca3af;">${{ number_format($item['price'] * $item['quantity'], 2) }}</span></p>
                    @endforeach
                    @if($order['notes'])
                    <p style="font-size:0.75rem;color:#fbbf24;margin-top:0.375rem;">📝 {{ $order['notes'] }}</p>
                    @endif
                    <p style="font-size:0.75rem;color:#9ca3af;font-weight:600;margin-top:0.375rem;">Total: ${{ number_format($order['subtotal'], 2) }}</p>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    @if($order['status'] === 'pending')
                    <button wire:click="updateOrderStatus({{ $order['id'] }}, 'confirmed')" style="background:#1e3a5f;color:#93c5fd;border:1px solid #1e40af;padding:0.25rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">✅ Confirmar</button>
                    @endif
                    @if(in_array($order['status'], ['pending','confirmed']))
                    <button wire:click="updateOrderStatus({{ $order['id'] }}, 'preparing')" style="background:#713f12;color:#fde68a;border:1px solid #92400e;padding:0.25rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">👨‍🍳 Preparando</button>
                    @endif
                    <button wire:click="updateOrderStatus({{ $order['id'] }}, 'ready')" style="background:#064e3b;color:#34d399;border:1px solid #047857;padding:0.25rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">🔔 Listo</button>
                    <button wire:click="updateOrderStatus({{ $order['id'] }}, 'delivered')" style="background:#374151;color:#9ca3af;border:1px solid #4b5563;padding:0.25rem 0.75rem;border-radius:0.375rem;font-size:0.75rem;cursor:pointer;">✓ Entregado</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Agregar mesa --}}
    <div style="background-color:#1f2937;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;">
        <h3 style="font-size:0.9rem;font-weight:600;color:#fff;margin:0 0 1rem;">➕ Agregar Mesa</h3>
        @error('newTableName') <p style="color:#f87171;font-size:0.75rem;margin-bottom:0.5rem;">{{ $message }}</p> @enderror
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Nombre / Número</label>
                <input wire:model="newTableName" type="text" placeholder="Mesa 1, Terraza 2, Barra..."
                    style="background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem 0.75rem;color:#fff;font-size:0.875rem;width:180px;">
            </div>
            <div>
                <label style="font-size:0.75rem;color:#9ca3af;display:block;margin-bottom:0.25rem;">Capacidad (personas)</label>
                <input wire:model="newTableCapacity" type="number" min="1" max="30"
                    style="background:#111827;border:1px solid #374151;border-radius:0.375rem;padding:0.5rem 0.75rem;color:#fff;font-size:0.875rem;width:80px;">
            </div>
            <button wire:click="addTable"
                style="background:linear-gradient(135deg,#dc2626,#991b1b);color:white;padding:0.5rem 1.25rem;border-radius:0.5rem;border:none;font-size:0.875rem;font-weight:600;cursor:pointer;">
                Agregar Mesa
            </button>
        </div>
    </div>

    {{-- Lista de mesas --}}
    @if(count($tables) > 0)
    <div style="background-color:#111827;border-radius:0.75rem;border:1px solid #374151;overflow:hidden;">
        <div style="padding:1.25rem;border-bottom:1px solid #374151;">
            <h3 style="font-size:1rem;font-weight:600;color:#fff;margin:0;">Mis Mesas ({{ count($tables) }})</h3>
            <p style="font-size:0.8rem;color:#9ca3af;margin:0.25rem 0 0;">Clientes escanean el QR para ver el menú y hacer pedidos desde su lugar</p>
        </div>
        <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;">
            @foreach($tables as $table)
            @php $url = url('/mesa/' . $restaurant->slug . '/' . $table['table_code']); @endphp
            <div style="background-color:#1f2937;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;text-align:center;">
                <p style="font-size:1rem;font-weight:700;color:#fff;margin:0 0 0.75rem;">{{ $table['name'] }}</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($url) }}"
                    alt="QR" style="width:120px;height:120px;border-radius:0.5rem;background:#fff;padding:4px;margin:0 auto 0.5rem;display:block;">
                <p style="font-size:0.7rem;color:#9ca3af;margin:0 0 0.75rem;">{{ $table['capacity'] }} personas</p>
                <div style="display:flex;gap:0.5rem;justify-content:center;">
                    <a href="{{ $url }}" target="_blank"
                        style="font-size:0.75rem;color:#818cf8;border:1px solid #4f46e5;padding:0.3rem 0.75rem;border-radius:0.375rem;text-decoration:none;">
                        Vista previa
                    </a>
                    <button wire:click="deleteTable({{ $table['id'] }})"
                        wire:confirm="¿Eliminar {{ $table['name'] }}?"
                        style="font-size:0.75rem;color:#f87171;border:1px solid #7f1d1d;padding:0.3rem 0.75rem;border-radius:0.375rem;background:transparent;cursor:pointer;">
                        Eliminar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div style="background-color:#1f2937;border-radius:0.75rem;padding:3rem;text-align:center;border:1px solid #374151;">
        <p style="font-size:2.5rem;margin:0 0 0.5rem;">📱</p>
        <p style="color:#fff;font-weight:600;margin:0 0 0.5rem;">Sin mesas configuradas</p>
        <p style="color:#9ca3af;font-size:0.875rem;margin:0;">Agrega mesas arriba y genera QR codes que los clientes pueden escanear</p>
    </div>
    @endif

</div>
@endif
</x-filament-panels::page>
