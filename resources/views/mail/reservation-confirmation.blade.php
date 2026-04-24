<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de reserva</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #7c3a2d; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 8px 0 0; opacity: .85; font-size: 14px; }
        .body { padding: 32px; }
        .greeting { font-size: 18px; margin-bottom: 16px; }
        .summary { background: #fdf8f6; border: 1px solid #f0e0d8; border-radius: 6px; padding: 20px 24px; margin: 24px 0; }
        .summary h2 { margin: 0 0 16px; font-size: 14px; text-transform: uppercase; letter-spacing: .5px; color: #7c3a2d; }
        .field { margin-bottom: 12px; display: flex; gap: 8px; }
        .label { font-size: 13px; color: #888; min-width: 160px; }
        .value { font-size: 13px; color: #222; font-weight: 600; }
        .note { font-size: 14px; color: #555; line-height: 1.6; margin-top: 24px; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .footer { padding: 16px 32px; background: #fafafa; font-size: 12px; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Little Claire Bakery</h1>
            <p>Confirmación de reserva</p>
        </div>
        <div class="body">
            <p class="greeting">Hola, {{ $reservation->name }}!</p>

            <p>Hemos recibido tu solicitud de reserva. Nos pondremos en contacto contigo a la brevedad para confirmar los detalles.</p>

            <div class="summary">
                <h2>Resumen de tu reserva</h2>

                <div class="field">
                    <span class="label">Fecha</span>
                    <span class="value">{{ $reservation->event_date?->format('d/m/Y') }}</span>
                </div>

                <div class="field">
                    <span class="label">Hora</span>
                    <span class="value">{{ $reservation->event_time }}</span>
                </div>

                <div class="field">
                    <span class="label">Cantidad de personas</span>
                    <span class="value">{{ $reservation->guests_count }}</span>
                </div>

                <div class="field">
                    <span class="label">Tipo de evento</span>
                    <span class="value">{{ $reservation->event_type?->value }}</span>
                </div>

                @if($reservation->notes)
                <div class="field">
                    <span class="label">Notas</span>
                    <span class="value">{{ $reservation->notes }}</span>
                </div>
                @endif
            </div>

            <p class="note">
                Si tienes alguna pregunta o necesitas modificar tu reserva, puedes responder directamente a este correo o contactarnos por teléfono.
            </p>

            <hr class="divider">

            <p class="note">
                Gracias por elegirnos. ¡Esperamos hacer de tu evento algo especial!
            </p>
        </div>
        <div class="footer">
            Little Claire Bakery
        </div>
    </div>
</body>
</html>
