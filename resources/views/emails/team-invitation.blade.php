<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .btn { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .role-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .role-owner { background: #fee2e2; color: #dc2626; }
        .role-manager { background: #fef3c7; color: #d97706; }
        .role-staff { background: #dbeafe; color: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Invitacion de Equipo</h1>
        </div>
        <div class="content">
            <p>Hola {{ $member->user->name }},</p>

            <p>Has sido invitado/a a unirte al equipo de <strong>{{ $member->restaurant->name }}</strong> en Restaurantes Mexicanos Famosos.</p>

            <div class="details">
                <div class="label">Restaurante</div>
                <div class="value">{{ $member->restaurant->name }}</div>

                <div class="label">Tu Rol</div>
                <div class="value">
                    <span class="role-badge role-{{ $member->role }}">
                        {{ $member->getRoleLabel() }}
                    </span>
                </div>

                <div class="label">Invitado por</div>
                <div class="value">{{ $member->inviter?->name ?? 'El propietario' }}</div>

                @if($member->role === 'owner')
                <p style="margin-top: 15px; padding: 10px; background: #fee2e2; border-radius: 6px; font-size: 14px;">
                    <strong>Nota:</strong> Como propietario tendras acceso completo al restaurante, incluyendo la gestion del equipo.
                </p>
                @elseif($member->role === 'manager')
                <p style="margin-top: 15px; padding: 10px; background: #fef3c7; border-radius: 6px; font-size: 14px;">
                    Como gerente podras gestionar reservaciones, responder resenas, actualizar el menu y ver estadisticas.
                </p>
                @else
                <p style="margin-top: 15px; padding: 10px; background: #dbeafe; border-radius: 6px; font-size: 14px;">
                    Como staff podras ver y gestionar las reservaciones del dia.
                </p>
                @endif
            </div>

            <p style="text-align: center;">
                <a href="{{ config('app.url') }}/team/accept/{{ $member->invitation_token }}" class="btn">
                    Aceptar Invitacion
                </a>
            </p>

            <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
                Esta invitacion expira el {{ $member->invitation_expires_at->format('d/m/Y') }}.
                Si no reconoces esta invitacion, puedes ignorar este email.
            </p>
        </div>
        <div class="footer">
            <p>Restaurantes Mexicanos Famosos</p>
            <p style="font-size: 11px;">Este es un mensaje automatico, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
