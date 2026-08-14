<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $subjectLine,
        public string $heading,
        public string $messageContent,
        public array $details = []
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine . ' — MediQueue',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.queue-notification',
        );
    }
}
