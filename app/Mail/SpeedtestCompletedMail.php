<?php

namespace App\Mail;

use App\Helpers\Number;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SpeedtestCompletedMail extends Mailable implements ShouldQueue
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
            subject: 'Speedtest Completed - #'.$this->result->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.speedtest-completed',
            with: [
                'id' => $this->result->id,
                'service' => Str::title($this->result->service),
                'serverName' => $this->result->server_name,
                'serverId' => $this->result->server_id,
                'isp' => $this->result->isp,
                'ping' => round($this->result->ping, 2).' ms',
                'download' => Number::toBitRate(bits: $this->result->download_bits, precision: 2),
                'upload' => Number::toBitRate(bits: $this->result->upload_bits, precision: 2),
                'packetLoss' => is_numeric($this->result->packet_loss) ? $this->result->packet_loss : 'n/a',
                'speedtest_url' => $this->result->result_url,
                'url' => url('/admin/results'),
            ],
        );
    }
}
