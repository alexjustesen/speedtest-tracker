<?php

namespace App\Enums;

use App\Helpers\Number;
use App\Models\Result;
use Filament\Support\Contracts\HasLabel;

enum BenchmarkMetric: string implements HasLabel
{
    case Download = 'download';
    case Upload = 'upload';
    case Ping = 'ping';
    case PacketLoss = 'packet_loss';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Download => __('enums.benchmark_metric.download'),
            self::Upload => __('enums.benchmark_metric.upload'),
            self::Ping => __('enums.benchmark_metric.ping'),
            self::PacketLoss => __('enums.benchmark_metric.packet_loss'),
        };
    }

    /**
     * Get the comparison direction required to pass this metric's benchmark.
     */
    public function direction(): string
    {
        return match ($this) {
            self::Download, self::Upload => 'min',
            self::Ping, self::PacketLoss => 'max',
        };
    }

    /**
     * Get the unit this metric is displayed and configured in.
     */
    public function unit(): string
    {
        return match ($this) {
            self::Download, self::Upload => 'mbps',
            self::Ping => 'ms',
            self::PacketLoss => '%',
        };
    }

    /**
     * Get the result's current value for this metric, in its display unit.
     */
    public function valueFor(Result $result): float|int|null
    {
        return match ($this) {
            self::Download => $result->download_bits !== null
                ? Number::bitsToMagnitude(bits: $result->download_bits, precision: 2, magnitude: 'mbit')
                : null,
            self::Upload => $result->upload_bits !== null
                ? Number::bitsToMagnitude(bits: $result->upload_bits, precision: 2, magnitude: 'mbit')
                : null,
            self::Ping => $result->ping,
            self::PacketLoss => $result->packet_loss,
        };
    }
}
