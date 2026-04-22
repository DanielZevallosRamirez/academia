<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo electronico</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%); padding: 40px 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 28px; font-weight: bold; margin: 0;">Academia</h1>
                            <p style="color: rgba(255, 255, 255, 0.9); font-size: 14px; margin: 8px 0 0;">Sistema de Gestion Academica</p>
                        </td>
                    </tr>
                    
                    {{-- Content --}}
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1e293b; font-size: 24px; font-weight: 600; margin: 0 0 16px;">Hola, {{ $user->name }}!</h2>
                            
                            <p style="color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 24px;">
                                Hemos recibido una solicitud para verificar tu direccion de correo electronico. Haz clic en el boton de abajo para confirmar que esta cuenta te pertenece.
                            </p>

                            {{-- Button --}}
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="text-align: center; padding: 20px 0;">
                                        <a href="{{ $verificationUrl }}" style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; padding: 16px 40px; border-radius: 12px; box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.4);">
                                            Verificar mi correo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 24px 0 0;">
                                Este enlace expirara en <strong>60 minutos</strong>. Si no solicitaste esta verificacion, puedes ignorar este correo.
                            </p>

                            {{-- Divider --}}
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 32px 0;">

                            <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0;">
                                Si el boton no funciona, copia y pega el siguiente enlace en tu navegador:
                            </p>
                            <p style="color: #10b981; font-size: 13px; word-break: break-all; margin: 8px 0 0;">
                                {{ $verificationUrl }}
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 13px; margin: 0;">
                                &copy; {{ date('Y') }} Academia. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
