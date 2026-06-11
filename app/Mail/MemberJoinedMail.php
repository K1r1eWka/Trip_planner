<?php

namespace App\Mail;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberJoinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Trip $trip, public User $newMember)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newMember->name . " joined your trip " . $this->trip->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "emails.member-joined",
        );
    }

    public function attachments(): array
    {
        return [];
    }
}