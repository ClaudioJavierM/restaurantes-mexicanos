<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitacion de Equipo</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669 0%, #C9A84C 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px;">
                                Restaurantes Mexicanos Famosos
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">
                                Invitacion de Equipo
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Greeting -->
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Hola <strong>{{ $member->user->name }}</strong>,
                            </p>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 25px 0;">
                                Has sido invitado/a a unirte al equipo de <strong style="color: #dc2626;">{{ $member->restaurant->name }}</strong> en Restaurantes Mexicanos Famosos.
                            </p>

                            <!-- Invitation Details Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; margin: 0 0 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="color: #6b7280; font-size: 13px; text-transform: uppercase;">Restaurante</span><br>
                                                    <span style="color: #1f2937; font-size: 16px; font-weight: bold;">{{ $member->restaurant->name }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="color: #6b7280; font-size: 13px; text-transform: uppercase;">Tu Rol</span><br>
                                                    @php
                                                        $roleColors = [
                                                            'admin' => ['bg' => '#fee2e2', 'text' => '#dc2626'],
                                                            'manager' => ['bg' => '#fef3c7', 'text' => '#d97706'],
                                                            'editor' => ['bg' => '#dbeafe', 'text' => '#2563eb'],
                                                            'viewer' => ['bg' => '#f3f4f6', 'text' => '#4b5563'],
                                                        ];
                                                        $colors = $roleColors[$member->role] ?? $roleColors['viewer'];
                                                    @endphp
                                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; margin-top: 4px;">
                                                        {{ \App\Models\RestaurantTeamMember::getRoleLabel($member->role) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span style="color: #6b7280; font-size: 13px; text-transform: uppercase;">Invitado por</span><br>
                                                    <span style="color: #1f2937; font-size: 16px; font-weight: bold;">{{ $member->inviter?->name ?? 'El propietario' }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Role Description -->
                            @php
                                $roleDescriptions = [
                                    'admin' => 'Como administrador tendras acceso completo al restaurante, incluyendo la gestion del equipo y todas las configuraciones.',
                                    'manager' => 'Como gerente podras gestionar reservaciones, responder resenas, actualizar el menu y ver estadisticas.',
                                    'editor' => 'Como editor podras editar el menu, gestionar fotos y actualizar el contenido del restaurante.',
                                    'viewer' => 'Podras ver la informacion y estadisticas del restaurante.',
                                ];
                                $roleBgColors = [
                                    'admin' => '#fee2e2',
                                    'manager' => '#fef3c7',
                                    'editor' => '#dbeafe',
                                    'viewer' => '#f3f4f6',
                                ];
                                $roleTextColors = [
                                    'admin' => '#991b1b',
                                    'manager' => '#92400e',
                                    'editor' => '#1e40af',
                                    'viewer' => '#374151',
                                ];
                            @endphp
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: {{ $roleBgColors[$member->role] ?? '#f3f4f6' }}; border-radius: 8px; margin: 0 0 25px 0;">
                                <tr>
                                    <td style="padding: 15px 20px;">
                                        <p style="color: {{ $roleTextColors[$member->role] ?? '#374151' }}; margin: 0; font-size: 14px; line-height: 1.5;">
                                            {{ $roleDescriptions[$member->role] ?? $roleDescriptions['viewer'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/team/accept/{{ $member->invitation_token }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);">
                                            ACEPTAR INVITACION
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiration Notice -->
                            @if($member->invitation_expires_at)
                            <p style="color: #6b7280; font-size: 13px; text-align: center; margin: 0 0 20px 0;">
                                Esta invitacion expira el <strong>{{ $member->invitation_expires_at->format('d/m/Y') }}</strong>.
                            </p>
                            @endif

                            <!-- Info -->
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
                                <p style="color: #6b7280; font-size: 13px; line-height: 1.6; margin: 0;">
                                    Si no reconoces esta invitacion, puedes ignorar este email de manera segura.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #9ca3af; margin: 0 0 10px 0; font-size: 13px;">
                                Este es un mensaje automatico, por favor no responder.
                            </p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">
                                <a href="{{ config('app.url') }}" style="color: #10b981; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
