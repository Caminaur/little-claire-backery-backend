<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reserva de evento</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f5f5f5; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #7c3a2d; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .field { margin-bottom: 16px; }
        .label { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #888; margin-bottom: 4px; }
        .value { font-size: 16px; color: #222; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .footer { padding: 16px 32px; background: #fafafa; font-size: 12px; color: #aaa; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Nueva reserva de evento</h1>
        </div>
        <div class="body">
            <p>Se ha recibido una nueva solicitud de reserva a través del sitio web.</p>

            <hr class="divider">

            <div class="field">
                <div class="label">Nombre</div>
                <div class="value">{{ $reservation->name }}</div>
            </div>

            <div class="field">
                <div class="label">Email</div>
                <div class="value">{{ $reservation->email }}</div>
            </div>

            <div class="field">
                <div class="label">Teléfono</div>
                <div class="value">{{ $reservation->phone }}</div>
            </div>

            <hr class="divider">

            <div class="field">
                <div class="label">Fecha del evento</div>
                <div class="value">{{ $reservation->event_date?->format('d/m/Y') }}</div>
            </div>

            <div class="field">
                <div class="label">Hora del evento</div>
                <div class="value">{{ $reservation->event_time }}</div>
            </div>

            <div class="field">
                <div class="label">Cantidad de personas</div>
                <div class="value">{{ $reservation->guests_count }}</div>
            </div>

            <div class="field">
                <div class="label">Tipo de evento</div>
                <div class="value">{{ $reservation->event_type?->value }}</div>
            </div>

            @if($reservation->notes)
            <hr class="divider">
            <div class="field">
                <div class="label">Notas</div>
                <div class="value">{{ $reservation->notes }}</div>
            </div>
            @endif
        </div>
        <div class="footer">
            Little Claire Bakery &mdash; Panel de administración
        </div>
    </div>
</body>
</html>
