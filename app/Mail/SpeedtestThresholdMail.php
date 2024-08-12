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

class SpeedtestThresholdMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public Result $result,
        public array $metrics,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Speedtest Threshold Breached - #'.$this->result->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.speedtest-threshold',
            with: [
                'id' => $this->result->id,
                'service' => Str::title($this->result->service),
                'serverName' => $this->result->server_name,
                'serverId' => $this->result->server_id,
                'isp' => $this->result->isp,
                'speedtest_url' => $this->result->result_url,
                'url' => url('/admin/results'),
                'metrics' => $this->metrics,
            ],
        );
    }
}
