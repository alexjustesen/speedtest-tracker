<?php

namespace App\Mail\Threshold;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbsoluteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $result;

    public $metrics;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Result $result, array $metrics)
    {
        $this->result = $result;

        $this->metrics = $metrics;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Speedtest Result #'.$this->result->id.' - Absolute threshold failed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.threshold.absolute',
            with: [
                'id' => $this->result->id,
                'url' => url('/admin/results'),
                'metrics' => $this->metrics,
            ],
        );
    }
}
