<?php

namespace App\Mail;

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkState;
use App\Enums\BenchmarkType;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BenchmarkAlarmMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Result $result,
        public BenchmarkState $state,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->state === BenchmarkState::Alarm
            ? 'Speedtest Benchmark Alarm - #'.$this->result->id
            : 'Speedtest Benchmark Recovered - #'.$this->result->id;

        return new Envelope(subject: $subject);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.benchmark.alarm',
            with: [
                'id' => $this->result->id,
                'service' => str($this->result->service->getLabel())->title(),
                'isp' => $this->result->isp,
                'url' => url('/admin/results'),
                'benchmarks' => $this->formatBenchmarks(),
                'recovered' => $this->state === BenchmarkState::Ok,
            ],
        );
    }

    /**
     * Format every configured benchmark on the result for display, so the
     * notification shows the full picture rather than only what changed.
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatBenchmarks(): array
    {
        return collect($this->result->benchmarks ?? [])
            ->map(fn (array $benchmark, string $metric): array => [
                'metric' => BenchmarkMetric::from($metric)->getLabel(),
                'type' => BenchmarkType::from($benchmark['type'])->getLabel(),
                'benchmark_value' => $benchmark['benchmark_value'].' '.$benchmark['unit'],
                'result_value' => $benchmark['test_value'].' '.$benchmark['unit'],
                'passed' => $benchmark['passed'],
            ])
            ->values()
            ->all();
    }
}
