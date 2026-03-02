<div class="space-y-4">
    {{-- Photo --}}
    <div class="flex justify-center">
        <img
            src="{{ $photo->getPhotoUrl() }}"
            alt="{{ $photo->caption ?: 'Foto del restaurante' }}"
            class="max-w-full max-h-96 rounded-lg shadow-lg"
        >
    </div>

    {{-- Info Grid --}}
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-semibold text-gray-700">Restaurante:</span>
            <span class="text-gray-600">{{ $photo->restaurant->name }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Usuario:</span>
            <span class="text-gray-600">{{ $photo->user?->name ?? 'Anonimo' }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Tipo:</span>
            <span class="text-gray-600">{{ $photo->getPhotoTypeLabel() }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Estado:</span>
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ match($photo->status) {
                'pending' => 'bg-yellow-100 text-yellow-800',
                'approved' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
            } }}">
                {{ match($photo->status) {
                    'pending' => 'Pendiente',
                    'approved' => 'Aprobada',
                    'rejected' => 'Rechazada',
                    default => $photo->status,
                } }}
            </span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Likes:</span>
            <span class="text-gray-600">{{ $photo->likes_count }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Vistas:</span>
            <span class="text-gray-600">{{ $photo->views_count }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Fecha:</span>
            <span class="text-gray-600">{{ $photo->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-700">Tamano:</span>
            <span class="text-gray-600">{{ $photo->getFileSizeFormatted() }}</span>
        </div>
    </div>

    @if($photo->caption)
    <div>
        <span class="font-semibold text-gray-700">Descripcion:</span>
        <p class="text-gray-600 mt-1">{{ $photo->caption }}</p>
    </div>
    @endif

    @if($photo->rejection_reason)
    <div class="p-3 bg-red-50 rounded-lg">
        <span class="font-semibold text-red-700">Razon de rechazo:</span>
        <p class="text-red-600 mt-1">{{ $photo->rejection_reason }}</p>
    </div>
    @endif

    @if($photo->reports_count > 0)
    <div class="p-3 bg-yellow-50 rounded-lg">
        <span class="font-semibold text-yellow-700">Reportes:</span>
        <span class="text-yellow-600">{{ $photo->reports_count }} reporte(s)</span>
    </div>
    @endif
</div>
