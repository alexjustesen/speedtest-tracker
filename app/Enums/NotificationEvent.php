<?php

namespace App\Enums;

use App\Events\SpeedtestBenchmarkHealthy;
use App\Events\SpeedtestBenchmarking;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestChecking;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestRunning;
use App\Events\SpeedtestSkipped;
use App\Events\SpeedtestStarted;
use App\Events\SpeedtestWaiting;
use Filament\Support\Contracts\HasLabel;

enum WebhookEvent: string implements HasLabel
{
    case Started = 'speedtest_started';
    case Waiting = 'speedtest_waiting';
    case Checking = 'speedtest_checking';
    case Running = 'speedtest_running';
    case Benchmarking = 'speedtest_benchmarking';
    case Completed = 'speedtest_completed';
    case Failed = 'speedtest_failed';
    case Skipped = 'speedtest_skipped';
    case BenchmarkHealthy = 'speedtest_benchmark_healthy';
    case BenchmarkUnhealthy = 'speedtest_benchmark_unhealthy';

    /**
     * Get the events grouped for a Select component.
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        return [
            __('webhooks.event_groups.speedtest') => self::optionsFor([
                self::Waiting,
                self::Started,
                self::Checking,
                self::Running,
                self::Benchmarking,
                self::Completed,
                self::Skipped,
                self::Failed,
            ]),
            __('webhooks.event_groups.benchmarks') => self::optionsFor([
                self::BenchmarkHealthy,
                self::BenchmarkUnhealthy,
            ]),
        ];
    }

    /**
     * Build a value => label map for the given cases.
     *
     * @param  array<int, self>  $cases
     * @return array<string, string>
     */
    private static function optionsFor(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->getLabel()])
            ->all();
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Started => __('enums.webhook_event.started'),
            self::Waiting => __('enums.webhook_event.waiting'),
            self::Checking => __('enums.webhook_event.checking'),
            self::Running => __('enums.webhook_event.running'),
            self::Benchmarking => __('enums.webhook_event.benchmarking'),
            self::Completed => __('enums.webhook_event.completed'),
            self::Failed => __('enums.webhook_event.failed'),
            self::Skipped => __('enums.webhook_event.skipped'),
            self::BenchmarkHealthy => __('enums.webhook_event.benchmark_healthy'),
            self::BenchmarkUnhealthy => __('enums.webhook_event.benchmark_unhealthy'),
        };
    }

    /**
     * Get the fully qualified event class this webhook event maps to.
     *
     * @return class-string
     */
    public function eventClass(): string
    {
        return match ($this) {
            self::Started => SpeedtestStarted::class,
            self::Waiting => SpeedtestWaiting::class,
            self::Checking => SpeedtestChecking::class,
            self::Running => SpeedtestRunning::class,
            self::Benchmarking => SpeedtestBenchmarking::class,
            self::Completed => SpeedtestCompleted::class,
            self::Failed => SpeedtestFailed::class,
            self::Skipped => SpeedtestSkipped::class,
            self::BenchmarkHealthy => SpeedtestBenchmarkHealthy::class,
            self::BenchmarkUnhealthy => SpeedtestBenchmarkUnhealthy::class,
        };
    }

    /**
     * Resolve the webhook event for a given event class, if any.
     *
     * @param  class-string  $class
     */
    public static function fromEventClass(string $class): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->eventClass() === $class) {
                return $case;
            }
        }

        return null;
    }
}
