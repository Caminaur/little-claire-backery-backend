<?php

namespace App\Services;

use App\Models\EventReservation;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    // Phase 2 — not called yet.
    public function sendReservationAlert(EventReservation $reservation): void
    {
        $url = 'https://graph.facebook.com/v19.0/'
             . config('services.whatsapp.phone_number_id')
             . '/messages';

        Http::withToken(config('services.whatsapp.token'))
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to'                => config('services.whatsapp.to_number'),
                'type'              => 'text',
                'text'              => [
                    'body' => "🎉 Nueva reserva de evento\n\n"
                            . "Nombre: {$reservation->name}\n"
                            . "Email: {$reservation->email}\n"
                            . "Tel: {$reservation->phone}\n"
                            . "Fecha: {$reservation->event_date->format('d/m/Y')}\n"
                            . "Hora: {$reservation->event_time}\n"
                            . "Invitados: {$reservation->guests_count}\n"
                            . "Tipo: {$reservation->event_type->value}\n"
                            . ($reservation->notes ? "Notas: {$reservation->notes}" : ''),
                ],
            ]);
    }
}
