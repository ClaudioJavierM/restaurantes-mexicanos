<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        {{-- Logo / Header --}}
        <div class="text-center mb-8">
            <p class="text-3xl mb-2">🎁</p>
            <h1 class="text-2xl font-bold text-gray-900">Verificar Tarjeta de Regalo</h1>
            <p class="text-gray-500 text-sm mt-1">Consulta el saldo de tu tarjeta</p>
        </div>

        @if($card)
        {{-- Card Found --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-red-700 to-red-900 p-6 text-white text-center">
                <p class="text-xs uppercase tracking-widest opacity-75 mb-1">{{ $card->restaurant->name ?? 'Restaurante' }}</p>
                <p class="font-mono text-lg font-bold tracking-widest">{{ $card->code }}</p>
            </div>

            {{-- Balance --}}
            <div class="p-6 text-center border-b border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Saldo disponible</p>
                <p class="text-4xl font-bold {{ $card->balance > 0 ? 'text-green-600' : 'text-gray-400' }}">
                    ${{ number_format($card->balance, 2) }}
                </p>
                @if($card->initial_amount != $card->balance)
                <p class="text-xs text-gray-400 mt-1">Valor original: ${{ number_format($card->initial_amount, 2) }}</p>
                @endif
            </div>

            {{-- Details --}}
            <div class="p-6 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Estado</span>
                    <span class="font-semibold {{ match($card->status) {
                        'active' => 'text-green-600',
                        'used' => 'text-gray-500',
                        'expired' => 'text-yellow-600',
                        'cancelled' => 'text-red-600',
                        default => 'text-gray-600'
                    } }}">
                        {{ match($card->status) {
                            'active' => '✅ Activa',
                            'used' => '✓ Usada',
                            'expired' => '⏰ Vencida',
                            'cancelled' => '✗ Cancelada',
                            default => ucfirst($card->status)
                        } }}
                    </span>
                </div>

                @if($card->recipient_name)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Para</span>
                    <span class="font-medium text-gray-800">{{ $card->recipient_name }}</span>
                </div>
                @endif

                @if($card->message)
                <div class="bg-gray-50 rounded-xl p-3 text-sm text-gray-600 italic">
                    "{{ $card->message }}"
                </div>
                @endif

                @if($card->expires_at)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Vence</span>
                    <span class="font-medium {{ $card->expires_at->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $card->expires_at->format('d M, Y') }}
                    </span>
                </div>
                @endif
            </div>

            @if($card->balance > 0 && $card->status === 'active')
            <div class="px-6 pb-6">
                <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-center text-sm text-green-700">
                    🎉 ¡Esta tarjeta tiene saldo disponible! Preséntala en el restaurante.
                </div>
            </div>
            @endif
        </div>

        @else
        {{-- Not Found State --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="text-center mb-6">
                <p class="text-red-500 text-4xl mb-2">❌</p>
                <p class="font-semibold text-gray-800">Tarjeta no encontrada</p>
                <p class="text-sm text-gray-500 mt-1">El código <span class="font-mono font-bold">{{ $code }}</span> no existe en nuestro sistema.</p>
            </div>
        </div>
        @endif

        {{-- Back link --}}
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-400 hover:text-gray-600">← Volver al inicio</a>
        </div>

    </div>
</div>
