<?php

namespace App\Enums;

use App\Services\Speedtests\CloudflareSpeedtest;
use App\Services\Speedtests\Contracts\Speedtest;
use App\Services\Speedtests\FileDownloadSpeedtest;
use App\Services\Speedtests\Iperf3Speedtest;
use App\Services\Speedtests\YandexSpeedtest;
use Filament\Support\Contracts\HasLabel;

enum ResultService: string implements HasLabel
{
    case Faker = 'faker';
    case Ookla = 'ookla';
    case Cloudflare = 'cloudflare';
    case Yandex = 'yandex';
    case Iperf3 = 'iperf3';
    case File = 'file';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Faker => __('enums.service.faker'),
            self::Ookla => __('enums.service.ookla'),
            self::Cloudflare => __('enums.service.cloudflare'),
            self::Yandex => __('enums.service.yandex'),
            self::Iperf3 => __('enums.service.iperf3'),
            self::File => __('enums.service.file'),
        };
    }

    /**
     * Services a user can run a speedtest with, keyed by value.
     */
    public static function selectable(): array
    {
        return collect(self::cases())
            ->reject(fn (self $service): bool => $service === self::Faker)
            ->mapWithKeys(fn (self $service): array => [$service->value => $service->getLabel()])
            ->toArray();
    }

    /**
     * Whether the service needs a target (server or URL) to run against.
     */
    public function requiresTarget(): bool
    {
        return in_array($this, [self::Iperf3, self::File], true);
    }

    /**
     * Targets configured for the service, used for scheduled tests and as suggestions in the UI.
     */
    public function configuredTargets(): array
    {
        $targets = match ($this) {
            self::Iperf3 => config('speedtest.iperf3.servers'),
            self::File => config('speedtest.file.urls'),
            default => null,
        };

        return array_values(array_filter(array_map('trim', explode(',', (string) $targets))));
    }

    /**
     * Get the runner for services that are measured by the application itself.
     */
    public function speedtest(): ?Speedtest
    {
        return match ($this) {
            self::Cloudflare => app(CloudflareSpeedtest::class),
            self::Yandex => app(YandexSpeedtest::class),
            self::Iperf3 => app(Iperf3Speedtest::class),
            self::File => app(FileDownloadSpeedtest::class),
            default => null,
        };
    }
}
