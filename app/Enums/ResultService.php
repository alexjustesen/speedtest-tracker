<?php

namespace App\Enums;

use App\Services\Speedtests\CloudflareSpeedtest;
use App\Services\Speedtests\Contracts\Speedtest;
use App\Services\Speedtests\FileDownloadSpeedtest;
use App\Services\Speedtests\Iperf3Speedtest;
use App\Services\Speedtests\YandexSpeedtest;
use App\Settings\SpeedtestSettings;
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
     * Sources configured for the service in the speedtest settings, used when a test
     * has no target and as suggestions in the UI.
     */
    public function configuredTargets(): array
    {
        $settings = app(SpeedtestSettings::class);

        $targets = match ($this) {
            self::Iperf3 => $settings->iperf3_servers,
            self::File => $settings->file_urls,
            default => [],
        };

        return array_values(array_filter(array_map('trim', $targets)));
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
