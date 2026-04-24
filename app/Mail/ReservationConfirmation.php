<?php

namespace App\Mail;

use App\Models\EventReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventReservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmación de reserva — Little Claire');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.reservation-confirmation');
    }
}
