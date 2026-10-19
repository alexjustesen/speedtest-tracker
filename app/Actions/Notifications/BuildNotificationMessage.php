<?php

namespace App\Actions\Notifications;

use App\Enums\BenchmarkMetric;
use App\Enums\BenchmarkType;
use App\Enums\NotificationEvent;
use App\Helpers\Number;
use App\Models\Result;
use App\Notifications\NotificationMessage;
use App\Services\SpeedtestFakeResultGenerator;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class BuildNotificationMessage
{
    use AsAction;

    /**
     * Build the notification message for a result and event.
     */
    public function handle(Result $result, NotificationEvent $event, bool $test = false): NotificationMessage
    {
        $title = __('notification_channels.titles.'.$this->titleKey($event), [
            'id' => $result->id ?? '0',
        ]);

        return new NotificationMessage(
            event: $event,
            result: $result,
            title: $test ? __('notification_channels.test_title_prefix').' '.$title : $title,
            body: view($this->view($event), $this->viewData($result, $event))->render(),
            level: $event->level(),
            url: url('/admin/results'),
            speedtestUrl: $result->result_url,
            test: $test,
        );
    }

    /**
     * Build a test message from a fake (unsaved) completed result.
     */
    public static function test(): NotificationMessage
    {
        return static::run(SpeedtestFakeResultGenerator::completed(), NotificationEvent::Completed, test: true);
    }

    /**
     * Get the translation key suffix for the message title.
     */
    private function titleKey(NotificationEvent $event): string
    {
        return match ($event) {
            NotificationEvent::Completed => 'completed',
            NotificationEvent::Failed => 'failed',
            NotificationEvent::Recovered => 'recovered',
            NotificationEvent::BenchmarkUnhealthy => 'benchmark_unhealthy',
            NotificationEvent::BenchmarkRecovered => 'benchmark_recovered',
        };
    }

    /**
     * Get the markdown view used to render the message body.
     */
    private function view(NotificationEvent $event): string
    {
        return match ($event) {
            NotificationEvent::Completed => 'notifications.completed',
            NotificationEvent::Failed => 'notifications.failed',
            NotificationEvent::Recovered => 'notifications.recovered',
            NotificationEvent::BenchmarkUnhealthy => 'notifications.benchmark-unhealthy',
            NotificationEvent::BenchmarkRecovered => 'notifications.benchmark-recovered',
        };
    }

    /**
     * Get the data shared by every message view.
     *
     * @return array<string, mixed>
     */
    private function viewData(Result $result, NotificationEvent $event): array
    {
        return [
            'event' => $event,
            'id' => $result->id,
            'service' => Str::title($result->service?->getLabel() ?? ''),
            'serverName' => $result->server_name,
            'serverId' => $result->server_id,
            'isp' => $result->isp,
            'ping' => $result->ping !== null ? round($result->ping).' ms' : null,
            'download' => $result->download_bits !== null ? Number::toBitRate(bits: $result->download_bits, precision: 2) : null,
            'upload' => $result->upload_bits !== null ? Number::toBitRate(bits: $result->upload_bits, precision: 2) : null,
            'packetLoss' => filled($result->packet_loss) ? round($result->packet_loss, precision: 2) : null,
            'errorMessage' => $result->error_message,
            'benchmarks' => $this->formatBenchmarks($result),
        ];
    }

    /**
     * Format every benchmark recorded on the result for display.
     *
     * @return array<int, array<string, mixed>>
     */
    private function formatBenchmarks(Result $result): array
    {
        return collect($result->benchmarks ?? [])
            ->map(fn (array $benchmark, string $metric): array => [
                'name' => BenchmarkMetric::tryFrom($metric)?->getLabel() ?? Str::headline($metric),
                'type' => BenchmarkType::tryFrom($benchmark['type'] ?? '')?->getLabel(),
                'benchmark' => $benchmark['benchmark_value'].' '.$benchmark['unit'],
                'value' => $benchmark['test_value'].' '.$benchmark['unit'],
                'passed' => (bool) $benchmark['passed'],
            ])
            ->values()
            ->all();
    }
}
