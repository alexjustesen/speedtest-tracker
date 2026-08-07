<?php

namespace App\Filament\Widgets;

use App\Enums\ResultStatus;
use App\Helpers\Number;
use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public ?Result $result = null;

    protected ?string $pollingInterval = '60s';

    protected function getCards(): array
    {
        $results = Result::query()
            ->select(['id', 'ping', 'download', 'upload', 'data', 'status', 'created_at'])
            ->where('status', '=', ResultStatus::Completed)
            ->latest()
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

        $this->result = $results->last();

        if (blank($this->result)) {
            return [
                Stat::make('Latest download', '-')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make('Latest upload', '-')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make('Latest ping', '-')
                    ->icon('heroicon-o-clock'),
                Stat::make('Latest packet loss', '-')
                    ->icon('heroicon-o-signal-slash'),
            ];
        }

        $downloadChart = $results->map(fn (Result $item): float => Number::bitsToMagnitude(bits: $item->download_bits, precision: 2, magnitude: 'mbit'))->all();
        $uploadChart = $results->map(fn (Result $item): float => Number::bitsToMagnitude(bits: $item->upload_bits, precision: 2, magnitude: 'mbit'))->all();
        $pingChart = $results->pluck('ping')->all();
        $packetLossChart = $results->pluck('packet_loss')->filter(fn ($value) => ! blank($value))->values()->all();

        $previous = $results->count() >= 2 ? $results[$results->count() - 2] : null;

        if (! $previous) {
            return [
                Stat::make('Latest download', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->download_bits, precision: 2) : 'n/a')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->chart($downloadChart),
                Stat::make('Latest upload', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->upload_bits, precision: 2) : 'n/a')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->chart($uploadChart),
                Stat::make('Latest ping', fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' ms' : 'n/a')
                    ->icon('heroicon-o-clock')
                    ->chart($pingChart),
                Stat::make('Latest packet loss', fn (): string => ! blank($this->result->packet_loss) ? number_format($this->result->packet_loss, 2).' %' : 'n/a')
                    ->icon('heroicon-o-signal-slash')
                    ->chart($packetLossChart),
            ];
        }

        $downloadChange = Number::percentChange($this->result->download, $previous->download, 2);
        $uploadChange = Number::percentChange($this->result->upload, $previous->upload, 2);
        $pingChange = Number::percentChange($this->result->ping, $previous->ping, 2);
        $packetLossChange = (! blank($this->result->packet_loss) && ! blank($previous->packet_loss))
            ? Number::percentChange($this->result->packet_loss, $previous->packet_loss, 2)
            : null;

        return [
            Stat::make('Latest download', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->download_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-down-tray')
                ->description($downloadChange > 0 ? $downloadChange.'% faster' : abs($downloadChange).'% slower')
                ->descriptionIcon($downloadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($downloadChange > 0 ? 'success' : 'danger')
                ->chart($downloadChart),
            Stat::make('Latest upload', fn (): string => ! blank($this->result) ? Number::toBitRate(bits: $this->result->upload_bits, precision: 2) : 'n/a')
                ->icon('heroicon-o-arrow-up-tray')
                ->description($uploadChange > 0 ? $uploadChange.'% faster' : abs($uploadChange).'% slower')
                ->descriptionIcon($uploadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uploadChange > 0 ? 'success' : 'danger')
                ->chart($uploadChart),
            Stat::make('Latest ping', fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' ms' : 'n/a')
                ->icon('heroicon-o-clock')
                ->description($pingChange > 0 ? $pingChange.'% slower' : abs($pingChange).'% faster')
                ->descriptionIcon($pingChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pingChange > 0 ? 'danger' : 'success')
                ->chart($pingChart),
            Stat::make('Latest packet loss', fn (): string => ! blank($this->result->packet_loss) ? number_format($this->result->packet_loss, 2).' %' : 'n/a')
                ->icon('heroicon-o-signal-slash')
                ->description($packetLossChange === null ? null : ($packetLossChange > 0 ? $packetLossChange.'% more' : abs($packetLossChange).'% less'))
                ->descriptionIcon($packetLossChange === null ? null : ($packetLossChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'))
                ->color($packetLossChange === null ? null : ($packetLossChange > 0 ? 'danger' : 'success'))
                ->chart($packetLossChart),
        ];
    }
}
