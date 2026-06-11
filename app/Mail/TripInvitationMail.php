<?php

namespace App\Mail;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TripInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Trip $trip, public string $invitedEmail)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join " . $this->trip->name . "!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "emails.trip-invitation",
        );
    }

    public function attachments(): array
    {
        return [];
    }
}