<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    public ?Result $result = null;

    protected function getPollingInterval(): ?string
    {
        return config('speedtest.dashboard_polling');
    }

    protected function getCards(): array
    {
        if (blank($this->result) || ! $this->result->successful) {
            return [
                Stat::make('Latest download', '-')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make('Latest upload', '-')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make('Latest ping', '-')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $previous = Result::query()
            ->where('id', '<', $this->result->id)
            ->latest()
            ->first();

        if (! $previous || ! $previous->successful) {
            return [
                Stat::make('Latest download', fn (): string => ! blank($this->result) ? toBits(convertSize($this->result->download), 2).' (Mbps)' : 'n/a')
                    ->icon('heroicon-o-arrow-down-tray'),
                Stat::make('Latest upload', fn (): string => ! blank($this->result) ? toBits(convertSize($this->result->upload), 2).' (Mbps)' : 'n/a')
                    ->icon('heroicon-o-arrow-up-tray'),
                Stat::make('Latest ping', fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' (ms)' : 'n/a')
                    ->icon('heroicon-o-clock'),
            ];
        }

        $downloadChange = percentChange($this->result->download, $previous->download, 2);
        $uploadChange = percentChange($this->result->upload, $previous->upload, 2);
        $pingChange = percentChange($this->result->ping, $previous->ping, 2);

        return [
            Stat::make('Latest download', fn (): string => ! blank($this->result) ? toBits(convertSize($this->result->download), 2).' (Mbps)' : 'n/a')
                ->icon('heroicon-o-arrow-down-tray')
                ->description($downloadChange > 0 ? $downloadChange.'% faster' : abs($downloadChange).'% slower')
                ->descriptionIcon($downloadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($downloadChange > 0 ? 'success' : 'danger'),
            Stat::make('Latest upload', fn (): string => ! blank($this->result) ? toBits(convertSize($this->result->upload), 2).' (Mbps)' : 'n/a')
                ->icon('heroicon-o-arrow-up-tray')
                ->description($uploadChange > 0 ? $uploadChange.'% faster' : abs($uploadChange).'% slower')
                ->descriptionIcon($uploadChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($uploadChange > 0 ? 'success' : 'danger'),
            Stat::make('Latest ping', fn (): string => ! blank($this->result) ? number_format($this->result->ping, 2).' (ms)' : 'n/a')
                ->icon('heroicon-o-clock')
                ->description($pingChange > 0 ? $pingChange.'% slower' : abs($pingChange).'% faster')
                ->descriptionIcon($pingChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($pingChange > 0 ? 'danger' : 'success'),
        ];
    }
}
