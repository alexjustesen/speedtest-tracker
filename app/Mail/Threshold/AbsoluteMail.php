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
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Speedtest Result #'.$this->result->id.' - Absolute threshold failed',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
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
