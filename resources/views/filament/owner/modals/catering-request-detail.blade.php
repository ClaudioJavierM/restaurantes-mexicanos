<div style="display: flex; flex-direction: column; gap: 1rem; padding: 0.5rem 0;">

    {{-- Contacto --}}
    <div style="background-color: #1f2937; border-radius: 0.5rem; padding: 1rem; border: 1px solid #374151;">
        <h4 style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0 0 0.75rem; font-weight: 600;">Datos de Contacto</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Nombre</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0.125rem 0 0;">{{ $request->contact_name }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Email</p>
                <a href="mailto:{{ $request->contact_email }}" style="color: #818cf8; margin: 0.125rem 0 0; display: block;">{{ $request->contact_email }}</a>
            </div>
            @if($request->contact_phone)
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Teléfono</p>
                <a href="tel:{{ $request->contact_phone }}" style="color: #818cf8; margin: 0.125rem 0 0; display: block;">{{ $request->contact_phone }}</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Evento --}}
    <div style="background-color: #1f2937; border-radius: 0.5rem; padding: 1rem; border: 1px solid #374151;">
        <h4 style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0 0 0.75rem; font-weight: 600;">Detalles del Evento</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Tipo</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0.125rem 0 0;">{{ $request->event_type_label }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Fecha</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0.125rem 0 0;">{{ $request->event_date->format('d M, Y') }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Invitados</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0.125rem 0 0;">{{ number_format($request->guest_count) }} personas</p>
            </div>
            @if($request->budget)
            <div>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Presupuesto</p>
                <p style="color: #22c55e; font-weight: 600; margin: 0.125rem 0 0;">${{ number_format($request->budget, 2) }} USD</p>
            </div>
            @endif
            @if($request->event_location)
            <div style="grid-column: 1 / -1;">
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0;">Ubicación del Evento</p>
                <p style="color: #ffffff; font-weight: 500; margin: 0.125rem 0 0;">{{ $request->event_location }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Notas --}}
    @if($request->notes)
    <div style="background-color: #1f2937; border-radius: 0.5rem; padding: 1rem; border: 1px solid #374151;">
        <h4 style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; margin: 0 0 0.5rem; font-weight: 600;">Notas del Cliente</h4>
        <p style="color: #d1d5db; font-size: 0.875rem; margin: 0; line-height: 1.6;">{{ $request->notes }}</p>
    </div>
    @endif

    {{-- Cotizacion enviada --}}
    @if($request->quote_amount)
    <div style="background: linear-gradient(135deg, #14532d, #166534); border-radius: 0.5rem; padding: 1rem; border: 1px solid #16a34a;">
        <h4 style="font-size: 0.75rem; color: #86efac; text-transform: uppercase; margin: 0 0 0.5rem; font-weight: 600;">Cotización Enviada</h4>
        <p style="color: #ffffff; font-size: 1.5rem; font-weight: bold; margin: 0 0 0.5rem;">${{ number_format($request->quote_amount, 2) }} USD</p>
        @if($request->owner_notes)
        <p style="color: #d1d5db; font-size: 0.875rem; margin: 0;">{{ $request->owner_notes }}</p>
        @endif
    </div>
    @endif

</div>
