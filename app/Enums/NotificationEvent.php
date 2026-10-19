<?php

namespace App\Enums;

use App\Events\SpeedtestBenchmarkRecovered;
use App\Events\SpeedtestBenchmarkUnhealthy;
use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestRecovered;
use Filament\Support\Contracts\HasLabel;

enum NotificationEvent: string implements HasLabel
{
    case Completed = 'speedtest_completed';
    case Failed = 'speedtest_failed';
    case Recovered = 'speedtest_recovered';
    case BenchmarkUnhealthy = 'speedtest_benchmark_unhealthy';
    case BenchmarkRecovered = 'speedtest_benchmark_recovered';

    /**
     * Get the events grouped for a Select component.
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        return [
            __('notification_channels.event_groups.speedtest') => self::optionsFor([
                self::Completed,
                self::Failed,
                self::Recovered,
            ]),
            __('notification_channels.event_groups.benchmarks') => self::optionsFor([
                self::BenchmarkUnhealthy,
                self::BenchmarkRecovered,
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
            self::Completed => __('enums.notification_event.completed'),
            self::Failed => __('enums.notification_event.failed'),
            self::Recovered => __('enums.notification_event.recovered'),
            self::BenchmarkUnhealthy => __('enums.notification_event.benchmark_unhealthy'),
            self::BenchmarkRecovered => __('enums.notification_event.benchmark_recovered'),
        };
    }

    /**
     * Determine if notifications for this event carry benchmark results.
     */
    public function includesBenchmarks(): bool
    {
        return in_array($this, [
            self::BenchmarkUnhealthy,
            self::BenchmarkRecovered,
        ], strict: true);
    }

    /**
     * Get the severity of a notification for this event.
     */
    public function level(): NotificationLevel
    {
        return match ($this) {
            self::Failed => NotificationLevel::Failure,
            self::BenchmarkUnhealthy => NotificationLevel::Warning,
            self::Completed, self::Recovered, self::BenchmarkRecovered => NotificationLevel::Success,
        };
    }

    /**
     * Get the fully qualified event class this notification event maps to.
     *
     * @return class-string
     */
    public function eventClass(): string
    {
        return match ($this) {
            self::Completed => SpeedtestCompleted::class,
            self::Failed => SpeedtestFailed::class,
            self::Recovered => SpeedtestRecovered::class,
            self::BenchmarkUnhealthy => SpeedtestBenchmarkUnhealthy::class,
            self::BenchmarkRecovered => SpeedtestBenchmarkRecovered::class,
        };
    }

    /**
     * Resolve the notification event for a given event class, if any.
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
