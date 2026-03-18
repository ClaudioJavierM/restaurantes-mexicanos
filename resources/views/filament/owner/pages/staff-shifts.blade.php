<x-filament-panels::page>
    <div style="background:#1f2937;border-radius:0.75rem;padding:1.25rem;border:1px solid #374151;margin-bottom:1.5rem;">
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;">
            <div>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Empleado</p>
                <p style="font-size:1rem;font-weight:700;color:#fff;margin:0.25rem 0 0;">{{ $this->staffMember->name }}</p>
            </div>
            <div>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Puesto</p>
                <p style="font-size:0.875rem;color:#d1d5db;margin:0.25rem 0 0;">{{ $this->staffMember->role_label }}</p>
            </div>
            @if($this->staffMember->hourly_rate)
            <div>
                <p style="font-size:0.75rem;color:#9ca3af;margin:0;">Tarifa</p>
                <p style="font-size:0.875rem;color:#d1d5db;margin:0.25rem 0 0;">${{ number_format($this->staffMember->hourly_rate, 2) }}/hr</p>
            </div>
            @endif
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
