<?php

namespace App\Mail;

use App\Notifications\NotificationMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Sent synchronously from the queued SendChannelNotification job, so delivery
 * failures are recorded on the channel.
 */
class NotificationMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public NotificationMessage $notification,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->notification->title);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'notifications.mail',
            with: [
                'title' => $this->notification->title,
                'body' => $this->notification->body,
                'speedtestUrl' => $this->notification->speedtestUrl,
                'url' => $this->notification->url,
            ],
        );
    }
}
