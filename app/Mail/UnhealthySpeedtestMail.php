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

class UnhealthySpeedtestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
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
            subject: 'Speedtest Threshold Breached - #'.$this->result->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $benchmarks = [];

        foreach ($this->result->benchmarks as $metric => $benchmark) {
            $benchmarks[] = $this->formatBenchmark($metric, $benchmark);
        }

        return new Content(
            markdown: 'mail.speedtest.unhealthy',
            with: [
                'id' => $this->result->id,
                'service' => str($this->result->service->getLabel())->title(),
                'isp' => $this->result->isp,
                'url' => url('/admin/results'),
                'benchmarks' => $benchmarks,
            ],
        );
    }

    /**
     * Format a benchmark for display in the email.
     */
    private function formatBenchmark(string $metric, array $benchmark): array
    {
        $metricName = str($metric)->title();
        $type = str($benchmark['type'])->title();
        $thresholdValue = $benchmark['benchmark_value'].' '.str($benchmark['unit'])->title();

        // Get the actual result value
        $resultValue = match ($metric) {
            'download' => Number::toBitRate($this->result->download_bits, 2),
            'upload' => Number::toBitRate($this->result->upload_bits, 2),
            'ping' => round(Number::castToType($this->result->ping, 'float'), 2).' ms',
            default => 'N/A',
        };

        return [
            'metric' => $metricName,
            'type' => $type,
            'threshold_value' => $thresholdValue,
            'result_value' => $resultValue,
            'passed' => $benchmark['passed'],
        ];
    }
}
