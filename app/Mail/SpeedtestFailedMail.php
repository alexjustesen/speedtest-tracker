<?php

namespace App\Mail;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SpeedtestFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Result $result,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Speedtest Failed - #'.$this->result->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.speedtest-failed',
            with: [
                'id' => $this->result->id,
                'service' => Str::title($this->result->service->getLabel()),
                'serverName' => $this->result->server_name,
                'serverId' => $this->result->server_id,
                'errorMessage' => $this->result->data['message'] ?? 'Unknown error during speedtest.',
                'url' => url('/admin/results'),
            ],
        );
    }
}
