<?php

namespace App\Mail;

use App\Models\EventReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReservationAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventReservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nueva reserva de evento — Little Claire');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.reservation-admin');
    }
}
